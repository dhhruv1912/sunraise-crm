<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestHistory;
use App\Models\Settings;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuoteRequestController extends Controller
{
    public static $STATUS = [
        'new_request'               => 'New Request',
        'viewed'                    => 'Viewed',
        'pending'                   => 'Pending',
        'responded'                 => 'Responded',
        'called'                    => 'Called',
        'called_converted_to_lead'  => 'Called & Converted to Lead',
        'called_closed'             => 'Called & Closed',
    ];

    /* LIST page */
    public function index()
    {
        $users = User::orderBy('fname')->get(['id','fname','lname']);
        $statuses = self::$STATUS;

        return view('page.quote_requests.list', compact('users','statuses'));
    }

    /* AJAX list */
    public function ajaxList(Request $request)
    {
        $perPage = (int)$request->get('per_page', 20);
        $query = QuoteRequest::query();

        // Filters
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('name','like',"%$search%")
                  ->orWhere('email','like',"%$search%")
                  ->orWhere('number','like',"%$search%")
                  ->orWhere('module','like',"%$search%");
            });
        }

        if ($request->filled('filter_type'))     $query->where('type',$request->filter_type);
        if ($request->filled('filter_status'))   $query->where('status',$request->filter_status);
        if ($request->filled('filter_name'))     $query->where('name','like',"%{$request->filter_name}%");
        if ($request->filled('filter_mobile'))   $query->where('number','like',"%{$request->filter_mobile}%");
        if ($request->filled('filter_module'))   $query->where('module','like',"%{$request->filter_module}%");
        if ($request->filled('filter_kw'))       $query->where('kw',$request->filter_kw);
        if ($request->filled('filter_assigned')) $query->where('assigned_to',$request->filter_assigned);
        if ($request->filled('filter_from'))     $query->whereDate('created_at','>=',$request->filter_from);
        if ($request->filled('filter_to'))       $query->whereDate('created_at','<=',$request->filter_to);

        $data = $query->orderBy('id','desc')->paginate($perPage);

        return response()->json([
            'pagination' => $data,
            'data'       => $data->items(),
            'users'      => User::select('id','fname','lname')->get()
        ]);
    }

    /* VIEW WRAPPER PAGE (Option D) */
    public function view($id)
    {
        // just return wrapper — modal will auto-fetch /view-json
        return view('page.quote_requests.view_wrapper', ['id' => $id]);
    }

    /* VIEW JSON (modal content) */
    public function viewJson($id)
    {
        $qr = QuoteRequest::findOrFail($id);

        $history = QuoteRequestHistory::where('quote_request_id',$id)
            ->latest()
            ->get()
            ->map(function($h){
                return [
                    'action'   => $h->action,
                    'message'  => $h->message,
                    'user'     => $h->user->fname . " " . $h->user->lname ?? 'System',
                    'datetime' => optional($h->created_at)->format('d M Y h:i A'),
                ];
            });

        return response()->json([
            'data'    => $qr,
            'history' => $history,
            'users'   => User::select('id','fname','lname')->get()
        ]);
    }

    /* CREATE form */
    public function create()
    {
        $statuses = self::$STATUS;
        return view('page.quote_requests.form', compact('statuses'));
    }

    /* STORE */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $qr = QuoteRequest::create($data);

        if ($this->sendAutoResponseEnabled()) {
            $this->createLeadIfNotExists($qr);

            try { $this->sendQuoteEmailInternal($qr); }
            catch (\Throwable $e) {
                Log::warning("Auto mail failed: ".$e->getMessage());
            }
        }

        return redirect()->route('quote_requests.index')->with('success', 'Quote request saved.');
    }

    /* EDIT */
    public function edit($id)
    {
        $row = QuoteRequest::findOrFail($id);
        $statuses = self::$STATUS;

        return view('page.quote_requests.form', compact('row','statuses'));
    }

    /* UPDATE */
    public function update(Request $request, $id)
    {
        $data = $this->validateRequest($request);
        $qr = QuoteRequest::findOrFail($id);
        $qr->update($data);

        return redirect()->route('quote_requests.index')->with('success', 'Updated.');
    }

    /* DELETE */
    public function delete(Request $request)
    {
        QuoteRequest::where('id',$request->id)->delete();
        return response()->json(['status'=>true,'message'=>'Deleted']);
    }

    /* EXPORT CSV */
    public function export()
    {
        $fileName = 'quote_requests_'.now()->format('Ymd_His').'.csv';
        $rows = QuoteRequest::orderBy('id','desc')->get()->toArray();

        $columns = array_keys($rows[0] ?? []);

        return new StreamedResponse(function() use ($rows,$columns){
            $h = fopen('php://output','w');
            fputcsv($h,$columns);
            foreach($rows as $r){ fputcsv($h,$r); }
            fclose($h);
        },200,[
            'Content-Type'=>'text/csv',
            'Content-Disposition'=>"attachment; filename={$fileName}",
        ]);
    }

    /* IMPORT CSV */
    public function import(Request $request)
    {
        if (!$request->hasFile('file')) {
            return back()->with('error','Upload a file.');
        }

        $fp = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($fp);

        while ($row = fgetcsv($fp)) {
            $data = array_combine($header, $row);
            $payload = [
                'type'   => $data['type'] ?? null,
                'name'   => $data['name'] ?? null,
                'number' => $data['number'] ?? null,
                'email'  => $data['email'] ?? null,
                'module' => $data['module'] ?? null,
                'kw'     => $data['kw'] ?? null,
                'mc'     => $data['mc'] ?? null,
                'status' => $data['status'] ?? 'new_request',
            ];

            $exist = QuoteRequest::where('number',$payload['number'])->first()
                ?: QuoteRequest::where('email',$payload['email'])->first();

            $exist ? $exist->update($payload) : QuoteRequest::create($payload);
        }

        fclose($fp);
        return back()->with('success','Import completed.');
    }

    /* STATUS UPDATE */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status'=>'required|string|in:'.implode(',',array_keys(self::$STATUS))]);

        $qr = QuoteRequest::findOrFail($id);
        $old = $qr->status;
        $new = $request->status;

        if ($old === $new) {
            return response()->json(['status'=>true,'message'=>'No change']);
        }

        $qr->update(['status'=>$new]);

        if (in_array($new, ['responded','called_converted_to_lead'])) {
            $this->createLeadIfNotExists($qr);

            if ($this->sendAutoResponseEnabled()) {
                try { $this->sendQuoteEmailInternal($qr); } catch (\Throwable $e) {}
            }
        }

        return response()->json(['status'=>true,'message'=>'Status updated']);
    }

    /* ASSIGN */
    public function assign(Request $request, $id)
    {
        $request->validate(['assigned_to'=>'nullable|exists:users,id']);

        $qr = QuoteRequest::findOrFail($id);
        $qr->assigned_to = $request->assigned_to;
        $qr->save();

        QuoteRequestHistory::create([
            'quote_request_id'=>$qr->id,
            'action'=>'assign',
            'message'=>'Assigned to user ID '.$request->assigned_to,
            'user_id'=>auth()->id(),
        ]);

        return response()->json(['status'=>true,'message'=>'Assigned']);
    }

    /* SEND MAIL */
    public function sendMail(Request $request, $id)
    {
        $qr = QuoteRequest::findOrFail($id);

        try {
            $this->sendQuoteEmailInternal($qr);
            return response()->json(['status'=>true,'message'=>'Mail sent']);
        }
        catch(\Throwable $e){
            Log::error($e);
            return response()->json(['status'=>false,'message'=>'Mail failed'],500);
        }
    }

    /* VALIDATION */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'type'         => 'nullable|string|in:quote,call',
            'name'         => 'required|string',
            'number'       => 'nullable|string',
            'email'        => 'nullable|email',
            'module'       => 'nullable|string',
            'kw'           => 'nullable|numeric',
            'mc'           => 'nullable|integer',
            'status'       => 'nullable|string',
            'assigned_to'  => 'nullable|exists:users,id',
            'notes'        => 'nullable|string',
            'source'       => 'nullable|string',
        ]);
    }

    /* CREATE LEAD */
    protected function createLeadIfNotExists(QuoteRequest $qr)
    {
        if ($existing = Lead::where('quote_request_id',$qr->id)->first()) {
            return $existing;
        }

        return Lead::create([
            'quote_request_id' => $qr->id,
            'lead_code'        => 'LD-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
            'assigned_to'      => $qr->assigned_to,
            'status'           => 'new',
            'remarks'          => 'Auto-created from quote request '.$qr->id,
            'created_by'       => auth()->id(),
        ]);
    }

    /* READ SETTINGS */
    protected function sendAutoResponseEnabled()
    {
        $row = Settings::where('name','send_auto_response')->first();
        if (!$row) return false;

        return in_array(strtolower(trim($row->value)), ['1','yes','true','on']);
    }

    /* SEND QUOTE MAIL */
    protected function sendQuoteEmailInternal(QuoteRequest $qr)
    {
        if (!$qr->email) return false;

        $projects = Project::where('status','complete')->latest()->limit(5)->get();
        $lead = Lead::where('quote_request_id',$qr->id)->first();

        $data = [
            'request'  => $qr,
            'projects' => $projects,
            'lead'     => $lead,
        ];

        // generate PDF
        $fileName = "quote_{$qr->id}_".date('Ymd_His').'.pdf';
        $path = storage_path("app/public/quotes/$fileName");

        try {
            if (!is_dir(dirname($path))) mkdir(dirname($path),0755,true);
            Pdf::loadView('emails.quote_sent_pdf',$data)->save($path);
        } catch (\Throwable $e) {
            $path = null;
        }

        Mail::send('emails.quote_sent',$data,function($m) use ($qr,$path){
            $m->to($qr->email,$qr->name)->subject('Your Quotation');
            if ($path && file_exists($path)) {
                $m->attach($path);
            }
        });

        return true;
    }
}
