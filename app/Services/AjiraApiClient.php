<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AjiraApiClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.ajira.url', ''), '/');
    }

    private function client(): PendingRequest
    {
        $client = Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout((int) config('services.ajira.timeout', 30))
            ->connectTimeout((int) config('services.ajira.connect_timeout', 10));

        if ($token = config('services.ajira.token')) {
            $client->withToken($token);
        }

        return $client;
    }

    /**
     * Obtain ZanID from external Ajira API.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    public function getZanID(array $data): array
    {
        $endpoint = (string) config('services.ajira.zanid_endpoint', '');

        $startTime = microtime(true);

        try {
            $response = $this->client()
                ->post($endpoint, $data)
                ->throw();

            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Ajira API getZanID request successful', [
                'method' => 'POST',
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'duration_ms' => $duration,
            ]);

            return $response->json();
        } catch (RequestException $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            Log::error('Ajira API getZanID request failed', [
                'method' => 'POST',
                'endpoint' => $endpoint,
                'status' => $e->response?->status(),
                'duration_ms' => $duration,
            ]);

            throw $e;
        }
    }
}
