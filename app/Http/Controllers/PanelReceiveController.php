<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Batch;
use App\Models\Panel;
use App\Models\PanelAttachment;
use App\Models\PanelMovement;
use App\Models\Warehouse;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PanelReceiveController extends Controller
{
    /**
     * STEP 1 — Upload Invoice Page
     */
    public function uploadPage()
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('page.panels.receive.upload', compact('items', 'warehouses'));
    }

    /**
     * STEP 1 — Handle file upload
     * Store invoice, extract raw text (OCR step later), show preview page.
     */
    public function uploadInvoice(Request $request)
    {
        $request->validate([
            'item_id'       => 'required|integer',
            'warehouse_id'  => 'required|integer',
            'invoice_file'  => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);

        // Store original file
        $path = $request->file('invoice_file')->store('panel_invoices', 'public');

        $attachment = PanelAttachment::create([
            'batch_id'          => null,
            'panel_id'          => null,
            'path'              => $path,
            'type'              => 'invoice',
            'original_filename' => $request->file('invoice_file')->getClientOriginalName(),
            'ocr_text'          => null,       // to be filled later
            'structured_data'   => null,
            'uploaded_by'       => Auth::id(),
        ]);

        /**
         * OCR NOT IMPLEMENTED YET — Only set placeholder.
         * In the next iteration, we will extract text and fill:
         *  - ocr_text
         *  - structured_data
         */
        $fakeOCR = "OCR not implemented. Please enter serial numbers manually.";

        $attachment->ocr_text = $fakeOCR;
        $attachment->save();

        return redirect()->route('panels.receive.confirm', [
            'attachment_id' => $attachment->id,
            'item_id'       => $request->item_id,
            'warehouse_id'  => $request->warehouse_id,
        ]);
    }

    /**
     * STEP 2 — Confirm Page
     * User reviews extracted serials + batch info.
     */
    public function confirmPage(Request $request)
    {
        $attachment = PanelAttachment::findOrFail($request->attachment_id);

        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('page.panels.receive.confirm', [
            'attachment'  => $attachment,
            'item_id'     => $request->item_id,
            'warehouse_id'=> $request->warehouse_id,
            'items'       => $items,
            'warehouses'  => $warehouses,
        ]);
    }

    /**
     * STEP 3 — Final save: create batch + panels
     */
    public function savePanels(Request $request)
    {
        $request->validate([
            'item_id'       => 'required|integer',
            'warehouse_id'  => 'required|integer',
            'serials'       => 'required|array|min:1',
            'attachment_id' => 'required|integer',
        ]);

        return DB::transaction(function () use ($request) {

            /**
             * 1) MERGE BATCH LOGIC
             * If a batch exists with same item_id and created today → MERGE.
             */
            $existingBatch = Batch::where('item_id', $request->item_id)
                ->whereDate('created_at', now()->toDateString())
                ->first();

            if ($existingBatch) {
                $batch = $existingBatch;
            } else {
                $batch = Batch::create([
                    'batch_no'          => 'BATCH-' . now()->format('Ymd-His'),
                    'item_id'           => $request->item_id,
                    'warehouse_id'      => $request->warehouse_id,
                    'invoice_number'    => null,
                    'invoice_date'      => null,
                    'quantity_expected' => null,
                    'quantity_received' => 0,
                    'meta'              => null,
                    'status'            => 'received',
                    'created_by'        => Auth::id(),
                ]);
            }

            /**
             * 2) Create panels (ONE PER SERIAL)
             */
            foreach ($request->serials as $serial) {

                $serial = trim($serial);
                if (!$serial) continue;

                if (Panel::where('serial_number', $serial)->exists()) {
                    continue; // skip duplicates
                }

                $panel = Panel::create([
                    'batch_id'        => $batch->id,
                    'item_id'         => $batch->item_id,
                    'serial_number'   => $serial,
                    'model'           => null,
                    'batch_no_copy'   => $batch->batch_no,
                    'status'          => 'in_stock',
                    'warehouse_id'    => $request->warehouse_id,
                    'customer_id'     => null,
                ]);

                PanelMovement::create([
                    'panel_id'          => $panel->id,
                    'action'            => 'received',
                    'from_warehouse_id' => null,
                    'to_warehouse_id'   => $request->warehouse_id,
                    'customer_id'       => null,
                    'performed_by'      => Auth::id(),
                    'note'              => 'Panel received via batch import',
                ]);
            }

            /**
             * 3) Update quantity received
             */
            $batch->quantity_received = Panel::where('batch_id', $batch->id)->count();
            $batch->save();

            /**
             * 4) Attach invoice to batch
             */
            if ($request->attachment_id) {
                PanelAttachment::where('id', $request->attachment_id)
                    ->update(['batch_id' => $batch->id]);
            }

            return redirect()
                ->route('panels.index')
                ->with('success', 'Panels successfully created.');
        });
    }
}
