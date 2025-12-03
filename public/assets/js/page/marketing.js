document.addEventListener("DOMContentLoaded", function () {
    loadLeads();

    document.getElementById("btnFilter").addEventListener("click", loadLeads);
    document.getElementById("btnReset").addEventListener("click", () => {
        document.querySelectorAll("#filter_name,#filter_mobile,#filter_status,#filter_assigned,#filter_from,#filter_to")
            .forEach(el => el.value = "");
        loadLeads();
    });
});

async function loadLeads(page = 1) {

    let params = new URLSearchParams({
        page,
        filter_name: document.getElementById("filter_name").value,
        filter_mobile: document.getElementById("filter_mobile").value,
        filter_status: document.getElementById("filter_status").value,
        filter_assigned: document.getElementById("filter_assigned").value,
        filter_from: document.getElementById("filter_from").value,
        filter_to: document.getElementById("filter_to").value,
    });

    const res = await fetch(`/marketing/ajax?` + params.toString());
    const json = await res.json();
    renderLeadRows(json.data);
    renderPagination(json);
}

function renderLeadRows(rows) {
    const body = document.getElementById("leadTableBody");
    body.innerHTML = "";

    rows.forEach(row => {
        body.innerHTML += `
            <tr>
                <td>${row.lead_code}</td>
                <td>${row?.quote_request?.name || '—'}</td>
                <td>${row?.quote_request?.number || '—'}</td>

                <td>
                    <select class="form-select form-select-sm"
                        onchange="changeStatus(${row.id}, this.value)">
                        ${Object.entries(window.LEAD_STATUS)
                            .map(([key, label]) =>
                                `<option value="${key}" ${key === row.status ? 'selected' : ''}>${label}</option>`
                            ).join('')}
                    </select>
                </td>

                <td>
                    <select class="form-select form-select-sm"
                        onchange="changeAssigned(${row.id}, this.value)">
                        <option value="">-- None --</option>
                        ${window.allUsers
                            .map(u =>
                                `<option value="${u.id}" ${u.id == row.assigned_to ? 'selected' : ''}>${u.fname} ${u.lname}</option>`
                            ).join('')}
                    </select>
                </td>

                <td>${row.next_followup_at || '—'}</td>

                <td>
                    <button class="btn btn-sm btn-info" onclick="viewLead(${row.id})">View</button>
                    <a href="/marketing/${row.id}/edit" class="btn btn-sm btn-primary">Edit</a>
                    <button class="btn btn-sm btn-danger" onclick="deleteLead(${row.id})">Delete</button>
                </td>
            </tr>
        `;
    });
}

function renderPagination(json) {
    let html = "";
    for (let i = 1; i <= json.last_page; i++) {
        html += `
            <button class="btn btn-sm ${i == json.current_page ? 'btn-primary' : 'btn-outline-primary'}"
                    onclick="loadLeads(${i})">${i}</button>
        `;
    }
    document.getElementById("leadPagination").innerHTML = html;
}

async function changeStatus(id, status) {
    await fetch(`/marketing/${id}/status`, {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": TOKEN },
        body: JSON.stringify({ status }),
    });
}

async function changeAssigned(id, assigned_to) {
    await fetch(`/marketing/${id}/assign`, {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": TOKEN },
        body: JSON.stringify({ assigned_to }),
    });
}

async function viewLead(id) {
    const res = await fetch(`/marketing/${id}/view-json`);
    const data = await res.json();

    document.getElementById("modal-id").textContent = data.id;
    document.getElementById("modal-lead-code").textContent = data.lead_code;
    document.getElementById("modal-name").textContent = data?.quote_request?.name;
    document.getElementById("modal-number").textContent = data?.quote_request?.number;
    document.getElementById("modal-email").textContent = data?.quote_request?.email;
    document.getElementById("modal-assigned").textContent = data?.assignee?.fname + " " + data?.assignee?.lname ?? '—';
    document.getElementById("modal-status").textContent = window.LEAD_STATUS[data.status];
    document.getElementById("modal-remarks").textContent = data.remarks ?? '—';

    document.getElementById("modal-history").innerHTML = data.history_html;

    new bootstrap.Modal(document.getElementById('leadViewModal')).show();
}

async function deleteLead(id) {
    if (!confirm("Are you sure?")) return;

    await fetch(`/marketing/delete`, {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": TOKEN },
        body: JSON.stringify({ id }),
    });

    loadLeads();
}
