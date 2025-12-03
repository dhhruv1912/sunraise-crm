@php $row = $row ?? null; @endphp

<div class="modal fade" id="quoteRequestViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request — <span id="modal-type"></span> #<span id="modal-id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9" id="modal-name">—</dd>
                    <dt class="col-sm-3">Mobile</dt>
                    <dd class="col-sm-9" id="modal-number">—</dd>
                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9" id="modal-email">—</dd>

                    <div id="quote-fields">
                        <dt class="col-sm-3">Module</dt>
                        <dd class="col-sm-9" id="modal-module">—</dd>
                        <dt class="col-sm-3">KW</dt>
                        <dd class="col-sm-9" id="modal-kw">—</dd>
                        <dt class="col-sm-3">MC</dt>
                        <dd class="col-sm-9" id="modal-mc">—</dd>
                    </div>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9" id="modal-status">—</dd>
                    <dt class="col-sm-3">Assigned</dt>
                    <dd class="col-sm-9" id="modal-assigned">—</dd>
                    <dt class="col-sm-3">Notes</dt>
                    <dd class="col-sm-9" id="modal-notes">—</dd>
                </dl>
                <div class="mt-4">
                    <h5>History</h5>
                    <div id="modal-history" class="timeline"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="modal-send-mail" class="btn btn-success">Send Email</button>
                <button id="modal-convert-lead" class="btn btn-primary">Convert to Lead</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
