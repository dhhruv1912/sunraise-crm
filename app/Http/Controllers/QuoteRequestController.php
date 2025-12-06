<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Project;
use App\Models\QuoteMaster;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestHistory;
use App\Models\Settings;
use App\Models\User;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuoteRequestController extends Controller
{
    /**
     * Status map used in UI
     */
    public static $STATUS = [
        'new_request' => 'New Request',
        'viewed' => 'Viewed',
        'pending' => 'Pending',
        'responded' => 'Responded',
        'called' => 'Called',
        'called_converted_to_lead' => 'Called & Converted to Lead',
        'called_closed' => 'Called & Closed',
    ];

    /* ---------------------------------------------------------
     | LIST PAGE (Blade)
     --------------------------------------------------------- */
    public function index()
    {
        return view('page.quote_requests.list', [
            'users' => User::orderBy('fname')->get(['id', 'fname', 'lname']),
            'statuses' => self::$STATUS,
        ]);
    }

    /* ---------------------------------------------------------
     | AJAX LIST (table)
     --------------------------------------------------------- */
    public function ajaxList(Request $request)
    {
        $perPage = (int) ($request->per_page ?? 20);

        $q = QuoteRequest::query()->with('customer');

        if ($s = $request->search) {
            $q->where(function ($x) use ($s) {
                $x->where('name', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('number', 'like', "%$s%")
                    ->orWhere('module', 'like', "%$s%");
            });
        }

        if ($request->filled('filter_type')) {
            $q->where('type', $request->filter_type);
        }
        if ($request->filled('filter_status')) {
            $q->where('status', $request->filter_status);
        }
        if ($request->filled('filter_name')) {
            $q->where('name', 'like', "%{$request->filter_name}%");
        }
        if ($request->filled('filter_mobile')) {
            $q->where('number', 'like', "%{$request->filter_mobile}%");
        }
        if ($request->filled('filter_module')) {
            $q->where('module', 'like', "%{$request->filter_module}%");
        }
        if ($request->filled('filter_kw')) {
            $q->where('kw', $request->filter_kw);
        }
        if ($request->filled('filter_assigned')) {
            $q->where('assigned_to', $request->filter_assigned);
        }
        if ($request->filled('filter_from')) {
            $q->whereDate('created_at', '>=', $request->filter_from);
        }
        if ($request->filled('filter_to')) {
            $q->whereDate('created_at', '<=', $request->filter_to);
        }

        $data = $q->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'pagination' => $data,
            'data' => $data->items(),
            'users' => User::select('id', 'fname', 'lname')->get(),
        ]);
    }

    /* ---------------------------------------------------------
     | VIEW WRAPPER PAGE (modal triggers AJAX)
     --------------------------------------------------------- */
    public function view($id)
    {
        return view('page.quote_requests.view_wrapper', ['id' => $id]);
    }

    /* ---------------------------------------------------------
     | VIEW JSON (modal content)
     --------------------------------------------------------- */
    public function viewJson($id)
    {
        $qr = QuoteRequest::with([
            'customer',
            'assignedUser',
            'creator',
            'quotations',
            'history.user', // load user inside history so no extra queries
        ])->findOrFail($id);

        // map history from eager-loaded relation (no duplicate query)
        $history = $qr->history->sortByDesc('created_at')->map(function ($h) {
            return [
                'action' => $h->action,
                'message' => $h->message,
                'user' => optional($h->user)->fname.' '.optional($h->user)->lname,
                'datetime' => optional($h->created_at)->format('d M Y h:i A'),
            ];
        })->values(); // reset index

        return response()->json([
            'data' => $qr,
            'history' => $history,
            'master' => QuoteMaster::pluck('sku','id'),
            'users' => User::select('id', 'fname', 'lname')->get(),
        ]);

    }
    public function apiView($id)
    {
        $qr = QuoteRequest::
            with([
                'quote',
            ])->
            findOrFail($id);

        return response()->json($qr);

    }

    /* ---------------------------------------------------------
     | CREATE FORM
     --------------------------------------------------------- */
    public function create()
    {
        return view('page.quote_requests.form', [
            'statuses' => self::$STATUS,
            'users' => User::select('id','fname','lname')->get()
        ]);
    }

    /* ---------------------------------------------------------
     | STORE
     --------------------------------------------------------- */
    public function store(Request $request)
    {
        $payload = $this->validatePayload($request);
        $customerId = null;
        if ($request->filled('mobile') || $request->filled('email')) {
            $customer = Customer::firstOrCreate(
                ['mobile' => $request->number],
                ['name' => $request->name ?? null, 'email' => $request->email ?? null]
            );
            $customerId = $customer->id;
        }
        $qr = QuoteRequest::create([
            'type' => $request->type,
            'customer_id' => $customerId,
            'module' => $request->module,
            'kw' => $request->kw,
            'mc' => $request->mc,
            'budget' => $request->budget,
            'status' => $request->status,
            'assigned_to' => Auth::id(),
            'quote_master_id' => null,
            'created_by' => Auth::id(),
            'notes' => $request->notes,
            'source' => $request->source,
            'ip' => $request->ip,
            'location' => $request->location,
        ]);

        // Auto actions
        if ($this->autoMailEnabled()) {
            $this->createLeadIfMissing($qr);
            $this->safeSendMail($qr);
        }

        return redirect()->route('quote_requests.index')
            ->with('success', 'Quote request saved.');
    }

    /* ---------------------------------------------------------
     | EDIT FORM
     --------------------------------------------------------- */
    public function edit($id)
    {
        return view('page.quote_requests.form', [
            'row' => QuoteRequest::findOrFail($id),
            'statuses' => self::$STATUS,
        ]);
    }

    /* ---------------------------------------------------------
     | UPDATE
     --------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $payload = $this->validatePayload($request);

        QuoteRequest::findOrFail($id)->update($payload);

        return redirect()->route('quote_requests.index')
            ->with('success', 'Quote request updated.');
    }

    /* ---------------------------------------------------------
     | DELETE (AJAX)
     --------------------------------------------------------- */
    public function delete(Request $request)
    {
        QuoteRequest::where('id', $request->id)->delete();

        return response()->json(['status' => true, 'message' => 'Deleted']);
    }

    /* ---------------------------------------------------------
     | EXPORT CSV
     --------------------------------------------------------- */
    public function export()
    {
        $fileName = 'quote_requests_'.now()->format('Ymd_His').'.csv';
        $rows = QuoteRequest::orderBy('id', 'desc')->get()->toArray();

        $columns = array_keys($rows[0] ?? []);

        return new StreamedResponse(function () use ($rows, $columns) {
            $h = fopen('php://output', 'w');
            fputcsv($h, $columns);
            foreach ($rows as $r) {
                fputcsv($h, $r);
            }
            fclose($h);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);
    }

    /* ---------------------------------------------------------
     | IMPORT CSV
     --------------------------------------------------------- */
    public function import(Request $request)
    {
        if (! $request->hasFile('file')) {
            return back()->with('error', 'Upload CSV file.');
        }

        $fp = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($fp);

        while ($row = fgetcsv($fp)) {
            $data = array_combine($header, $row);

            $payload = [
                'type' => $data['type'] ?? null,
                'name' => $data['name'] ?? null,
                'number' => $data['number'] ?? null,
                'email' => $data['email'] ?? null,
                'module' => $data['module'] ?? null,
                'kw' => $data['kw'] ?? null,
                'mc' => $data['mc'] ?? null,
                'status' => $data['status'] ?? 'new_request',
            ];

            $existing = QuoteRequest::where('number', $payload['number'])->first()
                     ?: QuoteRequest::where('email', $payload['email'])->first();

            $existing ? $existing->update($payload) : QuoteRequest::create($payload);
        }

        fclose($fp);

        return back()->with('success', 'Import done.');
    }

    /* ---------------------------------------------------------
     | UPDATE STATUS
     --------------------------------------------------------- */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:'.implode(',', array_keys(self::$STATUS)),
        ]);

        $qr = QuoteRequest::findOrFail($id);
        $old = $qr->status;
        $new = $request->status;

        if ($old === $new) {
            return response()->json(['status' => true, 'message' => 'No change']);
        }

        $qr->update(['status' => $new]);

        // auto create lead & send mail
        if (in_array($new, ['responded', 'called_converted_to_lead'])) {
            $this->createLeadIfMissing($qr);

            if ($this->autoMailEnabled()) {
                $this->safeSendMail($qr);
            }
        }

        return response()->json(['status' => true, 'message' => 'Status updated']);
    }
    public function updateQuoteMaster(Request $request, $id)
    {
        $request->validate([
            'quote_master_id' => 'required',
        ]);

        $qr = QuoteRequest::findOrFail($id);
        $old = $qr->quote_master_id;
        $new = $request->quote_master_id;
        // dd($qr,$new);
        if ($old === $new) {
            return response()->json(['status' => true, 'message' => 'No change']);
        }

        $qr->update(['quote_master_id' => $new]);
        QuoteRequestHistory::create([
            'quote_request_id' => $qr->id,
            'action' => 'quote_master',
            'message' => 'Change Quote Master ID from ' . $old . ' to ' . $new,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'message' => 'Status updated']);
    }

    /* ---------------------------------------------------------
     | ASSIGN
     --------------------------------------------------------- */
    public function assign(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'nullable|exists:users,id']);

        $qr = QuoteRequest::findOrFail($id);
        $qr->assigned_to = $request->assigned_to;
        $qr->save();

        QuoteRequestHistory::create([
            'quote_request_id' => $qr->id,
            'action' => 'assign',
            'message' => 'Assigned to user ID '.$request->assigned_to,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['status' => true, 'message' => 'Assigned']);
    }

    /* ---------------------------------------------------------
     | SEND MAIL (Manual)
     --------------------------------------------------------- */
    public function sendMail(Request $request, $id)
    {
        $qr = QuoteRequest::with('customer')->findOrFail($id);
        // try {
            $this->sendMailNow($qr);

            return response()->json(['status' => true, 'message' => 'Mail sent']);
        // } catch (\Throwable $e) {
        //     Log::error($e);

        //     return response()->json(['status' => false, 'message' => 'Mail failed'], 500);
        // }
    }

    /* ---------------------------------------------------------
     | VALIDATION
     --------------------------------------------------------- */
    protected function validatePayload(Request $request)
    {
        $rules = [
            'type' => 'nullable|string|in:quote,call',
            'name' => 'required|string',
            'number' => 'nullable|string',
            'email' => 'nullable|email',
            'module' => 'nullable|string',
            'kw' => 'nullable|numeric',
            'mc' => 'nullable|integer',
            'status' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'source' => 'nullable|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $v->errors()
            ], 422);
        }
    }

    /* ---------------------------------------------------------
     | CREATE LEAD (idempotent)
     --------------------------------------------------------- */
    public function createLeadIfMissing($id)
    {
        $qr = QuoteRequest::findOrFail($id);
        if ($lead = Lead::where('quote_request_id', $qr->id)->first()) {
            return $lead;
        }

        return Lead::create([
            'quote_request_id' => $qr->id,
            'customer_id' => $qr->customer_id,
            'lead_code' => 'LD-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
            'assigned_to' => $qr->assigned_to,
            'status' => 'new',
            'remarks' => 'Auto-created from Quote Request #'.$qr->id,
            'created_by' => Auth::id(),
        ]);
    }

    /* ---------------------------------------------------------
     | SETTINGS: Auto mail?
     --------------------------------------------------------- */
    protected function autoMailEnabled()
    {
        $row = Settings::where('name', 'send_auto_response')->first();
        if (! $row) {
            return false;
        }

        return in_array(strtolower(trim($row->value)), ['1', 'yes', 'true', 'on']);
    }

    /* ---------------------------------------------------------
     | SEND QUOTATION MAIL (safe wrapper)
     --------------------------------------------------------- */
    protected function safeSendMail(QuoteRequest $qr)
    {
        // try {
            return $this->sendMailNow($qr);
        // } catch (\Throwable $e) {
        //     Log::warning("Mail failed for QR {$qr->id}: ".$e->getMessage());

        //     return false;
        // }
    }

    /* ---------------------------------------------------------
     | SEND QUOTATION MAIL + PDF
     --------------------------------------------------------- */
    protected function sendMailNow(QuoteRequest $qr)
    {
        if (!$qr->customer->email) {
            return false;
        }

        $projects = Project::where('status', 'complete')->latest()->limit(5)->get();
        $lead = Lead::where('quote_request_id', $qr->id)->first();

        $data = compact('qr', 'projects', 'lead');

        // PDF generation
        $fileName = "quote_{$qr->id}_".date('Ymd_His').'.pdf';
        $path = storage_path("app/public/quotes/$fileName");

        try {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            Pdf::loadView('emails.quote_sent_pdf', [
                'request' => $qr,
                'projects' => $projects,
                'lead' => $lead,
            ])->save($path);
        } catch (\Throwable $e) {
            $path = null; // continue mail without PDF
        }

        // Mail
        Mail::send('emails.quote_sent', [
            'request' => $qr,
            'projects' => $projects,
            'lead' => $lead,
        ], function ($m) use ($qr, $path) {
            $m->to($qr->customer->email, $qr->customer->name)->subject('Your Quotation');

            if ($path && file_exists($path)) {
                $m->attach($path);
            }
        });

        return true;
    }
}
