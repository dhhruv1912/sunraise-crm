<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    public function index()
    {
        return view('page.quotations.index');
    }

    /* ================= LIST ================= */
    public function ajaxList(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $q = Quotation::with([
                'lead.customer',
                'sentBy',
                'lead.quoteMaster',
            ])
            ->latest();

        /* ---------- FILTERS ---------- */

        if ($request->status) {
            if ($request->status === 'sent') {
                $q->whereNotNull('sent_at');
            }

            if ($request->status === 'draft') {
                $q->whereNull('sent_at');
            }
        }

        if ($request->search) {
            $s = $request->search;
            $q->whereHas('lead.customer', function ($c) use ($s) {
                $c->where('name', 'like', "%{$s}%")
                ->orWhere('mobile', 'like', "%{$s}%");
            });
        }

        /* ---------- PAGINATION ---------- */

        $total = (clone $q)->count();

        $rows = $q
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($q) {
                return [
                    'id'           => $q->id,
                    'quotation_no' => $q->quotation_no,
                    'customer'     => optional($q->lead->customer)->name,
                    'mobile'       => optional($q->lead->customer)->mobile,
                    'price'        => $q->final_price,
                    'sent'         => (bool) $q->sent_at,
                    'sku'          => $q->lead->quoteMaster->sku ?? '',
                    'lead_code'    => $q->lead->lead_code,
                    'sent_at'      => optional($q->sent_at)?->format('d M Y'),
                ];
            });

        return response()->json([
            'data' => $rows,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $perPage),
            ]
        ]);
    }


    /* ================= WIDGETS ================= */
    public function ajaxWidgets()
    {
        return view('page.quotations.widgets', [
            'total' => Quotation::count(),
            'sent' => Quotation::whereNotNull('sent_at')->count(),
            'draft' => Quotation::whereNull('sent_at')->count(),
            'value' => Quotation::sum('final_price'),
        ]);
    }

    public function view(Quotation $quotation)
    {
        $quotation->load([
            'lead.customer',
            'lead.quoteRequest',
            'sentBy',
        ]);

        return view('page.quotations.view', [
            'data' => $quotation,
        ]);
    }

    public function create(Lead $lead)
    {
        $lead->load([
            'customer',
            'quoteRequest',
            'quoteMaster',
        ]);

        return view('page.quotations.create', [
            'lead' => $lead,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'base_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
        ]);

        $quotation = Quotation::create([
            'quotation_no' => Quotation::generateNumber(),
            'lead_id' => $request->lead_id,
            'base_price' => $request->base_price,
            'discount' => $request->discount ?? 0,
            'final_price' => $request->final_price,
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Quotation created successfully',
            'redirect' => route('quotations.view.show', $quotation->id),
        ]);
    }

    public function generatePdf(Quotation $quotation)
    {
        $quotation->load([
            'lead.customer',
            'lead.quoteRequest',
            'lead.quoteMaster',
        ]);

        // File name
        $fileName = 'quotation_'.$quotation->quotation_no.'.pdf';
        $path = 'quotations/'.$fileName;
        // dd($quotation->toArray());
        // Generate PDF
        $pdf = Pdf::loadView(
            'pdf.quotation',
            ['data' => $quotation]
        )->setPaper('A4', 'portrait');

        Storage::disk('public')->put($path, $pdf->output());

        // Save path
        $quotation->pdf_path = $path;
        $quotation->save();

        return response()->json([
            'status' => true,
            'message' => 'PDF generated successfully',
            'pdf_url' => asset('storage/'.$path),
        ]);
    }

    public function sendEmail(Quotation $quotation)
    {
        if (! $quotation->pdf_path) {
            return response()->json([
                'status' => false,
                'message' => 'Generate PDF before sending email',
            ], 422);
        }

        $lead = $quotation->lead;
        $customer = $lead->customer;

        $recentProjects = Project::where('status', 'complete')
            ->latest()
            ->limit(5)
            ->get();
        Mail::send(
            'emails.quotation',
            compact('quotation', 'lead', 'customer', 'recentProjects'),
            function ($mail) use ($quotation, $customer) {
                $mail->to($customer->email, $customer->name)
                    ->subject('Your Solar Quotation - '.config('app.name'))
                    ->attach(
                        storage_path('app/public/'.$quotation->pdf_path),
                        ['as' => 'Solar-Quotation.pdf']
                    );
            }
        );

        $quotation->update([
            'sent_at' => now(),
            'sent_by' => Auth::id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Quotation email sent successfully',
        ]);
    }
}
