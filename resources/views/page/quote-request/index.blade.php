@extends('temp.common')

@section('title', 'Quote Requests')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-file-signature me-2"></i>
                        Quote Requests
                    </h4>
                    <div class="text-muted small">
                        Incoming customer pricing requests
                    </div>
                </div>
                @can("quote.request.edit")
                    <div class="d-flex gap-2">
                        <a href="{{ route('quote_requests.view.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus me-1"></i>
                            New Quote Request
                        </a>

                        <button class="btn btn-outline-secondary btn-sm" onclick="openImportModal()">
                            <i class="fa-solid fa-file-excel me-1"></i>
                            Import
                        </button>
                    </div>
                @endcan
            </div>

            {{-- WIDGETS --}}
            <div class="row g-3 mt-2 position-relative" id="qrWidgets" style="min-height: 100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="crm-section mt-3">
                <div class="row g-3 align-items-end">

                    <div class="col-md-2">
                        <label class="form-label small">Per Page</label>
                        <select id="perPage" class="form-select" onchange="loadQuotations(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">All</option>
                            @foreach ($status as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Type</label>
                        <select id="filterType" class="form-select">
                            <option value="">All</option>
                            <option value="quote">Quote</option>
                            <option value="call">Call</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small">Search</label>
                        <input type="text" id="searchBox" class="form-control" placeholder="Customer name or mobile">
                    </div>

                    <div class="col-md-2 text-end">
                        <button class="btn btn-primary" onclick="loadRequests()">
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
                                <th>Requirement</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="qrTable">
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="4">
                                        <div class="crm-skeleton"></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div class="crm-loader-overlay d-none" id="qrLoader">
                        <div class="crm-spinner"></div>
                    </div>

                </div>
                <div class="d-flex justify-content-end mt-2" id="quoteRequestPagination"></div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title">
                        <i class="fa-solid fa-file-excel me-1"></i>
                        Import Quote Requests
                    </h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="file" id="importFile" class="form-control" accept=".xlsx,.csv">
                    <div class="form-text mt-1">
                        Upload Excel / CSV file
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light btn-sm" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="importQuoteRequests()">
                        Import
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div class="crm-loader-overlay d-none fixed" id="listLoader">
        <div class="crm-spinner"></div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let statusChart = null;
        let trendChart = null;
        let qrTotalCount = 0;
        const LIST_URL = "{{ route('quote_requests.ajax.list') }}";
        const WIDGET_URL = "{{ route('quote_requests.ajax.widgets') }}";

        document.addEventListener('DOMContentLoaded', () => {
            loadWidgets();
            loadRequests();
        });

        function loadWidgets() {
            crmFetch(WIDGET_URL)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('qrWidgets').innerHTML = html;
                    loadCharts(); // ⬅️ charts AFTER widgets exist
                });
        }

        function loadCharts() {
            crmFetch("{{ route('quote_requests.ajax.charts') }}")
                .then(res => res.json())
                .then(data => {
                    qrTotalCount = data.total;

                    renderStatusChart(data.status);
                    renderTrendChart(data.trend);
                    renderWeekdayHeatmap(data.weekday);
                    renderResponseTimeChart(data.response_time);
                    renderSla(data.sla);
                });
        }

        function renderWeekdayHeatmap(data) {
            const ctx = document.getElementById('weekdayHeatmap');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        data: Object.values(data),
                        backgroundColor: '#5FA6D6'
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        function renderResponseTimeChart(data) {
            const ctx = document.getElementById('responseTimeChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(data),
                    datasets: [{
                        data: Object.values(data).map(v => Math.round(v)),
                        backgroundColor: '#FFB457'
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            title: {
                                display: true,
                                text: 'Minutes'
                            }
                        }
                    }
                }
            });
        }

        function renderSla(data) {
            document.getElementById('slaCount').innerText = data.breach;

            if (data.breach > 0) {
                document.getElementById('slaWidget')
                    .onclick = () => filterStale();
            }
        }


        function renderStatusChart(data) {
            const statusDonutLoader = document.getElementById('statusDonutLoader');
            statusDonutLoader.classList.add('d-none');
            const ctx = document.getElementById('statusDonut');
            if (!ctx) return;

            if (statusChart) statusChart.destroy();

            const labels = Object.keys(data);
            const values = Object.values(data);

            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            '#5FA6D6', // new
                            '#FFB457', // pending
                            '#56CA00', // responded
                            '#E5533D' // others
                        ]
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const count = context.parsed;
                                    const percent = qrTotalCount ?
                                        ((count / qrTotalCount) * 100).toFixed(1) :
                                        0;

                                    return [
                                        `${context.label.replaceAll('_',' ')}`,
                                        `${count} requests`,
                                        `Conversion: ${percent}%`
                                    ];
                                }
                            }
                        }
                    },

                    // 🔥 CLICK → FILTER
                    onClick: function(_, elements) {
                        if (!elements.length) return;

                        const index = elements[0].index;
                        const status = labels[index];

                        filterByStatus(status);
                    }
                }
            });
        }

        function renderTrendChart(data) {
            const trendLoader = document.getElementById('trendLoader');
            trendLoader.classList.add('d-none');
            const ctx = document.getElementById('requestTrend');
            if (!ctx) return;

            if (trendChart) trendChart.destroy();

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(i => i.date),
                    datasets: [{
                        data: data.map(i => i.total),
                        borderColor: '#F28C28',
                        backgroundColor: 'rgba(242,140,40,0.15)',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }


        function loadRequests(page = 1) {
            const loader = document.getElementById('qrLoader');
            loader.classList.remove('d-none');

            const params = new URLSearchParams({
                page,
                status: document.getElementById('filterStatus').value,
                type: document.getElementById('filterType').value,
                search: document.getElementById('searchBox').value,
                per_page: document.getElementById('perPage').value,
            });

            crmFetch(LIST_URL + '?' + params)
                .then(res => res.json())
                .then(res => {
                    renderRows(res.data,res.canEdit)
                    renderPagination(res.pagination)
                })
                .finally(() => loader.classList.add('d-none'));
        }

        function renderPagination(meta) {
            const wrap = document.getElementById('quoteRequestPagination');
            wrap.innerHTML = '';

            if (meta.last_page <= 1) return;

            for (let i = 1; i <= meta.last_page; i++) {
                wrap.innerHTML += `
                    <button class="btn btn-sm ${
                        meta.current_page === i
                            ? 'btn-primary'
                            : 'btn-outline-secondary'
                    } me-1"
                    onclick="loadRequests(${i})">
                        ${i}
                    </button>
                `;
            }
        }

        /* FILTER HELPERS */
        function filterByStatus(status) {
            document.getElementById('filterStatus').value = status;
            loadRequests();

            // Smooth scroll to table
            document
                .querySelector('.crm-table-wrapper')
                .scrollIntoView({
                    behavior: 'smooth'
                });
        }


        function filterStale() {
            document.getElementById('filterStatus').value = 'new_request';
            loadRequests();
        }

        function renderRows(rows,canEdit) {
            const tbody = document.getElementById('qrTable');
            tbody.innerHTML = '';

            if (!rows.length) {
                tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    No quote requests found
                </td>
            </tr>`;
                return;
            }

            rows.forEach(r => {
                tbody.innerHTML += `
        <tr>
            <td>
                <div class="fw-semibold">${r.customer_name ?? '—'}</div>
                <div class="text-muted small">${r.mobile ?? ''}</div>
            </td>

            <td>
                ${r.kw ?? '-'} kW
                <div class="text-muted small">${r.type ?? '-'}</div>
            </td>

            <td>
                <span class="badge bg-info-subtle text-info">
                    ${r.status.replaceAll('_',' ')}
                </span>
            </td>

            <td class="text-end">
                ${canEdit ? `<div class="btn-group">
                    <a href="{{ url('/quote-requests') }}/${r.id}"
                    class="btn btn-sm btn-light"
                    title="View Request">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                </div>` : ""}
                
            </td>
        </tr>`;
            });
        }

        function updateStatus(id, status) {
            crmFetch(`{{ url('/quote-requests/ajax/status') }}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status
                    })
                })
                .then(res => res.json())
                .then(res => {
                    showToast('success', res.message);
                    loadRequests();
                    loadWidgets();
                });
        }

        function openImportModal() {
            new bootstrap.Modal(document.getElementById('importModal')).show();
        }

        function importQuoteRequests() {
            const fileInput = document.getElementById('importFile');
            if (!fileInput.files.length) {
                showToast('warning', 'Please select a file');
                return;
            }

            const loader = document.getElementById('listLoader');
            loader.classList.remove('d-none');

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            fetch(`{{ route('quote_requests.ajax.import') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => {
                    if (!res.ok) throw res;
                    return res.json();
                })
                .then(res => {
                    showToast('success', res.message);
                    location.reload();
                })
                .catch(async err => {
                    let msg = 'Import failed';
                    try {
                        const data = await err.json();
                        msg = data.message || msg;
                    } catch {}
                    showToast('danger', msg);
                })
                .finally(() => {
                    loader.classList.add('d-none');
                });
        }
    </script>
@endpush
