<?php

namespace App\Services\Api\Clients;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * BaseApiClient centralizes HTTP configuration, retries, and error handling
 * for external API integrations. Concrete API clients should extend this
 * class and use the protected request helpers.
 */
abstract class BaseApiClient
{
    protected string $baseUrl;
    protected ?string $token;
    protected int $timeoutSeconds;
    protected int $retryTimes;
    protected int $retrySleepMilliseconds;

    /**
     * Initialize API client with config values from config/auth_api.php.
     * Never hardcode credentials or URLs.
     */
    public function __construct(array $config)
    {
        $this->baseUrl = (string) ($config['base_url'] ?? '');
        $this->token = $config['token'] ?? null;
        $this->timeoutSeconds = (int) ($config['timeout'] ?? 10);
        $this->retryTimes = (int) ($config['retry'] ?? 2);
        $this->retrySleepMilliseconds = 200; // ms, can be made configurable
    }

    /**
     * Centralized request handler for API calls.
     * Handles retries, timeouts, and error logging.
     *
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return array
     */
    protected function request(string $method, string $uri, array $options = []): array
    {
        $request = $this->buildRequest();

        try {
            /** @var array<string, mixed> $requestOptions */
            $requestOptions = [];

            if (! empty($options['query'])) {
                $requestOptions['query'] = $options['query'];
            }

            if (! empty($options['json'])) {
                $requestOptions['json'] = $options['json'];
            }

            if (! empty($options['headers'])) {
                $requestOptions['headers'] = $options['headers'];
            }

            /** @var Response $response */
            $response = $request->send($method, $uri, $requestOptions);
        } catch (ConnectionException $exception) {
            Log::warning('External API connection issue', [
                'service' => static::class,
                'uri' => $uri,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('External API unexpected error', [
                'service' => static::class,
                'uri' => $uri,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        if (! $response->successful()) {
            Log::error('External API returned non-success status', [
                'service' => static::class,
                'uri' => $uri,
                'status' => $response->status(),
            ]);

            // Let callers decide how to handle an unsuccessful response.
            // Here we still attempt to parse JSON safely.
        }

        $data = $response->json();

        if (! is_array($data)) {
            Log::error('External API returned invalid JSON structure', [
                'service' => static::class,
                'uri' => $uri,
                'status' => $response->status(),
            ]);

            return [];
        }

        return $data;
    }

    /**
     * Build the configured HTTP client instance.
     * Adds Bearer token, timeout, retry, and JSON headers.
     *
     * @return PendingRequest
     */
    protected function buildRequest(): PendingRequest
    {
        $request = Http::baseUrl($this->baseUrl)
            ->timeout($this->timeoutSeconds)
            ->retry(
                $this->retryTimes,
                $this->retrySleepMilliseconds,
                function ($exception) {
                    return $exception instanceof ConnectionException;
                }
            )
            ->acceptJson();

        if ($this->token !== null && $this->token !== '') {
            $request = $request->withToken($this->token);
        }

        return $request;
    }
}
