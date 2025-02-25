<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends Controller
{
    /**
     * Show the form to change the user's password.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('auth.passwords.edit');
    }

    /**
     * Update the user's password.
     *
     * @param  \App\Http\Requests\UpdatePasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePasswordRequest $request)
    {
        $user = auth()->user();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.password.edit')->with('message', 'Password updated successfully.');
    }

    /**
     * Update the user's profile.
     *
     * @param  \App\Http\Requests\UpdateProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        // Update the profile
        $user->update($request->validated());

        return redirect()->route('profile.password.edit')->with('message', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $user = auth()->user();

        // Soft delete the user
        $user->update([
            'email' => time() . '_' . $user->email, // Append timestamp to email to make it unique
        ]);

        $user->delete();

        return redirect()->route('login')->with('message', 'Your account has been deleted.');
    }
}