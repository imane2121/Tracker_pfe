<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail; // Import Mail
use App\Mail\VerifyEmail; // Import VerifyEmail Mailable
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Ensure this is imported
use Illuminate\Support\Facades\URL; // Add this line to import the URL facade
class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'role'       => ['required', 'in:contributor,supervisor'], // Ensure role is provided
        ]);
    }

    protected function registered(Request $request, $user)
{
    $user->sendEmailVerificationNotification(); // Laravel's built-in method
    return redirect()->route('login')->with('success', 'Please check your email to verify your account.');
}



    /**
     * Create the email verification URL.
     *
     * @param User $user
     * @return string
     */
    protected function createEmailVerificationUrl(User $user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }

    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => $data['role'],
            'verified'   => 0, // Mark as unverified initially
        ]);

        // Set account_status based on role
        if ($user->role === 'supervisor') {
            $user->account_status = 'under_review';
        } else {
            $user->account_status = 'inactive'; // Contributors are inactive until email verification
        }

        $user->save();

        return $user;
    }
}