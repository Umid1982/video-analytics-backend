<?php

namespace App\HttpClients;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FastApiHttpClient
{
    protected string $baseUrl;
    protected string $apiKey;
    protected array $defaultHeaders;

    public function __construct(
        string $baseUrl,
        string $apiKey,
        array  $defaultHeaders = []
    )
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->defaultHeaders = $defaultHeaders;
    }

    /**
     * @param array $extra
     * @return array
     */
    private function headers(array $extra = []): array
    {
        return array_merge([
            'X-API-KEY' => $this->apiKey,
        ], $this->defaultHeaders, $extra);
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return Response
     * @throws ConnectionException
     */
    public function get(string $endpoint, array $params = [], array $headers = []): Response
    {
        return Http::withHeaders($this->headers($headers))
            ->timeout(config('services.fastapi.timeout', 10))
            ->retry(
                config('services.fastapi.retries', 2),
                config('services.fastapi.retry_sleep_ms', 200)
            )
            ->get($this->baseUrl . $endpoint, $params);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return Response
     * @throws ConnectionException
     */
    public function post(string $endpoint, array $data = [], array $headers = []): Response
    {
        return Http::withHeaders($this->headers($headers))
            ->timeout(config('services.fastapi.timeout', 10))
            ->retry(
                config('services.fastapi.retries', 2),
                config('services.fastapi.retry_sleep_ms', 200)
            )
            ->post($this->baseUrl . $endpoint, $data);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return Response
     * @throws ConnectionException
     */
    public function put(string $endpoint, array $data = [], array $headers = []): Response
    {
        return Http::withHeaders($this->headers($headers))
            ->timeout(config('services.fastapi.timeout', 10))
            ->retry(
                config('services.fastapi.retries', 2),
                config('services.fastapi.retry_sleep_ms', 200)
            )
            ->put($this->baseUrl . $endpoint, $data);
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @return Response
     * @throws ConnectionException
     */
    public function delete(string $endpoint, array $headers = []): Response
    {
        return Http::withHeaders($this->headers($headers))
            ->timeout(config('services.fastapi.timeout', 10))
            ->retry(
                config('services.fastapi.retries', 2),
                config('services.fastapi.retry_sleep_ms', 200)
            )
            ->delete($this->baseUrl . $endpoint);
    }
}

