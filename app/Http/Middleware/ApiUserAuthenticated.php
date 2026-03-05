<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ApiUserAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('auth.api_token') || !session()->has('auth.user')) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
