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
        Log::info('[ZanIdService] Verification initiated', ['zanid' => $zanId]);

        if (!$this->isConfigured()) {
            Log::error('[ZanIdService] Service is not configured. Endpoint or token missing.', [
                'target_url' => $this->getUrl(),
                'has_token' => !empty($this->token),
            ]);

            return $this->result('unconfigured', 'Identity verification service is not available at the moment. Please try again later.');
        }

        $apiKey = config('services.ajira.token');
        $zanIdClean = trim($zanId);
        $innerPayload = [
            'apiKey' => $apiKey,
            'zanid' => $zanIdClean,
        ];
        $innerPayloadJson = json_encode($innerPayload);

        Log::info('[ZanIdService] Inner payload prepared', ['innerPayloadJson' => $innerPayloadJson]);

        try {
            $signingService = app(SigningService::class);
            $signature = $signingService->sign($innerPayloadJson);
            Log::info('[ZanIdService] Inner payload signed successfully', ['signature' => $signature]);
        } catch (\Throwable $e) {
            Log::error('[ZanIdService] Payload Signing Exception: ' . $e->getMessage(), ['zanid' => $zanId, 'trace' => $e->getTraceAsString()]);
            return $this->result('error', 'Unable to sign verification request payload: ' . $e->getMessage());
        }

        $finalPayload = [
            'payload' => $innerPayload,
            'signature' => $signature,
        ];
        $targetUrl = $this->getUrl();

        Log::info('[ZanIdService] Sending POST request to remote API', [
            'url' => $targetUrl,
            'finalPayload' => $finalPayload,
            'timeout' => $this->timeout,
        ]);

        try {
            $request = Http::timeout($this->timeout)
                ->connectTimeout($this->connectTimeout)
                ->acceptJson()
                ->asJson();

            $response = $request->post($targetUrl, $finalPayload);
        } catch (\Throwable $e) {
            Log::error('[ZanIdService] HTTP Request Exception: ' . $e->getMessage(), ['url' => $targetUrl, 'trace' => $e->getTraceAsString()]);

            return $this->result('error', 'Unable to reach the identity verification service. Please try again.');
        }

        Log::info('[ZanIdService] Remote API response received', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        if ($response->status() === 404) {
            Log::warning('[ZanIdService] Citizen record not found (HTTP 404)', ['zanid' => $zanId]);
            return $this->result('not_found', 'No citizen record was found for the provided ZAN ID.');
        }

        if (!$response->successful()) {
            Log::error('[ZanIdService] HTTP request failed non-2xx status', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->result('error', 'Identity verification failed. Please try again.');
        }

        $apiData = $response->json() ?? [];
        $payloadObj = $apiData['payload'] ?? null;

        Log::info('[ZanIdService] Parsing API response envelope', ['payloadObj' => $payloadObj]);

        if ($payloadObj && isset($payloadObj['code'])) {
            $code = (int) $payloadObj['code'];
            $msg = $payloadObj['msg'] ?? 'Unknown Error';

            Log::info('[ZanIdService] Envelope code evaluated', ['code' => $code, 'msg' => $msg]);

            if ($code === 9000 && !empty($payloadObj['data'])) {
                $citizen = $this->normalize($payloadObj['data'], $zanId);
                Log::info('[ZanIdService] Verification successful (Code 9000)', ['citizen' => $citizen]);
                return [
                    'success' => true,
                    'status' => 'ok',
                    'message' => 'ZAN ID verified successfully.',
                    'data' => $citizen,
                ];
            }

            if ($code === 9003) {
                Log::warning('[ZanIdService] Code 9003: Citizen not found', ['msg' => $msg]);
                return $this->result('not_found', 'No citizen record was found for the provided ZAN ID.');
            }

            if ($code === 9001) {
                Log::error('[ZanIdService] Code 9001: Signature verification failed on remote server', ['msg' => $msg]);
                return $this->result('error', 'Digital signature verification failed (9001).');
            }

            if ($code === 9002) {
                Log::error('[ZanIdService] Code 9002: Invalid API Key', ['msg' => $msg]);
                return $this->result('error', 'Invalid API Key provided (9002).');
            }

            Log::error('[ZanIdService] Unhandled response code', ['code' => $code, 'msg' => $msg]);
            return $this->result('error', "ZAN ID API Error ($code): $msg");
        }

        $citizen = $this->normalize($apiData, $zanId);

        if (!$citizen || empty($citizen['name'])) {
            Log::warning('[ZanIdService] Failed to normalize citizen record or missing name', [
                'body' => $response->body(),
                'apiData' => $apiData,
            ]);

            return $this->result('not_found', 'No citizen record was found for the provided ZAN ID.');
        }

        Log::info('[ZanIdService] Verification successful via direct normalized fallback', ['citizen' => $citizen]);
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

    $content = (string) file_get_contents($certificatePath);
    $privateKey = false;

    // Check if it's PEM format
    if (str_contains($content, '-----BEGIN')) {
        $privateKey = openssl_pkey_get_private($content, $password);
        if ($privateKey === false) {
            $privateKey = openssl_pkey_get_private($content);
        }
    } else {
        // Fallback to PFX format
        if (openssl_pkcs12_read($content, $certificates, $password) && !empty($certificates['pkey'])) {
            $privateKey = openssl_pkey_get_private($certificates['pkey']);
        }
    }

    if ($privateKey === false) {
        Log::error('Unable to read ZAN ID signing certificate (invalid file format or password).');
        return null;
    }

    $signature = null;

    if (!openssl_sign(
        $payload,
        $signature,
        $privateKey,
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
