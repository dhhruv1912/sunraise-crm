// public/assets/js/page/quote_master.js
document.addEventListener("DOMContentLoaded", () => {
    const searchBox = document.getElementById("searchBox");
    const perPage = document.getElementById("perPage");

    if (searchBox) {
        searchBox.addEventListener("input", debounce(() => loadData(1), 300));
    }
    if (perPage) {
        perPage.addEventListener("change", () => loadData(1));
    }

    // initial load
    loadData(1);
});

function loadData(page = 1) {
    const search = encodeURIComponent(document.getElementById("searchBox")?.value ?? '');
    const per_page = document.getElementById("perPage")?.value ?? 20;

    fetch(`/quote/master/ajax?search=${search}&page=${page}&per_page=${per_page}`)
        .then(r => r.json())
        .then(res => {
            renderTable(res.data || res.data === undefined ? res.data : []);
            renderPagination(res);
            renderTableInfo(res);
        })
        .catch(err => {
            console.error(err);
            document.getElementById("dataBody").innerHTML = "<tr><td colspan='8'>Failed to load data</td></tr>";
        });
}

function renderTable(rows = []) {
    const tbody = document.getElementById("dataBody");
    if (!tbody) return;

    if (!rows || rows.length === 0) {
        tbody.innerHTML = "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
        return;
    }

    let html = "";
    rows.forEach(row => {
        html += renderRow(row);
    });

    tbody.innerHTML = html;

    // reattach event listeners if needed
}

function renderRow(row) {
    return `
        <tr data-id="${row.id}">
            <td>${escapeHtml(row.sku ?? '')}</td>
            <td>${escapeHtml(row.module ?? '')}</td>
            <td>${row.kw ?? ''}</td>
            <td>${row.module_count ?? ''}</td>
            <td>${row.value ?? ''}</td>
            <td>${row.payable ?? ''}</td>
            <td>${row.projected ?? ''}</td>
            <td>
                <a href="/quote/master/edit/${row.id}" class="btn btn-sm btn-primary">Edit</a>
                <button class="btn btn-sm btn-danger" onclick="deleteItem(${row.id})">Delete</button>
            </td>
        </tr>
    `;
}

function saveInline(btn) {
    const tr = btn.closest('tr');
    const id = tr.dataset.id;
    const inputs = tr.querySelectorAll('input.inline');
    const payload = {};

    inputs.forEach(i => {
        const key = i.dataset.field;
        payload[key] = i.value;
    });

    // If payable and subsidy present, calculate projected before sending
    if (payload.payable !== undefined && payload.subsidy !== undefined) {
        payload.projected = (parseFloat(payload.payable || 0) - parseFloat(payload.subsidy || 0)).toFixed(2);
    }

    fetch(`/quote/master/update-inline/${id}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": (window.TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'))
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(json => {
        if (json.status) {
            btn.innerText = "Saved";
            btn.classList.remove('btn-success'); btn.classList.add('btn-info');
            setTimeout(() => { btn.innerText = "Save"; btn.classList.remove('btn-info'); btn.classList.add('btn-success'); }, 1200);
            // optionally refresh the row or page
        } else {
            alert(json.message || 'Update failed');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Update failed');
    });
}

function deleteItem(id) {
    if (!confirm("Are you sure to delete this record?")) return;

    fetch("/quote/master/delete", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": (window.TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'))
        },
        body: JSON.stringify({ id })
    })
    .then(r => r.json())
    .then(json => {
        if (json.status) {
            alert(json.message || 'Deleted');
            loadData(1);
        } else {
            alert('Delete failed');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Delete failed');
    });
}

function renderPagination(payload) {
    // payload is the whole JSON paginated response
    if (!payload || !payload.meta && !payload.last_page && payload.current_page === undefined) {
        // older paginate format
        payload = payload || {};
        if (!payload.meta) return;
    }

    // Laravel paginator structure when returned via ->json() is the paginator itself
    // It contains current_page, last_page, etc.
    const current = payload.current_page || (payload.meta && payload.meta.current_page) || 1;
    const last = payload.last_page || (payload.meta && payload.meta.last_page) || 1;
    const base = 1;

    let html = `<nav><ul class="pagination pagination-sm mb-0">`;

    if (current > 1) {
        html += `<li class="page-item"><a href="#" class="page-link" onclick="loadData(${current - 1});return false;">Prev</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">Prev</span></li>`;
    }

    // simple page window (max 7 pages shown)
    const maxPages = 7;
    let start = Math.max(base, current - Math.floor(maxPages/2));
    let end = Math.min(last, start + maxPages - 1);
    if (end - start + 1 < maxPages) {
        start = Math.max(base, end - maxPages + 1);
    }

    for (let i = start; i <= end; i++) {
        html += `<li class="page-item ${i === current ? 'active' : ''}"><a href="#" class="page-link" onclick="loadData(${i});return false;">${i}</a></li>`;
    }

    if (current < last) {
        html += `<li class="page-item"><a href="#" class="page-link" onclick="loadData(${current + 1});return false;">Next</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">Next</span></li>`;
    }

    html += `</ul></nav>`;

    document.getElementById('pagination').innerHTML = html;
}

function renderTableInfo(payload) {
    if (!payload) return;
    const from = payload.from || (payload.meta && payload.meta.from) || 0;
    const to = payload.to || (payload.meta && payload.meta.to) || 0;
    const total = payload.total || (payload.meta && payload.meta.total) || 0;
    const info = `Showing ${from} to ${to} of ${total}`;
    document.getElementById('tableInfo').innerText = info;
}

function debounce(fn, delay) {
    let t;
    return function() {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, arguments), delay);
    };
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
