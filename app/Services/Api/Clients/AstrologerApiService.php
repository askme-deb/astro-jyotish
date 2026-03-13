<?php

namespace App\Services\Api\Clients;

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
}

