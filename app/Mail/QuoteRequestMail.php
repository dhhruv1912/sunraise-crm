<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;

class QuoteRequestMail extends Mailable
{
    public $qr;
    public $projects;

    public function __construct($qr, $projects)
    {
        $this->qr = $qr;
        $this->projects = $projects;
    }

    public function build()
    {
        return $this
            ->subject('Your Solar Quote & Project Overview')
            ->view('emails.quote_request');
    }
}
