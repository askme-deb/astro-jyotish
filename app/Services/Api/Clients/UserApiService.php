<?php

namespace App\Services\Api\Clients;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\UploadedFile;

class UserApiService extends BaseApiClient
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Update user profile via external API
     * @param array $data
     * @param string $token
     * @return array
     */
    public function updateProfile(array $data, string $token): array
    {
        $options = [
            'json' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ];
        return $this->request('PUT', '/user/profile', $options);
    }

    public function listSupportTickets(array $filters = [], $token = null)
    {
        $options = [
            'query' => array_filter($filters, function ($value) {
                return $value !== null && $value !== '';
            }),
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('GET', 'support-tickets', $options);
        } catch (RequestException $e) {
            $result = $e->response ? $e->response->json() : [];
            return $this->formatSupportTicketApiError(is_array($result) ? $result : [], $e->response?->status() ?? 422, 'Failed to load support tickets.');
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function getSupportTicketDetails($ticketId, $token = null)
    {
        $options = [];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('GET', 'support-tickets/' . $ticketId, $options);
        } catch (RequestException $e) {
            $result = $e->response ? $e->response->json() : [];
            return $this->formatSupportTicketApiError(is_array($result) ? $result : [], $e->response?->status() ?? 422, 'Failed to load support ticket details.');
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function createSupportTicket(array $payload, array $attachments = [], $token = null)
    {
        $request = $this->buildRequest();

        if ($token) {
            $request = $request->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ]);
        }

        foreach ($attachments as $attachment) {
            if (!$attachment instanceof UploadedFile) {
                continue;
            }

            $request = $request->attach(
                'attachments[]',
                fopen($attachment->getRealPath(), 'r'),
                $attachment->getClientOriginalName()
            );
        }

        try {
            $response = $request->post('support-tickets', $payload);
            $result = $response->json();

            if (!is_array($result)) {
                $result = [];
            }

            if (!$response->successful()) {
                return $this->formatSupportTicketApiError($result, $response->status(), 'Failed to create support ticket.');
            }

            return $result;
        } catch (RequestException $e) {
            $result = $e->response ? $e->response->json() : [];
            return $this->formatSupportTicketApiError(is_array($result) ? $result : [], $e->response?->status() ?? 422, 'Failed to create support ticket.');
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    private function formatSupportTicketApiError(array $result, int $statusCode, string $default): array
    {
        $errors = $this->extractValidationErrors($result);

        return [
            'error' => true,
            'message' => $this->extractFirstErrorMessage($result, $errors, $default),
            'errors' => $errors,
            'status_code' => $statusCode,
            'data' => $result,
        ];
    }

    private function extractValidationErrors(array $result): array
    {
        $candidates = [
            $result['errors'] ?? null,
            data_get($result, 'error.errors'),
            data_get($result, 'data.errors'),
        ];

        foreach ($candidates as $candidate) {
            if (!is_array($candidate)) {
                continue;
            }

            $normalized = [];

            foreach ($candidate as $field => $messages) {
                $key = is_string($field) && $field !== '' ? $field : 'general';

                if (is_array($messages)) {
                    $normalized[$key] = array_values(array_filter(array_map(function ($message) {
                        return is_scalar($message) ? trim((string) $message) : null;
                    }, $messages)));
                } elseif (is_scalar($messages)) {
                    $normalized[$key] = [trim((string) $messages)];
                }
            }

            $normalized = array_filter($normalized, function ($messages) {
                return is_array($messages) && $messages !== [];
            });

            if ($normalized !== []) {
                return $normalized;
            }
        }

        return [];
    }

    private function extractFirstErrorMessage(array $result, array $errors, string $default): string
    {
        foreach ($errors as $fieldErrors) {
            if (is_array($fieldErrors) && isset($fieldErrors[0]) && is_string($fieldErrors[0]) && trim($fieldErrors[0]) !== '') {
                return trim($fieldErrors[0]);
            }

            if (is_string($fieldErrors) && trim($fieldErrors) !== '') {
                return trim($fieldErrors);
            }
        }

        $message = $result['message'] ?? data_get($result, 'error.message') ?? data_get($result, 'data.message');

        return is_string($message) && trim($message) !== '' ? trim($message) : $default;
    }
}
