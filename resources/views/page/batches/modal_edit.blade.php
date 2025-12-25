<div class="modal fade" id="editBatchModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background: var(--arham-gradient-blue); color:white;">
                <h5 class="modal-title">Edit Batch Metadata</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('batches.update', $batch->id) }}">
                @csrf

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Invoice Number</label>
                            <input type="text" class="form-control" name="invoice_number"
                                   value="{{ $batch->invoice_number }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" class="form-control" name="invoice_date"
                                   value="{{ $batch->invoice_date }}">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="material_description">{{ $batch->material_description }}</textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Model No</label>
                            <input type="text" class="form-control" name="model_no"
                                   value="{{ $batch->model_no }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dimensions</label>
                            <input type="text" class="form-control" name="dimensions"
                                   value="{{ $batch->dimensions }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Net Weight</label>
                            <input type="text" class="form-control" name="net_weight"
                                   value="{{ $batch->net_weight }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gross Weight</label>
                            <input type="text" class="form-control" name="gross_weight"
                                   value="{{ $batch->gross_weight }}">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save Changes</button>
                </div>

            </form>

        </div>
    </div>
</div>
