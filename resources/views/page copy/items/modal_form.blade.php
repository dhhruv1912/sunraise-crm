<div class="modal fade" id="itemModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header" style="background: var(--arham-gradient-blue); color:white;">
                <h5 class="modal-title" id="itemModalTitle"></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="itemForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="item_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="item_category" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="item_name" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" id="item_sku">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Model No</label>
                            <input type="text" class="form-control" id="item_model">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Watt</label>
                            <input type="text" class="form-control" id="item_watt">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="item_description"></textarea>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" id="item_image">
                            <img id="itemImagePreview" class="mt-2" style="max-width: 150px; display:none;">
                        </div>

                    </div>

                    <button class="btn w-100"
                        style="background: var(--arham-button-primary-bg); color:white;">
                        Save Item
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>
