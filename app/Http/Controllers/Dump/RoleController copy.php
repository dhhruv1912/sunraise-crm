<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    // LIST ROLES
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('id')->get();
        return view('page.roles.index', compact('roles'));
    }

    // CREATE FORM
    public function create()
    {
        $permissions = Permission::get();
        return view('page.roles.create', compact('permissions'));
    }

    // STORE NEW ROLE
    public function store(Request $request)
    {
        $rules = ['name' => 'required|unique:roles,name'];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully!');
    }

    // EDIT FORM
    public function edit(Role $role)
    {
        $permissions = Permission::get();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('page.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    // UPDATE ROLE
    public function update(Request $request, Role $role)
    {
        $rules = ['name' => 'required|unique:roles,name,' . $role->id];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        $role->update(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role updated successfully!');
    }

    // DELETE ROLE
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()
            ->route('roles.index')
            ->with('success', 'Role deleted successfully!');
    }
}
