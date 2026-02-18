
document.addEventListener('DOMContentLoaded', () => {
    loadWidgets();
    loadInvoices();
});

/* ================= WIDGETS ================= */
function loadWidgets() {
    crmFetch(WIDGET_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById('invoiceWidgets').innerHTML = html;
        });
    crmFetch(UPCOMING_PAYMENTS_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById('invoiceWidgets2').innerHTML = html;
        });
}

/* ================= LIST ================= */
function loadInvoices(page = 1) {

    const status  = document.getElementById('filterStatus').value;
    const search  = document.getElementById('searchBox').value;
    const perPage = document.getElementById('perPage').value;
    const loader  = document.getElementById('invoiceLoader');

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
            renderInvoiceRows(res.data,res.canEdit);
            renderInvoicePagination(res.pagination);
        })
        .finally(() => loader.classList.add('d-none'));
}

function renderInvoiceRows(rows,canEdit) {
    const tbody = document.getElementById('invoiceTable');
    tbody.innerHTML = '';

    if (!rows.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No invoices found
                </td>
            </tr>`;
        return;
    }

    rows.forEach(inv => {
        tbody.innerHTML += `
            <tr>
                <td>
                    <div class="fw-semibold">${inv.invoice_no}</div>
                    <div class="text-muted small">
                        ${inv.invoice_date}
                    </div>
                </td>

                <td>
                    <div class="fw-semibold">${inv.customer ?? '—'}</div>
                    <div class="text-muted small">${inv.mobile ?? ''}</div>
                </td>

                <td>${inv.project_code ?? '—'}</td>

                <td>
                    ₹ ${Number(inv.total).toLocaleString('en-IN')}
                </td>

                <td>
                    <span class="badge ${inv.status_class}">
                        ${inv.status_label}
                    </span>
                </td>

                <td class="text-end">
                <a href="/invoices/${inv.id}"
                       class="btn btn-sm btn-light"
                       title="View">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    ${canEdit ? `<a href="/invoices/${inv.id}/edit"
                       class="btn btn-sm btn-light"
                       title="View">
                        <i class="fa-solid fa-pen"></i>
                    </a>` : "" }
                    
                </td>
            </tr>
        `;
    });
}

function renderInvoicePagination(meta) {
    const wrap = document.getElementById('invoicePagination');
    wrap.innerHTML = '';

    if (meta.last_page <= 1) return;

    for (let i = 1; i <= meta.last_page; i++) {
        wrap.innerHTML += `
            <button class="btn btn-sm ${
                meta.current_page === i
                    ? 'btn-primary'
                    : 'btn-outline-secondary'
            } me-1"
            onclick="loadInvoices(${i})">
                ${i}
            </button>
        `;
    }
}