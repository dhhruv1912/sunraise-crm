<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Lead;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Validator;

class QuotationController extends Controller
{
    /* ---------------------------------------------------------
     | MAIN LIST PAGE
     --------------------------------------------------------- */
    public function index($id="")
    {
        return view('page.quotations.list',compact('id'));
    }

    /* ---------------------------------------------------------
     | AJAX LIST (pagination)
     --------------------------------------------------------- */
    public function ajaxList(Request $request,$id=null)
    {
        $perPage = (int)($request->per_page ?? 20);

        $query = Quotation::with(['lead.customer', 'sentBy']);
        if($id){
            $query = $query->where('id',$id);
        }
        if ($search = $request->search) {
            $query->where('quotation_no', 'like', "%$search%")
                  ->orWhere('meta->sku', 'like', "%$search%")
                  ->orWhereHas('lead', function ($ld) use ($search) {
                      $ld->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%")
                         ->orWhere('number', 'like', "%$search%");
                  });
        }

        return response()->json(
            $query->orderBy('id', 'desc')->paginate($perPage)
        );
    }

    /* ---------------------------------------------------------
     | CREATE FORM
     --------------------------------------------------------- */
    public function create()
    {
        return view('page.quotations.form', [
            'leads' => Lead::with('customer')->latest()->limit(200)->get()
        ]);
    }

