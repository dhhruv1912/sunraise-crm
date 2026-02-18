@extends('temp.common')

@section('title', 'Quotations')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-file-invoice me-2"></i>
                        Quotations
                    </h4>
                    <div class="text-muted small">
                        Generated quotations list
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-2 position-relative" id="quotationWidgets" style="min-height: 100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="crm-section mt-2">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small">Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">All</option>
                            <option value="sent">Sent</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small">Search</label>
                        <input type="text" id="searchBox" class="form-control" placeholder="Customer / Mobile">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Per Page</label>
                        <select id="perPage" class="form-select" onchange="loadQuotations(1)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                    <div class="col-md-2 text-end">
                        <button class="btn btn-primary w-100" onclick="loadQuotations(1)">
                            <i class="fa-solid fa-filter me-1"></i>
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="crm-section mt-3">
                <div class="crm-table-wrapper position-relative">

                    <table class="table crm-table mb-0">
                        <thead>
                            <tr>
                                <th>Quotation</th>
                                <th>Customer</th>
                                <th>Package</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="quotationTable">
                            {{-- skeleton --}}
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="6">
                                        <div class="crm-skeleton"></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div id="quotationLoader" class="crm-loader-overlay d-none">
                        <div class="crm-spinner"></div>
                    </div>

                </div>

                {{-- PAGINATION --}}
                <div class="d-flex justify-content-end mt-2" id="quotationPagination"></div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const LIST_URL = "{{ route('quotations.ajax.list') }}";
        const WIDGET_URL = "{{ route('quotations.ajax.widgets') }}";

        document.addEventListener('DOMContentLoaded', () => {
            loadWidgets();
            loadQuotations();
        });

        /* ================= WIDGETS ================= */
        function loadWidgets() {
            crmFetch(WIDGET_URL)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('quotationWidgets').innerHTML = html;
                });
        }

        /* ================= LIST ================= */
        function loadQuotations(page = 1) {
            QUO_PAGE = page;

            const status  = document.getElementById('filterStatus').value;
            const search  = document.getElementById('searchBox').value;
            const perPage = document.getElementById('perPage').value;
            const loader  = document.getElementById('quotationLoader');

            loader.classList.remove('d-none');

            const params = new URLSearchParams({
                page,
                per_page: perPage,
                status,
                search
            });

            crmFetch(LIST_URL + '?' + params.toString())
                .then(res => res.json())
                .then(res => {
                    renderQuotationRows(res.data);
                    renderQuotationPagination(res.pagination);
                })
                .finally(() => loader.classList.add('d-none'));
        }

        function renderQuotationRows(rows) {
            const tbody = document.getElementById('quotationTable');
            tbody.innerHTML = '';

            if (!rows.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6"
                            class="text-center text-muted py-4">
                            No quotations found
                        </td>
                    </tr>`;
                return;
            }

            rows.forEach(q => {
                tbody.innerHTML += `
                    <tr>
                        <td>
                            <div class="fw-semibold">${q.quotation_no}</div>
                            <div class="text-muted small">
                                ${q.lead_code}
                            </div>
                        </td>

                        <td>
                            <div class="fw-semibold">${q.customer ?? '—'}</div>
                            <div class="text-muted small">${q.mobile ?? ''}</div>
                        </td>

                        <td>${q.sku ?? '—'}</td>

                        <td>
                            ₹ ${Number(q.price).toLocaleString('en-IN')}
                        </td>

                        <td>
                            ${
                                q.sent
                                ? `<span class="badge bg-success-subtle text-success">Sent</span>`
                                : `<span class="badge bg-warning-subtle text-warning">Draft</span>`
                            }
                        </td>

                        <td class="text-end">
                            <a href="/quotations/${q.id}"
                            class="btn btn-sm btn-light"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });
        }

        function renderQuotationPagination(meta) {
            const wrap = document.getElementById('quotationPagination');
            wrap.innerHTML = '';

            if (meta.last_page <= 1) return;

            for (let i = 1; i <= meta.last_page; i++) {
                wrap.innerHTML += `
                    <button class="btn btn-sm ${
                        meta.current_page === i
                            ? 'btn-primary'
                            : 'btn-outline-secondary'
                    } me-1"
                    onclick="loadQuotations(${i})">
                        ${i}
                    </button>
                `;
            }
        }
    </script>
@endpush
