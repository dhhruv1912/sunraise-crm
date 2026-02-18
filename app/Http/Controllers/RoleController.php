<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use App\Models\User;

class RoleController extends Controller
{
    public function index()
    {
        return view('page.roles.index');
    }

    public function ajaxList()
    {
        return response()->json(
            Role::withCount('users')
                ->orderBy('name')
                ->get()
        );
    }

    public function ajaxWidgets()
    {
        return view('page.roles.widgets', [
            'total'       => Role::count(),
            'permissions' => Permission::count(),
            'users'       => User::count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name'),
            ],
        ]);

        Role::create([
            'name'       => strtolower($validated['name']),
            'guard_name' => 'web',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Role created successfully',
        ]);
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['admin', 'super-admin'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This role cannot be deleted',
            ], 403);
        }

        $role->users()->detach();
        $role->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Role deleted successfully',
        ]);
    }

    /* ================= ROLE → PERMISSIONS PAGE ================= */
    public function permissions(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        $assigned = $role->permissions->pluck('name')->toArray();

        return view('page.roles.permissions', [
            'role'        => $role,
            'permissions' => $permissions,
            'assigned'    => $assigned,
            'total'       => Permission::count(),
            'assignedCnt' => count($assigned),
        ]);
    }

    public function permissionWidgets(Role $role)
    {
        $total = Permission::count();
        $assigned = $role->permissions()->count();

        return view('page.roles.permissions-widgets', [
            'total'       => $total,
            'assignedCnt' => $assigned,
            'unassigned'  => $total - $assigned,
        ]);
    }

    /* ================= SAVE ROLE PERMISSIONS ================= */
    public function syncPermissions(Request $request, Role $role)
    {
        $data = $request->validate([
            'permissions' => 'array'
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return response()->json([
            'status'  => 'success',
            'message' => 'Permissions updated successfully'
        ]);
    }
}
