<?php

namespace App\Services;

use App\Services\Api\Clients\AstrologerApiService;

class AstrologerBookingService
{
    protected $apiClient;

    public function __construct(AstrologerApiService $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getAstrologers()
    {
        return $this->apiClient->getAstrologers();
    }

    public function getSlots($astrologerId, $date)
    {
        return $this->apiClient->getAvailableSlots($astrologerId, $date);
    }

    public function bookConsultation(array $data, $token = null)
    {
        return $this->apiClient->createBooking($data, $token);
    }

    public function getSessionDuration($astrologerId)
    {
        return $this->apiClient->getSessionDuration($astrologerId);
    }
}
