<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use App\Services\Api\Contracts\AuthApiServiceInterface;

class LoginController extends Controller
{
    public function __construct(protected AuthApiServiceInterface $authApi) {}

    public function login(Request $request)
    {
        $context = $request->input('context', 'header');
        $email = $request->input('email');
        $password = $request->input('password');

        $response = $this->authApi->login($email, $password);

        if (!($response['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Login failed.',
                'context' => $context,
            ], 422);
        }

        $userPayload = is_array($response['user'] ?? null)
            ? $response['user']
            : ($response['data']['user'] ?? $response['data']['customer'] ?? $response['data'] ?? []);
        $token = $response['token'] ?? $response['access_token'] ?? ($response['data']['token'] ?? $response['data']['access_token'] ?? null);

        if (!$token) {
            Log::warning('Login succeeded but no token returned from API.', [
                'email' => $email,
                'context' => $context,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Authentication service did not return a session token.',
                'context' => $context,
            ], 500);
        }

        Session::regenerate();
        Session::put('auth.api_token', $token);

        $userData = $userPayload ?? [];
        if (!isset($userData['email'])) {
            $userData['email'] = $email;
        }
        // Store roles in session if present
        $roles = [];
        if (isset($response['roles']) && is_array($response['roles'])) {
            $roles = $response['roles'];
        } elseif (isset($userData['roles']) && is_array($userData['roles'])) {
            // If roles are objects/arrays, extract 'name' if present
            $roles = array_map(function($role) {
                return is_array($role) && isset($role['name']) ? $role['name'] : $role;
            }, $userData['roles']);
        }
        Session::put('auth.user', $userData);
        Session::put('auth.roles', $roles);
        if (isset($userData['id'])) {
            Session::put('api_user_id', $userData['id']);
        }
        Session::save();

        Log::info('login session saved', [
            'session_id' => Session::getId(),
            'token_preview' => substr($token, 0, 20) . '...',
            'user_id' => $userData['id'] ?? null,
        ]);
        // Debug: Log session ID and all session data before returning response
        Log::info('Session ID after login', ['session_id' => Session::getId()]);
        Log::info('Session data after login', ['session_data' => Session::all()]);

        $redirectUrl = $context === 'checkout'
            ? route('checkout.index')
            : (url()->previous() ?: '/');

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
            'message' => $response['message'] ?? 'Logged in successfully.',
            'token' => $token,
            'user' => $userData,
            'redirect_url' => $redirectUrl,
            'context' => $context,
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
}
