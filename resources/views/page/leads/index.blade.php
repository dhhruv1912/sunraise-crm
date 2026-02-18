@extends('temp.common')

@section('title', 'Leads')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-user-group me-2"></i>
                        Leads
                    </h4>
                    <div class="text-muted small">
                        Sales-qualified customer opportunities
                    </div>
                </div>
            </div>

            {{-- WIDGETS --}}
            <div class="row g-3 mt-2 position-relative" id="leadWidgets" style="min-height: 100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="crm-section mt-3">
                <div class="row g-3 align-items-end">

                    <div class="col-md-2">
                        <label class="form-label small">Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">All</option>
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="site_visit_planned">Site Visit Planned</option>
                            <option value="site_visited">Site Visited</option>
                            <option value="negotiation">Negotiation</option>
                            <option value="converted">Converted</option>
                            <option value="dropped">Dropped</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="small text-muted form-label">Rows</label>
                        <select id="leadPerPage"
                                class="form-select"
                                onchange="changeLeadPerPage()">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Follow Up</label>
                        <select id="filterFollowup" class="form-select">
                            <option value="">All</option>
                            <option value="today">Follow-ups Today</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>


                    <div class="col-md-4">
                        <label class="form-label small">Search</label>
                        <input type="text" id="searchBox" class="form-control" placeholder="Customer name or mobile">
                    </div>

                    <div class="col-md-2 text-end">
                        <button class="btn btn-primary" onclick="loadLeads()">
                            <i class="fa-solid fa-filter me-1"></i>
                            Apply
                        </button>
                    </div>

                </div>
            </div>

            {{-- TABLE --}}
            <div class="crm-section mt-3">
                <div class="crm-table-wrapper position-relative">

                    <table class="table crm-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Lead Info</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="leadTable">
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="5">
                                        <div class="crm-skeleton"></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div class="crm-loader-overlay d-none" id="leadLoader">
                        <div class="crm-spinner"></div>
                    </div>

                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small" id="leadPaginationInfo"></div>
                    <div class="btn-group" id="leadPaginationBtns"></div>
                </div>
            </div>

        </div>
    </div>

    {{-- GLOBAL PAGE LOADER --}}
    <div class="crm-loader-overlay d-none fixed" id="listLoader">
        <div class="crm-spinner"></div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let leadStatusChart = null;

        const LIST_URL = "{{ route('leads.ajax.list') }}";
        const WIDGET_URL = "{{ route('leads.ajax.widgets') }}";

        document.addEventListener('DOMContentLoaded', () => {
            loadWidgets();
            loadLeads();
        });

        /* ================= WIDGETS ================= */
        function loadWidgets() {
            crmFetch(WIDGET_URL)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('leadWidgets').innerHTML = html;
                    // loadCharts();
                });
        }

        function loadCharts() {
            crmFetch("{{ route('leads.ajax.charts') }}")
                .then(res => res.json())
                .then(data => {
                    renderStatusChart(data.status);
                });
        }

        function renderStatusChart(data) {
            const widgetLoader = document.getElementById('widgetLoader');
            widgetLoader.classList.add('d-none');
            const ctx = document.getElementById('leadStatusDonut');
            if (!ctx) return;

            if (leadStatusChart) leadStatusChart.destroy();

            leadStatusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        data: Object.values(data),
                        backgroundColor: [
                            '#5FA6D6',
                            '#FFB457',
                            '#56CA00',
                            '#E5533D'
                        ]
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    onClick(_, elements) {
                        if (!elements.length) return;
                        const status = Object.keys(data)[elements[0].index];
                        document.getElementById('filterStatus').value = status;
                        loadLeads();
                    }
                }
            });
        }

        /* ================= LIST ================= */
        let leadPage = 1;
        let leadPerPage = 10;

        function loadLeads(page = 1) {
            leadPage = page;

            const loader = document.getElementById('leadLoader');
            loader.classList.remove('d-none');

            const params = new URLSearchParams({
                page: leadPage,
                per_page: leadPerPage,
                status: document.getElementById('filterStatus')?.value || '',
                search: document.getElementById('searchBox')?.value || ''
            });

            crmFetch(`/leads/ajax/list?${params}`)
                .then(res => res.json())
                .then(res => {
                    renderRows(res.data,res.canEdit);
                    renderLeadPagination(res.pagination);
                })
                .finally(() => loader.classList.add('d-none'));
        }

        function changeLeadPerPage() {
            leadPerPage =
                document.getElementById('leadPerPage').value;

            leadPage = 1;
            loadLeads(1);
        }

        function renderLeadPagination(meta) {
            const info = document.getElementById('leadPaginationInfo');
            const btns = document.getElementById('leadPaginationBtns');

            info.innerText =
                `Page ${meta.current_page} of ${meta.last_page} • ${meta.total} leads`;

            btns.innerHTML = '';

            if (meta.current_page > 1) {
                btns.innerHTML += `
                    <button class="btn btn-sm btn-light"
                            onclick="loadLeads(${meta.current_page - 1})">
                        ‹ Prev
                    </button>`;
            }

            if (meta.current_page < meta.last_page) {
                btns.innerHTML += `
                    <button class="btn btn-sm btn-light"
                            onclick="loadLeads(${meta.current_page + 1})">
                        Next ›
                    </button>`;
            }
        }


        function renderRows(rows,canEdit) {
            const tbody = document.getElementById('leadTable');
            tbody.innerHTML = '';

            if (!rows.length) {
                tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    No leads found
                </td>
            </tr>`;
                return;
            }

            rows.forEach(l => {
                // const isConverted = false;
                const isConverted = l.status === 'converted';

                tbody.innerHTML += `
            <tr>

                {{-- CUSTOMER --}}
                <td>
                    <div class="fw-semibold">
                        ${l.customer_name ?? '—'}
                    </div>
                    <div class="text-muted small">
                        ${l.mobile ?? ''}
                    </div>
                </td>

                {{-- LEAD --}}
                <td>
                    <div class="fw-semibold text-primary">
                        ${l.lead_code}
                    </div>
                    <div class="text-muted small">
                        From QR #${l.quote_request_id ?? '—'}
                    </div>
                </td>

                {{-- STATUS --}}
                <td>
                    <span class="badge bg-info-subtle text-info text-capitalize">
                        ${l.status.replaceAll('_',' ')}
                    </span>
                </td>

                {{-- ACTIONS --}}
                <td class="text-end">
                    <div class="btn-group row-actions">

                        <a href="/leads/${l.id}"
                           class="btn btn-sm btn-light"
                           title="View Lead">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        ${
                            !isConverted && canEdit
                            ? `
                                    <a href="/leads/${l.id}/edit"
                                       class="btn btn-sm btn-light"
                                       title="Edit Lead">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                  `
                            : ''
                        }

                    </div>
                </td>

            </tr>
        `;
            });
        }
    </script>
@endpush
