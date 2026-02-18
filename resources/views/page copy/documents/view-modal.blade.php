<div class="modal fade" id="docViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog-centered modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="docModalTitle">Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row">
        <div id="docPreview" class="mb-3 text-center col-6"></div>
        <dl class="row col-6">
            <dt class="col-sm-4">File name</dt><dd class="col-sm-8" id="docFileName"></dd>
            <dt class="col-sm-4">Type</dt><dd class="col-sm-8" id="docTypeText"></dd>
            <dt class="col-sm-4">Project</dt><dd class="col-sm-8" id="docProject"></dd>
            <dt class="col-sm-4">Uploader</dt><dd class="col-sm-8" id="docUploader"></dd>
            <dt class="col-sm-4">Size</dt><dd class="col-sm-8" id="docSize"></dd>
            <dt class="col-sm-4">Tags</dt><dd class="col-sm-8" id="docTagsText"></dd>
            <dt class="col-sm-4">Description</dt><dd class="col-sm-8" id="docDescriptionText"></dd>
            <dt class="col-sm-4">Uploaded</dt><dd class="col-sm-8" id="docUploadedAt"></dd>
        </dl>
      </div>
      <div class="modal-footer">
        <a id="docDownloadBtn" class="btn btn-success" target="_blank">Download</a>
        <button id="docAttachBtn" class="btn btn-info d-none">Attach to Project</button>
        <button id="docDetachBtn" class="btn btn-secondary d-none">Detach</button>
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
