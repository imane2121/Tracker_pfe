<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'city'])->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('title', 'id');
        $cities = City::pluck('name', 'id')->prepend('Please Select', '');

        return view('admin.users.create', compact('roles', 'cities'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,contributor,supervisor'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ]);

        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'city_id' => $request->city_id,
        ]);

        // Assign roles if needed
        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles', []));
        }

        return redirect()->route('admin.users.index')->with('message', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('title', 'id');
        $cities = City::pluck('name', 'id')->prepend('Please Select', '');

        $user->load('roles', 'city');

        return view('admin.users.edit', compact('roles', 'user', 'cities'));
    }

    public function update(Request $request, User $user)
    {
        // Validate the request
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,contributor,supervisor'],
            'city_id' => ['nullable', 'exists:cities,id'],
        ]);

        // Update the user
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'city_id' => $request->city_id,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Assign roles if needed
        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles', []));
        }

        return redirect()->route('admin.users.index')->with('message', 'User updated successfully.');
    }

    public function show(User $user)
    {
        $user->load('roles', 'city');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('message', 'User deleted successfully.');
    }
}