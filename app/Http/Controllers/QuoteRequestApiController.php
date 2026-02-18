<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\QuoteRequestService;

class QuoteRequestAPIController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'type'        => 'required',
            'kw'          => 'required|numeric',
            'budget'      => 'nullable|numeric',
            'notes'       => 'nullable|string',
        ]);

        $qr = QuoteRequestService::create(
            $data,
            null,
            'api'
        );

        return response()->json([
            'id' => $qr->id,
            'message' => 'Quote request submitted successfully'
        ]);
    }
}
