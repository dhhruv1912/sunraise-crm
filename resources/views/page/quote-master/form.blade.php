@extends('temp.common')

@section('title', $mode === 'create' ? 'Create Quote Package' : 'Edit Quote Package')

@section('content')
<div class="container-fluid">
    <div class="crm-page">

        {{-- ================= HEADER ================= --}}
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">
                    <i class="fa-solid fa-solar-panel me-2"></i>
                    {{ $mode === 'create' ? 'Create Quote Package' : 'Edit Quote Package' }}
                </h4>
                <div class="text-muted small">
                    Pricing & capacity definition
                </div>
            </div>

            <a href="{{ route('quote_master.view.list') }}"
               class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>
                Back
            </a>
        </div>

        {{-- ================= FORM ================= --}}
        <form id="quoteMasterForm" class="row g-3 mt-3">

            {{-- LEFT: MAIN FORM --}}
            <div class="col-md-8">

                {{-- BASIC INFO --}}
                <div class="crm-section mb-3">
                    <div class="fw-semibold mb-2">
                        <i class="fa-solid fa-circle-info me-1 text-primary"></i>
                        Basic Information
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control"
                                   value="{{ $row->sku ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Module Name *</label>
                            <input type="text" name="module" class="form-control" required
                                   value="{{ $row->module ?? '' }}">
                        </div>
                    </div>
                </div>

                {{-- CAPACITY --}}
                <div class="crm-section mb-3">
                    <div class="fw-semibold mb-2">
                        <i class="fa-solid fa-bolt me-1 text-warning"></i>
                        Capacity
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Capacity (kW) *</label>
                            <input type="number" step="0.001" name="kw"
                                   class="form-control calc"
                                   value="{{ $row->kw ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Panel Count</label>
                            <input type="number" name="module_count"
                                   class="form-control"
                                   value="{{ $row->module_count ?? '' }}">
                        </div>
                    </div>
                </div>

                {{-- PRICING --}}
                <div class="crm-section mb-3">
                    <div class="fw-semibold mb-2">
                        <i class="fa-solid fa-indian-rupee-sign me-1 text-success"></i>
                        Pricing Breakdown
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Base Value *</label>
                            <input type="number" name="value"
                                   class="form-control calc"
                                   value="{{ $row->value ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Taxes</label>
                            <input type="number" name="taxes"
                                   class="form-control calc"
                                   value="{{ $row->taxes ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Metering Cost</label>
                            <input type="number" name="metering_cost"
                                   class="form-control calc"
                                   value="{{ $row->metering_cost ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">MCB / PPA</label>
                            <input type="number" name="mcb_ppa"
                                   class="form-control calc"
                                   value="{{ $row->mcb_ppa ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Subsidy</label>
                            <input type="number" name="subsidy"
                                   class="form-control calc"
                                   value="{{ $row->subsidy ?? '' }}">
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT: SUMMARY --}}
            <div class="col-md-4">
                <div class="crm-section position-sticky" style="top:80px">

                    <div class="fw-semibold mb-3">
                        <i class="fa-solid fa-calculator me-1 text-info"></i>
                        Price Summary
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Calculated Payable</span>
                        <strong id="payablePreview">₹ 0</strong>
                    </div>

                    <input type="hidden"
                           name="payable"
                           id="payableInput"
                           value="{{ $row->payable ?? 0 }}">

                    <hr>

                    <button type="submit"
                            id="saveBtn"
                            class="btn btn-primary w-100">
                        <i class="fa-solid fa-save me-1"></i>
                        {{ $mode === 'create' ? 'Create Package' : 'Update Package' }}
                    </button>
                </div>
            </div>

        </form>

        {{-- Loader --}}
        <div class="crm-loader-overlay d-none" id="formLoader">
            <div class="crm-spinner"></div>
        </div>

    </div>
</div>

<div id="toastContainer"
     class="toast-container position-fixed top-0 end-0 p-3"></div>
@endsection


@push('scripts')
<script>
const FORM_URL = "{{ $mode === 'create'
    ? route('quote_master.ajax.store')
    : route('quote_master.ajax.update', $row->id ?? 0) }}";

/* ================= AUTO CALC ================= */
function calcPayable(){
    const get = name =>
        Number(document.querySelector(`[name="${name}"]`)?.value || 0);

    const total =
        get('value') +
        get('taxes') +
        get('metering_cost') +
        get('mcb_ppa') -
        get('subsidy');

    document.getElementById('payablePreview').innerText =
        '₹ ' + total.toLocaleString();

    document.getElementById('payableInput').value = total;
}

document.querySelectorAll('.calc')
    .forEach(i => i.addEventListener('input', calcPayable));

calcPayable();

/* ================= SUBMIT ================= */
document.getElementById('quoteMasterForm')
    .addEventListener('submit', function (e) {
        e.preventDefault();

        const loader = document.getElementById('formLoader');
        const btn = document.getElementById('saveBtn');

        loader.classList.remove('d-none');
        btn.disabled = true;
        btn.innerHTML =
            '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving';

        crmFetch(FORM_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]').content
            },
            body: new FormData(this)
        })
        .then(res => res.json())
        .then(res => {
            showToast('success', res.message);
            setTimeout(() =>
                window.location.href =
                    "{{ route('quote_master.view.list') }}", 800);
        })
        .finally(() => {
            loader.classList.add('d-none');
            btn.disabled = false;
            btn.innerHTML =
                '<i class="fa-solid fa-save me-1"></i> Save';
        });
});
</script>
@endpush
