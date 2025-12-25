<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Batch;
use App\Models\Panel;
use App\Models\PanelMovement;
use App\Models\PanelAttachment;
use App\Models\ItemCategory;
use App\Models\Item;
use App\Models\Warehouse;
use Illuminate\Container\Attributes\Auth;
class BatchController extends Controller
{
    /* LIST ------------------------------------------------------ */
    public function index()
    {
        $batches = Batch::with(['item', 'warehouse'])
            ->withCount('panels')
            ->latest()
            ->paginate(20);

        return view('page.batches.index'
            // compact('batches')
        );
    }

    public function list(Request $request)
    {
        $q = Batch::with(['item', 'warehouse'])->withCount('panels')
            ->orderBy('id', 'desc');

        if ($request->search) {
            $q->where('batch_no', 'LIKE', "%{$request->search}%")
            ->orWhere('invoice_number', 'LIKE', "%{$request->search}%");
        }

        $batches = $q->paginate(10);

        // append total panels
        $batches->getCollection()->transform(function ($b) {
            $b->total_panels = $b->panels()->count();
            return $b;
        });

        return response()->json([
            'success' => true,
            'data' => $batches
        ]);
    }

    /* CREATE ---------------------------------------------------- */
    public function create()
    {
        $itemCategories = ItemCategory::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('page.batches.create', compact('itemCategories', 'items', 'warehouses'));
    }

    /* REVIEW (POST) --------------------------------------------- */
    public function review(Request $request)
    {
        $request->validate([
            'item_id'       => 'required',
            'warehouse_id'  => 'required',
            'invoice_file'  => 'required|file',
            'ocr_text'      => 'required',
            'serials'       => 'required|array|min:1'
        ]);
        $tempPath = $request->file('invoice_file')->store('temp_invoices', 'public');
        return response()->json([
            'redirected' => true,
            'url' => route('batches.reviewget', [
                'tempInvoicePath' => $tempPath,
                'item_id'         => $request->item_id,
                'warehouse_id'    => $request->warehouse_id,
                'ocr_text'        => $request->ocr_text,
                'serials'         => $request->serials,
                'serialNumbers'   => $request->serialNumbers,
                'serialDescs'     => $request->serialDescs,
                'fields'          => $request->fields,
            ])
        ]);

        // Move invoice file to temp storage
        return view('page.batches.review', [
            'tempInvoicePath' => $tempPath,
            'item_id'         => $request->item_id,
            'warehouse_id'    => $request->warehouse_id,
            'ocr_text'        => $request->ocr_text,
            'serials'         => $request->serials,
            'serialNumbers'   => $request->serialNumbers,
            'serialDescs'     => $request->serialDescs,
            'fields'          => $request->fields
        ]);
    }
    public function reviewGet(Request $request)
    {
        return view('page.batches.review', [
            'tempInvoicePath' => $request->tempInvoicePath,
            'item_id'         => $request->item_id,
            'warehouse_id'    => $request->warehouse_id,
            'ocr_text'        => $request->ocr_text,
            'serials'         => $request->serials,
            'serialNumbers'   => $request->serialNumbers,
            'serialDescs'     => $request->serialDescs,
            'fields'          => $request->fields,
            'request'         => $request
        ]);
    }

    /* STORE ----------------------------------------------------- */
    public function store(Request $request)
    {   dump($request->all());
        $request->validate([
            'item_id'       => 'required',
            'warehouse_id'  => 'required',
            'invoice_file'  => 'required|string',
            'serials'       => 'required|array|min:1',
            'fields'        => 'required|array'
        ]);

        /* MOVE FILE TO FINAL STORAGE */
        $newPath = str_replace('temp_invoices/', 'invoices/', $request->invoice_file);
        Storage::disk('public')->move($request->invoice_file, $newPath);
        dump($newPath);
        /* CREATE ATTACHMENT */
        $attachment = PanelAttachment::create([
            'path' => $newPath,
            'type' => 'panel',
            'ocr_text'  => $request->fields['ocr_text'] ?? ''
        ]);

        /* CREATE BATCH */
        $batch = Batch::create([
            'batch_no'            => "BATCH-" . strtoupper(uniqid()),
            'item_id'             => $request->item_id,
            'warehouse_id'        => $request->warehouse_id,
            'invoice_number'      => $request->fields['invoice_number'] ?? null,
            'invoice_date'        => $request->fields['invoice_date'] ?? null,
            'material_description'=> $request->fields['material_description'] ?? null,
            'model_no'            => $request->fields['model_no'] ?? null,
            'net_weight'          => $request->fields['net_weight'] ?? null,
            'gross_weight'        => $request->fields['gross_weight'] ?? null,
            'dimensions'          => $request->fields['dimensions'] ?? null,
            'attachment_id'       => $attachment->id,
        ]);

        /* CREATE PANELS + INITIAL MOVEMENT */
        foreach ($request->serials['numbers'] as $serial) {
            $panel = Panel::create([
                'batch_id'     => $batch->id,
                'item_id'      => $batch->item_id,
                'warehouse_id' => $batch->warehouse_id,
                'serial_number'=> strtoupper($serial),
                'status'       => 'in_stock'
            ]);

            PanelMovement::create([
                'panel_id'    => $panel->id,
                'batch_id'    => 1,
                'action'      => 'received',
                'happened_at' => now(),
                'note'        => 'Received from invoice'
            ]);
        }

        return redirect()->route('batches.show', $batch->id)
            ->with('success', 'Batch created successfully!');
    }

    /* SHOW DETAIL PAGE ------------------------------------------- */
    public function show($id)
    {
        $batch = Batch::with(['item', 'warehouse', 'attachments'])->findOrFail($id);

        $panels = Panel::where('batch_id', $id)->get();

        // $movementSummary = PanelMovement::selectRaw('action, COUNT(*) as total')
        //     ->where('batch_id', $id)
        //     ->groupBy('action')
        //     ->get();
        $movementSummary = [];

        // $timeline = PanelMovement::where('batch_id', $id)
        //     ->orderBy('happened_at')
        //     ->get();
        $timeline = PanelMovement::orderBy('happened_at')
            ->get();

        $isAdmin = Auth::user()->role == 1;

        return view('page.batches.show', compact(
            'batch', 
            'panels', 
            'movementSummary', 
            'timeline', 
            'isAdmin'
        ));
    }
}
