<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PanelAttachment;
use App\Models\Batch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PanelAttachmentController extends Controller
{
    public function download($id)
    {
        $file = PanelAttachment::findOrFail($id);

        if (!Storage::disk('public')->exists($file->path)) {
            abort(404, 'File not found');
        }

        return response()->download(storage_path("app/public/" . $file->path));
    }

    /**
     * Save OCR text or structured JSON (for review/edit)
     */
    public function updateOCR(Request $request, $id)
    {
        $attachment = PanelAttachment::findOrFail($id);

        $attachment->ocr_text = $request->ocr_text ?? $attachment->ocr_text;
        $attachment->structured_data = $request->structured_data ?? $attachment->structured_data;
        $attachment->save();

        return response()->json(['success' => true]);
    }

    /**
     * Generate a new clean PDF invoice from structured data.
     * (Template created later)
     */
    public function generatePDF(Request $request, $id)
    {
        $attachment = PanelAttachment::findOrFail($id);

        $data = $attachment->structured_data;

        // View → PDF (we will create the Blade template later)
        $pdf = PDF::loadView('pdf.generated-invoice', compact('data'));

        $newPath = "panel_invoices/generated_" . time() . ".pdf";
        Storage::disk('public')->put($newPath, $pdf->output());

        PanelAttachment::create([
            'batch_id'          => $attachment->batch_id,
            'panel_id'          => null,
            'path'              => $newPath,
            'type'              => 'generated_invoice',
            'original_filename' => 'generated_invoice.pdf',
            'uploaded_by'       => Auth::id(),
        ]);

        return response()->json(['success' => true, 'path' => $newPath]);
    }
}
