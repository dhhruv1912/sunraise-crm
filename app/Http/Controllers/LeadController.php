<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    /* ================= INDEX VIEW ================= */
    public function index()
    {
        return view('page.leads.index');
    }

    /* ================= WIDGETS (HTML) ================= */
    public function ajaxWidgets()
    {
        $today = Carbon::today();

        $total = Lead::count();

        $active = Lead::whereNotIn('status', ['converted','dropped'])->count();

        $followupsToday = Lead::whereDate('next_followup_at', $today)
            ->whereNotIn('status', ['converted','dropped'])
            ->count();

        $overdue = Lead::whereDate('next_followup_at', '<', $today)
            ->whereNotIn('status', ['converted','dropped'])
            ->count();

        // Avg response time (hours) from lead creation to first follow-up
        $avgResponse = Lead::whereNotNull('next_followup_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, next_followup_at)) as avg')
            ->value('avg');

        return view('page.leads.widgets', [
            'total'          => $total,
            'active'         => $active,
            'today'          => $followupsToday,
            'overdue'        => $overdue,
            'avgResponse'    => round($avgResponse ?? 0, 1)
        ]);
    }

    /* ALERT DATA (for toast / badge use later) */
    public function ajaxAlerts()
    {
        $count = Lead::whereDate('next_followup_at', '<', now())
            ->whereNotIn('status', ['converted','dropped'])
            ->count();

        return response()->json([
            'overdue' => $count
        ]);
    }

    /* ================= CHART DATA ================= */
    public function ajaxCharts()
    {
        $statusData = Lead::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'status' => $statusData
        ]);
    }

    /* ================= LIST DATA ================= */
    public function ajaxList(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $query = Lead::query()
            ->leftJoin('customers', 'customers.id', '=', 'leads.customer_id')
            ->select([
                'leads.id',
                'leads.lead_code',
                'leads.status',
                'leads.quote_request_id',
                'leads.next_followup_at',
                'leads.created_at',
                'customers.name as customer_name',
                'customers.mobile'
            ])
            ->latest('leads.created_at');

        /* ---------------- FILTERS ---------------- */

        // STATUS
        if ($request->filled('status')) {
            $query->where('leads.status', $request->status);
        }

        // FOLLOW-UP
        if ($request->followup === 'today') {
            $query->whereDate('leads.next_followup_at', now());
        }

        if ($request->followup === 'overdue') {
            $query->whereDate('leads.next_followup_at', '<', now());
        }

        // SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'like', "%{$search}%")
                ->orWhere('customers.mobile', 'like', "%{$search}%")
                ->orWhere('leads.lead_code', 'like', "%{$search}%");
            });
        }

        /* ---------------- PAGINATION ---------------- */

        $total = (clone $query)->count();

        $rows = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'data' => $rows,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $perPage),
            ],
            "canEdit" => Gate::allows('marketing.lead.edit')
        ]);
    }


    /* ===============================
     * VIEW PAGE (READ ONLY)
     * =============================== */
    public function view($lead)
    {
        $lead = Lead::with([
            'customer',
            'quoteRequest',
            'quoteMaster',
            'assignedUser',
            'history.user',
            'quotation.sentBy',
            'project'
        ])->findOrFail($lead);

        return view('page.leads.view', [
            'lead' => $lead
        ]);
    }

    /* ===============================
     * EDIT PAGE (FORM)
     * =============================== */
    public function edit($lead)
    {
        $lead = Lead::with([
            'customer',
            'quoteRequest',
            'quoteMaster',
            'assignedUser',
            'history.user'
        ])->findOrFail($lead);
        // if ($lead->status === 'converted') {
        //     abort(403, 'Converted leads cannot be edited');
        // }

        $users = User::select('id', 'fname', 'lname')->get();

        return view('page.leads.edit', [
            'lead'  => $lead,
            'users' => $users
        ]);
    }

    /* ===============================
     * AJAX UPDATE
     * =============================== */
    public function update(Request $request, $lead)
    {
        $lead = Lead::findOrFail($lead);
        // if ($lead->status === 'converted') {
        //     return response()->json([
        //         'status'  => false,
        //         'message' => 'Converted leads cannot be updated'
        //     ], 403);
        // }

        $data = $request->validate([
            'assigned_to'       => 'nullable|exists:users,id',
            'status'            => 'required|string',
            'next_followup_at'  => 'nullable|date',
            'remarks'           => 'nullable|string'
        ]);

        $lead->update($data);

        LeadHistory::create([
            'lead_id'    => $lead->id,
            'action'     => 'updated',
            'message'    => 'Lead details updated',
            'changed_by' => Auth::id()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Lead updated successfully'
        ]);
    }

    public function preview(Lead $lead)
    {
        $existing = Project::where('lead_id', $lead->id)->first();
        if ($existing) {
            return response()->json([
                'status' => false,
                'code'   => 409,
                'project'=> $existing
            ]);
        }

        $lead->load([
            'customer',
            'quoteRequest',
            'quoteMaster',
            'assignedUser'
        ]);

        return response()->json([
            'status' => true,
            'html'   => view(
                'page.leads.convert_canvas',
                compact('lead')
            )->render()
        ]);
    }

    public function store(Request $request, Lead $lead)
    {
        if (Project::where('lead_id', $lead->id)->exists()) {
            return response()->json([
                'status' => false,
                'message'=> 'Project already exists'
            ], 409);
        }

        $data = $request->validate([
            'finalize_price' => 'required|numeric|min:0',
            'priority'       => 'required|in:low,medium,high',
            'emi'            => 'nullable|array',
        ]);
        $project = Project::create([
            'customer_id'      => $lead->customer_id,
            'lead_id'          => $lead->id,
            'quote_request_id' => $lead->quote_request_id,
            'quote_master_id'  => $lead->quote_master_id,
            'project_code'     => Project::generateCode(),
            'finalize_price'   => $data['finalize_price'],
            'priority'         => $data['priority'],
            'emi'              => $data['emi'] ?? null,
            'assignee'         => $lead->assigned_to,
            'reporter'         => Auth::id(),
            'status'           => 'new'
        ]);

        $lead->update(['status' => 'converted']);

        LeadHistory::create([
            'lead_id'    => $lead->id,
            'action'     => 'converted',
            'message'    => 'Lead converted to project',
            'changed_by' => Auth::id()
        ]);

        return response()->json([
            'status' => true,
            'redirect' => route('projects.view', $project->id)
        ]);
    }
}
