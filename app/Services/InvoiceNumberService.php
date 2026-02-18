<?php

namespace App\Services;

use App\Models\Invoice;

class InvoiceNumberService
{
    public static function generate(): string
    {
        $prefix = 'INV-' . now()->format('Ym');
        $last   = Invoice::where('invoice_no', 'like', "$prefix%")
                    ->orderByDesc('id')
                    ->value('invoice_no');

        $next = 1;

        if ($last) {
            $parts = explode('-', $last);
            $next  = intval(end($parts)) + 1;
        }

        return sprintf('%s-%04d', $prefix, $next);
    }
}
