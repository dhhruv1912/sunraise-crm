@php
    $companyName = config('app.name');
    $customer = $request->name ?? ($request->email ?? 'Customer');
@endphp

<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Quotation from {{ $companyName }}</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial; color:#333; }
    .container{ max-width:680px; margin:0 auto; padding:20px; }
    .header{ display:flex; align-items:center; gap:12px; }
    .logo{ width:80px; height:80px; background:#eee; display:inline-block; border-radius:8px; }
    .btn { display:inline-block; padding:10px 14px; border-radius:6px; text-decoration:none; }
    .primary{ background:#0d6efd; color:white; }
    .muted{ color:#666; font-size:13px; }
    .card{ border-radius:8px; padding:16px; background:#fafafa; margin-top:18px; }
    .projects { margin-top:12px; }
    table { width:100%; border-collapse:collapse; margin-top:12px; }
    th, td { padding:8px 6px; border-bottom:1px solid #eee; text-align:left; }
    .center { text-align:center; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="logo"></div>
      <div>
        <h3 style="margin:0">{{ $companyName }}</h3>
        <div class="muted">Quotation for {{ $customer }}</div>
      </div>
    </div>

    <div class="card">
      <p>Hi {{ $request->name ?? 'there' }},</p>

      <p>Thanks for contacting {{ $companyName }}. We've prepared a quotation for your request (ID: <strong>#{{ $request->id }}</strong>).</p>

      <p>
        <a href="{{ $quotePdfUrl ?? '#' }}" class="btn primary" target="_blank">Download Quotation (PDF)</a>
        &nbsp;
        <a href="{{ $quoteViewUrl ?? '#' }}" class="btn" style="background:#f8f9fa; color:#333; border:1px solid #ddd;">View Online</a>
      </p>

      <h4 style="margin-top:18px">Summary</h4>
      <table>
        <tr><th>Requested Module</th><td>{{ $request->module ?? '-' }}</td></tr>
        <tr><th>KW</th><td>{{ $request->kw ?? '-' }}</td></tr>
        <tr><th>MC</th><td>{{ $request->mc ?? '-' }}</td></tr>
        <tr><th>Status</th><td>{{ \App\Http\Controllers\QuoteRequestController::$STATUS[$request->status] ?? $request->status }}</td></tr>
      </table>

      @if($projects && $projects->count())
        <div class="projects">
          <h5>Recent completed projects</h5>
          <ul>
            @foreach($projects as $p)
              <li>{{ $p->customer_name ?? '—' }} — {{ $p->project_code ?? "PID-{$p->id}" }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <p style="margin-top:18px">If you have any questions, reply to this email or call us.</p>

      <p class="muted">Regards,<br>{{ $companyName }} team</p>
    </div>

    <div style="font-size:12px; color:#999; margin-top:18px">
      This email was sent automatically. If you did not request this, ignore it.
    </div>
  </div>
</body>
</html>
