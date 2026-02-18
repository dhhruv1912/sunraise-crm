<p>Hello {{ $invoice->customer?->name }},</p>

<p>Please find attached your invoice <strong>{{ $invoice->invoice_no }}</strong>.</p>

<p>Total Amount: ₹ {{ number_format($invoice->total,2) }}</p>

<p>Thank you,<br>Sunraise Energy</p>
