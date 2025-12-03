<?php
use App\Models\Lead;
use App\Models\QuoteRequest;
use App\Models\User;
use Illuminate\Http\Request;

public function index()
{
    // statuses array provided to blade (use Lead::$STATUS or define here)
    $statuses = Lead::$STATUS ?? [
        'new' => 'New',
        'contacted' => 'Contacted',
        'site_visit_planned' => 'Site Visit Planned',
        'site_visited' => 'Site Visited',
        'follow_up' => 'Follow Up',
        'negotiation' => 'Negotiation',
        'converted' => 'Converted',
        'dropped' => 'Dropped',
    ];
    return view('page.marketing.list', compact('statuses'));
}

/**
 * AJAX paginated list
 */
public function ajaxList(Request $request)
{
    $perPage = (int) $request->get('per_page', 20);
    $query = Lead::with(['quoteRequest']); // eager load related QR

    if ($s = $request->get('search')) {
        $query->where(function($q) use ($s) {
            $q->where('lead_code','like', "%{$s}%")
              ->orWhereHas('quoteRequest', function($qq) use ($s){
                    $qq->where('name', 'like', "%{$s}%")
                       ->orWhere('number','like', "%{$s}%");
              });
        });
    }

    if ($status = $request->get('filter_status')) $query->where('status', $status);
    if ($assigned = $request->get('filter_assigned')) $query->where('assigned_to', $assigned);

    $data = $query->orderBy('updated_at', 'desc')->paginate($perPage);

    // map assignee name
    $data->getCollection()->transform(function($item){
        $item->assigned_to_name = $item->assigned_to ? optional(User::find($item->assigned_to))->name : null;
        $item->name = $item->quoteRequest->name ?? $item->customer_name ?? null;
        $item->number = $item->quoteRequest->number ?? null;
        return $item;
    });

    return response()->json($data);
}

/**
 * Provide users short list to populate selects
 */
public function usersList()
{
    $users = User::select('id','name')->orderBy('name')->get();
    return response()->json($users);
}

/**
 * Kanban data grouped by status
 */
public function kanbanData()
{
    $statuses = Lead::$STATUS ?? [
        'new' => 'New',
        'contacted' => 'Contacted',
        'site_visit_planned' => 'Site Visit Planned',
        'site_visited' => 'Site Visited',
        'follow_up' => 'Follow Up',
        'negotiation' => 'Negotiation',
        'converted' => 'Converted',
        'dropped' => 'Dropped',
    ];

    $result = [];
    foreach(array_keys($statuses) as $st) {
        $leads = Lead::with('quoteRequest')->where('status', $st)->orderBy('updated_at', 'desc')->limit(200)->get();
        $result[$st] = $leads->map(function($l){
            return [
                'id'=>$l->id,
                'lead_code'=>$l->lead_code,
                'name'=>$l->quoteRequest->name ?? $l->customer_name,
                'number'=>$l->quoteRequest->number ?? null,
                'assigned_to_name' => optional(User::find($l->assigned_to))->name,
            ];
        })->toArray();
    }

    return response()->json($result);
}

/**
 * Move (update) status via Kanban or table
 */
public function updateStatus(Request $request, $id)
{
    $request->validate(['status'=>'required|string']);
    $lead = Lead::findOrFail($id);
    $lead->status = $request->status;
    $lead->save();

    // create history record if you have a LeadHistory model
    // LeadHistory::create([...]);

    return response()->json(['status'=>true, 'data'=>$lead]);
}

/**
 * Update assignee via ajax
 */
public function updateAssignee(Request $request, $id)
{
    $request->validate(['assigned_to'=>'nullable|exists:users,id']);
    $lead = Lead::findOrFail($id);
    $lead->assigned_to = $request->assigned_to;
    $lead->save();
    return response()->json(['status'=>true]);
}

/**
 * View JSON (modal)
 */
public function viewJson($id)
{
    $lead = Lead::with(['quoteRequest'])->findOrFail($id);

    // history example - if you have a history table fetch it
    $history = \DB::table('lead_history')->where('lead_id', $id)->orderBy('created_at','desc')->limit(50)->get();

    return response()->json(array_merge($lead->toArray(), [
        'history' => $history->map(function($h){ return (array)$h; })->toArray()
    ]));
}

/**
 * Convert quote to lead (if you want to convert a QuoteRequest into a Lead from modal)
 */
public function convertFromRequest(Request $request, $id)
{
    // find quoteRequest
    $qr = \App\Models\QuoteRequest::findOrFail($id);

    // create lead (idempotent)
    $lead = Lead::firstOrCreate(
      ['quote_request_id' => $qr->id],
      [
        'lead_code' => 'LD-'.now()->format('Ymd').' -'. strtoupper(\Illuminate\Support\Str::random(4)),
        'assigned_to' => $qr->assigned_to ?? null,
        'status' => 'new',
        'remarks' => 'Converted from Quote Request',
        'created_by' => auth()->id()
      ]
    );

    return response()->json(['status'=>true, 'data'=>$lead]);
}

/**
 * Send mail (triggered from modal) - stub
 */
public function sendMail(Request $request, $id)
{
    // Use your existing mail method to send quote email
    // e.g. $this->sendQuoteEmailInternal($qr)
    $lead = Lead::findOrFail($id);
    // implement sending logic...
    return response()->json(['status'=>true, 'message'=>'Mail queued/sent (stub).']);
}
