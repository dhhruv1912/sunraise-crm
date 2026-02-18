<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index()
    {
        return view('page.permissions.index');
    }

    public function ajaxList()
    {
        return response()->json(
            Permission::orderBy('name')->get()
        );
    }

    public function ajaxWidgets()
    {
        $modules = Permission::all()
            ->pluck('name')
            ->map(fn ($n) => explode('.', $n)[0])
            ->unique()
            ->count();

        return view('page.permissions.widgets', [
            'total'   => Permission::count(),
            'modules' => $modules,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:100',
            'action' => 'required|string|max:100',
        ]);

        $name = strtolower($validated['module'] . '.' . $validated['action']);

        Permission::create([
            'name'       => $name,
            'guard_name' => 'web',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Permission created successfully',
        ]);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Permission deleted successfully',
        ]);
    }
}
