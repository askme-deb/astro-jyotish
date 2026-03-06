<?php

namespace App\Services;

use App\Services\Api\Clients\BaseApiClient;

class RazorpayService extends BaseApiClient
{
    public function __construct()
    {
        // Load config from config/auth_api.php or .env
        $config = config('auth_api');
        parent::__construct([
            'base_url' => $config['base_url'] ?? '',
            'token' => $config['token'] ?? null,
            'timeout' => $config['timeout'] ?? 10,
            'retry' => $config['retry'] ?? 2,
        ]);
    }


    public function createOrder($bookingId, $amount, $currency)
    {
        $token = request()->cookie('auth_api_token') ?? '';
        return $this->request('POST', 'razorpay/order', [
            'json' => [
                'booking_id' => $bookingId,
                'amount' => $amount,
                'currency' => $currency,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);
    }


    public function verifyPayment($data)
    {
        $token = request()->cookie('auth_api_token') ?? '';
        return $this->request('POST', 'razorpay/verify', [
            'json' => [
                'booking_id' => $data['booking_id'],
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature'],
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);
    }

    public function confirmBooking($data)
    {
        $token = request()->cookie('auth_api_token') ?? '';
        return $this->request('POST', 'razorpay/booking', [
            'json' => [
                'booking_id' => $data['booking_id'],
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature'],
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ]
        ]);
    }
}
