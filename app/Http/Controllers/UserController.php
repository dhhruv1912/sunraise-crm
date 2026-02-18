<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Models\Lead;
use App\Models\QuoteRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Users list page (Blade)
     */
    public function index()
    {
        return view('page.users.index');
    }

    /**
     * AJAX: User list (table data)
     */
    public function ajaxList(Request $request)
    {
        $query = User::query();

        /* ================= SEARCH ================= */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'like', "%{$search}%")
                    ->orWhere('lname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        /* ================= STATUS FILTER ================= */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /* ================= PAGINATION ================= */
        $perPage = (int) $request->get('per_page', 20);

        $users = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        $canEdit = Gate::allows('users.edit');
        
        return response()->json(["users"=>$users,"canEdit"=>$canEdit]);
    }

    /**
     * AJAX: KPI widgets
     */
    public function ajaxWidgets()
    {
        return view('page.users.partials.widgets', [
            'total' => User::count(),
            'active' => User::where('status', 1)->count(),
            'inactive' => User::where('status', 0)->count(),
        ]);
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
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'mobile' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(6)],
            'role' => 'required|exists:roles,name',
            'status' => 'required|boolean',
        ]);

        $user = User::create([
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'email' => $validated['email'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
        ]);

        // Assign Spatie role
        $user->assignRole($validated['role']);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
        ]);
    }

    public function edit(User $user)
    {
        return view('page.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
            'currentRole' => $user->roles->pluck('name')->first(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.$user->id,
            'mobile' => 'nullable|string|max:20',
            'status' => 'required|boolean',
            'role' => 'required|exists:roles,name',
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ]);

        $user->update([
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'email' => $validated['email'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'status' => $validated['status'],
        ]);

        // Update password only if provided
        if (! empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Sync role (single-role system)
        $user->syncRoles([$validated['role']]);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
        ]);
    }

    public function changeStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|boolean',
        ]);

        $user->update([
            'status' => $validated['status'],
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
        ]);
    }

    public function profile()
    {
        $userId = Auth::id();

        return view('page.profile.index', [
            'leadCount'     => Lead::where('assigned_to',$userId)->count(),
            'quoteCount'    => QuoteRequest::where('assigned_to',$userId)->count(),
            'projectCount'  => Project::where('assignee',$userId)
                                    ->where('status','!=','complete')->count(),

            'leads'         => Lead::where('assigned_to',$userId)
                                ->latest()->limit(10)->get(),

            'projects'      => Project::where('assignee',$userId)
                                    ->latest()->limit(10)->get(),

            'quoteRequests' => QuoteRequest::with('customer')->where('assigned_to',$userId)
                                        ->latest()->limit(10)->get(),
            'role'          => Role::where('id',Auth::user()->role)->pluck('name')->first()
        ]);
    }

    public function ajaxTimeline()
    {
        $user = Auth::user();
        $start = Carbon::today();
        $end   = Carbon::today()->addDays(6);

        $rows = [];

        // 🔹 Example: Leads follow-ups
        $leads = Lead::where('assigned_to', $user->id)
            ->whereBetween('next_followup_at', [$start, $end])
            ->get();

        foreach ($leads as $l) {
            $rows[] = [
                'date'  => Carbon::parse($l->next_followup_at)->toDateString(),
                'time'  => Carbon::parse($l->next_followup_at)->format('H:i'),
                'type'  => 'Follow-up',
                'title' => $l->lead_code,
                'meta'  => $l->remarks
            ];
        }

        // 🔹 Example: Projects inspections
        $projects = Project::where('assignee', $user->id)
            ->whereBetween('inspection_date', [$start, $end])
            ->get();

        foreach ($projects as $p) {
            $rows[] = [
                'date'  => Carbon::parse($p->inspection_date)->toDateString(),
                'time'  => '—',
                'type'  => 'Inspection',
                'title' => $p->project_code,
                'meta'  => $p->customer?->name
            ];
        }

        // group by date
        $days = collect();
        for ($i = 0; $i < 7; $i++) {
            $d = Carbon::today()->addDays($i)->toDateString();
            $days[$d] = collect($rows)->where('date', $d)->values();
        }

        return view('page.profile.widgets.timeline', [
            'days' => $days
        ]);
    }

    public function ajaxBasicInfo()
    {
        $u = Auth::user();

        return view('page.profile.widgets.basic_info', compact('u'));
    }

    /* ================= ASSIGNMENTS ================= */
    public function ajaxAssignments()
    {
        $userId = Auth::id();

        return view('page.profile.widgets.assignments', [
            'leads'   => Lead::where('assigned_to', $userId)->count(),
            'projects'=> Project::where('assignee', $userId)
                                ->where('status','!=','complete')->count(),
            'quotes'  => QuoteRequest::where('assigned_to', $userId)->count(),
        ]);
    }
}
