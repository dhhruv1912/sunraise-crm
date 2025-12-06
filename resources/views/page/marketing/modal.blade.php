<div class="modal fade" id="leadViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Lead #<span id="modal-id"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <dl class="row">

          <dt class="col-sm-3">Lead Code</dt>
          <dd class="col-sm-9" id="modal-lead-code"></dd>

          <dt class="col-sm-3">Name</dt>
          <dd class="col-sm-9" id="modal-name"></dd>

          <dt class="col-sm-3">Mobile</dt>
          <dd class="col-sm-9" id="modal-number"></dd>

          <dt class="col-sm-3">Email</dt>
          <dd class="col-sm-9" id="modal-email"></dd>

          <dt class="col-sm-3">Assigned</dt>
          <dd class="col-sm-9" id="modal-assigned"></dd>

          <dt class="col-sm-3">Status</dt>
          <dd class="col-sm-9" id="modal-status"></dd>

          <dt class="col-sm-3">Remarks</dt>
          <dd class="col-sm-9" id="modal-remarks"></dd>

        </dl>

        <hr>

        <h5 class="mt-3">History</h5>
        <div id="modal-history"></div>

      </div>

      <div class="modal-footer">
        {{-- <button class="btn btn-warning" id="modal-convert-project">Convert to Project</button> --}}
        <button class="btn btn-success" id="create-project" onclick="createProject()">
          Convert to Project
        </button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
