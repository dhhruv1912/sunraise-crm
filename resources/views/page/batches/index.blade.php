@extends('temp.common')

@section('title', 'Batches')

@section('content')


    <div class="d-flex justify-content-between align-items-baseline mb-3">
        <h4 style="color: var(--arham-text-heading);">Batches</h4>

        <a href="{{ route('batches.create') }}" 
           class="btn btn-primary">
            + New Batch
        </a>
    </div>

    <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
        <div class="card-body">
            <input type="text"
                   id="batchSearch"
                   placeholder="Search by batch number or invoice number..."
                   class="form-control"
                   style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
        </div>
    </div>

    <div class="card" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
        <div class="card-body">

            <table class="table table-hover" id="batchTable">
                <thead>
                    <tr>
                        <th>Batch No</th>
                        <th>Item</th>
                        <th>Warehouse</th>
                        <th>Panels</th>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    {{-- @foreach($batches as $b)
                    <tr>
                        <td>{{ $b->batch_no }}</td>
                        <td>{{ $b->item->name ?? '-' }}</td>
                        <td>{{ $b->warehouse->name ?? '-' }}</td>
                        <td>{{ $b->panels_count }}</td>
                        <td>{{ $b->invoice_number ?? '-' }}</td>
                        <td>{{ $b->invoice_date }}</td>

                        <td>
                            <a href="{{ route('batches.show', $b->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach --}}
                </tbody>

            </table>

            <div class="mt-3">
                {{-- {{ $batches->links() }} --}}
            </div>

        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{ asset('assets/js/page/batches.js') }}"></script>
@endsection