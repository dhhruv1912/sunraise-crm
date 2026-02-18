@extends('temp.common')

@section('title', 'Roles')

@section('content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fa-solid fa-shield-halved me-2"></i> Roles
            </h4>
            <div class="text-muted small">
                Manage system roles and permissions
            </div>
        </div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            + New Role
        </button>
    </div>

    {{-- ================= WIDGETS ================= --}}
    <div class="row g-3 mb-4 position-relative" id="roleWidgets" style="min-height: 100px">
            <div class="crm-loader-overlay">
                <div class="crm-spinner"></div>
            </div>
        {{-- AJAX --}}
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive crm-table-wrapper position-relative">
            <table class="table crm-table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="roleTable">
                    @for($i=0;$i<5;$i++)
                        <tr>
                            <td colspan="3">
                                <div class="crm-skeleton"></div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <div id="roleTableLoader" class="crm-loader-overlay d-none">
                <div class="crm-spinner"></div>
            </div>
        </div>
    </div>
</div>

{{-- ================= CREATE ROLE MODAL ================= --}}
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title">Create Role</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small text-muted">Role Name</label>
                <input type="text" id="roleName" class="form-control">
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="storeRole()">Create</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= DELETE CONFIRM MODAL ================= --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <h5 class="mb-2">Delete Role</h5>
                <p class="text-muted mb-4">
                    This role will be permanently removed.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" onclick="deleteRole()">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= TOAST CONTAINER ================= --}}
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>
@endsection


@push('scripts')
<script>
const ROLE_LIST_URL    = "{{ route('roles.ajax.list') }}";
const ROLE_WIDGET_URL  = "{{ route('roles.ajax.widgets') }}";
const ROLE_STORE_URL   = "{{ route('roles.ajax.store') }}";

let deleteRoleId = null;
const roleModal   = new bootstrap.Modal(document.getElementById('roleModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

document.addEventListener('DOMContentLoaded', () => {
    loadWidgets();
    loadRoles();
});

/* ================= WIDGETS ================= */
function loadWidgets(){
    crmFetch(ROLE_WIDGET_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById('roleWidgets').innerHTML = html;
        });
}

/* ================= LIST ================= */
function loadRoles(){
    crmFetch(ROLE_LIST_URL)
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('roleTable');
            tbody.innerHTML = '';

            if(!res.length){
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No roles found
                        </td>
                    </tr>`;
                return;
            }

            res.forEach(r => {
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="crm-avatar">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-capitalize">${r.name}</div>
                                    <div class="text-muted small">
                                        ${r.users_count} users assigned
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="/roles/${r.id}/permissions"
                                   class="btn btn-sm btn-outline-primary">
                                    Permissions
                                </a>
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete(${r.id})">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>`;
            });
        });
}

/* ================= CREATE ================= */
function openCreateModal(){
    document.getElementById('roleName').value = '';
    roleModal.show();
}

function storeRole(){
    crmFetch(ROLE_STORE_URL, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: document.getElementById('roleName').value
        })
    })
    .then(res => res.json())
    .then(res => {
        roleModal.hide();
        showToast('success', res.message || 'Role created');
        loadWidgets();
        loadRoles();
    })
    .catch(() => showToast('danger', 'Unable to create role'));
}

/* ================= DELETE ================= */
function confirmDelete(id){
    deleteRoleId = id;
    deleteModal.show();
}

function deleteRole(){
    crmFetch(`/roles/ajax/delete/${deleteRoleId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(res => {
        deleteModal.hide();
        showToast('success', res.message || 'Role deleted');
        loadWidgets();
        loadRoles();
    })
    .catch(() => showToast('danger', 'Unable to delete role'));
}

/* ================= TOAST ================= */
function showToast(type, message){
    const id = `toast-${Date.now()}`;
    const html = `
        <div id="${id}" class="toast text-bg-${type} border-0 mb-2">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>`;

    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', html);

    const el = document.getElementById(id);
    const toast = new bootstrap.Toast(el, { delay: 3000 });
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}
</script>
@endpush
