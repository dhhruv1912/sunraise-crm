@extends('temp.common')

@section('title', 'Create Batch')

@section('content')


    <h4 style="color: var(--arham-text-heading);">Create New Batch</h4>

    <div class="row mt-3">

        <!-- LEFT: FORM + OCR RESULTS -->
        <div class="col-lg-6">

            <!-- ITEM + WAREHOUSE SELECTION -->
            <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">

                    <h5 class="mb-3" style="color: var(--arham-text-heading);">Batch Information</h5>

                    <div class="mb-3">
                        <label class="form-label">Item Category</label>
                        <select id="categorySelect" class="form-select">
                            <option value="">Select Category</option>
                            @foreach ($itemCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Item</label>
                        <select id="itemSelect" class="form-select">
                            <option value="">Select Item</option>
                            @foreach ($items as $it)
                                <option value="{{ $it->id }}">{{ $it->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Warehouse</label>
                        <select id="warehouseSelect" class="form-select">
                            <option value="">Select Warehouse</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <div class="card mb-3" style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">

                    <h5 class="mb-3" style="color: var(--arham-text-heading);">Upload Invoice</h5>

                    <input type="file" accept="image/*,application/pdf" id="invoiceInput" class="form-control mb-3">

                    <div id="cropContainer" style="display:none;">
                        <img id="cropImage" style="max-width:100%;">
                    </div>

                </div>
            </div>

            <!-- OCR-EXTRACTED FIELDS -->
            <div class="card mb-3" id="ocrResultCard"
                style="display:none; background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">

                    <h5 class="mb-3" style="color: var(--arham-text-heading);">Extracted Invoice Fields</h5>

                    <div class="mb-3">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" id="invoiceNumber">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Invoice Date</label>
                        <input type="date" class="form-control" id="invoiceDate">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Material Description</label>
                        <textarea class="form-control" id="materialDescription"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Model No</label>
                            <input type="text" class="form-control" id="modelNo">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dimensions</label>
                            <input type="text" class="form-control" id="dimensions">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quentity</label>
                            <input type="text" class="form-control" id="quentity">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Net Weight</label>
                            <input type="text" class="form-control" id="netWeight">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gross Weight</label>
                            <input type="text" class="form-control" id="grossWeight">
                        </div>
                    </div>

                </div>
            </div>

            <!-- SERIAL NUMBERS -->


            <button id="continueBtn" class="btn w-100"
                style="display:none; background: var(--arham-gradient-button); color:white;" onclick="submitForReview()">
                Continue to Review
            </button>

        </div>

        <!-- RIGHT: UPLOAD + CROP + PREVIEW -->
        <div class="col-lg-6">
            <div class="card mb-3" id="ocrProgressWrapper"  style="background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">
                    <div class="mt-3" id="ocrProgress" >
                        <p>Extracting text... Please wait</p>
                    </div>
                    <div class="progress bg-label-info" style="height:15px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" id="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="card mb-3" id="serialCard"
                style="display:none; background: var(--arham-bg-card); box-shadow: var(--arham-shadow);">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <h5 style="color: var(--arham-text-heading);">Serial Numbers</h5>

                        <button class="btn btn-sm btn-secondary" onclick="addSerialRow()">
                            + Add Serial
                        </button>
                    </div>

                    <ul class="list-group" id="serialList"></ul>
                    <ul class="list-group" id="serialList2"></ul>

                </div>
            </div>


        </div>

    </div>


    <!-- Hidden form for posting to /batches/review -->
    <form id="reviewForm" method="POST" action="{{ route('batches.review') }}" enctype="multipart/form-data"
        style="display:none;">
        @csrf
        <input type="hidden" name="fields[item_id]">
        <input type="hidden" name="fields[warehouse_id]">
        <input type="hidden" name="fields[invoice_number]">
        <input type="hidden" name="fields[invoice_date]">
        <input type="hidden" name="fields[material_description]">
        <input type="hidden" name="fields[model_no]">
        <input type="hidden" name="fields[net_weight]">
        <input type="hidden" name="fields[gross_weight]">
        <input type="hidden" name="fields[dimensions]">
        <input type="hidden" name="fields[ocr_text]">
        <input type="hidden" name="fields[invoice_file]">
    </form>

@endsection

@section('scripts')

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <!-- Tesseract.js -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/tesseract.js@4.1.1/dist/tesseract.min.js"></script> --}}


    <script src="{{ asset('assets/js/page/batch_create.js') }}"></script>

@endsection
