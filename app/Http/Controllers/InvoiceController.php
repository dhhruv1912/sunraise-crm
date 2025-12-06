<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Project;
use App\Models\QuoteMaster;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /* ---------------------------------------------------------
     | LIST PAGE
     --------------------------------------------------------- */
    public function index()
    {
        return view('page.quotations.invoices.list', [
            'statuses' => Invoice::STATUS_LABELS,
        ]);
    }

    /* ---------------------------------------------------------
     | AJAX LIST
     --------------------------------------------------------- */
    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);

        $q = Invoice::query()->with(['customer', 'project']);

        if ($search = trim($request->search)) {
            $q->where(function ($x) use ($search) {
                $x->where('invoice_no', 'like', "%$search%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$search%"))
                    ->orWhereHas('project', fn ($p) => $p->where('project_code', 'like', "%$search%"));
            });
        }

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $q->whereDate('invoice_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('invoice_date', '<=', $request->to);
        }

        $data = $q->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($data);
    }

    /* ---------------------------------------------------------
     | VIEW MODEL
     --------------------------------------------------------- */
    public function show($id)
    {
        return view('page.quotations.invoices.view_wrapper',compact('id'));
    }
    public function viewJson($id)
    {
        $invoice = Invoice::with([
            'items',
            'payments',
            'customer',
            'project.lead',
            'project.documents',
            'creator',
            'sender',
        ])->findOrFail($id);

        // Format frontend-ready fields
        $invoice->status_label = ucfirst($invoice->status);
        $invoice->customer_name = $invoice->customer->name ?? null;
        $invoice->project_code = $invoice->project->project_code ?? null;

        // Attach PDF URL if exists
        $invoice->pdf_url = $invoice->pdf_path
            ? asset('storage/'.$invoice->pdf_path)
            : null;

        return response()->json([
            'status' => true,
            'data' => $invoice,
        ]);
    }

    /* ---------------------------------------------------------
     | CREATE FORM
     --------------------------------------------------------- */
    public function create()
    {
        return view('page.quotations.invoices.form', [
            'statuses' => Invoice::STATUS_LABELS,
            'quoteMasters' => QuoteMaster::orderBy('kw')->get(),
        ]);
    }

    /* ---------------------------------------------------------
     | STORE
     --------------------------------------------------------- */
    public function store(Request $request)
    {
        $rules = [
            'project_id' => 'nullable|exists:projects,id',
            'invoice_no' => 'nullable|string|unique:invoices,invoice_no',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'is_recurring' => 'nullable|boolean',
            'recurring_type' => 'nullable|string|in:daily,weekly,monthly,yearly,custom',
            'recurring_interval' => 'nullable|integer',
            'recurring_end_at' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.sku' => 'nullable|string',
            'items.*.description' => 'required|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.tax' => 'nullable|numeric',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return back()->withErrors($v)->withInput();
        }

        $payload = $request->only([
            'project_id', 'invoice_no', 'invoice_date', 'due_date',
            'notes', 'discount', 'is_recurring', 'recurring_type',
            'recurring_interval', 'recurring_end_at',
        ]);

        /** Auto-assign invoice_no */
        if (empty($payload['invoice_no'])) {
            $payload['invoice_no'] = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        }

        /** Auto-fill customer from project */
        if ($request->project_id) {
            $project = Project::with('customer')->find($request->project_id);
            if ($project && $project->customer_id) {
                $payload['customer_id'] = $project->customer_id;
            }
        }

        $payload['created_by'] = Auth::id();

        /** Compute subtotal, tax_total, total */
        $sub = 0;
        $tax = 0;

        foreach ($request->items as $it) {
            $line = $it['unit_price'] * $it['quantity'];
            $sub += $line;
            $tax += ($it['tax'] ?? 0);
        }

        $payload['sub_total'] = $sub;
        $payload['tax_total'] = $tax;
        $payload['total'] = $sub + $tax - ($payload['discount'] ?? 0);
        $payload['balance'] = $payload['total'];

        $invoice = Invoice::create($payload);
        // dd($request->all());
        /** Insert items */
        foreach ($request->items as $it) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'quote_master_id' => $this->resolveSku(@$it['sku']),
                'description' => $it['description'],
                'unit_price' => $it['unit_price'],
                'quantity' => $it['quantity'],
                'tax' => $it['tax'] ?? 0,
                'line_total' => ($it['unit_price'] * $it['quantity']) + ($it['tax'] ?? 0),
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully');
    }

    /* ---------------------------------------------------------
     | EDIT
     --------------------------------------------------------- */
    public function edit($id)
    {
        $invoice = Invoice::with(['items', 'customer', 'project'])->findOrFail($id);

        return view('page.quotations.invoices.form', [
            'invoice' => $invoice,
            'quoteMasters' => QuoteMaster::orderBy('kw')->get(),
            'statuses' => Invoice::STATUS_LABELS,
        ]);
    }

    public function sku($id)
    {
        $m = QuoteMaster::findOrFail($id);

        return response()->json([
            'description' => "{$m->module} {$m->KW}KW ({$m->module_count} panels)",
            'unit_price'  => $m->value,
            'tax'         => $m->taxes,
            'quantity'    => 1
        ]);
    }

    /* ---------------------------------------------------------
     | UPDATE
     --------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

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
            'invoice_date', 'due_date', 'notes', 'discount', 'status',
        ]);

        $payload['sub_total'] = $sub;
        $payload['tax_total'] = $tax;
        $payload['total'] = $sub + $tax - ($payload['discount'] ?? 0);
        $payload['balance'] = $payload['total'] - $invoice->paid_amount;

        $invoice->update($payload);

        /** Update items */
        InvoiceItem::where('invoice_id', $invoice->id)->delete();

        foreach ($request->items as $it) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'quote_master_id' => $this->resolveSku($it['sku']),
                'description' => $it['description'],
                'unit_price' => $it['unit_price'],
                'quantity' => $it['quantity'],
                'tax' => $it['tax'] ?? 0,
                'line_total' => ($it['unit_price'] * $it['quantity']) + ($it['tax'] ?? 0),
            ]);
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully');
    }

    /* ---------------------------------------------------------
     | VIEW (MODAL JSON)
     --------------------------------------------------------- */
    // public function viewJson($id)
    // {
    //     return Invoice::with(['items', 'customer', 'project', 'payments'])->findOrFail($id);
    // }

    /* ---------------------------------------------------------
     | RECORD PAYMENT (AJAX)
     --------------------------------------------------------- */
    public function recordPayment(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'nullable|string',
            'reference' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        InvoicePayment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'paid_at' => $request->paid_at,
            'received_by' => Auth::id(),
        ]);

        $invoice->paid_amount += $request->amount;
        $invoice->balance = $invoice->total - $invoice->paid_amount;

        if ($invoice->balance <= 0) {
            $invoice->status = 'paid';
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'partial';
        }

        $invoice->save();

        return response()->json(['status' => true]);
    }

    /* ---------------------------------------------------------
     | SKU AUTOFILL
     --------------------------------------------------------- */
    private function resolveSku($sku)
    {
        if (! $sku) {
            return null;
        }

        $row = QuoteMaster::where('sku', $sku)->first();

        return $row ? $row->id : null;
    }

    /* ---------------------------------------------------------
     | DOWNLOAD PDF
     --------------------------------------------------------- */
    public function pdf($id)
    {
        $invoice = Invoice::with(['items', 'customer', 'project'])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'));

        return $pdf->download("invoice_{$invoice->invoice_no}.pdf");
    }

    /* ---------------------------------------------------------
     | DELETE
     --------------------------------------------------------- */
    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();

        return response()->json(['status' => true]);
    }
}
