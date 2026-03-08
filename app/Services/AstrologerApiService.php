<?php

namespace App\Services;

use App\Services\Api\BaseApiClient;

class AstrologerApiService
{
    protected $apiClient;

    public function __construct()
    {
        $this->apiClient = new BaseApiClient();
    }

    /**
     * Fetch appointments for astrologer by user ID
     */
    public function getAppointments($userId)
    {
        $response = $this->apiClient->get("/api/v1/astrologer/{$userId}/bookings");
        return $response['data'] ?? [];
    }
}
