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
                <td>${row?.customer?.name || '—'}</td>
                <td>${row?.customer?.mobile || '—'}</td>

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
                    <a href="/marketing/${row.id}/view" class="btn btn-sm btn-info">View</a>
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

function renderHistory(history) {
    const container = document.getElementById('modal-history');
    if (!container) return;

    if (history.length === 0) {
        container.innerHTML = `<div class="text-muted small">No history available.</div>`;
        return;
    }

    let html = "";

    history.forEach(h => {
        html += `
            <div class="timeline-entry">
                <strong>${h.action}</strong><br>
                ${h.message}<br>
                <small>${h.updated_at} — by ${h.user.fname} ${h.user.lname}</small>
            </div>
        `;
    });

    container.innerHTML = html;
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

async function createProject() {
    if (!confirm("Create project from this lead?")) return;
    leadId = document.getElementById("modal-id").innerHTML

    const res = await fetch(`/marketing/${leadId}/create-project`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    });

    const json = await res.json();

    if (!json.status) {
        alert(json.message);
        return;
    }

    alert("Project created successfully!");

    // Redirect to project edit page
    // window.location.href = json.project_url;
}
