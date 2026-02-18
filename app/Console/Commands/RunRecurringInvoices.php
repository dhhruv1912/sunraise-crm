<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RecurringInvoiceService;

class RunRecurringInvoices extends Command
{
    protected $signature = 'invoices:recurring';
    protected $description = 'Generate recurring invoices';

    public function handle()
    {
        $count = RecurringInvoiceService::run();
        $this->info("Generated {$count} recurring invoices.");
    }
}
