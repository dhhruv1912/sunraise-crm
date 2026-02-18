<?php

namespace App\Http\Controllers;

use App\Mail\QuoteRequestMail;
use App\Models\Project;
use App\Models\QuoteMaster;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestHistory;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Lead;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Services\QuoteRequestService;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;
use App\Imports\QuoteRequestImport;

class QuoteRequestController extends Controller
{
    public function index()
    {
        return view('page.quote-request.index', [
            'status' => QuoteRequest::STATUS_LABELS,
        ]);
    }

    public function create()
    {
        return view('page.quote-request.create', [
            'users' => User::select('id','fname','lname')->get(),
        ]);
    }

    public function store(Request $request)
    {

        $data = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email'  => 'nullable|email',
            'address'=> 'nullable|string',

            'type'            => 'required|string',
            'kw'              => 'required|numeric',
            'budget'          => 'nullable|numeric',
            'assigned_to'     => 'nullable|exists:users,id',
            'notes'           => 'nullable|string',
        ]);

        if ($data->fails()) {
            return response()->json([
                'errors' => $data,
            ]);
        }

        /* ================= CUSTOMER UPSERT ================= */
        $customer = Customer::firstOrCreate(
            ['mobile' => $request->mobile],
            [
                'name'    => $request->name,
                'email'   => $request->email ?? null,
                'address' => $request->address ?? null,
            ]
        );

        /* ================= CREATE QUOTE REQUEST ================= */
        QuoteRequestService::create([
            'customer_id' => $customer->id,
            'type'        => $request->type,
            'kw'          => $request->kw,
            'budget'      => $request->budget ?? null,
            'assigned_to' => $request->assigned_to ?? null,
            'notes'       => $request->notes ?? null,
        ], Auth::id(), 'manual');

        return response()->json([
            'message' => 'Quote request created successfully',
        ]);
    }

    /* ================= WIDGETS ================= */
    public function ajaxWidgets()
    {
        $today = now()->toDateString();

        $statusCounts = DB::table('quote_requests')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $trend = DB::table('quote_requests')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $stale = DB::table('quote_requests')
            ->whereIn('status', ['new_request', 'pending'])
            ->where('created_at', '<=', now()->subDays(3))
            ->count();

        return view('page.quote-request.widgets', [
            'total' => $statusCounts->sum(),
            'new' => $statusCounts['new_request'] ?? 0,
            'pending' => $statusCounts['pending'] ?? 0,
            'responded' => $statusCounts['responded'] ?? 0,
            'today' => DB::table('quote_requests')
                ->whereDate('created_at', $today)
                ->count(),
            'statusCounts' => $statusCounts,
            'trend' => $trend,
            'stale' => $stale,
        ]);
    }

    public function ajaxChartData()
    {
        /* ================= STATUS ================= */
        $statusCounts = DB::table('quote_requests')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $total = $statusCounts->sum();

        /* ================= TREND ================= */
        $trend = DB::table('quote_requests')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        /* ================= WEEKDAY HEATMAP ================= */
        $weekday = DB::table('quote_requests')
            ->select(
                DB::raw('DAYNAME(created_at) as day'),
                DB::raw('count(*) as total')
            )
            ->groupBy('day')
            ->pluck('total', 'day');

        /* ================= RESPONSE TIME ================= */
        $responseTime = DB::table('quote_requests')
            ->whereNotNull('updated_at')
            ->select(
                'status',
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as avg_minutes')
            )
            ->groupBy('status')
            ->pluck('avg_minutes', 'status');

        /* ================= SLA ================= */
        $slaLimit = 1440; // 24 hours
        $slaBreached = DB::table('quote_requests')
            ->whereIn('status', ['new_request', 'pending'])
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, NOW()) > ?', [$slaLimit])
            ->count();

        return response()->json([
            'total' => $total,
            'status' => $statusCounts,
            'trend' => $trend,
            'weekday' => $weekday,
            'response_time' => $responseTime,
            'sla' => [
                'limit' => $slaLimit,
                'breach' => $slaBreached,
            ],
        ]);
    }

    /* ================= LIST ================= */
    public function ajaxList(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);
        $q = DB::table('quote_requests')
            ->leftJoin('customers', 'customers.id', '=', 'quote_requests.customer_id')
            ->select(
                'quote_requests.*',
                'customers.name as customer_name',
                'customers.mobile'
            );

        if ($request->filled('status')) {
            $q->where('quote_requests.status', $request->status);
        }
        if ($request->filled('type')) {
            $q->where('quote_requests.type', $request->type);
        }

        if ($request->filled('search')) {
            $q->where(function ($s) use ($request) {
                $s->where('customers.name', 'like', '%'.$request->search.'%')
                    ->orWhere('customers.mobile', 'like', '%'.$request->search.'%');
            });
        }

        $total = (clone $q)->count();
        $rows = $q->orderByDesc('quote_requests.created_at')
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
            "canEdit" => Gate::allows('quote.request.edit')
        ]);
    }

    public function view($id)
    {
        /* ================= LOAD QUOTE REQUEST ================= */
        $qr = QuoteRequest::with([
            'customer',
            'assignedUser',
            'creator',
            // 'quotations', // optional / future
            'quoteMaster',
            'history.user',
        ])->findOrFail($id);
            // dd($qr);
        /* ================= RELATED PROJECTS ================= */
        $projects = null;
        if ($qr->quoteMaster) {
            $projects = Project::where('quote_master_id', $qr->quoteMaster->id)
                ->limit(5)
                ->get();
        }
        /* ================= STATUS TRANSITION ================= */
        if ($qr->status === 'new_request') {

            QuoteRequestHistory::create([
                'quote_request_id' => $qr->id,
                'action' => 'view',
                'message' => 'Viewed Quote Request',
                'user_id' => Auth::id(),
            ]);

            $qr->status = 'viewed';
            $qr->save();

        } elseif ($qr->status === 'viewed') {

            $qr->status = 'pending';
            $qr->save();
        }

        /* ================= HISTORY MAPPING ================= */
        $history = $qr->history
            ->sortByDesc('created_at')
            ->map(function ($h) {
                return [
                    'action' => $h->action,
                    'message' => $h->message,
                    'user' => trim(
                        optional($h->user)->fname.' '.optional($h->user)->lname
                    ),
                    'datetime' => optional($h->created_at)
                        ? $h->created_at->format('d M Y h:i A')
                        : null,
                ];
            })
            ->values();

        /* ================= RETURN VIEW ================= */
        return view('page.quote-request.view', [
            'data' => $qr,
            'history' => $history,
            'master' => QuoteMaster::get(),
            'users' => User::select('id', 'fname', 'lname')->get(),
            'projects' => $projects,
        ]);
    }

    public function convertToLead($id)
    {
        $qr = QuoteRequest::with(['customer'])->findOrFail($id);

        /* ================= HARD BUSINESS RULE ================= */
        abort_if(
            !$qr->quote_email_sent_at,
            403,
            'Quote email must be sent before converting to lead'
        );

        /* ================= PREVENT DUPLICATE ================= */
        if ($qr->lead_id) {
            return response()->json([
                'message' => 'This quote request is already converted to a lead',
            ], 409);
        }

        /* ================= CREATE LEAD ================= */
        $lead = Lead::create([
            'quote_request_id' => $qr->id,
            'lead_code'        => 'LD-' . strtoupper(Str::random(6)),
            'status'           => 'new',
            'assigned_to'      => $qr->assigned_to,
            'created_by'       => Auth::id(),
            'customer_id'      => $qr->customer_id,
            'remarks'          => 'Lead created from Quote Request',
        ]);

        /* ================= LINK BACK ================= */
        $qr->lead_id = $lead->id;
        $qr->status  = 'called_converted_to_lead';
        $qr->save();

        /* ================= HISTORY ================= */
        QuoteRequestHistory::create([
            'quote_request_id' => $qr->id,
            'action'           => 'convert',
            'message'          => 'Quote Request converted to Lead',
            'user_id'          => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Quote Request converted to Lead successfully',
            'lead_id' => $lead->id,
        ]);
    }

    public function assignUser(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $qr = QuoteRequest::findOrFail($id);
        $qr->assigned_to = $request->user_id;
        $qr->save();

        QuoteRequestHistory::create([
            'quote_request_id' => $qr->id,
            'action' => 'assign',
            'message' => 'Assigned to user',
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'User assigned successfully',
        ]);
    }

    public function sendQuoteEmail($id)
    {
        $qr = QuoteRequest::with(['customer', 'quoteMaster'])->findOrFail($id);

        abort_if(!$qr->customer || !$qr->customer->email, 400, 'Customer email missing');

        $projects = Project::where('status', 'complete')
            ->latest()
            ->limit(5)
            ->get();

        Mail::to($qr->customer->email)
            ->send(new QuoteRequestMail($qr, $projects));

        // 🔐 MARK EMAIL SENT
        $qr->quote_email_sent_at = now();
        $qr->save();

        QuoteRequestHistory::create([
            'quote_request_id' => $qr->id,
            'action' => 'email',
            'message' => 'Quote email sent to customer',
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Quote email sent successfully',
        ]);
    }


    /* ================= UPDATE QUOTE MASTER ================= */
    public function updateQuoteMaster(Request $request, $id)
    {
        $request->validate([
            'quote_master_id' => 'required|exists:quote_master,id',
        ]);

        $qr = QuoteRequest::findOrFail($id);
        $qr->quote_master_id = $request->quote_master_id;
        $qr->save();

        QuoteRequestHistory::create([
            'quote_request_id' => $qr->id,
            'action' => 'quote_master',
            'message' => 'Quote master updated',
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Quote master updated successfully',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        Excel::import(new QuoteRequestImport(Auth::id()), $request->file('file'));

        return response()->json([
            'message' => 'Quote requests imported successfully'
        ]);
    }
}
