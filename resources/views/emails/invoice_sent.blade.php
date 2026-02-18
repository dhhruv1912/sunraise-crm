<!doctype html>
<html>
  <body>
    <p>Hi,</p>
    <p>Please find attached the invoice <strong>{{ $invoice->invoice_no }}</strong> for your project.</p>
    <p>Total: {{ number_format($invoice->total,2) }} {{ $invoice->currency }}</p>
    <p>Thank you,<br>{{ config('app.name') }}</p>
  </body>
</html>
