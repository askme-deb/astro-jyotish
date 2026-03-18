<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;

class ApiUserAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('auth_api_token')
            ?? session('auth.api_token')
            ?? session('auth_api_token');

        if (! $token) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $sessionUser = session('auth.user', []);
        $sessionUserId = session('api_user_id') ?? data_get($sessionUser, 'id');

        if ($sessionUserId) {
            $resolvedUser = new GenericUser(array_merge(
                is_array($sessionUser) ? $sessionUser : [],
                ['id' => (int) $sessionUserId]
            ));

            $request->setUserResolver(static fn () => $resolvedUser);
        }

        return $next($request);
    }
}