    /* ---------------------------------------------------------
     | STORE NEW QUOTATION
     --------------------------------------------------------- */
    public function store(Request $request)
    {
        if ($request->has('meta') && is_string($request->meta)) {
            $request->merge([
                'meta' => json_decode($request->meta, true)
            ]);
        }

        $rules = [
            // 'quote_request_id' => 'nullable|exists:quote_requests,id',
            'lead_id' => 'nullable|exists:leads,id',
            'base_price'       => 'required|numeric',
            'discount'         => 'nullable|numeric',
            'final_price'      => 'nullable|numeric',
            'meta'             => 'nullable|array'
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $v->errors()
            ], 422);
        }


        $quotation = new Quotation($request->toArray());

        $quotation->quotation_no = $this->generateQuotationNo();
        $quotation->sent_by      = null;
        $quotation->final_price  = $request['final_price']
                                   ?? ($request['base_price'] - ($request['discount'] ?? 0));
        $quotation->meta         = $request['meta'] ?? [];

        $quotation->save();

        return redirect()->route('quotations.index')->with('success', 'Quotation created.');
    }

    /* ---------------------------------------------------------
     | EDIT FORM
     --------------------------------------------------------- */
    public function edit($id)
    {
        return view('page.quotations.form', [
            'quotation'     => Quotation::findOrFail($id),
            'quoteRequests' => QuoteRequest::latest()->limit(200)->get(),
        ]);
    }

    /* ---------------------------------------------------------
     | UPDATE QUOTATION
     --------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        $validated = $request->validate([
            'lead_id' => 'nullable|exists:leads,id',
            // 'quote_request_id' => 'nullable|exists:quote_requests,id',
            'base_price'       => 'required|numeric',
            'discount'         => 'nullable|numeric',
            'final_price'      => 'nullable|numeric',
            'meta'             => 'nullable|array'
        ]);

        $quotation->fill($validated);
        $quotation->final_price = $validated['final_price']
                                  ?? ($validated['base_price'] - ($validated['discount'] ?? 0));
        $quotation->meta        = $validated['meta'] ?? $quotation->meta;
        $quotation->save();

        return redirect()->route('quotations.index')->with('success', 'Quotation updated.');
    }

    /* ---------------------------------------------------------
     | DELETE (AJAX)
     --------------------------------------------------------- */
    public function destroy(Request $request, $id)
    {
        $q = Quotation::findOrFail($id);

        if ($q->pdf_path) {
            Storage::disk('public')->delete($q->pdf_path);
        }

        $q->delete();

        return response()->json(['status' => true, 'message' => 'Deleted']);
    }

    /* ---------------------------------------------------------
     | GENERATE PDF + SAVE PATH
     --------------------------------------------------------- */
    public function generatePdf($id)
    {
        $q = Quotation::with('lead.customer','quoteMaster','lead')->findOrFail($id);
        $pdf = Pdf::loadView('emails.quote_sent_pdf', [
            'quotation' => $q,
            'lead'   => $q->lead,
            'master'   => $q->quoteMaster,
            'company'   => [
                "COMPANY_NAME" => env("COMPANY_NAME"),
                "COMPANY_NUMBER" => env("COMPANY_NUMBER"),
                "COMPANY_EMAIL" => env("COMPANY_EMAIL"),
                "COMPANY_GST" => env("COMPANY_GST"),
                "COMPANY_ADDRESS" => env("COMPANY_ADDRESS"),
            ],
        ]);

        $file = "quote_" . $q->id . "_" . now()->format('Ymd_His') . ".pdf";
        $path = "quotes/" . $file;

        Storage::disk('public')->put($path, $pdf->output());
        // $url = Storage::disk('public')->url($path);
        $q->pdf_path = $path;
        $q->save();

        return response()->json([
            'status'   => true,
            'message'  => 'PDF generated',
            // 'pdf_url'  => $url
            'pdf_url'  => asset('storage/' . $path)
        ]);
    }

    /* ---------------------------------------------------------
     | DOWNLOAD PDF DIRECTLY
     --------------------------------------------------------- */
    public function downloadPdf($id)
    {
        $q = Quotation::findOrFail($id);

        if (!$q->pdf_path || !Storage::disk('public')->exists($q->pdf_path)) {
            abort(404, 'PDF not found. Please generate again.');
        }

        return response()->download(storage_path("app/public/" . $q->pdf_path));
    }

    /* ---------------------------------------------------------
     | SEND EMAIL WITH PDF
     --------------------------------------------------------- */
    public function sendEmail(Request $request, $id)
    {
        $q = Quotation::with('lead.customer')->findOrFail($id);
        $qr = $q->lead;

        if (!$qr || !$qr->customer->email) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer email not available.'
            ], 422);
        }

        // If PDF missing → generate
        if (!$q->pdf_path || !Storage::disk('public')->exists($q->pdf_path)) {
            $this->generatePdf($id);
            $q->refresh();
        }

        $pdfPath = storage_path("app/public/" . $q->pdf_path);

        Mail::send('emails.quotation_sent', [
            'quotation' => $q,
            'request'   => $qr
        ], function ($m) use ($qr, $q, $pdfPath) {
            $m->to($qr->customer->email, $qr->customer->name)
              ->subject("Quotation {$q->quotation_no} - " . config('app.name'))
              ->attach($pdfPath);
        });

        $q->sent_at = now();
        $q->sent_by = Auth::id();
        $q->save();

        return response()->json([
            'status'  => true,
            'message' => 'Email sent successfully'
        ]);
    }

    /* ---------------------------------------------------------
     | EXPORT CSV
     --------------------------------------------------------- */
    public function export()
    {
        $rows = Quotation::with('lead.customer')
            ->latest()
            ->get()
            ->map(function ($q) {
                return [
                    'id'            => $q->id,
                    'quotation_no'  => $q->quotation_no,
                    'request_id'    => $q->quote_request_id,
                    'customer'      => optional($q->lead->customer)->name,
                    'email'         => optional($q->lead->customer)->email,
                    'base_price'    => $q->base_price,
                    'discount'      => $q->discount,
                    'final_price'   => $q->final_price,
                    'sent_at'       => $q->sent_at,
                ];
            })
            ->toArray();

        $columns = array_keys($rows[0] ?? [
            'id','quotation_no','request_id','customer','email','final_price','sent_at'
        ]);

        $fileName = "quotations_export_" . now()->format('Ymd_His') . ".csv";

        $response = new StreamedResponse(function () use ($rows, $columns) {
            $handle = fopen("php://output", "w");
            fputcsv($handle, $columns);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename={$fileName}");

        return $response;
    }

    /* ---------------------------------------------------------
     | QUOTATION NUMBER GENERATOR
     --------------------------------------------------------- */
    protected function generateQuotationNo()
    {
        return "Q-" . now()->format('Ymd') . "-" . strtoupper(Str::random(4));
    }
}
