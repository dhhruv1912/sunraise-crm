document.addEventListener("DOMContentLoaded", () => {
    loadPanels();

    document.getElementById("search").addEventListener("input", loadPanels);
    document.getElementById("filter_item_id").addEventListener("change", loadPanels);
    document.getElementById("filter_warehouse_id").addEventListener("change", loadPanels);
    document.getElementById("filter_status").addEventListener("change", loadPanels);
});

let panelsPage = 1;

function loadPanels(page = 1) {
    panelsPage = page;

    const params = {
        search: document.getElementById("search").value,
        item_id: document.getElementById("filter_item_id").value,
        warehouse_id: document.getElementById("filter_warehouse_id").value,
        status: document.getElementById("filter_status").value,
        page,
    };

    fetch("/panels/list?" + new URLSearchParams(params))
        .then(res => res.json())
        .then(res => {
            if (!res.success) return;

            const data = res.data;
            renderTable(data);
        });
}

function renderTable(data) {
    let html = `
        <table class="table table-hover">
            <thead>
                <tr style="color: var(--arham-text-heading);">
                    <th>ID</th>
                    <th>Serial</th>
                    <th>Status</th>
                    <th>Item</th>
                    <th>Warehouse</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
    `;

    if (data.data.length === 0) {
        html += `
            <tr>
                <td colspan="6" class="text-center text-muted">No panels found</td>
            </tr>
        `;
    }

    data.data.forEach(p => {
        html += `
            <tr>
                <td>${p.id}</td>
                <td>${p.serial_number}</td>
                <td><span class="badge bg-info">${p.status}</span></td>
                <td>${p.item_id ?? "-"}</td>
                <td>${p.warehouse_id ?? "-"}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewPanel(${p.id})">View</button>
                </td>
            </tr>
        `;
    });

    html += "</tbody></table>";

    // Pagination
    html += renderPagination(data);

    document.getElementById("panelsTable").innerHTML = html;
}

function renderPagination(data) {
    let html = `<nav><ul class="pagination justify-content-center">`;

    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" href="javascript:loadPanels(${i})">${i}</a>
            </li>
        `;
    }

    html += "</ul></nav>";

    return html;
}

function viewPanel(id) {
    fetch(`/panels/show/${id}`)
        .then(res => res.text())
        .then(html => openModal(html));
}
