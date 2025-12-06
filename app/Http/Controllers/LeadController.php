<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\Project;
use App\Models\ProjectHistory;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    /**
     * Human friendly status map (fallback if Lead::$STATUS not present).
     */
    protected $statusMap = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'site_visit_planned' => 'Site Visit Planned',
        'site_visited' => 'Site Visited',
        'follow_up' => 'Follow Up',
        'negotiation' => 'Negotiation',
        'converted' => 'Converted',
        'dropped' => 'Dropped',
    ];

    protected function getStatusMap(): array
    {
        return property_exists(Lead::class, 'STATUS') ? Lead::$STATUS : $this->statusMap;
    }

    /**
     * Show list blade (filters loaded on page; frontend will call ajaxList).
     */
    public function index()
    {
        $statuses = $this->getStatusMap();
        $users = User::orderBy('fname')->get(['id', 'fname', 'lname']);

        return view('page.marketing.list', compact('statuses', 'users'));
    }

    /**
     * AJAX list for datatable / frontend.
     * Returns a Laravel paginator JSON with added convenience fields.
     */
    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $query = Lead::query()->with(['quoteRequest','customer']);

        // Search across lead_code, remarks, quote_request name, mobile etc.
        if ($q = $request->get('search')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('lead_code', 'like', "%{$q}%")
                    ->orWhere('remarks', 'like', "%{$q}%")
                    ->orWhereHas('quoteRequest', function ($qr) use ($q) {
                        $qr->where('name', 'like', "%{$q}%")
                            ->orWhere('number', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        if ($name = $request->get('filter_name')) {
            $query->whereHas('quoteRequest', function ($qr) use ($name) {
                $qr->where('name', 'like', "%{$name}%");
            });
        }

        if ($assigned = $request->get('filter_assigned')) {
            $query->where('assigned_to', $assigned);
        }

        if ($status = $request->get('filter_status')) {
            $query->where('status', $status);
        }

        if ($from = $request->get('filter_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('filter_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $page = $request->get('page', 1);
        $data = $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // add convenience fields
        $statusMap = $this->getStatusMap();
        $data->getCollection()->transform(function ($item) use ($statusMap) {
            $assignedUser = $item->assigned_to ? User::find($item->assigned_to) : null;
            $item->assigned_to_name = $assignedUser ? trim(($assignedUser->fname ?? '').' '.($assignedUser->lname ?? '')) : null;
            $item->quote_request_name = optional($item->quoteRequest)->name;
            $item->status_label = $statusMap[$item->status] ?? $item->status;

            return $item;
        });

        return response()->json($data);
    }

    /**
     * Create blade form
     */
    public function create()
    {
        $statuses = $this->getStatusMap();
        $users = User::orderBy('fname')->get(['id', 'fname', 'lname']);

        return view('page.marketing.form', compact('statuses', 'users'));
    }

    /**
     * Store new lead (blade POST)
     */
    public function store(Request $request)
    {
        $payload = $this->validateRequest($request);

        // generate lead_code if missing
        if (empty($payload['lead_code'])) {
            $payload['lead_code'] = 'LD-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        }

        $payload['created_by'] = Auth::id() ?? ($payload['created_by'] ?? null);

        $lead = Lead::create($payload);

        $this->logHistory($lead->id, 'created', 'Lead created', Auth::id());

        return redirect()->route('marketing.index')->with('success', 'Lead created.');
    }

    /**
     * Edit blade form
     */
    public function edit($id)
    {
        $lead = Lead::with(['quoteRequest','customer'])->findOrFail($id);
        $statuses = $this->getStatusMap();
        $users = User::orderBy('fname')->get(['id', 'fname', 'lname']);
        // dd($lead);
        return view('page.marketing.form', compact('lead', 'statuses', 'users'));
    }

    /**
     * Update lead (blade POST)
     */
    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $payload = $this->validateRequest($request, $id);

        $lead->update($payload);
        $this->logHistory($lead->id, 'updated', 'Lead updated', Auth::id());

        return redirect()->route('marketing.index')->with('success', 'Lead updated.');
    }

    /**
     * Delete lead (AJAX)
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $lead = Lead::find($id);

        if (! $lead) {
            return response()->json(['status' => false, 'message' => 'Not found'], 404);
        }

        $lead->delete();
        $this->logHistory($id, 'deleted', 'Lead deleted', Auth::id());

        return response()->json(['status' => true, 'message' => 'Deleted']);
    }

    /**
     * Show lead detail blade
     */
    public function view($id)
    {
        $lead = Lead::with(['quoteRequest', 'history'])->findOrFail($id);
        $statuses = $this->getStatusMap();

        // prepare history for blade
        $history = $lead->history()->orderBy('id', 'desc')->get();

        return view('page.marketing.view', compact('lead', 'statuses', 'history'));
    }

    /**
     * Return JSON data for a lead (modal use)
     */
    public function viewJson($id)
    {
        $lead = Lead::with(['quoteRequest', 'history', 'creator', 'assignedUser','customer'])->findOrFail($id);

        $assignedUser = $lead->assigned_to ? User::find($lead->assigned_to) : null;

        $payload = [
            'id' => $lead->id,
            'lead_code' => $lead->lead_code,
            'assigned_to' => $lead->assigned_to,
            'assigned_to_name' => $assignedUser ? trim(($assignedUser->fname ?? '').' '.($assignedUser->lname ?? '')) : null,
            'status' => $lead->status,
            'remarks' => $lead->remarks,
            'quote_request' => $lead->quoteRequest,
            'created_at' => $lead->created_at ? $lead->created_at->toDateTimeString() : null,
            'history' => $lead->history()->orderBy('id', 'desc')->get()->map(function ($h) {
                return [
                    'id' => $h->id,
                    'action' => $h->action,
                    'message' => $h->message,
                    'changed_by' => $h->changed_by ? optional(User::find($h->changed_by))->fname.' '.optional(User::find($h->changed_by))->lname : null,
                    'created_at' => $h->created_at ? $h->created_at->format('d M Y h:i A') : null,
                ];
            })->values(),
        ];

        return response()->json($lead);
    }

    /**
     * Assign lead to user (AJAX)
     */
    public function assign(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'nullable|exists:users,id']);

        $lead = Lead::findOrFail($id);
        $old = $lead->assigned_to;
        $lead->assigned_to = $request->assigned_to;
        $lead->save();

        $this->logHistory($lead->id, 'assign', sprintf('Assigned to %s (was %s)', $request->assigned_to ?: 'none', $old ?: 'none'), Auth::id());

        return response()->json(['status' => true, 'message' => 'Assigned']);
    }

    /**
     * Update status (AJAX)
     */
    public function updateStatus(Request $request, $id)
    {
        $statusMap = array_keys($this->getStatusMap());
        $request->validate([
            'status' => 'required|string|in:'.implode(',', $statusMap),
        ]);

        $lead = Lead::findOrFail($id);
        $old = $lead->status;
        $lead->status = $request->status;
        $lead->save();

        $this->logHistory($lead->id, 'status_change', sprintf('Status changed from %s to %s', $old, $lead->status), Auth::id());

        // If converted, create project (idempotent)
        if ($lead->status === 'converted') {
            if (! Project::where('lead_id', $lead->id)->exists()) {
                try {
                    $proj = Project::create([
                        'lead_id' => $lead->id,
                        'project_code' => 'PRJ-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
                        'customer_name' => optional($lead->quoteRequest)->name ?? null,
                        'mobile' => optional($lead->quoteRequest)->number ?? null,
                        'status' => 'new',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->logHistory($lead->id, 'converted', 'Lead converted to project ID '.$proj->id, Auth::id());
                } catch (\Throwable $th) {
                    Log::warning("Project create failed for lead {$lead->id}: ".$th->getMessage());
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Status updated']);
    }

    /**
     * Export CSV
     */
    public function export()
    {
        $fileName = 'leads_export_'.now()->format('Ymd_Hi').'.csv';
        $rows = Lead::with('quoteRequest')->orderBy('id', 'desc')->get();

        $columns = ['id', 'lead_code', 'assigned_to', 'assigned_to_name', 'status', 'remarks', 'created_at'];

        $response = new StreamedResponse(function () use ($rows, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($rows as $row) {
                $assignedUser = $row->assigned_to ? User::find($row->assigned_to) : null;
                fputcsv($handle, [
                    $row->id,
                    $row->lead_code,
                    $row->assigned_to,
                    $assignedUser ? trim(($assignedUser->fname ?? '').' '.($assignedUser->lname ?? '')) : '',
                    $row->status,
                    $row->remarks,
                    $row->created_at ? $row->created_at->toDateTimeString() : '',
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);

        return $response;
    }

    /**
     * Import CSV (simple)
     */
    public function import(Request $request)
    {
        if (! $request->hasFile('file')) {
            return back()->with('error', 'Upload a file.');
        }

        $fp = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($fp);
        while ($row = fgetcsv($fp)) {
            $data = array_combine($header, $row);
            $payload = [
                'lead_code' => $data['lead_code'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
                'status' => $data['status'] ?? 'new',
                'remarks' => $data['remarks'] ?? null,
                'created_by' => Auth::id(),
            ];

            $found = null;
            if (! empty($payload['lead_code'])) {
                $found = Lead::where('lead_code', $payload['lead_code'])->first();
            }

            if ($found) {
                $found->update($payload);
            } else {
                Lead::create($payload);
            }
        }
        fclose($fp);

        return back()->with('success', 'Import completed.');
    }

    /**
     * Kanban JSON (status => leads[]).
     */
    public function kanban(Request $request)
    {
        $statuses = array_keys($this->getStatusMap());
        $data = [];
        foreach ($statuses as $st) {
            $leads = Lead::with('quoteRequest')->where('status', $st)->orderBy('updated_at', 'desc')->limit(200)->get();
            $data[$st] = $leads->map(function ($l) {
                return [
                    'id' => $l->id,
                    'lead_code' => $l->lead_code,
                    'name' => optional($l->quoteRequest)->name ?? '',
                    'mobile' => optional($l->quoteRequest)->number ?? '',
                    'assigned_to' => $l->assigned_to,
                    'next_followup_at' => $l->next_followup_at,
                ];
            })->values();
        }

        return response()->json($data);
    }

    /**
     * Move lead to new status (drag-drop).
     */
    public function move(Request $request, $leadId)
    {
        $statusMapKeys = array_keys($this->getStatusMap());
        $request->validate(['status' => 'required|in:'.implode(',', $statusMapKeys)]);

        $lead = Lead::findOrFail($leadId);
        $old = $lead->status;
        $lead->status = $request->status;
        $lead->save();

        $this->logHistory($lead->id, 'moved', sprintf('Moved from %s to %s', $old, $lead->status), Auth::id());

        return response()->json(['status' => true, 'data' => $lead]);
    }

    /**
     * API: create lead from quick form (returns JSON).
     */
    public function storeApi(Request $request)
    {
        // create / find customer if provided mobile
        $customerId = null;
        if ($request->filled('mobile') || $request->filled('email')) {
            $customer = Customer::firstOrCreate(
                ['mobile' => $request->mobile],
                ['name' => $request->name ?? null, 'email' => $request->email ?? null]
            );
            $customerId = $customer->id;
        }

        $payload = $this->validateRequest($request);
        $payload['customer_id'] = $customerId;
        $payload['lead_code'] = $payload['lead_code'] ?? 'LD-'.time();
        $payload['created_by'] = Auth::id();

        $lead = Lead::create($payload);

        $this->logHistory($lead->id, 'created', 'API created lead', Auth::id());

        return response()->json(['success' => true, 'id' => $lead->id]);
    }

    /**
     * Convert lead to project (AJAX).
     */
    public function convertToProject($leadId)
    {
        $lead = Lead::with(['quoteRequest'])->findOrFail($leadId);

        // Use existing customer if present or create from quoteRequest
        $customerId = $lead->customer_id ?? null;
        if (! $customerId && $lead->quoteRequest) {
            $qr = $lead->quoteRequest;
            $customer = Customer::firstOrCreate(
                ['mobile' => $qr->number],
                ['name' => $qr->name, 'email' => $qr->email]
            );
            $customerId = $customer->id;
        }

        $project = Project::create([
            'lead_id' => $lead->id,
            'project_code' => 'P-'.time(),
            'customer_name' => optional($lead->quoteRequest)->name ?? null,
            'mobile' => optional($lead->quoteRequest)->number ?? null,
            'status' => 'new',
        ]);

        $lead->status = 'converted';
        $lead->save();

        $this->logHistory($lead->id, 'converted', 'Converted to project ID '.$project->id, Auth::id());

        return response()->json(['success' => true, 'project_id' => $project->id]);
    }

    /**
     * Update assignment (convenience endpoint).
     */
    public function updateAssignment(Request $request, Lead $lead)
    {
        $request->validate(['assigned_to' => 'nullable|exists:users,id']);
        $lead->update(['assigned_to' => $request->assigned_to]);
        $this->logHistory($lead->id, 'assign', 'Assignment updated', Auth::id());

        return response()->json(['success' => true]);
    }

    /**
     * Update status (convenience endpoint).
     */
    public function updateStatusSimple(Request $request, Lead $lead)
    {
        $request->validate(['status' => 'required|string']);
        $lead->update(['status' => $request->status]);
        $this->logHistory($lead->id, 'status_change', 'Status updated', Auth::id());

        return response()->json(['success' => true]);
    }

    /**
     * Helper to write a lead history row.
     */
    protected function logHistory($leadId, $action, $message = null, $userId = null)
    {
        try {
            LeadHistory::create([
                'lead_id' => $leadId,
                'action' => $action,
                'message' => $message ?? $action,
                'changed_by' => $userId ?? Auth::id(),
            ]);
        } catch (\Throwable $th) {
            Log::warning('LeadHistory create failed: '.$th->getMessage());
        }
    }

    /**
     * Validate request payload for create/update.
     */
    protected function validateRequest(Request $request, $id = null)
    {
        $statusKeys = implode(',', array_keys($this->getStatusMap()));

        return $request->validate([
            'quote_request_id' => 'nullable|exists:quote_requests,id',
            'lead_code' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:'.$statusKeys,
            'next_followup_at' => 'nullable|date',
            'remarks' => 'nullable|string',
            'meta' => 'nullable|array',
            'customer_id' => 'nullable|exists:customers,id',
        ]);
    }

    public function createProjectFromLead($leadId)
    {
        $lead = Lead::with(['customer', 'quoteRequest'])->findOrFail($leadId);
        dd($lead);
        // Prevent duplicate project
        if (Project::where('lead_id', $lead->id)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Project already exists for this lead.',
            ], 409);
        }

        // Auto-generate project code
        $projectCode = 'PRJ-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));

        // Build payload using lead + customer + quote request
        $payload = [
            'lead_id' => $lead->id,
            'project_code' => $projectCode,
            'customer_name' => $lead->customer->name ?? ($lead->quoteRequest->name ?? ''),
            'mobile' => $lead->customer->mobile ?? ($lead->quoteRequest->number ?? ''),
            'address' => $lead->customer->address ?? ($lead->quoteRequest->address ?? ''),
            'kw' => $lead->quoteRequest->kw ?? null,
            'module_count' => $lead->quoteRequest->mc ?? null,
            'module_brand' => null,
            'inverter_brand' => null,
            'status' => 'new',
            'assignee' => $lead->assigned_to ?? null,
            'reporter' => Auth::id(),
        ];

        // Create project
        $project = Project::create($payload);

        // Log project history
        ProjectHistory::create([
            'project_id' => $project->id,
            'status' => 'new',
            'changed_by' => Auth::id(),
            'notes' => "Project created from Lead #{$lead->lead_code}",
        ]);

        // Update lead status
        $lead->update(['status' => 'converted']);

        return response()->json([
            'status' => true,
            'message' => 'Project created successfully.',
            'project_id' => $project->id,
            'project_url' => route('projects.edit', $project->id),
        ]);
    }
}
