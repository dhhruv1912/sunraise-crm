@extends('temp.common')

@section('title', 'Batch Details')

@section('content')

<div class="container-fluid py-3">

    <!-- HEADER + ACTIONS -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 style="color: var(--arham-text-heading);">
            Batch: {{ $batch->batch_no }}
        </h4>

        <div class="d-flex gap-2">

            @if($batch->attachment)
            <a href="{{ route('batches.download_original', $batch->id) }}"
               class="btn btn-outline-primary">
                Download Original Invoice
            </a>
            @endif

            @if($batch->generated_pdf)
            <a href="{{ route('batches.download_generated', $batch->id) }}"
               class="btn btn-outline-success">
                Download Generated PDF
            </a>
            @endif

            <form action="{{ route('batches.regenerate_pdf', $batch->id) }}"
                  method="POST" style="display:inline;">
                @csrf
                <button class="btn btn-warning">
                    Regenerate PDF
                </button>
            </form>

        </div>
    </div>

    <!-- TWO COLUMN LAYOUT -->
    <div class="row">

        <!-- LEFT COLUMN - Metadata, OCR, Movement -->
        <div class="col-lg-7">

            <!-- BATCH INFO CARD -->
            <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 style="color: var(--arham-text-heading);">Batch Information</h5>

                        @if($isAdmin)
                        <button class="btn btn-sm btn-primary" onclick="openEditBatchModal()">
                            Edit
                        </button>
                        @endif
                    </div>

                    <table class="table table-borderless mb-0">
                        <tr>
                            <th>Invoice No</th>
                            <td>{{ $batch->invoice_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Invoice Date</th>
                            <td>{{ $batch->invoice_date ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Item</th>
                            <td>{{ $batch->item->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Warehouse</th>
                            <td>{{ $batch->warehouse->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $batch->material_description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Model</th>
                            <td>{{ $batch->model_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Net Weight</th>
                            <td>{{ $batch->net_weight ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Gross Weight</th>
                            <td>{{ $batch->gross_weight ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dimensions</th>
                            <td>{{ $batch->dimensions ?? '-' }}</td>
                        </tr>
                    </table>

                </div>
            </div>

            <!-- OCR TEXT -->
            <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">

                    <h5 style="color: var(--arham-text-heading); cursor:pointer;"
                        onclick="toggleOCR()">
                        OCR Extracted Text
                        <span id="ocrArrow">▼</span>
                    </h5>

                    <div id="ocrText" style="display:none;">
                        <textarea class="form-control mt-2" rows="6" readonly
                                  style="background: var(--arham-bg-light);">
                            {{ $batch->attachment->ocr_text ?? 'No OCR Data' }}
                        </textarea>
                    </div>

                </div>
            </div>

            <!-- MOVEMENT SUMMARY -->
            <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">
                    <h5 style="color: var(--arham-text-heading);">Movement Summary</h5>

                    <div class="row mt-3">
                        @foreach($movementSummary as $m)
                        <div class="col-md-4 mb-2">
                            <div class="p-2"
                                 style="background: var(--arham-bg-light); border-radius: 6px;">
                                <strong>{{ ucfirst($m->action) }}</strong><br>
                                {{ $m->total }} panels
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>

            <!-- MOVEMENT TIMELINE -->
            <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">
                    <h5 style="color: var(--arham-text-heading);">Movement Timeline</h5>

                    <ul class="list-group mt-3">
                        @foreach($timeline as $t)
                        <li class="list-group-item">
                            <strong>{{ ucfirst($t->action) }}</strong>  
                            <span class="text-muted">{{ $t->happened_at }}</span><br>
                            <small>{{ $t->note }}</small>
                        </li>
                        @endforeach
                    </ul>

                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN - Invoice Preview -->
        <div class="col-lg-5">

            <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">

                    <h5 class="mb-3" style="color: var(--arham-text-heading);">Invoice Preview</h5>

                    @if($batch->attachment)

                        @php
                            $file = $batch->attachment->file_path;
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        @endphp

                        @if($ext == 'pdf')
                            <iframe src="/storage/{{ $file }}"
                                style="width: 100%; height: 500px; border:1px solid var(--arham-border);"></iframe>

                        @else
                            <img src="/storage/{{ $file }}" 
                                 style="width: 100%; max-width: 450px; border-radius: 6px;">
                        @endif

                    @else
                    <p class="text-muted">No invoice uploaded.</p>
                    @endif

                </div>
            </div>

        </div>

    </div>

    <!-- SERIAL NUMBERS -->
    <div class="row mt-4">

        <!-- GRID VIEW -->
        <div class="col-12 mb-3">
            <div class="card" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">
                    <h5 style="color: var(--arham-text-heading);">Serial Numbers (Grid View)</h5>

                    <div class="row mt-3">
                        @foreach($panels as $p)
                        <div class="col-md-3 col-sm-4 col-6 mb-2">
                            <div class="p-2 text-center"
                                 style="background: var(--arham-bg-light); border-radius:6px;">
                                {{ $p->serial_number }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

        <!-- TABLE VIEW -->
        <div class="col-12">
            <div class="card" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">
                    <h5 style="color: var(--arham-text-heading);">Serial Numbers (Table View)</h5>

                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Serial Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($panels as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $p->serial_number }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

</div>

@include('page.batches.modal_edit')

@endsection

@section('scripts')
<script>
function toggleOCR() {
    let box = document.getElementById("ocrText");
    let arrow = document.getElementById("ocrArrow");
    box.style.display = box.style.display === "none" ? "block" : "none";
    arrow.innerHTML = arrow.innerHTML === "▼" ? "▲" : "▼";
}

function openEditBatchModal() {
    new bootstrap.Modal(document.getElementById("editBatchModal")).show();
}
</script>
@endsection
