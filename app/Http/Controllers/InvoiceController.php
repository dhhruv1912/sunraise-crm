<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\CustomerActivity;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Project;
use App\Services\InvoiceNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

// use App\Models\CustomerActivity;

class InvoiceController extends Controller
{
    /* ================= VIEW ================= */

    public function index()
    {
        return view('page.invoices.index');
    }

    public function view(Invoice $invoice)
    {
        $invoice->load(['customer', 'project', 'items']);

        return view('page.invoices.view', compact('invoice'));
    }

    public function create()
    {
        return view('page.invoices.create');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['items', 'customer']);

        return view('page.invoices.edit', compact('invoice'));
    }

    /* ================= AJAX ================= */

    public function ajaxList(Request $request)
    {
        $page    = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 10);

        $q = Invoice::with(['customer', 'project'])
            ->latest();

        /* ---------- FILTERS ---------- */

        if ($request->status) {
            $q->where('status', $request->status);
        }

        if ($request->search) {
            $s = $request->search;
            $q->where(function ($qq) use ($s) {
                $qq->where('invoice_no', 'like', "%{$s}%")
                ->orWhereHas('customer', function ($c) use ($s) {
                        $c->where('name', 'like', "%{$s}%")
                        ->orWhere('mobile', 'like', "%{$s}%");
                });
            });
        }

        /* ---------- PAGINATION ---------- */

        $total = (clone $q)->count();
        $rows = $q
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($inv) {

                $statusMap = [
                    'draft'   => ['Draft',   'bg-warning-subtle text-warning'],
                    'sent'    => ['Sent',    'bg-primary-subtle text-primary'],
                    'partial' => ['Partial', 'bg-info-subtle text-info'],
                    'paid'    => ['Paid',    'bg-success-subtle text-success'],
                    'overdue' => ['Overdue', 'bg-danger-subtle text-danger'],
                ];

                return [
                    'id'           => $inv->id,
                    'invoice_no'   => $inv->invoice_no,
                    'invoice_date' => optional($inv->invoice_date)?->format('d M Y'),
                    'customer'     => optional($inv->customer)->name,
                    'mobile'       => optional($inv->customer)->mobile,
                    'project_code' => optional($inv->project)->project_code,
                    'total'        => $inv->total,
                    'status_label' => $statusMap[$inv->status][0] ?? ucfirst($inv->status),
                    'status_class' => $statusMap[$inv->status][1] ?? 'bg-secondary',
                ];
            });

        return response()->json([
            'data' => $rows,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => (int) ceil($total / $perPage),
            ],
            'canEdit' => Gate::allows('billing.edit')
        ]);
    }

    public function ajaxWidgets()
    {
        return view('page.invoices.widgets', [
            'total'   => Invoice::count(),
            'paid'    => Invoice::where('status', 'paid')->count(),
            'partial' => Invoice::where('status', 'partial')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'value'   => Invoice::sum('total'),
        ]);
    }


    public function ajaxPayments(Invoice $invoice)
    {
        $project = $invoice->project;

        // EMI dates already paid via invoice payments
        $paidEmiDates = $invoice->payments()
            ->pluck('meta')
            ->filter()
            ->pluck('emi_date')
            ->filter()
            ->toArray();

        $emis = collect($project->emi ?? [])
            ->map(function ($amount, $date) use ($paidEmiDates) {
                return [
                    'date'   => $date,
                    'amount' => (float) $amount,
                    'paid'   => in_array($date, $paidEmiDates),
                    'type'   => $amount < 0 ? 'adjustment' : 'emi',
                ];
            })
            ->values();

        return response()->json([
            'total'     => $invoice->total,
            'paid'      => $invoice->paid_amount,
            'remaining' => $invoice->balance,

            'emis'      => $emis,

            'payments'  => $invoice->payments()
                ->latest()
                ->get()
                ->map(fn ($p) => [
                    'amount'   => $p->amount,
                    'method'   => $p->method,
                    'ref'      => $p->reference,
                    'date'     => optional($p->paid_at)->format('d M Y'),
                    'emi_date' => $p->meta['emi_date'] ?? null,
                    'by'       => $p->receiver?->fname ?? '—',
                ]),
        ]);
    }

    public function store(Request $request)
    {
        if ($request->project_id) {
            $exists = Invoice::where('project_id', $request->project_id)->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Invoice already exists for this project'
                ], 422);
            }
        }
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'project_id' => 'nullable|exists:projects,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',

            'is_recurring' => 'boolean',
            'recurring_type' => 'nullable|in:daily,weekly,monthly,yearly,custom',
            'recurring_interval' => 'nullable|integer|min:1',
            'recurring_end_at' => 'nullable|date',

            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);
        
        $invoice = Invoice::create([
            'invoice_no' => InvoiceNumberService::generate(),
            'customer_id' => $data['customer_id'],
            'project_id' => $data['project_id'] ?? null,
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'] ?? null,
            'discount' => $data['discount'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'status' => 'draft',
            'currency' => 'INR',
            'is_recurring' => $data['is_recurring'] ?? false,
            'recurring_type' => $data['recurring_type'] ?? null,
            'recurring_interval' => $data['recurring_interval'] ?? null,
            'recurring_end_at' => $data['recurring_end_at'] ?? null,
            'created_by' => Auth::id(),
        ]);

        foreach ($data['items'] as $row) {
            $invoice->items()->create([
                'quote_master_id' => $row['quote_master_id'] ?? null,
                'description'     => $row['description'],
                'unit_price'      => $row['unit_price'],
                'quantity'        => $row['quantity'],
                'tax'             => $row['tax'] ?? 0,
            ]);
        }

        $invoice->recalculateTotals();

        return response()->json([
            'message' => 'Invoice draft created',
            'id' => $invoice->id,
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {

        $project = Project::findOrFail($invoice->project_id);
        $rules = [
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'status' => 'nullable|in:'.implode(',', array_keys(Invoice::STATUS_LABELS)),
            'items' => 'required|array|min:1',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        /** Recompute totals */
        $sub = 0;
        $tax = 0;

        foreach ($request->items as $it) {
            $sub += ($it['unit_price'] * $it['quantity']);
            $tax += $it['tax'] ?? 0;
        }

        $payload = $request->only([
            'invoice_date', 'due_date', 'notes', 'discount', 'status','project_id'
        ]);
        
        $payload['sub_total'] = $sub;
        $payload['tax_total'] = $tax;
        $payload['total'] = $sub + $tax - ($payload['discount'] ?? 0);
        $payload['balance'] = $payload['total'] - $invoice->paid_amount;
        
        $lastemidate   = array_key_last($project->emi);
        $lastemiamount = $project->emi[$lastemidate];
        $all_amount = 0;
        foreach ($project->emi as $date => $amount) {
            $all_amount = $all_amount + $amount;
        }
        $emi = $project->emi;
        $emi[$lastemidate] = $payload['total'] - $all_amount + $lastemiamount;
        $project->emi = $emi;
        $project->finalize_price = $payload['total'];
        
        /** Update items */
        $project->save();
        $invoice->update($payload);
        InvoiceItem::where('invoice_id', $invoice->id)->delete();

        foreach ($request->items as $it) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'quote_master_id' => @$it['quote_master_id'],
                'description' => $it['description'],
                'unit_price' => $it['unit_price'],
                'quantity' => $it['quantity'],
                'tax' => $it['tax'] ?? 0,
                'line_total' => ($it['unit_price'] * $it['quantity']) + ($it['tax'] ?? 0),
            ]);
        }

        $invoice->recalculateTotals();

        return response()->json([
            'message' => 'Invoice updated successfully'
        ]);
    }

    public function storePayment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount'    => 'required|numeric|not_in:0',
            'method'    => 'required|string',
            'reference' => 'nullable|string',
            'paid_at'   => 'required|date',
            'emi_date'  => 'nullable|date',
        ]);

        if ($data['amount'] > $invoice->balance) {
            return response()->json([
                'message' => 'Payment exceeds remaining balance'
            ], 422);
        }

        DB::transaction(function () use ($invoice, $data) {

            // 1️⃣ Save payment
            $invoice->payments()->create([
                'amount'       => $data['amount'],
                'method'       => $data['method'],
                'reference'    => $data['reference'],
                'paid_at'      => $data['paid_at'],
                'received_by'  => auth()->id(),
                'meta'         => $data['emi_date']
                    ? ['emi_date' => $data['emi_date']]
                    : null,
            ]);

            // 3️⃣ Update invoice totals
            $paid = $invoice->payments()->sum('amount');

            $invoice->update([
                'paid_amount' => $paid,
                'balance'     => max($invoice->total - $paid, 0),
                'status'      => $paid >= $invoice->total ? 'paid' : 'partial',
            ]);
        });

        return response()->json([
            'message' => 'Payment added successfully'
        ]);
    }

    public function generatePdf(Invoice $invoice)
    {
        return DB::transaction(function () use ($invoice) {

            // delete old pdf if exists
            if ($invoice->pdf_path && Storage::disk('public')->exists($invoice->pdf_path)) {
                Storage::disk('public')->delete($invoice->pdf_path);
            }

            // generate pdf
            $pdf = Pdf::loadView('pdf.invoice', [
                'invoice' => $invoice->load(['items','customer','project'])
            ]);

            $file = 'invoice_' . $invoice->invoice_no . '.pdf';
            $path = 'invoices/' . $file;

            Storage::disk('public')->put($path, $pdf->output());

            $invoice->update([
                'pdf_path' => $path
            ]);

            return response()->json([
                'message' => 'Invoice PDF generated successfully',
                'pdf_url' => Storage::disk('public')->url($path)
            ]);
        });
    }

    public function sendEmail(Invoice $invoice)
    {
        if (! $invoice->pdf_path) {
            return response()->json([
                'message' => 'Generate PDF first',
            ], 422);
        }

        if ($invoice->sent_at) {
            return response()->json([
                'message' => 'Invoice already sent',
            ], 422);
        }

        $invoice->load('customer');

        Mail::to($invoice->customer->email)
            ->send(new InvoiceMail($invoice));

        $invoice->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_by' => Auth::id(),
        ]);


        return response()->json([
            'message' => 'Invoice emailed successfully',
        ]);
    }

    public function ajaxQuoteMaster(Project $project)
    {
        $project->load('quoteMaster');

        if (!$project->quoteMaster) {
            return response()->json([
                'message' => 'No quote master linked to project'
            ], 422);
        }

        $qm = $project->quoteMaster;

        return response()->json([
            'description' => "{$qm->sku} | {$qm->module} | {$qm->kw} kW",
            'unit_price'  => $qm->payable ?? $qm->value,
            'quantity'    => 1,
            'tax'         => $qm->taxes ?? 0,
            'quote_master_id' => $qm->id,
        ]);
    }

    public function ajaxUpcomingPayments($invoice_id = null)
    {
        $today = Carbon::today()->toDateString();

        $rows = [];

        $projects = Project::whereNotNull('emi')
            ->when($invoice_id, function ($q) use ($invoice_id) {
                $q->whereHas('invoices', fn ($i) => $i->where('id', $invoice_id));
            })
            ->with(['customer', 'invoices.payments'])
            ->get();

        $projects->each(function ($project) use (&$rows, $today, $invoice_id) {

            // EMI dates already paid via invoice payments
            $paidEmis = $project->invoices
                ? $project->invoices->payments
                    ->pluck('meta')
                    ->filter()
                    ->pluck('emi_date')
                    ->filter()
                    ->toArray()
                : [];

            foreach (($project->emi ?? []) as $date => $amount) {

                // skip past dates
                if ($date < $today) continue;

                // skip already paid EMIs
                if (in_array($date, $paidEmis, true)) continue;

                // skip zero amounts
                if ((float) $amount === 0.0) continue;
                if ($invoice_id) {
                    $rows[] = [
                        'date'     => $date,
                        'amount'   => (float) $amount,
                        'customer' => $project->customer?->name,
                        'invoice'  => $project->invoices?->id,
                        'days'     => Carbon::parse($date)->diffInDays($today, false),

                    ];
                }else{
                    $rows[] = [
                        'date'     => $date,
                        'amount'   => (float) $amount,
                        'project'  => $project->project_code,
                        'customer' => $project->customer?->name,
                        'invoice'  => $project->invoices?->id,
                        'days'     => Carbon::parse($date)->diffInDays($today, false),
                    ];
                }
            }
        });

        // sort by nearest date
        usort($rows, function ($a, $b) {
            return $a['days'] <=> $b['days'];
        });

        return view('page.invoices.widgets_upcoming', [
            'rows' => collect($rows),
            'invoice_id' => $invoice_id
        ]);
    }
}
