@extends('temp.common')

@section('title', 'Role Permissions')

@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">
                    <i class="fa-solid fa-shield-halved me-2"></i>
                    Role Permissions
                </h4>
                <div class="text-muted small">
                    Role: <strong class="text-capitalize">{{ $role->name }}</strong>
                </div>
            </div>
            <a href="{{ route('roles.view.list') }}" class="btn btn-outline-secondary">
                Back
            </a>
        </div>

        {{-- ================= WIDGETS (AJAX) ================= --}}
        <div class="row g-3 mt-2 position-relative" id="permissionWidgets" style="min-height: 100px">
            {{-- loader --}}
            <div class="crm-loader-overlay">
                <div class="crm-spinner"></div>
            </div>
        </div>

        {{-- ================= MATRIX ================= --}}
        <div class="crm-section mt-3" id="permissionSection">

            @foreach($permissions as $module => $perms)
                <div class="mb-4">
                    <div class="crm-section-header">
                        <div class="crm-section-title">
                            <i class="fa-solid fa-layer-group"></i>
                            {{ ucfirst($module) }}
                        </div>
                        <div>
                            <button class="btn btn-sm btn-light"
                                    onclick="selectAll('{{ $module }}')">
                                Select All
                            </button>
                            <button class="btn btn-sm btn-light"
                                    onclick="clearAll('{{ $module }}')">
                                Clear
                            </button>
                        </div>
                    </div>

                    <div class="row g-2">
                        @foreach($perms as $perm)
                            <div class="col-md-3">
                                <label class="d-flex align-items-center gap-2 p-2 border rounded">
                                    <input type="checkbox"
                                           class="form-check-input perm {{ $module }}"
                                           value="{{ $perm->name }}"
                                           @checked($role->hasPermissionTo($perm->name))>
                                    <span class="small">
                                        {{ str_replace($module.'.', '', $perm->name) }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="text-end mt-4">
                <button id="saveBtn"
                        class="btn btn-primary btn-lg"
                        onclick="savePermissions()">
                    <i class="fa-solid fa-save me-1"></i>
                    Save Changes
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ================= TOAST ================= --}}
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>
@endsection


@push('scripts')
<script>
const WIDGET_URL = "{{ route('roles.ajax.permissions.widgets', $role->id) }}";
const SAVE_URL   = "{{ route('roles.ajax.permissions', $role->id) }}";

/* ================= LOAD WIDGETS ================= */
function loadWidgets(){
    const box = document.getElementById('permissionWidgets');
    box.innerHTML = `
        <div class="crm-loader-overlay">
            <div class="crm-spinner"></div>
        </div>`;

    crmFetch(WIDGET_URL)
        .then(res => res.text())
        .then(html => box.innerHTML = html);
}

document.addEventListener('DOMContentLoaded', loadWidgets);

/* ================= SELECT HELPERS ================= */
function selectAll(module){
    document.querySelectorAll(`.${module}`).forEach(el => el.checked = true);
}

function clearAll(module){
    document.querySelectorAll(`.${module}`).forEach(el => el.checked = false);
}

/* ================= SAVE ================= */
function savePermissions(){
    const section = document.getElementById('permissionSection');
    const btn = document.getElementById('saveBtn');

    btn.classList.add('btn-loading');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving';

    showOverlay(section);

    const permissions = [...document.querySelectorAll('.perm:checked')]
        .map(el => el.value);

    crmFetch(SAVE_URL, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ permissions })
    })
    .then(res => res.json())
    .then(res => {
        showToast('success', res.message);
        loadWidgets(); // 🔥 REFRESH WIDGETS
    })
    .catch(() => showToast('danger', 'Failed to save permissions'))
    .finally(() => {
        hideOverlay(section);
        btn.classList.remove('btn-loading');
        btn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Save Changes';
    });
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
