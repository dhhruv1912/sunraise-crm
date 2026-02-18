@extends('temp.common')

@section('title', 'Quote Request')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-file-lines me-2"></i>
                        Quote Request
                    </h4>
                    <div class="text-muted small">
                        ID #{{ $data->id }} • Created by {{ optional($data->creator)->fname }}
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <span class="badge bg-info-subtle text-info text-capitalize align-content-around">
                        {{ str_replace('_', ' ', $data->status) }}
                    </span>

                    <a href="{{ route('quote_requests.view.list') }}" class="btn btn-light btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="row g-3">

                {{-- ================= LEFT ================= --}}
                <div class="col-md-8">

                    {{-- CUSTOMER --}}
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-user me-1"></i>
                            Customer
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="text-muted small">Name</div>
                                <div class="fw-semibold">{{ optional($data->customer)->name }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Mobile</div>
                                <div class="fw-semibold">{{ optional($data->customer)->mobile }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Email</div>
                                <div class="fw-semibold">{{ optional($data->customer)->email }}</div>
                            </div>

                            <div class="col-md-6">
                                <div class="text-muted small">Address</div>
                                <div>{{ optional($data->customer)->address ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- REQUIREMENT --}}
                    <div class="crm-section mt-3">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-bolt me-1"></i>
                            Requirement
                        </div>

                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="text-muted small">Type</div>
                                <div class="fw-semibold">{{ $data->type }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">Capacity</div>
                                <div class="fw-semibold">{{ $data->kw }} kW</div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small">Budget</div>
                                <div class="fw-semibold">
                                    {{ $data->budget ? '₹ ' . number_format($data->budget) : '—' }}
                                </div>
                            </div>
                        </div>

                        @if ($data->notes)
                            <div class="mt-2">
                                <div class="text-muted small">Notes</div>
                                <div>{{ $data->notes }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- QUOTE MASTER --}}
                    <div class="crm-section mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">
                                <i class="fa-solid fa-box me-1"></i>
                                Quote Master
                            </div>

                            <button class="btn btn-sm btn-primary" id="updateQuoteMasterBtn" disabled>
                                <i class="fa-solid fa-save me-1"></i>
                                Update
                            </button>
                        </div>

                        <select class="form-select mb-3" id="quoteMasterSelect" data-id="{{ $data->id }}">
                            <option value="">Select Quote Package</option>
                            @foreach ($master as $m)
                                <option value="{{ $m->id }}"
                                    {{ optional($data->quoteMaster)->id == $m->id ? 'selected' : '' }}>
                                    {{ $m->module }} — {{ $m->kw }} kW
                                </option>
                            @endforeach
                        </select>

                        <dl class="row g-2 mb-0">

                            <dt class="col-sm-4 text-muted">SKU</dt>
                            <dd id="qm-sku" class="col-sm-8">
                                {{ optional($data->quoteMaster)->sku ?? '—' }}
                            </dd>

                            <dt class="col-sm-4 text-muted">Module</dt>
                            <dd id="qm-module" class="col-sm-8">
                                {{ optional($data->quoteMaster)->module ?? '—' }}
                            </dd>

                            <dt class="col-sm-4 text-muted">Capacity (kW)</dt>
                            <dd id="qm-kw" class="col-sm-8">
                                {{ optional($data->quoteMaster)->kw ?? '—' }}
                            </dd>

                            <dt class="col-sm-4 text-muted">Module Count</dt>
                            <dd id="qm-module-count" class="col-sm-8">
                                {{ optional($data->quoteMaster)->module_count ?? '—' }}
                            </dd>

                            <dt class="col-sm-4 text-muted">Base Value</dt>
                            <dd id="qm-value" class="col-sm-8">
                                ₹ {{ number_format(optional($data->quoteMaster)->value ?? 0) }}
                            </dd>

                            <dt class="col-sm-4 text-muted">Taxes</dt>
                            <dd id="qm-taxes" class="col-sm-8">
                                ₹ {{ number_format(optional($data->quoteMaster)->taxes ?? 0) }}
                            </dd>

                            <dt class="col-sm-4 text-muted">Metering Cost</dt>
                            <dd id="qm-metering" class="col-sm-8">
                                ₹ {{ number_format(optional($data->quoteMaster)->metering_cost ?? 0) }}
                            </dd>

                            <dt class="col-sm-4 text-muted">MCB / PPA</dt>
                            <dd id="qm-mcb" class="col-sm-8">
                                ₹ {{ number_format(optional($data->quoteMaster)->mcb_ppa ?? 0) }}
                            </dd>

                            <dt class="col-sm-4 fw-semibold text-primary">Payable</dt>
                            <dd id="qm-payable" class="col-sm-8 fw-bold text-primary">
                                ₹ {{ number_format(optional($data->quoteMaster)->payable ?? 0) }}
                            </dd>

                            <dt class="col-sm-4 fw-semibold text-warning">Subsidy</dt>
                            <dd id="qm-subsidy" class="col-sm-8 fw-bold text-warning">
                                ₹ {{ number_format(optional($data->quoteMaster)->subsidy ?? 0) }}
                            </dd>

                            <dt class="col-sm-4 fw-semibold text-success">Projected</dt>
                            <dd id="qm-projected" class="col-sm-8 fw-bold text-success">
                                ₹ {{ number_format(optional($data->quoteMaster)->projected ?? 0) }}
                            </dd>
                        </dl>
                    </div>

                    {{-- RELATED PROJECTS --}}
                    @if ($projects && $projects->count())
                        <div class="crm-section mt-3">
                            <div class="fw-semibold mb-2">
                                <i class="fa-solid fa-diagram-project me-1"></i>
                                Related Projects
                            </div>

                            <ul class="list-group list-group-flush">
                                @foreach ($projects as $p)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>{{ $p->project_code }}</span>
                                        <span class="text-muted small">{{ $p->status }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>

                {{-- ================= RIGHT ================= --}}
                <div class="col-md-4">

                    {{-- ASSIGN --}}
                    <div class="crm-section">
                        <div class="fw-semibold mb-2">
                            <i class="fa-solid fa-user-check me-1"></i>
                            Assigned To
                        </div>

                        <select class="form-select" id="assignUserSelect" data-id="{{ $data->id }}">
                            <option value="">Unassigned</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ $data->assigned_to == $u->id ? 'selected' : '' }}>
                                    {{ $u->fname }} {{ $u->lname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="crm-section mt-3">
                        <div class="fw-semibold mb-2 hookup-2">
                            <i class="fa-solid fa-gears me-1"></i>
                            Actions
                        </div>

                        <div class="d-grid gap-2">

                            <button class="btn btn-outline-info" onclick="sendQuoteEmail({{ $data->id }})">
                                <i class="fa-solid fa-envelope"></i>
                                {{$data->quote_email_sent_at ? "Resend" :"Send" }} Quote Email
                            </button>

                            <button class="btn btn-outline-success"
                                    id="convertLeadBtn"
                                    {{ !$data->quote_email_sent_at || $data->lead ? 'disabled' : '' }}
                                    title="{{ !$data->quote_email_sent_at ? 'Send quote email first' : '' }}"
                                    onclick="convertToLead({{ $data->id }})">
                                <i class="fa-solid fa-user-plus"></i>
                                Convert to Lead
                            </button>

                            @if(!$data->quote_email_sent_at)
                                <div class="text-muted small mt-1">
                                    <i class="fa-solid fa-circle-info"></i>
                                    Quote email must be sent before converting to lead
                                </div>
                            @endif

                            @if($data->lead)
                                <div class="text-success small mt-1">
                                    <i class="fa-solid fa-check-circle"></i>
                                    Already converted to Lead
                                </div>
                            @endif

                        </div>
                    </div>

                </div>

            </div>


            {{-- ================= HISTORY ================= --}}
            <div class="crm-section mt-4">
                <div class="fw-semibold mb-3">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i>
                    Activity Timeline
                </div>

                @forelse($history as $h)
                    <div class="d-flex gap-3 mb-2">
                        <div class="text-muted small" style="width:140px">
                            {{ $h['datetime'] }}
                        </div>
                        <div>
                            <div class="fw-semibold">
                                {{ $h['action'] }}
                            </div>
                            <div class="text-muted small">
                                {{ $h['message'] }} — {{ $h['user'] }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted small">No activity yet</div>
                @endforelse
            </div>

        </div>
    </div>
    <div class="crm-loader-overlay fixed d-none" id="emailLoader">
        <div class="crm-spinner"></div>
    </div>
@endsection
@push('scripts')
    <script>
        /* ================= ASSIGN USER ================= */
        document.getElementById('assignUserSelect')
            ?.addEventListener('change', function() {

                crmFetch(`{{ route('quote_requests.ajax.assign_user', $data->id) }}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_id: this.value || null
                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        showToast('success', res.message);
                    });
            });

        /* ================= QUOTE MASTER ================= */
        const qmSelect = document.getElementById('quoteMasterSelect');
        const qmBtn = document.getElementById('updateQuoteMasterBtn');

        const qmFields = {
            sku: 'qm-sku',
            module: 'qm-module',
            kw: 'qm-kw',
            module_count: 'qm-module-count',
            value: 'qm-value',
            taxes: 'qm-taxes',
            metering_cost: 'qm-metering',
            mcb_ppa: 'qm-mcb',
            payable: 'qm-payable',
            subsidy: 'qm-subsidy',
            projected: 'qm-projected'
        };

        qmSelect?.addEventListener('change', function() {
            const qmId = this.value;
            qmBtn.disabled = true;

            if (!qmId) return;

            // loader effect (optional)
            Object.values(qmFields).forEach(id => {
                document.getElementById(id).innerText = '—';
            });

            crmFetch(`{{ route('quote_master.ajax.single', '') }}/${qmId}`)
                .then(res => res.json())
                .then(data => {

                    Object.keys(qmFields).forEach(key => {
                        const el = document.getElementById(qmFields[key]);
                        if (!el) return;

                        if (['value', 'taxes', 'metering_cost', 'mcb_ppa', 'payable', 'subsidy',
                                'projected'
                            ].includes(key)) {
                            el.innerText = '₹ ' + Number(data[key] || 0).toLocaleString();
                        } else {
                            el.innerText = data[key] ?? '—';
                        }
                    });

                    qmBtn.disabled = false;
                })
                .catch(() => {
                    showToast('danger', 'Failed to load quote master details');
                });
        });

        qmBtn?.addEventListener('click', () => {
            qmBtn.disabled = true;
            qmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Updating';

            crmFetch(`{{ route('quote_requests.ajax.update_quote_master', $data->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        quote_master_id: qmSelect.value
                    })
                })
                .then(res => res.json())
                .then(res => {
                    showToast('success', res.message);
                })
                .finally(() => {
                    qmBtn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Update';
                });
        });

        function sendQuoteEmail(id) {
            const loader = document.getElementById('emailLoader');

            loader.classList.remove('d-none');

            crmFetch(`{{ route('quote_requests.ajax.send_email', '') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(res => {
                    showToast('success', res.message);
                    document.getElementById('convertLeadBtn')?.removeAttribute('disabled');
                })
                .catch(() => {
                    showToast('danger', 'Failed to send email');
                })
                .finally(() => {
                    loader.classList.add('d-none');
                });
        }

        function convertToLead(id) {
    const loader = document.getElementById('convertLeadLoader');
    const btn    = document.getElementById('convertLeadBtn');

    if (!btn || btn.hasAttribute('disabled')) return;

    loader.classList.remove('d-none');
    btn.setAttribute('disabled', true);

    crmFetch(`{{ route('quote_requests.ajax.convert_to_lead', '') }}/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(res => {
        if (!res.ok) throw res;
        return res.json();
    })
    .then(res => {
        showToast('success', res.message);

        // Optional: redirect to lead view
        setTimeout(() => {
            window.location.href = `/leads/${res.lead_id}`;
        }, 800);
    })
    .catch(async (err) => {
        let msg = 'Failed to convert to lead';
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
}
    </script>
@endpush
