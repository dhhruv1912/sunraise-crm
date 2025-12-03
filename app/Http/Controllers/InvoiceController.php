<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\QuoteMaster;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // dompdf facade
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    // list page
    public function index()
    {
        return view('page.quotations.invoices.list');
    }

    // AJAX list for datatable/pagination
    public function ajaxList(Request $request)
    {
        $perPage = (int)$request->get('per_page', 20);
        $q = Invoice::with(['project','lead','creator']);

        if ($search = $request->get('search')) {
            $q->where(function($qq) use ($search) {
                $qq->where('invoice_no', 'like', "%{$search}%")
                   ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) $q->where('status', $status);
        if ($project = $request->get('project_id')) $q->where('project_id', $project);
        if ($from = $request->get('from')) $q->whereDate('invoice_date', '>=', $from);
        if ($to = $request->get('to')) $q->whereDate('invoice_date', '<=', $to);

        $data = $q->orderBy('id','desc')->paginate($perPage);

        return response()->json($data);
    }

    // create form
    public function create()
    {
        $quoteMasters = QuoteMaster::orderBy('kw')->get();
        return view('page.quotations.invoices.form', compact('quoteMasters'));
    }

    // store invoice
    public function store(Request $request)
    {
        $rules = [
            'project_id'   => 'required|exists:projects,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'errors'=>$validator->errors(),'req'=>$request->all()], 422);
        }

        DB::transaction(function() use($request,&$invoice) {
            $invoice_no = $request->invoice_no ?: 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
            $invoice = Invoice::create([
                'project_id' => $request->project_id ?: null,
                'customer_id' => $request->customer_id ?: null,
                'invoice_no' => $invoice_no,
                'status' => $request->status ?: 'draft',
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'currency' => $request->currency ?: 'INR',
                'notes' => $request->notes ?: null,
                'created_by' => auth()->id(),
            ]);

            $calc = $this->calculateItemsAndTotals($request->items);
            foreach ($calc['items'] as $it) {
                InvoiceItem::create([
                    'invoice_id'        => $invoice->id,
                    'quote_master_id'   => $it['quote_master_id'],
                    'description'       => $it['description'],
                    'unit_price'        => $it['unit_price'],
                    'quantity'          => $it['quantity'],
                    'tax'               => $it['tax'],
                    'line_total'        => $it['line_total'],
                ]);
            }

            $invoice->update([
                'sub_total' => $calc['sub_total'],
                'tax_total' => $calc['tax_total'],
                'discount'  => $request->discount ?? 0,
                'total'     => ($calc['sub_total'] + $calc['tax_total'] - ($request->discount ?? 0)),
                'balance'   => ($calc['sub_total'] + $calc['tax_total'] - ($request->discount ?? 0)),
            ]);
        });

        return redirect()->route('invoices.index')->with('success','Invoice created.');
    }

    // show invoice
    public function show($id)
    {
        $invoice = Invoice::with(['items','payments','project','lead'])->findOrFail($id);
        return view('page.quotations.invoices.show', compact('invoice'));
    }

    // edit
    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $quoteMasters = QuoteMaster::orderBy('kw')->get();
        return view('page.quotations.invoices.form', compact('invoice','quoteMasters'));
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


    // update
    public function update(Request $request, $id)
    {
        // Similar validation as store; for brevity update minimal fields and items
        $rules = [
            'project_id' => 'required|exists:projects,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'errors'=>$validator->errors(),'req'=>$request->all()], 422);
        }
        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->only(['invoice_date','due_date','notes','status','currency']));
        // handle items: simplistic approach — delete all and re-add
        if ($request->has('items')) {
            InvoiceItem::where('invoice_id',$invoice->id)->delete();
            $calc = $this->calculateItemsAndTotals($request->items);
            foreach ($calc['items'] as $it) {
                InvoiceItem::create([
                    'invoice_id'        => $invoice->id,
                    'quote_master_id'   => $it['quote_master_id'],
                    'description'       => $it['description'],
                    'unit_price'        => $it['unit_price'],
                    'quantity'          => $it['quantity'],
                    'tax'               => $it['tax'],
                    'line_total'        => $it['line_total'],
                ]);
            }
            $invoice->update([
                'sub_total' => $calc['sub_total'],
                'tax_total' => $calc['tax_total'],
                'discount'  => $request->discount ?? 0,
                'total'     => ($calc['sub_total'] + $calc['tax_total'] - ($request->discount ?? 0)),
                'balance'   => ($calc['sub_total'] + $calc['tax_total'] - ($request->discount ?? 0)),
            ]);
        }
        return redirect()->route('invoices.index')->with('success','Invoice updated.');
    }

    // delete (AJAX)
    public function destroy(Request $request, $id)
    {
        Invoice::where('id',$id)->delete();
        return response()->json(['status'=>true,'message'=>'Deleted']);
    }

    // record payment
    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'amount'=>'required|numeric|min:0.01',
            'method'=>'nullable|string',
            'reference'=>'nullable|string',
            'paid_at'=>'nullable|date',
        ]);

        $invoice = Invoice::findOrFail($id);
        $p = InvoicePayment::create([
            'invoice_id'=>$invoice->id,
            'amount'=>$request->amount,
            'method'=>$request->method,
            'reference'=>$request->reference,
            'paid_at'=>$request->paid_at ?: now(),
            'received_by'=>auth()->id()
        ]);

        // update invoice totals
        $invoice->paid_amount = $invoice->paid_amount + $p->amount;
        $invoice->balance = max(0, $invoice->total - $invoice->paid_amount);
        $invoice->status = ($invoice->paid_amount >= $invoice->total) ? 'paid' : 'partial';
        $invoice->save();

        return response()->json(['status'=>true,'message'=>'Payment recorded','data'=>$p]);
    }

    // generate PDF and optionally store (returns download)
    public function generatePdf($id, Request $request)
    {
        $invoice = Invoice::with(['items','project','lead'])->findOrFail($id);

        $pdf = Pdf::loadView('emails.invoice_sent_pdf', compact('invoice'));
        $filename = "invoice_{$invoice->invoice_no}.pdf";

        if ($request->get('save') == '1') {
            $path = "invoices/{$filename}";
            \Storage::disk('public')->put($path, $pdf->output());
            $invoice->pdf_path = $path;
            $invoice->save();
            return response()->json(['status'=>true,'path'=>asset("storage/{$path}")]);
        }

        return $pdf->download($filename);
    }

    // send email with attached PDF (sync)
    public function sendEmail($id)
    {
        $invoice = Invoice::with(['items','project','lead'])->findOrFail($id);
        dd($invoice);
        if (!$invoice->lead && !$invoice->project) {
            // try customer email via customer_id or project/lead relation
        }

        $pdf = Pdf::loadView('emails.invoice_sent_pdf', compact('invoice'));
        $filename = "invoice_{$invoice->invoice_no}.pdf";
        $pdfData = $pdf->output();

        // store temporarily
        $tmpPath = sys_get_temp_dir() . '/' . $filename;
        file_put_contents($tmpPath, $pdfData);

        // use Mail::send
        Mail::send('emails.invoice_sent', ['invoice' => $invoice], function ($m) use ($invoice, $tmpPath, $filename) {
            $to = $invoice->project->customer_email ?? ($invoice->lead->email ?? null);
            if (!$to) {
                // no recipient
                return;
            }
            $m->to($to)->subject("Invoice {$invoice->invoice_no} from ".config('app.name'));
            $m->attach($tmpPath, ['as'=>$filename,'mime'=>'application/pdf']);
        });

        // cleanup
        @unlink($tmpPath);

        $invoice->sent_at = now();
        $invoice->sent_by = auth()->id();
        $invoice->status = $invoice->status === 'draft' ? 'sent' : $invoice->status;
        $invoice->save();

        return redirect()->back()->with('success','Mail queued/sent.');
    }

    // export CSV
    public function export()
    {
        $fileName = "invoices_export_".date('Ymd').".csv";
        $rows = Invoice::orderBy('id','desc')->get()->map(function($i){
            return [
                'id'=>$i->id,
                'invoice_no'=>$i->invoice_no,
                'invoice_date'=>$i->invoice_date,
                'due_date'=>$i->due_date,
                'total'=>$i->total,
                'paid'=>$i->paid_amount,
                'balance'=>$i->balance,
                'status'=>$i->status
            ];
        })->toArray();

        $response = new StreamedResponse(function() use ($rows) {
            $h = fopen('php://output','w');
            if (count($rows)) fputcsv($h,array_keys($rows[0]));
            foreach($rows as $r) fputcsv($h,$r);
            fclose($h);
        });

        $response->headers->set('Content-Type','text/csv');
        $response->headers->set('Content-Disposition',"attachment; filename={$fileName}");
        return $response;
    }

    protected function calculateItemsAndTotals($items)
{
    $sub = 0;
    $tax = 0;
    $finalItems = [];

    foreach ($items as $it) {
        $unit = (float)$it['unit_price'];
        $qty  = (int)$it['quantity'];
        $taxAmount = isset($it['tax']) ? (float)$it['tax'] : 0;

        $line = $unit * $qty;
        $lineTotal = $line + $taxAmount;

        $sub += $line;
        $tax += $taxAmount;

        $finalItems[] = [
            'quote_master_id' => $it['quote_master_id'] ?? null,
            'description'     => $it['description'],
            'unit_price'      => $unit,
            'quantity'        => $qty,
            'tax'             => $taxAmount,
            'line_total'      => $lineTotal
        ];
    }

    return [
        'sub_total' => $sub,
        'tax_total' => $tax,
        'items'     => $finalItems
    ];
}
}
