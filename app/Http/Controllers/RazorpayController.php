<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\RazorpayService;

class RazorpayController extends Controller
{
    // POST /v1/razorpay/order
    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
        ]);
        $service = new RazorpayService();
        $result = $service->createOrder($validated['booking_id'], $validated['amount'], $validated['currency']);
        if (isset($result['status']) && $result['status']) {
            return response()->json([
                'status' => true,
                'data' => $result['data'] ?? []
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => $result['message'] ?? 'Order not found'
        ], 400);
    }

    // POST /v1/razorpay/verify
    public function verifyPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);
        $service = new RazorpayService();
        $result = $service->verifyPayment($validated);
        if (isset($result['status']) && $result['status']) {
            return response()->json([
                'status' => true,
                'message' => $result['message'] ?? 'Payment verified successfully.',
                'data' => $result['data'] ?? []
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => $result['message'] ?? 'Payment verification failed.'
        ], 400);
    }

    // POST /v1/razorpay/booking
    public function confirmBooking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'booking_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);
        $service = new RazorpayService();
        $result = $service->confirmBooking($validated);
        if (isset($result['status']) && $result['status']) {
            return response()->json([
                'status' => true,
                'message' => $result['message'] ?? 'Booking created and payment verified successfully.',
                'data' => $result['data'] ?? []
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => $result['message'] ?? 'Booking not found for this order.'
        ], 400);
    }
}
