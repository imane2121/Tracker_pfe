<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerificationMiddleware 
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated and their email is not verified
        if (Auth::check()) {
            $user = Auth::user();

            // If the user has not verified their email, redirect to verification notice
            if (! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
        }

        // Proceed to the next middleware or request handler
        return $next($request);
    }
}
