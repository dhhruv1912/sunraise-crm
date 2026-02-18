document.addEventListener('DOMContentLoaded', () => {
    loadWidgets();
    loadCustomers();
});

/* ================= WIDGETS ================= */

function loadWidgets() {
    crmFetch(WIDGET_URL)
        .then(r => r.text())
        .then(html => {
            document.getElementById('customerWidgets').innerHTML = html;
        });
}

/* ================= LIST ================= */

function loadCustomers(page = 1) {

    const search  = document.getElementById('searchBox').value;
    const perPage = document.getElementById('perPage').value;
    const loader  = document.getElementById('customerLoader');

    loader.classList.remove('d-none');

    const params = new URLSearchParams({
        page,
        per_page: perPage,
        search
    });

    crmFetch(LIST_URL + '?' + params.toString())
        .then(r => r.json())
        .then(res => {
            renderRows(res.data,res.canEdit);
            renderPagination(res.meta);
        })
        .finally(() => loader.classList.add('d-none'));
}

function renderRows(rows,canEdit) {
    // function renderCustomerRows(rows) {
    const tbody = document.getElementById('customerTable');
    tbody.innerHTML = '';

    if (!rows.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    No customers found
                </td>
            </tr>`;
        return;
    }

    rows.forEach(c => {
        tbody.innerHTML += `
            <tr>
                <td>
                    <div class="d-flex align-items-center gap-2">

                        ${renderAvatar(c)}

                        <div>
                            <div class="fw-semibold">${c.name}</div>
                        </div>

                    </div>
                </td>

                <td class="text-muted small">${c.mobile ?? ''}</td>

                <td>${c.email ?? '—'}</td>

                <td class="text-end">
                    <a href="/customers/${c.id}"
                       class="btn btn-sm btn-light">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    ${canEdit ? `<a href="/customers/${c.id}/edit"
                       class="btn btn-sm btn-light">
                        <i class="fa-solid fa-pen"></i>
                    </a>` : ""}
                    
                </td>
            </tr>
        `;
    });
}
function renderAvatar(c) {
    if (c.avatar) {
        return `
            <img src="${c.avatar}"
                 class="crm-avatar"
                 onerror="this.onerror=null;this.src='/assets/img/avatar-placeholder.png'">
        `;
    }

    const initials = (c.name || '')
        .split(' ')
        .map(w => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();

    return `
        <div class="crm-avatar-placeholder">
            ${initials || 'U'}
        </div>
    `;
}


function renderPagination(meta) {
    const wrap = document.getElementById('customerPagination');
    wrap.innerHTML = '';

    if (meta.last_page <= 1) return;

    for (let i = 1; i <= meta.last_page; i++) {
        wrap.innerHTML += `
            <button class="btn btn-sm ${
                meta.current_page === i
                    ? 'btn-primary'
                    : 'btn-outline-secondary'
            } me-1"
            onclick="loadCustomers(${i})">
                ${i}
            </button>`;
    }
}
