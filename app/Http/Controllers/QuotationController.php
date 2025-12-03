<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuoteRequest;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; // requires barryvdh/laravel-dompdf
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;

class QuotationController extends Controller
{
    // List page
    public function index()
    {
        return view('page.quotations.list');
    }

    // AJAX paginated list
    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);
        $query = Quotation::query()->with(['quoteRequest', 'sentBy']);

        if ($q = $request->get('search')) {
            $query->where(function($qq) use ($q) {
                $qq->where('quotation_no', 'like', "%{$q}%")
                   ->orWhere('meta->sku', 'like', "%{$q}%")
                   ->orWhereHas('quoteRequest', function($qr) use ($q) {
                        $qr->where('name', 'like', "%{$q}%")
                           ->orWhere('email', 'like', "%{$q}%")
                           ->orWhere('number', 'like', "%{$q}%");
                   });
        });

        }

        $data = $query->orderBy('id', 'desc')->paginate($perPage);
        return response()->json($data);
    }

    // Create form
    public function create()
    {
        $quoteRequests = QuoteRequest::orderBy('created_at','desc')->limit(200)->get();
        return view('page.quotations.form', compact('quoteRequests'));
    }

    // Store
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'quote_request_id' => 'nullable|exists:quote_requests,id',
            'base_price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'final_price' => 'nullable|numeric',
            'meta' => 'nullable|array',
        ]);

        if ($v->fails()) {
            dd($v);
            // return back()->withErrors($v)->withInput();
        }

        $quotation = new Quotation($v->validated());
        $quotation->quotation_no = $this->generateQuotationNo();
        $quotation->sent_by = auth()->id() ?? null;
        $quotation->final_price = $request->final_price ?? ($request->base_price - ($request->discount ?? 0));
        $quotation->meta = $request->meta ?? [];

        $quotation->save();

        return redirect()->route('quotations.index')->with('success', 'Quotation created.');
    }

    // Edit form
    public function edit($id)
    {
        $quotation = Quotation::findOrFail($id);
        $quoteRequests = QuoteRequest::orderBy('created_at','desc')->limit(200)->get();
        return view('page.quotations.form', compact('quotation','quoteRequests'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        $v = Validator::make($request->all(), [
            'quote_request_id' => 'nullable|exists:quote_requests,id',
            'base_price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'final_price' => 'nullable|numeric',
            'meta' => 'nullable|array',
        ]);

        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $quotation->fill($v->validated());
        $quotation->final_price = $request->final_price ?? ($request->base_price - ($request->discount ?? 0));
        $quotation->meta = $request->meta ?? $quotation->meta;
        $quotation->save();

        return redirect()->route('quotations.index')->with('success', 'Quotation updated.');
    }

    // Delete (AJAX)
    public function destroy(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        // delete pdf if exists
        if ($quotation->pdf_path) {
            Storage::disk('public')->delete($quotation->pdf_path);
        }

        $quotation->delete();

        return response()->json(['status' => true, 'message' => 'Deleted']);
    }

    // Generate PDF (returns public URL)
    public function generatePdf($id)
    {
        $quotation = Quotation::with('quoteRequest')->findOrFail($id);

        $data = [
            'quotation' => $quotation,
            'request' => $quotation->quoteRequest,
            'company' => config('app.name'),
        ];

        $pdf = Pdf::loadView('emails.quote_sent_pdf', $data);
        $filename = 'quote_' . $quotation->id . '_' . now()->format('Ymd_His') . '.pdf';

        $path = 'quotes/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        $quotation->pdf_path = $path;
        $quotation->save();
        return response()->json([
            'status' => true,
            'message' => 'PDF generated',
            'pdf_url' => asset('storage/'.$path)
        ]);
    }

    // Download PDF (direct)
    public function downloadPdf($id)
    {
        $quotation = Quotation::findOrFail($id);
        if (!$quotation->pdf_path || !Storage::disk('public')->exists($quotation->pdf_path)) {
            abort(404, 'PDF not available. Generate it first.');
        }

        return response()->download(storage_path('app/public/' . $quotation->pdf_path));
    }

    // Send email (attach PDF, optional generate first)
    public function sendEmail(Request $request, $id)
    {
        $quotation = Quotation::with('quoteRequest')->findOrFail($id);
        $qr = $quotation->quoteRequest;

        // ensure PDF exists
        if (!$quotation->pdf_path || !Storage::disk('public')->exists($quotation->pdf_path)) {
            // generate
            $this->generatePdf($id); // will update pdf_path
            $quotation->refresh();
        }

        if (!$qr || !$qr->email) {
            return response()->json(['status' => false, 'message' => 'Associated request or customer email not available'], 422);
        }

        $pdfPath = storage_path('app/public/' . $quotation->pdf_path);

        Mail::send('emails.quotation_sent', [
            'quotation' => $quotation,
            'request' => $qr,
        ], function ($m) use ($qr, $pdfPath, $quotation) {
            $m->to($qr->email, $qr->name ?? null)
                ->subject("Quotation {$quotation->quotation_no} from " . config('app.name'))
                ->attach($pdfPath);
        });

        $quotation->sent_at = now();
        $quotation->sent_by = auth()->id() ?? $quotation->sent_by;
        $quotation->save();

        return response()->json(['status' => true, 'message' => 'Email sent']);
    }

    // Export CSV
    public function export()
    {
        $fileName = "quotations_export_" . date("Y-m-d") . ".csv";
        $rows = Quotation::with('quoteRequest')->orderBy('id','desc')->get()->map(function($q){
            return [
                'id' => $q->id,
                'quotation_no' => $q->quotation_no,
                'request_id' => $q->quote_request_id,
                'customer' => optional($q->quoteRequest)->name,
                'email' => optional($q->quoteRequest)->email,
                'base_price' => $q->base_price,
                'discount' => $q->discount,
                'final_price' => $q->final_price,
                'sent_at' => $q->sent_at,
            ];
        })->toArray();

        $columns = array_keys($rows[0] ?? ['id','quotation_no','request_id','customer','email','final_price','sent_at']);

        $response = new StreamedResponse(function() use ($rows, $columns) {
            $handle = fopen('php://output','w');
            fputcsv($handle, $columns);
            foreach($rows as $row) fputcsv($handle, $row);
            fclose($handle);
        });

        $response->headers->set('Content-Type','text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename={$fileName}");

        return $response;
    }

    /*----------------------
     | Helpers
    ----------------------*/
    protected function generateQuotationNo()
    {
        $prefix = 'Q-';
        $date = now()->format('Ymd');
        $rand = strtoupper(Str::random(4));
        return "{$prefix}{$date}-{$rand}";
    }
}
