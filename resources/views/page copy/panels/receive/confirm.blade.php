@extends('layouts.master')

@section('title', 'Receive Panels - Confirm')

@section('content')
<div class="container-fluid py-4">

    <div class="card" style="background: var(--arham-bg-card); box-shadow: 0 2px 6px var(--arham-shadow);">
        <div class="card-body">

            <h4 class="mb-3" style="color: var(--arham-text-heading);">
                Step 2: Confirm Serial Numbers & Details
            </h4>

            <!-- OCR TEXT PREVIEW -->
            <div class="mb-4">
                <label class="form-label text-muted">Extracted Text (OCR Output)</label>
                <textarea class="form-control" rows="5" readonly
                    style="background: var(--arham-bg-light); border-color: var(--arham-border);">{{ $attachment->ocr_text }}</textarea>
            </div>

            <form action="{{ route('panels.receive.savePanels') }}" method="POST">
                @csrf

                <input type="hidden" name="attachment_id" value="{{ $attachment->id }}">
                <input type="hidden" name="item_id" value="{{ $item_id }}">
                <input type="hidden" name="warehouse_id" value="{{ $warehouse_id }}">

                <!-- SERIAL LIST -->
                <label class="form-label">Serial Numbers (One per line)</label>
                <textarea name="serials[]" class="form-control" rows="10" required
                    style="background: var(--arham-input-bg); border-color: var(--arham-input-border);"></textarea>
                <textarea name="serials[]" class="form-control" rows="10">
                {{ request('ocr_serials') }}
                </textarea>
                <button class="btn mt-4 w-100"
                    style="
                        background: var(--arham-gradient-brand);
                        color: white;
                        border-radius: 8px;
                    ">
                    Save Panels to Inventory
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
@section('scripts')
<script>
    document.querySelector("form").addEventListener("submit", () => {
    document.querySelector("input[name='ocr_serials']").value =
        document.getElementById("serialResult").value;
});
</script>
@endsection