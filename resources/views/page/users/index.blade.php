@extends('temp.common')

@section('title', 'Users')

@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">
                    <i class="fa-solid fa-users me-2"></i> Users
                </h4>
                <div class="text-muted small">
                    Manage system users
                </div>
            </div>
            @can("users.edit")
                <a href="{{ route('users.view.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> New User
                </a>
            @endcan
        </div>

        {{-- ================= WIDGETS ================= --}}
        <div class="row g-3 mt-2 position-relative" id="userWidgets" style="min-height: 100px">
            <div class="crm-loader-overlay">
                <div class="crm-spinner"></div>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
        <div class="crm-section mt-3">
            <div class="crm-table-wrapper position-relative">
                <table class="table crm-table mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTable">
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
            <div class="d-flex justify-content-between mt-2">
                <div id="tableInfo"></div>
                <div id="pagination"></div>
            </div>
            {{-- <div class="d-flex justify-content-end mt-2" id="tableInfo"></div> --}}
        </div>
    </div>
</div>

{{-- ================= CONFIRM STATUS MODAL ================= --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <h5 id="statusModalTitle" class="mb-2"></h5>
                <p id="statusModalText" class="text-muted mb-4"></p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="confirmStatusBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- ================= TOAST ================= --}}
@endsection


@push('scripts')
<script>
const USER_LIST_URL   = "{{ route('users.ajax.list') }}";
const USER_WIDGET_URL = "{{ route('users.ajax.widgets') }}";

/* ================= INIT ================= */
document.addEventListener('DOMContentLoaded', () => {
    loadUserWidgets();
    loadUsers();

 statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
});

/* ================= WIDGETS ================= */
function loadUserWidgets(){
    crmFetch(USER_WIDGET_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById('userWidgets').innerHTML = html;
        });
}

/* ================= USERS ================= */
function loadUsers(){
    const table = document.getElementById('tableLoader');
    table.classList.remove('d-none');

    crmFetch(USER_LIST_URL)
        .then(res => res.json())
        .then(res => {
            renderTable(res);
            renderPagination(res.users);
        })
        .finally(() => table.classList.add('d-none'));
}

function renderTable(data) {

    const users   = data.users.data || [];
    const canEdit = !!data.canEdit;
    const tbody   = document.getElementById('userTable');

    if (!tbody) return;

    tbody.innerHTML = '';

    if (!users.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-muted py-4">
                    No users found
                </td>
            </tr>`;
        return;
    }

    let rows = '';

    users.forEach(u => {

        const statusBadge = u.status
            ? 'bg-success-subtle text-success'
            : 'bg-danger-subtle text-danger';

        const statusText = u.status ? 'Active' : 'Inactive';

        rows += `
        <tr>
            <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="crm-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">${u.fname || ''} ${u.lname || ''}</div>
                        <div class="text-muted small">${u.email || '-'}</div>
                    </div>
                </div>
            </td>

            <td>
                <span class="badge rounded-pill ${statusBadge}">
                    ${statusText}
                </span>
            </td>

            <td class="text-end">
                <div class="d-inline-flex gap-1">

                    ${
                        canEdit
                        ? `
                            <a href="/users/edit/${u.id}" class="btn btn-sm btn-light">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                          `
                        : ''
                    }

                    <button 
                        class="btn btn-sm btn-light"
                        ${!canEdit ? 'disabled' : ''}
                        onclick="confirmStatus(${u.id}, ${u.status ? 1 : 0})"
                    >
                        <i class="fa-solid fa-power-off"></i>
                    </button>

                </div>
            </td>
        </tr>`;
    });

    tbody.innerHTML = rows;
}
function renderPagination(res){
    document.getElementById('tableInfo').innerText =
        `Showing ${res.from} to ${res.to} of ${res.total}`;

    let html = `<ul class="pagination pagination-sm mb-0">`;
    for(let i=1;i<=res.last_page;i++){
        html += `
            <li class="page-item ${i === res.current_page ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadUsers(${i})">${i}</a>
            </li>`;
    }
    html += `</ul>`;
    document.getElementById('pagination').innerHTML = html;
}

/* ================= STATUS ================= */
function confirmStatus(userId, currentStatus){
    selectedUserId = userId;
    selectedStatus = currentStatus == 1 ? 0 : 1;

    document.getElementById('statusModalTitle').innerText =
        selectedStatus == 1 ? 'Activate User' : 'Deactivate User';

    document.getElementById('statusModalText').innerText =
        selectedStatus == 1
            ? 'This user will regain system access.'
            : 'This user will be blocked from the system.';

    document.getElementById('confirmStatusBtn').onclick = updateStatus;
    statusModal.show();
}

function updateStatus(){
    crmFetch(`/users/ajax/status/${selectedUserId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: selectedStatus })
    })
    .then(res => res.json())
    .then(res => {
        statusModal.hide();
        showToast('success', res.message || 'User updated successfully');
        loadUsers();
    })
    .catch(() => {
        showToast('danger', 'Failed to update user status');
    });
}

/* ================= TOAST ================= */
function showToast(type, message){
    const id = `toast-${Date.now()}`;

    const toastHtml = `
        <div id="${id}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>`;

    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', toastHtml);

    const toastEl = document.getElementById(id);
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

/* ================= UTIL ================= */
function debounce(fn, delay){
    let t;
    return function(){
        clearTimeout(t);
        t = setTimeout(() => fn(), delay);
    }
}
</script>
@endpush
