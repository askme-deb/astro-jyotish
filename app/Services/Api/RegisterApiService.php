<?php

namespace App\Services\Api;

use App\Services\Api\Clients\BaseApiClient;
use Illuminate\Support\Str;

class RegisterApiService extends BaseApiClient
{
    /**
     * Register a new user via external API
     * @param array $data (first_name, last_name, mobile_no, email, password)
     * @return array
     */
    public function register(array $data): array
    {
        $correlationId = (string) Str::uuid();
        $endpoint = '/register';
        $payload = [
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'mobile_no' => $data['mobile_no'] ?? '',
            'email' => $data['email'] ?? '',
            'password' => $data['password'] ?? '',
        ];
        return $this->callApi('POST', $endpoint, $payload, $correlationId);
    }

    
    /**
     * Proxy to AuthApiService's callApi implementation (copied for RegisterApiService)
     */
    protected function callApi(string $method, string $endpoint, array $payload, string $correlationId): array
    {
        // You can refactor this to a trait if needed for DRY
        $maskedPayload = $payload;
        if (isset($maskedPayload['otp'])) {
            $maskedPayload['otp'] = '***';
        }

        \Log::info('Register API request', [
            'service' => static::class,
            'endpoint' => $endpoint,
            'correlation_id' => $correlationId,
            'payload' => $maskedPayload,
        ]);

        try {
            $data = $this->request($method, $endpoint, [
                'json' => $payload,
            ]);
        } catch (\Illuminate\Http\Client\RequestException $exception) {
            $responseBody = $exception->response ? $exception->response->json() : null;

            $userMessage = 'Registration service temporarily unavailable.';
            if (is_array($responseBody)) {
                // Prefer first validation error if present
                if (isset($responseBody['errors']) && is_array($responseBody['errors'])) {
                    foreach ($responseBody['errors'] as $fieldErrors) {
                        if (is_array($fieldErrors) && count($fieldErrors) > 0) {
                            $userMessage = $fieldErrors[0];
                            break;
                        }
                    }
                } elseif (isset($responseBody['message'])) {
                    $userMessage = (string) $responseBody['message'];
                }
            }

            \Log::error('Register API call failed', [
                'service' => static::class,
                'endpoint' => $endpoint,
                'correlation_id' => $correlationId,
                'status' => $exception->response?->status(),
                'message' => $exception->getMessage(),
                'api_message' => $userMessage,
            ]);

            return [
                'success' => false,
                'message' => $userMessage,
            ];
        }

        \Log::info('Register API response', [
            'service' => static::class,
            'endpoint' => $endpoint,
            'correlation_id' => $correlationId,
            'status' => $data['status'] ?? null,
        ]);

        return $data;
    }
}
