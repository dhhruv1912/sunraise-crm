<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Illuminate\Support\Str;

class GenerateRecurringInvoices extends Command
{
    protected $signature = 'invoice:generate-recurring';
    protected $description = 'Generate next occurrences of recurring invoices';

    public function handle()
    {
        $today = date('Y-m-d');

        $dueInvoices = Invoice::where('is_recurring', true)
            ->whereDate('recurring_next_at', '<=', $today)
            ->get();

        foreach ($dueInvoices as $inv) {

            if ($inv->recurring_end_at && $inv->recurring_end_at < $today) {
                continue;
            }

            $new = $inv->replicate();
            $new->invoice_no = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
            $new->status = 'draft';
            $new->paid_amount = 0;
            $new->balance = $inv->total;
            $new->sent_at = null;
            $new->sent_by = null;
            $new->is_recurring = false; // Only original is recurring
            $new->save();

            foreach ($inv->items as $it) {
                $new->items()->create($it->replicate()->toArray());
            }

            // schedule next
            $next = match ($inv->recurring_type) {
                'daily'   => date('Y-m-d', strtotime("+{$inv->recurring_interval} days", strtotime($inv->recurring_next_at))),
                'weekly'  => date('Y-m-d', strtotime("+{$inv->recurring_interval} weeks", strtotime($inv->recurring_next_at))),
                'monthly' => date('Y-m-d', strtotime("+{$inv->recurring_interval} months", strtotime($inv->recurring_next_at))),
                'yearly'  => date('Y-m-d', strtotime("+{$inv->recurring_interval} years", strtotime($inv->recurring_next_at))),
                default   => null
            };

            $inv->update(['recurring_next_at' => $next]);
        }

        $this->info("Recurring invoices generated successfully.");
    }
}
