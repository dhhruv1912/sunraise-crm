@extends('temp.common')

@section('title', 'Receive Panels - Upload Invoice')

@section('content')
    <div class="card" style="background: var(--arham-bg-card); box-shadow: 0 2px 6px var(--arham-shadow); margin: 0 auto;">
        <div class="card-body">

            <h4 class="mb-3" style="color: var(--arham-text-heading);">
                Receive New Panels — Step 1: Upload Invoice
            </h4>

            <form action="{{ route('panels.receive.uploadInvoice') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Item Type</label>
                    <select name="item_id" class="form-select" required
                        style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
                        <option value="">Select Item</option>
                        @foreach ($items as $i)
                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Warehouse</label>
                    <select name="warehouse_id" class="form-select" required
                        style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
                        <option value="">Select Warehouse</option>
                        @foreach ($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload Invoice File</label>
                    <input type="file" name="invoice_file" class="form-control" required id="invoiceFile"
                        style="background: var(--arham-input-bg); border-color: var(--arham-input-border);">
                    <small class="text-muted">Allowed: JPG, PNG, PDF</small>
                </div>

                <button class="btn w-100 mt-3"
                    style="
                        background: var(--arham-button-primary-bg);
                        color: var(--arham-button-primary-text);
                        border-radius: 8px;
                    ">
                    Continue → OCR & Preview
                </button>


                <div class="mb-3">
                    <label class="form-label">Preview</label>
                    <img id="ocrPreview" src="" style="max-width: 200px; display:block;">
                </div>

                <button type="button" class="btn" style="background: var(--arham-button-accent-bg);"
                    onclick="runOCR('invoiceFile', 'serialResult', 'ocrPreview')">
                    Extract Serials Using OCR
                </button>

                <textarea id="serialResult" rows="6" class="form-control mt-3" placeholder="OCR results will appear here..."></textarea>
            </form>

        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
    <script src="{{ asset('/assets/js/page/ocr.js') }}"></script>
@endsection
