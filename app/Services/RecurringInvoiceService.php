<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class RecurringInvoiceService
{
    public static function run(): int
    {
        $count = 0;

        $templates = Invoice::where('is_recurring', 1)
            ->whereNotNull('recurring_next_at')
            ->whereDate('recurring_next_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('recurring_end_at')
                  ->orWhereDate('recurring_end_at', '>=', now());
            })
            ->get();

        foreach ($templates as $template) {
            DB::transaction(function () use ($template, &$count) {

                // Clone invoice
                $new = Invoice::create([
                    'invoice_no'   => InvoiceNumberService::generate(),
                    'customer_id'  => $template->customer_id,
                    'project_id'   => $template->project_id,
                    'invoice_date' => now()->toDateString(),
                    'due_date'     => $template->due_date,
                    'discount'     => $template->discount,
                    'notes'        => $template->notes,
                    'status'       => 'draft',
                    'currency'     => $template->currency,
                    'created_by'   => $template->created_by,
                ]);

                // Clone items
                foreach ($template->items as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $new->id,
                        'description'=> $item->description,
                        'unit_price' => $item->unit_price,
                        'quantity'   => $item->quantity,
                        'tax'        => $item->tax,
                    ]);
                }

                $new->recalculateTotals();

                // Update next run
                $template->update([
                    'recurring_next_at' => self::nextDate($template)
                ]);

                $count++;
            });
        }

        return $count;
    }

    protected static function nextDate(Invoice $invoice): ?string
    {
        $interval = max($invoice->recurring_interval ?? 1, 1);

        return match ($invoice->recurring_type) {
            'daily'   => now()->addDays($interval),
            'weekly'  => now()->addWeeks($interval),
            'monthly' => now()->addMonths($interval),
            'yearly'  => now()->addYears($interval),
            'custom'  => now()->addDays($interval),
            default   => null,
        };
    }
}
