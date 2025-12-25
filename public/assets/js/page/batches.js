document.addEventListener("DOMContentLoaded", () => {
    loadBatches();

    document.getElementById("batchSearch").addEventListener("input", () => {
        loadBatches();
    });
});

function loadBatches(page = 1) {
    const params = {
        search: document.getElementById("batchSearch").value,
        page
    };

    fetch("/batches/list?" + new URLSearchParams(params))
        .then(res => res.json())
        .then(res => renderBatchTable(res.data));
}

function renderBatchTable(data) {

    let html = `
        <table class="table table-hover align-middle">
            <thead>
                <tr style="color: var(--arham-text-heading);">
                    <th>Batch No</th>
                    <th>Invoice No</th>
                    <th>Item</th>
                    <th>Warehouse</th>
                    <th>Qty</th>
                    <th>Received</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    if (data.data.length === 0) {
        html += `<tr><td colspan="7" class="text-center text-muted">No batches found</td></tr>`;
    }

    data.data.forEach(b => {
        html += `
            <tr>
                <td>${b.batch_no}</td>
                <td>${b.invoice_number ?? '-'}</td>
                <td>${b.item?.name ?? '-'}</td>
                <td>${b.warehouse?.name ?? '-'}</td>
                <td><span class="badge bg-primary">${b.total_panels}</span></td>
                <td>${b.created_at}</td>
                <td>
                    <a href="/batches/${b.id}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
            </tr>
        `;
    });

    html += `</tbody></table>`;

    // Pagination
    html += `<nav><ul class="pagination justify-content-center">`;

    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" href="javascript:loadBatches(${i})">${i}</a>
            </li>`;
    }

    html += "</ul></nav>";

    document.getElementById("batchTable").innerHTML = html;
}
