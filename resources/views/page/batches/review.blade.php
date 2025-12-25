@extends('temp.common')

@section('title', 'Review Batch')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 style="color: var(--arham-text-heading);">Review Batch — Confirm before saving</h4>

        <div class="d-flex gap-2">
            <a href="{{ route('batches.create') }}" class="btn btn-outline-secondary">Back to Upload</a>
        </div>
    </div>
    <div class="row">

        <!-- LEFT: Metadata & Serial Editor -->
        <div class="col-lg-7">

            <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">
                    <h5 style="color: var(--arham-text-heading);">Selected Item & Warehouse</h5>

                    @php
                        $item = \App\Models\Item::find($item_id);
                        $warehouse = \App\Models\Warehouse::find($warehouse_id);
                    @endphp

                    <table class="table table-borderless">
                        <tr>
                            <th>Item</th>
                            <td>{{ $item->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Warehouse</th>
                            <td>{{ $warehouse->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Extracted Fields (editable) -->
            <form id="storeForm" method="POST" action="{{ route('batches.store') }}">
                @csrf

                <input type="hidden" name="item_id" value="{{ $item_id }}">
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">
                <input type="hidden" name="invoice_file" value="{{ $tempInvoicePath }}">

                <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                    <div class="card-body">
                        <h5 style="color: var(--arham-text-heading);">Extracted Invoice Fields</h5>

                        <div class="mb-3">
                            <label class="form-label">Invoice Number</label>
                            <input type="text" name="fields[invoice_number]" class="form-control"
                                   value="{{ old('fields.invoice_number', $fields['invoice_number'] ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" name="fields[invoice_date]" class="form-control"
                                   value="{{ old('fields.invoice_date', $fields['invoice_date'] ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Material Description</label>
                            <textarea name="fields[material_description]" class="form-control"
                                rows="2">{{ old('fields.material_description', $fields['material_description'] ?? '') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Model No</label>
                                <input type="text" name="fields[model_no]" class="form-control"
                                       value="{{ old('fields.model_no', $fields['model_no'] ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dimensions</label>
                                <input type="text" name="fields[dimensions]" class="form-control"
                                       value="{{ old('fields.dimensions', $fields['dimensions'] ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Net Weight</label>
                                <input type="text" name="fields[net_weight]" class="form-control"
                                       value="{{ old('fields.net_weight', $fields['net_weight'] ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gross Weight</label>
                                <input type="text" name="fields[gross_weight]" class="form-control"
                                       value="{{ old('fields.gross_weight', $fields['gross_weight'] ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">OCR Raw Text</label>
                            <textarea id="ocrRaw" name="fields[ocr_text]" class="form-control" rows="5" readonly>{{ $ocr_text ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Serial Editor -->
                <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 style="color: var(--arham-text-heading);">Serial Numbers</h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="addSerialRow()">+ Add</button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="downloadSerialsCSV()">Export CSV</button>
                            </div>
                        </div>

                        <small class="text-muted">Edit or remove any serial. Use bulk paste below to add many at once.</small>

                        <ul class="list-group mt-3" id="reviewSerialList">
                            @foreach($serials as $i => $s)
                                <li class="list-group-item d-flex gap-2 align-items-center">
                                    {{-- <input type="text" name="serials[]" class="form-control serial-input" value="{{ $s }}">
                                    <button type="button" class="btn btn-sm btn-danger ms-2 remove-serial-btn">Remove</button> --}}

                                    <span>{{ $i }}</span>
                                    <input type="text" class="form-control form-control-sm serial-no-input" name="serials[numbers][{{ $i }}]" data-idx="{{ $i }}" value="{{ $serialNumbers[$i] }}">
                                    <input type="text" class="form-control form-control-sm serial-desc-input" name="serials[descs][{{ $i }}]" data-idx="{{ $i }}" value="{{ $serialDescs[$i] }}">
                                    <button class="btn btn-sm btn-danger ms-2 remove-serial-btn">Remove</button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-3">
                            <label class="form-label">Bulk Paste (one per line)</label>
                            <textarea id="bulkSerials" class="form-control" rows="4"></textarea>
                            <div class="text-end mt-2">
                                <button type="button" class="btn btn-sm btn-secondary" id="applyBulk">Add All</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn" style="background: var(--arham-gradient-button); color:white;">
                        Save Batch & Create Panels
                    </button>

                    <a href="{{ route('batches.create') }}" class="btn btn-outline-secondary">Restart</a>
                </div>

            </form>

        </div>

        <!-- RIGHT: Invoice Preview -->
        <div class="col-lg-5">

            <div class="card" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">
                    <h5 style="color: var(--arham-text-heading);">Invoice Preview</h5>

                    @if($tempInvoicePath)
                        @php
                            $ext = strtolower(\Illuminate\Support\Str::afterLast($tempInvoicePath, '.'));
                        @endphp

                        @if($ext === 'pdf')
                            <iframe src="{{ asset('storage/' . $tempInvoicePath) }}"
                                    style="width:100%; height:500px; border:1px solid var(--arham-border);"></iframe>
                        @else
                            <img src="{{ asset('storage/' . $tempInvoicePath) }}"
                                 style="width:100%; max-width:480px; border-radius:6px;">
                        @endif

                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $tempInvoicePath) }}" target="_blank" class="btn btn-sm btn-outline-primary">Open Original</a>
                        </div>
                    @else
                        <p class="text-muted">No invoice available.</p>
                    @endif

                </div>
            </div>

        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Remove row
    document.querySelectorAll('.remove-serial-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.target.closest('li').remove();
        });
    });

    // Bulk paste
    document.getElementById('applyBulk').addEventListener('click', () => {
        const txt = document.getElementById('bulkSerials').value || '';
        const lines = txt.split(/\r?\n/).map(l => l.trim()).filter(l => l.length);
        const list = document.getElementById('reviewSerialList');

        lines.forEach(l => {
            // create new li with input
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex gap-2 align-items-center';
            li.innerHTML = `<input type="text" name="serials[]" class="form-control serial-input" value="${l}">
                            <button type="button" class="btn btn-sm btn-danger ms-2 remove-serial-btn">Remove</button>`;
            list.appendChild(li);

            li.querySelector('.remove-serial-btn').addEventListener('click', () => li.remove());
        });

        document.getElementById('bulkSerials').value = '';
    });

    // Add manual row
    window.addSerialRow = function() {
        const list = document.getElementById('reviewSerialList');
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex gap-2 align-items-center';
        li.innerHTML = `<input type="text" name="serials[]" class="form-control serial-input" value="">
                        <button type="button" class="btn btn-sm btn-danger ms-2 remove-serial-btn">Remove</button>`;
        list.appendChild(li);
        li.querySelector('.remove-serial-btn').addEventListener('click', () => li.remove());
    };

    // Export CSV
    window.downloadSerialsCSV = function() {
        const inputs = Array.from(document.querySelectorAll('input[name="serials[]"]'));
        const values = inputs.map(i => i.value.trim()).filter(v => v.length);
        if (!values.length) { alert('No serials to export'); return; }
        const csv = values.join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'serials.csv';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    };

});
</script>
@endsection
