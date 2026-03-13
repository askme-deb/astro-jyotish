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

    /**
     * Fetch bookings for the authenticated user.
     */
    public function getBookings($token = null)
    {
        return $this->apiClient->getBookings($token);
    }

    /**
     * Fetch a single booking for the authenticated user.
     */
    public function getBookingById($bookingId, $token = null)
    {
        return $this->apiClient->getBookingById($bookingId, $token);
    }

    public function rescheduleBooking(array $payload, $token = null)
    {
        return $this->apiClient->rescheduleBooking($payload, $token);
    }

    public function rescheduleAstrologerBooking($bookingId, array $payload, $token = null)
    {
        return $this->apiClient->rescheduleAstrologerBooking($bookingId, $payload, $token);
    }

    public function cancelBooking($bookingId, $token = null)
    {
        return $this->apiClient->cancelBooking($bookingId, $token);
    }

    /**
     * Fetch bookings for a specific astrologer (admin/astrologer view).
     */
    public function getAstrologerBookings($astrologerId, $token = null)
    {
        return $this->apiClient->getAstrologerBookings($astrologerId, $token);
    }
}
