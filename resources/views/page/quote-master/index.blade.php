@extends('temp.common')

@section('title', 'Quote Master')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-bolt me-2"></i>
                        Quote Master
                    </h4>
                    <div class="text-muted small">
                        System pricing & capacity master data
                    </div>
                </div>
                @can("quote.master.edit")
                    <a href="{{ route('quote_master.view.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-1"></i>
                        New Package
                    </a>
                    
                @endcan

            </div>

            {{-- ================= WIDGETS ================= --}}
            <div class="row g-3 mt-2 position-relative" id="quoteMasterWidgets" style="min-height: 100px;">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            <div class="crm-section mt-3">
                <div class="row g-3 align-items-end">


                    <div class="col-md-1">
                        <label class="form-label small">Per Page</label>
                        <select id="perPage" class="form-select" onchange="loadQuoteMaster(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">
                            <i class="fa-solid fa-bolt me-1"></i>
                            Capacity (kW)
                        </label>
                        <div class="d-flex gap-2">
                            <input type="number" step="0.1" id="kwMin" class="form-control" placeholder="Min">
                            <input type="number" step="0.1" id="kwMax" class="form-control" placeholder="Max">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">
                            <i class="fa-solid fa-indian-rupee-sign me-1"></i>
                            Payable Price
                        </label>
                        <div class="d-flex gap-2">
                            <input type="number" id="priceMin" class="form-control" placeholder="Min">
                            <input type="number" id="priceMax" class="form-control" placeholder="Max">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted">
                            <i class="fa-solid fa-magnifying-glass me-1"></i>
                            Module Name
                        </label>
                        <input type="text" id="moduleSearch" class="form-control" placeholder="Search module">
                    </div>

                    <div class="col text-end">
                        <button class="btn btn-primary me-2" onclick="applyFilters()">
                            <i class="fa-solid fa-filter me-1"></i>
                            Apply
                        </button>

                        <button class="btn btn-outline-secondary" onclick="resetFilters()">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>

                </div>
            </div>
            {{-- ================= TABLE ================= --}}
            <div class="crm-section mt-3">
                <div class="crm-table-wrapper position-relative">

                    <table class="table crm-table mb-0">
                        <thead>
                            <tr>
                                <th>System</th>
                                <th>Capacity</th>
                                <th>Pricing</th>
                                <th>Subsidy</th>
                                <th>Payable</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="quoteMasterTable">
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="5">
                                        <div class="crm-skeleton"></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div id="quoteMasterLoader" class="crm-loader-overlay d-none">
                        <div class="crm-spinner"></div>
                    </div>

                </div>
                <div class="d-flex justify-content-end mt-2" id="quoteMasterPagination"></div>

            </div>

        </div>
    </div>

    {{-- ================= TOAST ================= --}}
    <div class="modal fade" id="deleteQuoteMasterModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
                        Delete Quote Package
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">
                        Are you sure you want to delete this quote package?
                    </p>
                    <div class="text-muted small mt-1">
                        This action cannot be undone.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">
                        <i class="fa-solid fa-trash me-1"></i>
                        Delete
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>
@endsection


