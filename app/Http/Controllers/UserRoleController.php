<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    // Return modal HTML OR JSON of roles (we provide both)
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();

        // if AJAX request expects JSON payload
        if (request()->ajax()) {
            return response()->json([
                'user' => $user,
                'roles' => $roles,
                'userRoles' => $userRoles
            ]);
        }

        // default: blade view (full page)
        return view('users.assign', compact('user','roles','userRoles'));
    }

    // JSON endpoint helpful for modal; convenient alias
    public function rolesJson(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        return response()->json(['roles' => $roles, 'userRoles' => $userRoles]);
    }

    // Update roles (expects roles[] array)
    public function update(Request $request, User $user)
    {
        $request->validate(['roles' => 'nullable|array']);

        $roles = $request->roles ?? [];

        $user->syncRoles($roles);

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Roles updated']);
        }

        return redirect()->route('Users')->with('success', 'Roles updated');
    }
}
