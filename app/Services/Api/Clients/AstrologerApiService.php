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

      
}

