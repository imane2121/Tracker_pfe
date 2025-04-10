<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/email/verify';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:contributor,supervisor'],
        ]);
    }

    protected function create(array $data)
    {
        // Set initial account status based on role
        $accountStatus = $data['role'] === 'supervisor' ? 'under_review' : 'inactive';

        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'account_status' => $accountStatus,
            'verified' => 0,
        ]);
    }

    protected function registered(Request $request, $user)
    {
        // Log the user in after registration
        auth()->login($user);
        
        // The verification email will be sent automatically by the VerifiesEmails trait
        return redirect()->route('verification.notice');
    }
}