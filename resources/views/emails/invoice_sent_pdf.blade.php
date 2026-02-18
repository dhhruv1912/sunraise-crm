<!doctype html>
<html>
<head>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size:12px; }
    table { width:100%; border-collapse: collapse; }
    th, td { padding:6px; border:1px solid #ddd; }
    .right { text-align:right; }
  </style>
</head>
<body>
  <h3>Invoice: {{ $invoice->invoice_no }}</h3>
  <p>Date: {{ $invoice->invoice_date }}</p>
  <p>Due: {{ $invoice->due_date }}</p>

  <table>
    <thead><tr><th>Description</th><th>Qty</th><th>Unit</th><th class="right">Total</th></tr></thead>
    <tbody>
      @foreach($invoice->items as $it)
        <tr>
          <td>{{ $it->description }}</td>
          <td class="right">{{ $it->quantity }}</td>
          <td class="right">{{ number_format($it->unit_price,2) }}</td>
          <td class="right">{{ number_format($it->line_total,2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <p style="text-align:right">
    Subtotal: {{ number_format($invoice->sub_total,2) }}<br>
    Tax: {{ number_format($invoice->tax_total,2) }}<br>
    Total: <strong>{{ number_format($invoice->total,2) }}</strong>
  </p>
</body>
</html>
