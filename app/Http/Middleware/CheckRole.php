<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();
        
        \Log::info('Role check in middleware', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'required_roles' => $roles
        ]);

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'You don\'t have permission to access this page. Only administrators and supervisors can manage collections.');
    }

    private function hasAnyRole($user, $roles)
    {
        $roles = explode(',', $roles[0]);
        foreach ($roles as $role) {
            if ($user->{'is' . ucfirst($role)}()) {
                return true;
            }
        }
        return false;
    }
} 