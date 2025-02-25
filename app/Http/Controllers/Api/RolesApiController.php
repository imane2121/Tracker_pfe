<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolesApiController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions'])->get();

        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create($request->all());

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions', []));
        }

        return response()->json($role, Response::HTTP_CREATED);
    }

    public function show(Role $role)
    {
        return response()->json($role->load(['permissions']));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($request->all());

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->input('permissions', []));
        }

        return response()->json($role, Response::HTTP_ACCEPTED);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}