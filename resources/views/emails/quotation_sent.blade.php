<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Quotation {{ $quotation->quotation_no }}</title>
  <style>
    body { font-family: Arial, sans-serif; color:#333; line-height:1.4 }
    .card { border:1px solid #eee; padding:16px; border-radius:6px; }
    .btn { display:inline-block; padding:8px 12px; background:#0d6efd; color:#fff; border-radius:4px; text-decoration:none }
    table { width:100%; border-collapse:collapse; margin-top:12px }
    th, td { border:1px solid #ddd; padding:8px }
  </style>
</head>
<body>
  <div class="card">
    <h2>Quotation — {{ $quotation->quotation_no }}</h2>
    <p>Hello {{ $request->name ?? 'Customer' }},</p>

    <p>Thank you for your interest. Please find attached the quotation. Summary:</p>

    <table>
      <tr><th>Base</th><td>{{ number_format($quotation->base_price,2) }}</td></tr>
      <tr><th>Discount</th><td>{{ number_format($quotation->discount ?? 0,2) }}</td></tr>
      <tr><th>Final</th><td>{{ number_format($quotation->final_price ?? 0,2) }}</td></tr>
    </table>

    <p>Recent projects (last 5):</p>
    {{-- @if(!empty($projects) && $projects->count())
      <ul>
        @foreach($projects as $p)
          <li>{{ $p->customer_name }} — {{ $p->project_code ?? '' }} — Completed at {{ optional($p->updated_at)->format('Y-m-d') }}</li>
        @endforeach
      </ul>
    @endif --}}

    <p>Regards,<br>{{ config('app.name') }}</p>
  </div>
</body>
</html>
