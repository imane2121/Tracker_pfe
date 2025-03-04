<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use VerifiesEmails;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
    }

    public function show(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo);
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request)
    {
        if ($request->route('id') != $request->user()->getKey()) {
            throw new \InvalidArgumentException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            
            // Update account status based on role
            $user = $request->user();
            if ($user->role === 'contributor') {
                $user->account_status = 'active';
            } else {
                $user->account_status = 'under_review';
            }
            $user->save();
        }

        if ($request->user()->role === 'supervisor') {
            return redirect($this->redirectTo)->with('status', 'Your email has been verified. Your account is under review by an administrator.');
        }

        return redirect($this->redirectTo)->with('status', 'Your email has been verified!');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
