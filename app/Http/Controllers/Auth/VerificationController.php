<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use VerifiesEmails;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verified(Request $request)
    {
        $user = $request->user();

        // Mark email as verified and update account status
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            $user->verified = 1;
            $user->account_status = 'active';
            $user->save();
        }

        return redirect('/dashboard')->with('success', 'Your email has been verified.');
    }

    // Redirect the user to a custom page based on their role
    protected function redirectTo()
    {
        if (auth()->user()->role === 'contributor') {
            return route('contributor.dashboard');
        }

        if (auth()->user()->role === 'supervisor') {
            return route('supervisor.dashboard');
        }

        return $this->redirectTo;
    }

    // Generate verification link and send it via email
    protected function registered(Request $request, $user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );

        // Send email with the verification URL
        Mail::to($user->email)->send(new VerifyEmail($verificationUrl));

        return redirect()->route('login')->with('success', 'Please check your email to verify your account.');
    }
}