@push('scripts')
    <script>
        const LIST_URL = "{{ route('quote_master.ajax.list') }}";
        const WIDGET_URL = "{{ route('quote_master.ajax.widgets') }}";

        document.addEventListener('DOMContentLoaded', () => {
            loadWidgets();
            loadQuoteMaster();
            let searchTimer;
            document.getElementById('moduleSearch')
                .addEventListener('input', () => {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(applyFilters, 400);
                });
        });



        /* ================= WIDGETS ================= */
        function loadWidgets() {
            crmFetch(WIDGET_URL)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('quoteMasterWidgets').innerHTML = html;
                    loadKwPriceChart()
                });

        }

        function applyFilters() {
            const filters = {
                kw_min: document.getElementById('kwMin').value,
                kw_max: document.getElementById('kwMax').value,
                price_min: document.getElementById('priceMin').value,
                price_max: document.getElementById('priceMax').value,
                module: document.getElementById('moduleSearch').value
            };

            loadQuoteMaster(filters);
        }

        function resetFilters() {
            ['kwMin', 'kwMax', 'priceMin', 'priceMax', 'moduleSearch']
            .forEach(id => document.getElementById(id).value = '');

            loadQuoteMaster();
        }

        /* ================= TABLE ================= */
        function loadQuoteMaster(page=1,filters = {}) {
            const loader = document.getElementById('quoteMasterLoader');
            const perPage = document.getElementById('perPage').value;
            loader.classList.remove('d-none');
            filters.page = page,
            filters.per_page = perPage,
            crmFetch(LIST_URL + '?' + new URLSearchParams(filters))
                .then(res => res.json())
                .then(res => {
                    renderRows(res.data, res.canEdit)
                    renderQuoteMasterPagination(res.pagination);
                })
                .finally(() => loader.classList.add('d-none'));
        }


        function renderQuoteMasterPagination(meta) {
            const wrap = document.getElementById('quoteMasterPagination');
            wrap.innerHTML = '';

            if (meta.last_page <= 1) return;

            for (let i = 1; i <= meta.last_page; i++) {
                wrap.innerHTML += `
                    <button class="btn btn-sm ${
                        meta.current_page === i
                            ? 'btn-primary'
                            : 'btn-outline-secondary'
                    } me-1"
                    onclick="loadQuoteMaster(${i})">
                        ${i}
                    </button>
                `;
            }
        }
        function renderRows(rows,canEdit) {
            const tbody = document.getElementById('quoteMasterTable');
            tbody.innerHTML = '';

            if (!rows.length) {
                tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No quote packages found
                </td>
            </tr>`;
                return;
            }

            rows.forEach(r => {
                tbody.innerHTML += `
        <tr>
            <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="crm-avatar">
                        <i class="fa-solid fa-solar-panel"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">
                            ${r.module ?? 'Solar System'}
                        </div>
                        <div class="text-muted small">
                            SKU: ${r.sku ?? '-'}
                        </div>
                    </div>
                </div>
            </td>

            <td>
                <span class="badge bg-info-subtle text-info">
                    ${r.kw} kW
                </span>
                <div class="text-muted small">
                    ${r.module_count ?? '-'} panels
                </div>
            </td>

            <td>
                <div class="fw-semibold">
                    ₹ ${Number(r.value).toLocaleString()}
                </div>
                <div class="text-muted small">
                    Taxes: ₹ ${Number(r.taxes ?? 0).toLocaleString()}
                </div>
            </td>

            <td>
                ₹ ${Number(r.subsidy ?? 0).toLocaleString()}
            </td>

            <td>
                <span class="fw-bold text-success">
                    ₹ ${Number(r.payable ?? 0).toLocaleString()}
                </span>
            </td>

            <td class="text-end">
                <div class="d-inline-flex gap-2">

                    {{-- Edit --}}
                ${canEdit ? `
                    <a href="/quote-master/${r.id}/edit"
                       class="btn btn-sm btn-light"
                       title="Edit">
                        <i class="fa-solid fa-pen"></i>
                    </a>

                    {{-- Delete --}}
                    <button class="btn btn-sm btn-light text-danger"
                            onclick="openDeleteModal(${r.id})"
                            title="Delete">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                ` : ''}
                </div>
            </td>
        </tr>`;
            });
        }

        let deleteId = null;
        const deleteModal = new bootstrap.Modal(
            document.getElementById('deleteQuoteMasterModal')
        );

        function openDeleteModal(id) {
            deleteId = id;
            deleteModal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {

            if (!deleteId) return;

            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Deleting';

            crmFetch(`/quote-master/ajax/${deleteId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(res => {
                    showToast('success', res.message);
                    deleteModal.hide();
                    loadQuoteMaster();
                    loadWidgets();
                    loadKwPriceChart();
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-trash me-1"></i> Delete';
                    deleteId = null;
                });
        });
        let kwPriceChartInstance = null;

        function loadKwPriceChart() {
            const loader = document.getElementById('kwPriceChartLoader');
            console.log(loader);

            loader.classList.remove('d-none');

            crmFetch("{{ route('quote_master.ajax.chart.kw_price') }}")
                .then(res => res.json())
                .then(data => renderKwPriceChart(data))
                .finally(() => loader.classList.add('d-none'));
        }

        function renderKwPriceChart(data) {

            const ctx = document.getElementById('kwPriceChart');
            if (!ctx) return;

            if (kwPriceChartInstance) {
                kwPriceChartInstance.destroy();
            }

            kwPriceChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Payable Price (₹)',
                        data: data.values,
                        borderColor: 'rgb(242,140,40)',
                        backgroundColor: 'rgba(242,140,40,0.15)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgb(242,140,40)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx =>
                                    '₹ ' + Number(ctx.parsed.y).toLocaleString()
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Capacity (kW)'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Payable Price (₹)'
                            },
                            ticks: {
                                callback: value =>
                                    '₹ ' + Number(value).toLocaleString()
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
