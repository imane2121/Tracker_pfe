<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect('login');
        }

        $userRole = $request->user()->role;
        if (in_array($userRole, explode(',', $roles[0]))) {
            return $next($request);
        }

        return redirect()->back()->with('error', 'You do not have permission to access this resource.');
    }
} 