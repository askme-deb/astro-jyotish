<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\RegisterApiService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class RegisterController extends Controller
{
    protected $registerApi;

    public function __construct(RegisterApiService $registerApi)
    {
        $this->registerApi = $registerApi;
    }

    public function register(Request $request)
    {
        $data = $request->only(['first_name', 'last_name', 'mobile_no', 'email', 'password']);
        $response = $this->registerApi->register($data);

        // If validation error, return errors to frontend
        if (isset($response['errors']) && is_array($response['errors'])) {
            // Get the first error message if available
            $firstError = $response['message'] ?? 'Validation error';
            foreach ($response['errors'] as $fieldErrors) {
                if (is_array($fieldErrors) && count($fieldErrors) > 0) {
                    $firstError = $fieldErrors[0];
                    break;
                }
            }
            return response()->json([
                'success' => false,
                'message' => $firstError,
                'errors' => $response['errors'],
            ], 422);
        }

        // If registration is successful, log the user in (store session like LoginController)
        if (($response['success'] ?? false) && isset($response['data']['user']) && isset($response['data']['token'])) {
            $userData = $response['data']['user'];
            $token = $response['data']['token'];

            // Store in session (same as LoginController)
            Session::regenerate();
            Session::put('auth.api_token', $token);
            Session::put('auth.user', $userData);
            if (isset($userData['id'])) {
                Session::put('api_user_id', $userData['id']);
            }
            Session::save();

            // Optionally, set cookies if needed (see LoginController for details)
            $secure = $request->isSecure();
            $sessionCookie = cookie(
                config('session.cookie'),
                Session::getId(),
                config('session.lifetime'),
                config('session.path'),
                config('session.domain'),
                config('session.secure'),
                config('session.http_only'),
                false,
                config('session.same_site')
            );

            return response()->json([
                'success' => true,
                'message' => $response['message'] ?? 'Registration successful. You are now logged in.',
                'token' => $token,
                'user' => $userData,
                'redirect_url' => url('/'),
            ])
            ->cookie($sessionCookie)
            ->cookie(
                'auth_api_token',
                $token,
                60 * 24 * 7,
                '/',
                null,
                $secure,
                true,
                false,
                'Lax'
            );
        }

        return response()->json($response);
    }
}
