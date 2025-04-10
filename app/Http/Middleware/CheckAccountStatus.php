<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // If user is a contributor, they can access the dashboard
        if ($user->role === 'contributor') {
            return $next($request);
        }

        // If user is a supervisor and their account is under review
        if ($user->role === 'supervisor' && $user->account_status === 'under_review') {
            return redirect()->route('account.under.review');
        }

        // If user is a supervisor and their account is active
        if ($user->role === 'supervisor' && $user->account_status === 'active') {
            return $next($request);
        }

        // For any other case, redirect to under review page
        return redirect()->route('account.under.review');
    }
} 