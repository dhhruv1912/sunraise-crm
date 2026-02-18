<div class="modal fade" id="deleteDocModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Delete Document
                </h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-2">
                    Are you sure you want to delete this document?
                </p>
                <div class="text-muted small">
                    This action <b>cannot be undone</b>.
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-light"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-danger" 
                        id="confirmDeleteBtn">
                    <i class="fa-solid fa-trash me-1"></i>
                    Delete
                </button>
            </div>

        </div>
    </div>
</div>