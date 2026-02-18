@extends('temp.common')

@section('title', 'Quotations')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Quotation{{$id ? "" : "s"}}</h4>

        <div class="d-flex gap-2 align-items-center">
            @if ($id != "")
                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Back</a>
                <input type="hidden" name="searchBox" id="searchBox"><input type="hidden" name="perPage" id="perPage">
            @else
                <a href="{{ route('quotations.create') }}" class="btn btn-primary">New Quotation</a>
                <a href="{{ route('quotations.export') }}" class="btn btn-success">Export</a>
                <input id="searchBox" class="form-control form-control-sm" placeholder="Search..." style="width:220px">
                <select id="perPage" class="form-select form-select-sm" style="width:80px">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                
            @endif
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Quotation #</th>
                        <th>Customer</th>
                        <th>Base</th>
                        <th>Discount</th>
                        <th>Final</th>
                        <th>Sent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="dataBody"></tbody>
            </table>
        </div>

        <nav id="pagination" class="p-3 {{ $id != "" ? "d-none" : "" }}"></nav>
    </div>
</div>

{{-- modal target --}}
<div id="quotationModalContainer"></div>
@endsection

@section('scripts')
<script>
    window.REQ_ID = "{{ $id  }}"
    window.QUOTE_ROUTES = {
        ajax: "{{ route('quotations.ajax') }}",
        create: "{{ route('quotations.create') }}",
        edit: "{{ url('quote/quotations') }}", // append /{id}/edit or /{id}/download etc
        generatePdf: "{{ url('quote/quotations') }}", // append /{id}/generate-pdf
        sendEmail: "{{ url('quote/quotations') }}" // append /{id}/send-email
    };
</script>
<script src="{{ asset('assets/js/page/quotations.js') }}"></script>
@endsection
