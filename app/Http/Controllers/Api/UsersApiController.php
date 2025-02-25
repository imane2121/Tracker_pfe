<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UsersApiController extends Controller
{
    public function index()
    {
        $users = User::with(['roles'])->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8',
            'role'       => 'required|in:admin,contributor,supervisor',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
        ]);

        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles', []));
        }

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function show(User $user)
    {
        return response()->json($user->load(['roles']));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'email'      => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password'   => 'sometimes|string|min:8',
            'role'       => 'sometimes|in:admin,contributor,supervisor',
        ]);

        $user->update($request->all());

        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles', []));
        }

        return response()->json($user, Response::HTTP_ACCEPTED);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}