<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifies citizens against the Zanzibar ID (ZAN ID) system through the
 * Ajira API. Configuration lives in config/services.php under "ajira".
 */
class ZanIdService
{
    private ?string $apiUrl;
    private ?string $endpoint;
    private ?string $token;
    private int $timeout;
    private int $connectTimeout;

    public function __construct()
    {
        $config = config('services.ajira');

        $this->apiUrl = (string) ($config['api_url'] ?? '');
        $this->endpoint = (string) ($config['zanid_endpoint'] ?? '');
        $this->token = $config['token'] ?? null;
        $this->timeout = (int) ($config['timeout'] ?? 30) ?: 30;
        $this->connectTimeout = (int) ($config['connect_timeout'] ?? 10) ?: 10;
    }

    public function getUrl(): string
    {
        $endpoint = trim((string) ($this->endpoint ?? ''));
        $apiUrl = trim((string) ($this->apiUrl ?? ''));

        if (str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')) {
            return $endpoint;
        }

        if (str_starts_with($apiUrl, 'http://') || str_starts_with($apiUrl, 'https://')) {
            $path = ltrim($endpoint, '/');
            if ($path === '') {
                return rtrim($apiUrl, '/');
            }
            return rtrim($apiUrl, '/') . '/' . $path;
        }

        return '';
    }

    /**
     * Verify a ZAN ID and retrieve the citizen's data.
     *
     * Returns an array with:
     *  - success (bool)
     *  - status  ('ok' | 'not_found' | 'unconfigured' | 'error')
     *  - message (string, user-presentable)
     *  - data    (normalized citizen data when success: zan_id, name,
     *             date_of_birth, gender, phone, raw)
     */
    public function verify(string $zanId): array
    {
        if (!$this->isConfigured()) {
            Log::error('ZAN ID service is not configured. Set AJIRA_API_URL or AJIRA_ZANID_ENDPOINT and AJIRA_API_TOKEN.');

            return $this->result('unconfigured', 'Identity verification service is not available at the moment. Please try again later.');
        }

        $payload = ['zan_id' => $zanId];
        $body = json_encode($payload);
        $targetUrl = $this->getUrl();

        try {
            $request = Http::withHeaders($this->headers($body))
                ->withBody($body, 'application/json')
                ->timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->acceptJson();

            $response = $request->post($targetUrl);
        } catch (\Throwable $e) {
            Log::error('ZAN ID API connection failed: ' . $e->getMessage());

            return $this->result('error', 'Unable to reach the identity verification service. Please try again.');
        }
        Log::info('ZAN ID API response', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        if ($response->status() === 404 || $response->status() === 422) {
            return $this->result('not_found', 'No citizen record was found for the provided ZAN ID.');
        }

        if (!$response->successful()) {
            Log::error('ZAN ID API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->result('error', 'Identity verification failed. Please try again.');
        }

        $citizen = $this->normalize($response->json() ?? [], $zanId);

        if (!$citizen || empty($citizen['name'])) {
            Log::warning('ZAN ID API returned an unexpected payload', [
                'body' => $response->body(),
            ]);

            return $this->result('not_found', 'No citizen record was found for the provided ZAN ID.');
        }

        return [
            'success' => true,
            'status' => 'ok',
            'message' => 'ZAN ID verified successfully.',
            'data' => $citizen,
        ];
    }

    public function isConfigured(): bool
    {
        return $this->getUrl() !== '' && !empty($this->token);
    }

    private function headers(string $body): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];

        $signature = $this->signPayload($body);
        if ($signature !== null) {
            $headers['X-Signature'] = $signature;
        }

        return $headers;
    }

    /**
     * Sign the request payload with the configured PKCS#12 certificate.
     * Signing is skipped when no certificate has been configured.
     */
   private function signPayload(string $payload): ?string
{
    $config = config('services.ajira');

    $path = trim((string) ($config['signing_certificate_path'] ?? ''), '/\\');
    $file = (string) ($config['signing_certificate_file'] ?? '');
    $password = (string) ($config['signing_certificate_password'] ?? '');

    if ($path === '' || $file === '') {
        return null;
    }

    $certificatePath = base_path($path . DIRECTORY_SEPARATOR . $file);

    if (!is_readable($certificatePath)) {
        Log::error('ZAN ID signing certificate is not readable: ' . $certificatePath);
        return null;
    }

    if (!openssl_pkcs12_read(
        (string) file_get_contents($certificatePath),
        $certificates,
        $password
    )) {
        Log::error('Unable to read ZAN ID signing certificate (invalid file or password).');
        return null;
    }

    $signature = null;

    if (!openssl_sign(
        $payload,
        $signature,
        $certificates['pkey'],
        OPENSSL_ALGO_SHA256
    )) {
        Log::error('Failed to sign ZAN ID API payload.');
        return null;
    }

    return base64_encode($signature);
}

    /**
     * Normalize the various possible API response shapes into a single
     * citizen data structure.
     */
    private function normalize(array $response, string $zanId): ?array
    {
        // Unwrap common response envelopes.
        foreach (['data', 'result', 'response', 'citizen', 'resident'] as $key) {
            if (isset($response[$key]) && is_array($response[$key])) {
                $response = $response[$key];
                break;
            }
        }

        $name = $this->pick($response, ['full_name', 'fullName', 'name', 'fullname']);

        if (!$name) {
            $parts = array_filter([
                $this->pick($response, ['first_name', 'firstName', 'fname']),
                $this->pick($response, ['middle_name', 'middleName', 'mname']),
                $this->pick($response, ['last_name', 'lastName', 'surname', 'lname']),
            ]);
            $name = $parts ? implode(' ', $parts) : null;
        }

        if (!$name) {
            return null;
        }

        $dateOfBirth = $this->pick($response, ['date_of_birth', 'dateOfBirth', 'dob', 'birth_date', 'birthDate']);
        $dateOfBirth = $this->normalizeDate($dateOfBirth);

        $gender = $this->pick($response, ['gender', 'sex']);
        if ($gender) {
            $gender = ucfirst(strtolower($gender));
            $gender = in_array($gender, ['Male', 'Female']) ? $gender : null;
        }

        return [
            'zan_id' => $this->pick($response, ['zan_id', 'zanId', 'national_id', 'nationalId', 'nin', 'resident_number', 'idNumber']) ?: $zanId,
            'name' => $name,
            'date_of_birth' => $dateOfBirth,
            'gender' => $gender,
            'phone' => $this->pick($response, ['phone', 'phone_number', 'phoneNumber', 'mobile', 'msisdn']),
            'raw' => $response,
        ];
    }

    private function pick(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && is_scalar($data[$key]) && trim((string) $data[$key]) !== '') {
                return trim((string) $data[$key]);
            }
        }

        return null;
    }

    private function normalizeDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        $timestamp = strtotime($date);

        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function result(string $status, string $message): array
    {
        return [
            'success' => false,
            'status' => $status,
            'message' => $message,
            'data' => null,
        ];
    }
}
