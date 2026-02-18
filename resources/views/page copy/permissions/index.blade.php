@extends('temp.common')

@section('title', 'Permissions')

@section('content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Permissions</h4>
            <div class="text-muted small">
                Manage system permissions
            </div>
        </div>
        <button class="btn btn-primary" onclick="openCreateModal()">
            + New Permission
        </button>
    </div>

    {{-- ================= WIDGETS ================= --}}
    <div class="row g-3 mb-4 position-relative" id="permissionWidgets" style="min-height: 100px">
        <div class="crm-loader-overlay">
            <div class="crm-spinner"></div>
        </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive crm-table-wrapper position-relative">
            <table class="table crm-table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Permission</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="permissionTable">
                    {{-- skeleton --}}
                    @for($i=0;$i<5;$i++)
                        <tr>
                            <td colspan="3">
                                <div class="crm-skeleton"></div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <div id="tableLoader" class="crm-loader-overlay d-none">
                <div class="crm-spinner"></div>
            </div>
        </div>
    </div>
</div>

{{-- ================= CREATE MODAL ================= --}}
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title">Create Permission</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label small text-muted">Module</label>
                    <input type="text" id="permModule" class="form-control"
                           placeholder="users, customers, projects">
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Action</label>
                    <input type="text" id="permAction" class="form-control"
                           placeholder="view, create, update, delete">
                </div>

                <div class="text-muted small">
                    Final permission will be:
                    <code><span id="permPreview">module.action</span></code>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="storePermission()">Create</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= DELETE CONFIRM MODAL ================= --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <h5 class="mb-2">Delete Permission</h5>
                <p class="text-muted mb-4">
                    This permission will be removed permanently.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" onclick="deletePermission()">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= TOAST ================= --}}
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>
@endsection


@push('scripts')
<script>
const LIST_URL    = "{{ route('permissions.ajax.list') }}";
const STORE_URL   = "{{ route('permissions.ajax.store') }}";
const DELETE_URL  = "{{ route('permissions.ajax.delete', ':id') }}";
const WIDGET_URL  = "{{ route('permissions.ajax.widgets') }}";

let deletePermissionId = null;
const createModal = new bootstrap.Modal(document.getElementById('permissionModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

document.addEventListener('DOMContentLoaded', () => {
    loadWidgets();
    loadPermissions();

    document.getElementById('permModule').addEventListener('input', updatePreview);
    document.getElementById('permAction').addEventListener('input', updatePreview);
});

/* ================= WIDGETS ================= */
function loadWidgets(){
    crmFetch(WIDGET_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById('permissionWidgets').innerHTML = html;
        });
}

/* ================= LIST ================= */
function loadPermissions(){
    crmFetch(LIST_URL)
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('permissionTable');
            tbody.innerHTML = '';

            if(!res.length){
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No permissions found
                        </td>
                    </tr>`;
                return;
            }

            res.forEach(p => {
                const parts = p.name.split('.');
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="crm-avatar">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">${p.name}</div>
                                    <div class="text-muted small">
                                        Module: ${p.name.split('.')[0]}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete(${p.id})">
                                Delete
                            </button>
                        </td>
                    </tr>`;
            });
        });
}

/* ================= CREATE ================= */
function openCreateModal(){
    document.getElementById('permModule').value = '';
    document.getElementById('permAction').value = '';
    document.getElementById('permPreview').innerText = 'module.action';
    createModal.show();
}

function updatePreview(){
    const m = document.getElementById('permModule').value.trim();
    const a = document.getElementById('permAction').value.trim();
    document.getElementById('permPreview').innerText = `${m}.${a}`;
}

function storePermission(){
    crmFetch(STORE_URL, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            module: document.getElementById('permModule').value,
            action: document.getElementById('permAction').value
        })
    })
    .then(res => res.json())
    .then(res => {
        createModal.hide();
        showToast('success', res.message || 'Permission created');
        loadWidgets();
        loadPermissions();
    })
    .catch(() => showToast('danger', 'Unable to create permission'));
}

/* ================= DELETE ================= */
function confirmDelete(id){
    deletePermissionId = id;
    deleteModal.show();
}

function deletePermission(){
    crmFetch(DELETE_URL.replace(':id', deletePermissionId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => res.json())
    .then(res => {
        deleteModal.hide();
        showToast('success', res.message || 'Permission deleted');
        loadWidgets();
        loadPermissions();
    })
    .catch(() => showToast('danger', 'Unable to delete permission'));
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

    document.getElementById('toastContainer')
        .insertAdjacentHTML('beforeend', html);

    const el = document.getElementById(id);
    const toast = new bootstrap.Toast(el, { delay: 3000 });
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}
</script>
@endpush
