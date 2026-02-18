<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Documents</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="docUploadForm">
            @csrf
            <div class="mb-2">
                <label>Files</label>
                <input type="file" name="files[]" id="filesInput" multiple class="form-control">
            </div>

            <div class="mb-2 row">
                <div class="col-md-6">
                    <label>Type</label>
                    <select name="type" id="docType" class="form-control">
                        <option value="">— Select —</option>
                        @foreach($types as $t)
                            <option value="{{ $t }}">{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 position-relative">
                    <label>Attach to Project (optional)</label>
                    <input type="text" id="uploadProjectSearch" class="form-control" placeholder="Project ID or code (optional)">
                    <input type="hidden" id="uploadProjectId" name="project_id">

                    <!-- Dropdown -->
                    <div id="projectSearchDropdown"
                        class="list-group position-absolute w-100 shadow-sm mt-1 bg-white"
                        style="z-index:9999; display:none;"></div>
                </div>
            </div>

            <div class="mb-2">
                <label>Description</label>
                <textarea name="description" id="docDescription" class="form-control"></textarea>
            </div>

            <div class="mb-2">
                <label>Tags (comma separated)</label>
                <input type="text" name="tags" id="docTags" class="form-control" />
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="uploadBtn" class="btn btn-primary">Upload</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
