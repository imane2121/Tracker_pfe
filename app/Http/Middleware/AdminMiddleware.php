<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Add debugging
        Log::info('AdminMiddleware check', [
            'is_authenticated' => auth()->check(),
            'user' => auth()->user(),
            'user_role' => auth()->check() ? auth()->user()->role : null,
            'route' => $request->route()->getName(),
        ]);

        if (!auth()->check() || auth()->user()->role !== 'admin') {
            Log::warning('Access denied in AdminMiddleware', [
                'user_id' => auth()->id(),
                'user_role' => auth()->check() ? auth()->user()->role : null,
            ]);
            return redirect()->route('home')->with('error', 'Access denied. You must be an administrator.');
        }

        return $next($request);
    }
} 