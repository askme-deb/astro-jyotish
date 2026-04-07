<?php

namespace App\Services\Api\Clients;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\Client\RequestException;

class AstrologerApiService extends BaseApiClient
{
    public function __construct()
    {
        parent::__construct(config('auth_api'));
    }

    public function getAstrologers()
    {
        return $this->request('GET', 'astrologers');
    }

    public function getAvailableSlots($astrologerId, $date)
    {
        return $this->request('GET', 'available-slots', [
            'query' => [
                'astrologer_id' => $astrologerId,
                'date' => $date,
            ]
        ]);
    }

    public function createBooking(array $data, $token = null)
    {
        $options = [
            'json' => $data
        ];
        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token
            ];
        }
        return $this->request('POST', 'bookings', $options);
    }

    public function getSessionDuration($astrologerId)
    {
            return $this->request('GET', "astrologers/{$astrologerId}/session-duration");
    }

    public function getCategories($token = null)
    {
        $options = [];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        return $this->request('GET', 'categories', $options);
    }

    public function getProductGrades($token = null)
    {
        $options = [
            'query' => [
                'sort_by' => 'name_asc',
            ],
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        return $this->request('GET', 'product-grades', $options);
    }

    /**
     * Fetch bookings for the authenticated user.
    */
    public function getBookings($token = null)
    {
        $options = [];
        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token
            ];
        }
        return $this->request('GET', 'bookings', $options);
    }

    /**
     * Fetch bookings for a specific astrologer (admin/astrologer view).
     */
    public function getAstrologerBookings($astrologerId, $token = null)
    {
        $options = [];
        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token
            ];
        }
        return $this->request('GET', "astrologer/{$astrologerId}/bookings", $options);
    }

        /**
     * Fetch a single booking by its ID.
     */
    public function getBookingById($bookingId, $token = null)
    {
        $options = [];
        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token
            ];
        }
        return $this->request('GET', "booking-by-id/{$bookingId}", $options);
    }

    public function rescheduleBooking(array $payload, $token = null)
    {
        $options = [
            'json' => $payload,
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        return $this->request('POST', 'booking/reschedule', $options);
    }

    public function rescheduleAstrologerBooking($bookingId, array $payload, $token = null)
    {
        $options = [
            'json' => $payload,
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('POST', 'booking/reschedule', $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function cancelBooking($bookingId, $token = null)
    {
        $options = [
            'json' => [
                'status' => 'cancelled',
            ],
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('PATCH', "booking-by-id/{$bookingId}", $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }



    /**
     * Mark a consultation as ready for the customer to join.
     */
    public function startVideoConsultation($bookingID, $token)
    {
        $payload = [
            'meeting_id' => 'astro-' . $bookingID,
            'status' => 'ready_to_start',
        ];
        $options = [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        try {
            return $this->request('PATCH', "booking-by-id/{$bookingID}", $options);
        } catch (\Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Mark a consultation as in progress once the customer joins.
     */
    public function joinVideoConsultation($bookingID, $token)
    {
        $payload = [
            'meeting_id' => 'astro-' . $bookingID,
            'meeting_started_at' => now()->toDateTimeString(),
            'status' => 'in_progress',
        ];
        $options = [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ];

        try {
            return $this->request('PATCH', "booking-by-id/{$bookingID}", $options);
        } catch (\Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * End a video consultation session (PATCH).
     */
    public function endVideoConsultation($bookingID, $token)
    {
        $payload = [
            'meeting_ended_at' => now()->toDateTimeString(),
            'status' => 'completed',
        ];
        $options = [
            'json' => $payload,
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        try {
            return $this->request('PATCH', "booking-by-id/{$bookingID}", $options);
        } catch (\Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Save or update the astrologer's consultation note (PATCH).
     */
    public function saveAstrologerNote($bookingId, ?string $astrologerNote, $token)
    {
        $options = [
            'json' => [
                'astrologer_note' => $astrologerNote ?? '',
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ];

        try {
            return $this->request('PATCH', "bookings/{$bookingId}/astrologer-note", $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function finalizeAstrologerNote($bookingId, $token)
    {
        $options = [
            'json' => [
                'astrologer_note_status' => 'finalized',
                'final_confirmation_from_astrologer' => true,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ];

        try {
            return $this->request('PATCH', "bookings/{$bookingId}/astrologer-note-status", $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function submitAstrologerAbuseReport(array $payload, $token = null)
    {
        $request = $this->buildRequest();

        if ($token) {
            $request = $request->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ]);
        }

        try {
            $response = $request->post('astrologer-abuse-reports', $payload);
            $result = $response->json();

            if (!is_array($result)) {
                $result = [];
            }

            if (!$response->successful()) {
                $errors = $this->extractValidationErrors($result);

                return [
                    'error' => true,
                    'message' => $this->extractFirstErrorMessage($result, $errors, 'Failed to submit abuse report.'),
                    'errors' => $errors,
                    'status_code' => $response->status(),
                    'data' => $result,
                ];
            }

            return $result;
        } catch (RequestException $e) {
            $result = $e->response ? $e->response->json() : [];
            if (!is_array($result)) {
                $result = [];
            }

            $errors = $this->extractValidationErrors($result);

            return [
                'error' => true,
                'message' => $this->extractFirstErrorMessage($result, $errors, 'Failed to submit abuse report.'),
                'errors' => $errors,
                'status_code' => $e->response?->status() ?? 422,
                'data' => $result,
            ];
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    private function extractValidationErrors(array $result): array
    {
        $errors = $result['errors'] ?? data_get($result, 'data.errors') ?? data_get($result, 'error.errors');

        return is_array($errors) ? $errors : [];
    }

    private function extractFirstErrorMessage(array $result, array $errors, string $default): string
    {
        foreach ($errors as $fieldErrors) {
            if (is_array($fieldErrors) && count($fieldErrors) > 0) {
                return (string) $fieldErrors[0];
            }

            if (is_string($fieldErrors) && trim($fieldErrors) !== '') {
                return trim($fieldErrors);
            }
        }

        $message = $result['message'] ?? data_get($result, 'error.message') ?? data_get($result, 'data.message');

        return is_string($message) && trim($message) !== '' ? trim($message) : $default;
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

    /**
     * Search products for astrologer suggestions using the external catalog API.
     */
    public function searchProducts(array $filters, $token = null)
    {
        $options = [
            'query' => $filters,
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('GET', 'product/search', $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function addSuggestedProduct(array $payload, $token = null)
    {
        $options = [
            'json' => $payload,
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('POST', 'astrologer/add-to-cart', $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function getAstrologerCarts(array $payload, $token = null)
    {
        $options = [
            'json' => $payload,
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('POST', 'astrologer-carts', $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
    public function getAstrologerSuggestedProducts(array $payload, $token = null)
    {
        $options = [
            'json' => $payload,
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('POST', 'astrologer-suggested-products', $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function removeSuggestedProduct(array $payload, $token = null)
    {
        $options = [
            'json' => $payload,
        ];

        if ($token) {
            $options['headers'] = [
                'Authorization' => 'Bearer ' . $token,
            ];
        }

        try {
            return $this->request('POST', 'astrologer/remove-suggested-product', $options);
        } catch (\Throwable $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}

