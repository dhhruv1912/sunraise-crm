<div class="modal fade" id="categoryModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header" style="background: var(--arham-gradient-blue); color:white;">
                <h5 class="modal-title" id="categoryModalTitle"></h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="categoryForm">
                    @csrf
                    <input type="hidden" id="cat_id">

                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="cat_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="cat_description"></textarea>
                    </div>

                    <button type="submit" class="btn w-100"
                        style="background: var(--arham-button-primary-bg); color:white;">
                        Save
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>
