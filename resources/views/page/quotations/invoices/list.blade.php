@extends('temp.common')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <h4>Invoices</h4>
    <div>
      <a href="{{ route('invoices.create') }}" class="btn btn-primary">Create Invoice</a>
      <a href="{{ route('invoices.export') }}" class="btn btn-success">Export CSV</a>
    </div>
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-4"><input id="searchBox" class="form-control" placeholder="Search invoice no or note"></div>
      <div class="col-md-2"><select id="perPage" class="form-control"><option>10</option><option>20</option><option>50</option></select></div>
      <div class="col-md-3"><select id="filterStatus" class="form-control">
        <option value="">All Status</option>
        <option value="draft">Draft</option><option value="sent">Sent</option><option value="partial">Partial</option><option value="paid">Paid</option>
      </select></div>
    </div>

    <table class="table table-bordered">
      <thead>
        <tr><th>#</th><th>Invoice No</th><th>Date</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody id="dataBody"></tbody>
    </table>

    <nav><ul class="pagination" id="pagination"></ul></nav>
  </div>
</div>
@endsection
{{-- @include('page.quotations.invoices.view_wrapper') --}}

@section('scripts')
<script>
  const INVOICE_AJAX_URL = "{{ route('invoices.ajax') }}";
</script>
<script src="{{ asset('assets/js/page/invoices.js') }}"></script>
@endsection
