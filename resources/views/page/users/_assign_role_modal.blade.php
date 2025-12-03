<div class="modal fade" id="assignRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="assignRoleForm">
        <div class="modal-header">
          <h5 class="modal-title">Assign Roles</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="assignRoleContent">
            <!-- ajax loaded roles checkboxes will go here -->
            <div class="text-center py-4">Loading...</div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" id="assign_user_id" name="user_id" value="">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Roles</button>
        </div>
      </form>
    </div>
  </div>
</div>
