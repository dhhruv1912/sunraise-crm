@extends('temp.common')

@section('title', 'Create Quote Request')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-plus me-2"></i>
                        Create Quote Request
                    </h4>
                    <div class="text-muted small">
                        Manual entry via CRM
                    </div>
                </div>

                <a href="{{ route('quote_requests.view.list') }}" class="btn btn-light btn-sm">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
            </div>

            {{-- ================= FORM ================= --}}
            <form id="quoteRequestForm">

                <div class="row g-3">

                    {{-- LEFT --}}
                    <div class="col-md-8">

                        {{-- CUSTOMER --}}
                        <div class="crm-section">
                            <div class="fw-semibold mb-2">
                                <i class="fa-solid fa-user me-1"></i>
                                Customer Details
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="text" name="mobile" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Assigned To</label>
                                    <select name="assigned_to" class="form-select">
                                        <option value="">Auto / Unassigned</option>
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}">
                                                {{ $u->fname }} {{ $u->lname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        {{-- REQUIREMENT --}}
                        <div class="crm-section mt-3">
                            <div class="fw-semibold mb-2">
                                <i class="fa-solid fa-bolt me-1"></i>
                                Requirement Details
                            </div>

                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="call">Call</option>
                                        <option value="quote">Quote</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Capacity (kW)</label>
                                    <input type="number" step="0.01" name="kw" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Budget (₹)</label>
                                    <input type="number" name="budget" class="form-control">
                                </div>
                            </div>

                            <div class="mt-2">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Any special requirement or remark"></textarea>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT --}}
                    <div class="col-md-4">

                        {{-- ACTION --}}
                        <div class="crm-section">
                            <div class="fw-semibold mb-2">
                                <i class="fa-solid fa-gears me-1"></i>
                                Action
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                <i class="fa-solid fa-save me-1"></i>
                                Create Quote Request
                            </button>

                            <div class="text-muted small mt-2">
                                Quote request will be created with status
                                <strong>New Request</strong>
                            </div>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- ================= LOADER ================= --}}
    <div class="crm-loader-overlay d-none" id="formLoader">
        <div class="crm-spinner"></div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('quoteRequestForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const loader = document.getElementById('formLoader');
            const btn = document.getElementById('submitBtn');

            loader.classList.remove('d-none');
            btn.setAttribute('disabled', true);

            const data = Object.fromEntries(new FormData(form).entries());

            crmFetch(`{{ route('quote_requests.ajax.store') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => {
                    if (!res.ok) throw res;
                    return res.json();
                })
                .then(res => {
                    showToast('success', res.message);
                    setTimeout(() => {
                        window.location.href = `{{ route('quote_requests.view.list') }}`;
                    }, 700);
                })
                .catch(async err => {
                    let msg = 'Failed to create quote request';
                    try {
                        const data = await err.json();
                        msg = data.message || msg;
                    } catch {}
                    showToast('danger', msg);
                    btn.removeAttribute('disabled');
                })
                .finally(() => {
                    loader.classList.add('d-none');
                });
        });
    </script>
@endpush
