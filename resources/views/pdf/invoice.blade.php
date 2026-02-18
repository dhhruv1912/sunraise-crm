<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #ddd; }
        th { background: #f4f5fa; }
    </style>
</head>
<body>

<h2>Invoice {{ $invoice->invoice_no }}</h2>

<p>
    <strong>Customer:</strong> {{ $invoice->customer?->name }}<br>
    <strong>Date:</strong> {{ $invoice->invoice_date?->format('d M Y') }}
</p>

<table>
    <thead>
        <tr>
            <th>Description</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Tax</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $i)
        <tr>
            <td>{{ $i->description }}</td>
            <td>{{ number_format($i->unit_price,2) }}</td>
            <td>{{ $i->quantity }}</td>
            <td>{{ number_format($i->tax,2) }}</td>
            <td>{{ number_format($i->line_total,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top:10px">
    <strong>Total:</strong> ₹ {{ number_format($invoice->total,2) }}
</p>

</body>
</html>
