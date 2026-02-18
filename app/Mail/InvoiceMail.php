<?php
namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Mail\Mailable;

class InvoiceMail extends Mailable
{
    public function __construct(public Invoice $invoice){}

    public function build()
    {
        return $this->subject('Invoice '.$this->invoice->invoice_no)
            ->view('emails.invoice')
            ->attachFromStorageDisk(
                'public',
                $this->invoice->pdf_path
            );
    }
}
