<?php
namespace App\Services;

use App\Models\QuoteRequest;
use App\Models\QuoteRequestHistory;

class QuoteRequestService
{
    public static function create(array $data, $userId = null, $source = 'manual')
    {
        $qr = QuoteRequest::create([
            'customer_id' => $data['customer_id'] ?? null,
            'type'        => $data['type'] ?? null,
            'module'      => $data['module'] ?? null,
            'kw'          => $data['kw'] ?? null,
            'mc'          => $data['mc'] ?? null,
            'budget'      => $data['budget'] ?? null,
            'notes'       => $data['notes'] ?? null,
            'status'      => 'new_request',
            'assigned_to' => $data['assigned_to'] ?? null,
            'created_by'  => $userId,
            'source'      => $source,
            'ip'          => request()->ip(),
            'location'    => $data['location'] ?? null,
        ]);

        QuoteRequestHistory::create([
            'quote_request_id' => $qr->id,
            'action' => 'create',
            'message' => "Quote request created via {$source}",
            'user_id' => $userId,
        ]);

        return $qr;
    }
}
