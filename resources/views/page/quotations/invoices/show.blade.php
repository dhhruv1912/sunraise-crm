@extends('temp.common')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h4>Invoice {{ $invoice->invoice_no }}</h4>
    <div>
      <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-outline-primary">Download PDF</a>
      <form method="POST" action="{{ route('invoices.send', $invoice->id) }}" style="display:inline">
        @csrf
        <button class="btn btn-success">Send Email</button>
      </form>
    </div>
  </div>

  <div class="card-body">
    <p><strong>Date:</strong> {{ $invoice->invoice_date }}</p>
    <p><strong>Due:</strong> {{ $invoice->due_date }}</p>

    <table class="table">
      <thead><tr><th>Description</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
      <tbody>
        @foreach($invoice->items as $it)
          <tr>
            <td>{{ $it->description }}</td>
            <td>{{ $it->quantity }}</td>
            <td>{{ number_format($it->unit_price,2) }}</td>
            <td>{{ number_format($it->line_total,2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="text-end">
      <p>Subtotal: {{ number_format($invoice->sub_total,2) }}</p>
      <p>Tax: {{ number_format($invoice->tax_total,2) }}</p>
      <p>Total: {{ number_format($invoice->total,2) }}</p>
      <p>Paid: {{ number_format($invoice->paid_amount,2) }}</p>
      <p>Balance: {{ number_format($invoice->balance,2) }}</p>
    </div>

    <hr>

    <h5>Record Payment</h5>
    <form id="paymentForm">
      @csrf
      <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
      <div class="row g-2">
        <div class="col-md-3"><input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount"></div>
        <div class="col-md-3"><input name="method" class="form-control" placeholder="Method"></div>
        <div class="col-md-3"><input name="reference" class="form-control" placeholder="Reference"></div>
        <div class="col-md-2"><input name="paid_at" type="date" class="form-control" value="{{ date('Y-m-d') }}"></div>
        <div class="col-md-1"><button class="btn btn-primary">Add</button></div>
      </div>
    </form>

    <hr>
    <h5>Payments</h5>
    <ul id="paymentsList">
      @foreach($invoice->payments as $pay)
        <li>{{ $pay->paid_at }} — {{ $pay->amount }} ({{ $pay->method }})</li>
      @endforeach
    </ul>

  </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('paymentForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const formData = new FormData(this);
  const id = {{ $invoice->id }};
  const res = await fetch(`/billing/invoices/${id}/payments`, {
    method:'POST',
    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
    body: formData
  });
  const json = await res.json();
  if (json.status) {
    alert('Payment added');
    location.reload();
  } else {
    alert('Failed');
  }
});
</script>
@endsection
