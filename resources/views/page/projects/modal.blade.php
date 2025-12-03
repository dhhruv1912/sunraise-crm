<div class="modal fade" id="projectViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Project <span id="modal-project-code"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <dl class="row">
            <dt class="col-sm-3">Customer</dt><dd class="col-sm-9" id="modal-customer"></dd>
            <dt class="col-sm-3">Mobile</dt><dd class="col-sm-9" id="modal-mobile"></dd>
            <dt class="col-sm-3">Address</dt><dd class="col-sm-9" id="modal-address"></dd>
            <dt class="col-sm-3">KW</dt><dd class="col-sm-9" id="modal-kw"></dd>
            <dt class="col-sm-3">Assignee</dt><dd class="col-sm-9" id="modal-assignee"></dd>
            <dt class="col-sm-3">Status</dt><dd class="col-sm-9" id="modal-status"></dd>
            <dt class="col-sm-3">Notes</dt><dd class="col-sm-9" id="modal-notes"></dd>
        </dl>

        <hr>
        <h6>Documents</h6>
        <div id="modal-documents"></div>

        <hr>
        <h6>History</h6>
        <div id="modal-history"></div>
      </div>
      <div class="modal-footer">
        <a href="#" id="modal-edit-link" class="btn btn-primary">Edit</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
