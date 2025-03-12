<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::guard()->guest()) {
            return redirect()->route('login');
        }

        // Check if the request is for a Blade view
        if ($request->is('profile*')) {
            return $next($request);
        }

        return $next($request);
    }
}
