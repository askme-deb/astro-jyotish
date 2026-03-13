<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\BaseApiClient;

class AstrologerAppointmentsController extends Controller
{
    public function index(Request $request)
    {
        // $user = Auth::user();
        // $userId = $user ? $user->id : null;
            // Get user ID from session (set by LoginController)
            $userId = session('api_user_id');
            if (!$userId) {
                return redirect()->route('login');
            }

        // Use existing AstrologerApiService from Services/Api/Clients
        $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);
        $token = session('auth.api_token');
        $response = $apiService->getAstrologerBookings($userId, $token);
        $appointments = $response['data'] ?? [];

        return view('astrologer.appointments', compact('appointments'));
    }


    public function show($id)
    {
        $userId = session('api_user_id');
        $token = session('auth.api_token');
        $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);
        $response = $apiService->getAstrologerBookings($userId, $token);
        $appointments = $response['data'] ?? [];
        $appointment = collect($appointments)->firstWhere('id', $id);
        return view('astrologer.appointment-details', compact('appointment'));
    }
}
