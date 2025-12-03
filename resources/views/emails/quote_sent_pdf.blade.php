<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <style>
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:13px; color:#222; }
    .wrap{ max-width:800px; margin:0 auto; padding:18px; }
    .header{ display:flex; align-items:center; justify-content:space-between; }
    .logo{ width:120px; height:45px; background:#efefef; display:inline-block; border-radius:4px; }
    h1{ font-size:18px; margin:0 0 8px 0; }
    table { width:100%; border-collapse:collapse; margin-top:12px; }
    th, td { padding:8px; border:1px solid #ddd; }
    .total { font-weight:700; }
    .meta { margin-top:18px; font-size:12px; color:#555; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <div>
        <h1>Quotation</h1>
        <div>Quote No: {{ $quoteNo ?? 'Q-'.$request->id }}</div>
      </div>
      <div class="logo"></div>
    </div>

    <div style="margin-top:12px">
      <strong>Customer:</strong> {{ $request->name ?? '-' }} <br>
      <strong>Phone:</strong> {{ $request->number ?? '-' }} <br>
      <strong>Email:</strong> {{ $request->email ?? '-' }}
    </div>

    <h3 style="margin-top:16px">Summary</h3>
    <table>
      <thead>
        <tr>
          <th>SKU / Package</th>
          <th>Module</th>
          <th>KW</th>
          <th>Count</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        @if(isset($package))
          <tr>
            <td>{{ $package['sku'] ?? '-' }}</td>
            <td>{{ $package['module'] ?? ($request->module ?? '-') }}</td>
            <td>{{ $package['kw'] ?? ($request->kw ?? '-') }}</td>
            <td>{{ $package['module_count'] ?? '' }}</td>
            <td>{{ number_format($package['payable'] ?? ($request->payable ?? 0), 2) }}</td>
          </tr>
        @else
          <tr>
            <td>-</td><td>{{ $request->module ?? '-' }}</td><td>{{ $request->kw ?? '-' }}</td><td>{{ $request->mc ?? '-' }}</td>
            <td>{{ number_format($request->payable ?? 0, 2) }}</td>
          </tr>
        @endif
      </tbody>
    </table>

    <div class="meta">
      <p>Notes: {{ $request->notes ?? '-' }}</p>
    </div>
{{--
    @if($projects && $projects->count())
      <h4>Recent Completed Projects</h4>
      <ul>
        @foreach($projects as $p)
          <li>{{ $p->customer_name ?? '—' }} — {{ $p->project_code ?? "PID-{$p->id}" }}</li>
        @endforeach
      </ul>
    @endif --}}

  </div>
</body>
</html>
