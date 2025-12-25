<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // List users (blade)
    public function index(Request $request)
    {
        // $users = User::orderBy('id', 'desc')->paginate(20);
        // $sessions = DB::table('sessions')->pluck('user_id')->values();
        // $sessions = array_values(DB::table('sessions')->pluck('user_id')->toArray());
        $role = Role::pluck('name','id');
        return view('page.users.index',compact('role'));
    }

    public function list(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);

        $q = User::query();

        if ($search = trim($request->search)) {
            $q->where(function ($x) use ($search) {
                $x->where('fname', 'like', "%$search%")
                    ->orWhere('lname', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        if ($request->filled('role')) {
            $q->where('role', $request->role);
        }

        if ($request->filled('company_access')) {
            $q->where('company_access', $request->assignee);
        }

        $data = $q->orderBy('id', 'desc')->paginate($perPage);

        $sessions = array_values(DB::table('sessions')->pluck('user_id')->toArray());
        return response()->json([
            'data' => $data,
            'sessions' => $sessions,
        ]);
        // return view('page.users.index', compact('users','sessions'));
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

    // Show profileDELETE
    // Delete
    public function destroy(Request $request, User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }
}
