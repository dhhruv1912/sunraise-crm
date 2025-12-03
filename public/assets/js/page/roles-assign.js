document.addEventListener("DOMContentLoaded", () => {
  const assignModalEl = document.getElementById('assignRoleModal');
  const assignModal = assignModalEl ? new bootstrap.Modal(assignModalEl) : null;
  const assignContent = document.getElementById('assignRoleContent');
  const assignUserIdInput = document.getElementById('assign_user_id');
  const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // Open modal when button clicked
  document.body.addEventListener('click', async (e) => {
    const btn = e.target.closest('.assign-role-btn');
    if (!btn) return;
    const userId = btn.dataset.user;
    assignUserIdInput.value = userId;
    assignContent.innerHTML = '<div class="text-center py-4">Loading...</div>';
    assignModal?.show();

    // fetch roles + user roles
    const res = await fetch(`/users/${userId}/roles-json`, {
      headers: { 'X-CSRF-TOKEN': token }
    });
    const out = await res.json();
    const roles = out.roles;
    const userRoles = out.userRoles || [];

    // build checkboxes
    let html = '<div class="row">';
    roles.forEach(r => {
      const checked = userRoles.includes(r.name) ? 'checked' : '';
      html += `<div class="col-md-4 mb-2">
        <label class="form-check-label">
          <input type="checkbox" class="form-check-input role-checkbox" value="${r.name}" ${checked}> ${r.name}
        </label>
      </div>`;
    });
    html += '</div>';
    assignContent.innerHTML = html;
  });

  // Submit assign form via ajax
  document.getElementById('assignRoleForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const userId = assignUserIdInput.value;
    const checked = [...document.querySelectorAll('#assignRoleContent .role-checkbox:checked')].map(i => i.value);

    const fd = new FormData();
    checked.forEach(r => fd.append('roles[]', r));

    const res = await fetch(`/users/${userId}/assign`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': token },
      body: fd
    });

    if (res.ok) {
      const out = await res.json();
      showDismissible(out.message || 'Roles updated', 'alert-success');
      assignModal?.hide();
      // optional: reload user list or update role chips in-row
      setTimeout(() => location.reload(), 600);
    } else {
      showDismissible('Failed to update roles', 'alert-danger');
    }
  });
});
