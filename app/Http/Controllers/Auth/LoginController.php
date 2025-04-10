<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // Check for supervisor account status
        if ($user->role === 'supervisor') {
            if ($user->account_status === 'under_review') {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Votre compte superviseur est en cours d\'examen. Vous ne pouvez pas vous connecter tant que votre compte n\'est pas activé par un administrateur.');
            }
            
            if ($user->account_status !== 'active') {
                auth()->logout();
                return redirect()->route('login')
                    ->with('error', 'Votre compte superviseur n\'est pas actif. Veuillez contacter l\'administrateur.');
            }
        }

        // For contributors, check if account is active
        if ($user->role === 'contributor' && $user->account_status !== 'active') {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Votre compte n\'est pas actif. Veuillez contacter l\'administrateur.');
        }

        return redirect()->intended($this->redirectTo);
    }
}
