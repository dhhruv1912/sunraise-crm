<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // List users (blade)
    public function index(Request $request)
    {
        $users = User::orderBy('id', 'desc')->paginate(20);
        return view('page.users.index', compact('users'));
    }

    // Create form
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('page.users.create', compact('roles'));
    }

    // Store (expects JSON from fetch or normal form)
    public function store(Request $request)
    {
        $rules = [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'mobile' => 'required|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
            'salary' => 'required|numeric',
            'role' => 'required',
            'status' => 'nullable|in:0,1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // return JSON for AJAX requests
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = new User();
        $user->fname = $request->firstname;
        $user->lname = $request->lastname;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->salary = $request->salary;
        $user->role = $request->role;
        $user->status = $request->status ? 1 : 0;
        $user->password = Hash::make($request->password);
        $user->password_d = $request->password;
        $user->save();

        // Assign role if provided (Spatie)
        if ($request->role) {
            $role = Role::find($request->role) ?: Role::where('name', $request->role)->first();
            if ($role) $user->assignRole($role);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Employee created', 'data' => $user], 201);
        }

        return redirect()->route('Users')->with('success', 'Employee created');
    }

    // Edit form
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('page.users.edit', compact('user','roles'));
    }

    // Update
    public function update(Request $request, User $user)
    {
        $rules = [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'mobile' => 'required|unique:users,mobile,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'salary' => 'required|numeric',
            'role' => 'required',
            'status' => 'nullable|in:0,1',
        ];

        // password optional on update
        if ($request->filled('password')) {
            $rules['password'] = 'nullable|min:4';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user->fname = $request->firstname;
        $user->lname = $request->lastname;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->salary = $request->salary;
        $user->role = $request->role;
        $user->status = $request->status ? 1 : 0;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // sync role
        if ($request->role) {
            $role = Role::find($request->role) ?: Role::where('name', $request->role)->first();
            if ($role) $user->syncRoles([$role->name]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Employee updated', 'data' => $user], 200);
        }

        return redirect()->route('Users')->with('success', 'Employee updated');
    }

    // Show profile
    public function show(User $user)
    {
        return view('page.users.show', compact('user'));
    }

    // Delete
    public function destroy(Request $request, User $user)
    {
        $user->delete();
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Deleted'], 200);
        }
        return redirect()->route('Users')->with('success', 'User deleted');
    }
}
