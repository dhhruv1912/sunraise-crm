document.addEventListener("DOMContentLoaded", () => {
    const searchBox = document.getElementById("searchBox");
    const filterType = document.getElementById("filter_type");
    const filterStatus = document.getElementById("filter_status");
    const filterAssigned = document.getElementById("filter_assigned");
    const filterFrom = document.getElementById("filter_from");
    const filterTo = document.getElementById("filter_to");
    const perPage = document.getElementById("perPage");
    const refreshBtn = document.getElementById("refreshBtn");
    const dataBody = document.getElementById("dataBody");
    const paginationContainer = document.getElementById("paginationContainer");

    const modalEl = new bootstrap.Modal(document.getElementById("quoteRequestViewModal"));
    const modal = document.getElementById("quoteRequestViewModal");

    // load initial
    loadData();

    // bind filters
    [searchBox, filterType, filterStatus, filterAssigned, filterFrom, filterTo, perPage].forEach(el => {
        if (!el) return;
        el.addEventListener("change", () => loadData());
        el.addEventListener("input", () => {
            // throttle search small delay
            if (el === searchBox) {
                clearTimeout(window.__qr_search_to);
                window.__qr_search_to = setTimeout(() => loadData(), 350);
            }
        });
    });

    refreshBtn?.addEventListener("click", () => loadData());

    // pagination click
    paginationContainer.addEventListener("click", (e) => {
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            e.preventDefault();
            loadData(parseInt(e.target.dataset.page));
        }
    });

    // load data
    async function loadData(page = 1) {
        const params = new URLSearchParams();
        params.append('per_page', perPage.value || 20);
        params.append('page', page);
        if (searchBox.value) params.append('search', searchBox.value);
        if (filterType.value) params.append('filter_type', filterType.value);
        if (filterStatus.value) params.append('filter_status', filterStatus.value);
        if (filterAssigned.value) params.append('filter_assigned', filterAssigned.value);
        if (filterFrom.value) params.append('filter_from', filterFrom.value);
        if (filterTo.value) params.append('filter_to', filterTo.value);

        const res = await fetch(`/quote/requests/ajax?` + params.toString(), { credentials: 'same-origin' });
        const json = await res.json();

        renderTable(json.data || json, json.users || {});
        renderPagination(json);
    };
    function getAssigneeDropdown(row, users) {
        let html = `<select class="form-select form-select-sm assign-user-dropdown" data-id="${row.id}">
                    <option value="">— Unassigned —</option>`;

        users.forEach(u => {
            let selected = row.assigned_to == u.id ? "selected" : "";
            html += `<option value="${u.id}" ${selected}>${u.fname} ${u.lname}</option>`;
        });

        html += `</select>`;
        return html;
    }

    function highlightRow(id) {
        const row = document.querySelector(`tr[data-row-id="${id}"]`);
        if (!row) return;

        row.classList.add("highlight-row");

        setTimeout(() => {
            row.classList.remove("highlight-row");
        }, 1200);
    }

    // render rows
    function renderTable(payload, marketingUsers) {
        let rows = payload.data || payload;
        let html = "";
        rows.forEach((r, idx) => {
            const idxNum = r.id;
            const typeLabel = r.type ? r.type.toUpperCase() : '—';
            const created = new Date(r.created_at).toLocaleString();

            html += `<tr data-row-id="${r.id}">
                <td>${idxNum}</td>
                <td>${typeLabel}</td>
                <td>${escapeHtml(r.customer.name ?? '')}</td>
                <td>${escapeHtml(r.customer.mobile ?? '')}</td>
                <td>${escapeHtml(r.module ?? '')} ${r.kw ? '/ ' + r.kw + 'kW' : ''}</td>
                <td>${getAssigneeDropdown(r, marketingUsers)}</td>
                <td>
                    <select class="form-select form-select-sm status-select" data-id="${r.id}">
                        <option value="">Select</option>
                        ${Object.entries(window.__QR_STATUS || {}).map(([k, v]) => {
                const sel = k === r.status ? 'selected' : '';
                return `<option value="${k}" ${sel}>${v}</option>`;
            }).join('')}
                    </select>
                </td>
                <td>${created}</td>
                <td>
                    <button class="btn btn-sm btn-primary view-btn" data-id="${r.id}">View</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${r.id}">Delete</button>
                </td>
            </tr>`;
        });

        dataBody.innerHTML = html;

        // attach listeners
        document.querySelectorAll(".view-btn").forEach(b => b.addEventListener("click", onView));
        document.querySelectorAll(".delete-btn").forEach(b => b.addEventListener("click", onDelete));
        document.querySelectorAll(".status-select").forEach(s => s.addEventListener("change", onStatusChange));
    }

    function renderPagination(payload) {
        // payload is Laravel paginator JSON
        if (!payload || !payload.last_page) {
            paginationContainer.innerHTML = "";
            return;
        }

        const current = payload.current_page;
        const last = payload.last_page;
        let html = `<ul class="pagination">`;

        // prev
        if (current > 1) html += `<li class="page-item"><a class="page-link" href="#" data-page="${current - 1}">Prev</a></li>`;

        // pages (simple)
        const start = Math.max(1, current - 2);
        const end = Math.min(last, current + 2);
        for (let p = start; p <= end; p++) {
            const active = p === current ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        }

        if (current < last) html += `<li class="page-item"><a class="page-link" href="#" data-page="${current + 1}">Next</a></li>`;
        html += `</ul>`;
        paginationContainer.innerHTML = html;
    }

    async function onView(e) {
        const id = e.currentTarget.dataset.id;
        // const res = await fetch(`/quote/requests/${id}/view`);
        // server returns HTML partial view - we will fetch JSON endpoint for modal content instead
        // Better: use ajax endpoint that returns JSON
        const json = await fetchJson(`/quote/requests/${id}/view-json`);
        populateModal(json);
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
                <small>${h.datetime} — by ${h.user}</small>
            </div>
        `;
        });

        container.innerHTML = html;
    }

    function populateModal(data) {
        if (!data) return;
        
        const row = data.data;      // main record
        const history = data.history || [];

        document.getElementById('modal-id').textContent = row.id;
        document.getElementById('modal-type').textContent = (row.type || '—').toUpperCase();
        document.getElementById('modal-name').textContent = row.customer.name || '—';
        document.getElementById('modal-number').textContent = row.customer.mobile || '—';
        document.getElementById('modal-email').textContent = row.customer.email || '—';
        document.getElementById('modal-module').textContent = row.module || '—';
        document.getElementById('modal-kw').textContent = row.kw || '—';
        document.getElementById('modal-mc').textContent = row.mc || '—';
        document.getElementById('modal-status').textContent = (window.__QR_STATUS || {})[row.status] ?? row.status;
        document.getElementById('modal-assigned').textContent = row.assigned_user.fname + " " + row.assigned_user.lname ?? '—';
        document.getElementById('modal-notes').textContent = row.notes || '—';

        // Quote-only info
        document.getElementById('quote-fields').style.display = row.type === 'quote' ? '' : 'none';
        const select = document.getElementById("quote_master_id");

        Object.entries(data.master).forEach(([key, value]) => {
            const opt = document.createElement("option");
            opt.value = key;
            if(row.quote_master_id == opt.value){
                opt.selected = true;
            }
            opt.textContent = value;
            select.appendChild(opt);
        });
        // Buttons
        document.getElementById('modal-send-mail').onclick = () => sendMail(row.id);
        document.getElementById('modal-convert-lead').onclick = () => convertToLead(row.id);
        document.getElementById('quote_master_id').onchange = (e) => changeQuoteMaster(row.id,e.target.value);

        // 🔥 Render History
        renderHistory(history);

        modalEl.show();
    }


    async function sendMail(id) {
        if (!confirm('Send quotation email to customer?')) return;
        try {
            const res = await fetch(`/quote/requests/${id}/send-mail`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN }
            });
            const json = await res.json();
            alert(json.message || 'Sent');
        } catch (err) {
            alert('Failed to send');
        }
    }

    async function convertToLead(id) {
        if (!confirm('Convert this request to lead?')) return;
        try {
            const res = await fetch(`/quote/requests/${id}/convert-to-lead`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN }
            });
            const json = await res.json();
            if (json.status) {
                alert('Converted to lead');
                modalEl.hide();
                loadData();
            } else alert('Failed');
        } catch (err) {
            alert('Error');
        }
    }

    async function onDelete(e) {
        const id = e.currentTarget.dataset.id;
        if (!confirm('Delete?')) return;
        const res = await fetch(`/quote/requests/delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN },
            body: JSON.stringify({ id })
        });
        const json = await res.json();
        alert(json.message || 'Deleted');
        loadData();
    }

    async function onStatusChange(e) {
        const id = e.currentTarget.dataset.id;
        const status = e.currentTarget.value;
        if (!status) return;
        const res = await fetch(`/quote/requests/${id}/status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN },
            body: JSON.stringify({ status })
        });
        const json = await res.json();
        if (json.status) {
            // optionally notify
            loadData();
        } else {
            alert('Update failed');
        }
    }

    async function changeQuoteMaster(id,quote_master_id) {
        if (!quote_master_id) return;
        const res = await fetch(`/quote/requests/${id}/quote-master`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN },
            body: JSON.stringify({ quote_master_id })
        });
        const json = await res.json();
        if (json.status) {
            // optionally notify
            alert('Updated!');
            // loadData();
        } else {
            alert('Update failed');
        }
    }

    // small helper to GET JSON
    async function fetchJson(url) {
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) return null;
        try { return await res.json(); } catch (e) { return null; }
    }

    document.addEventListener("change", function (e) {
        if (!e.target.classList.contains("assign-user-dropdown")) return;

        const select = e.target;
        const id = select.dataset.id;
        const assigned_to = select.value;

        // show spinner
        const originalHTML = select.outerHTML;
        select.disabled = true;
        select.classList.add("loading-dropdown");

        select.insertAdjacentHTML("afterend", `
        <span class="spinner-border spinner-border-sm text-primary ms-2" id="spinner-${id}"></span>
    `);

        fetch(`/quote/requests/${id}/assign`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": TOKEN
            },
            body: JSON.stringify({ assigned_to })
        })
            .then(res => res.json())
            .then(res => {
                document.getElementById(`spinner-${id}`)?.remove();
                select.disabled = false;

                if (res.status) {
                    highlightRow(id);
                    showDismissible("Assignee updated", "alert-success");
                } else {
                    showAlert("Assign failed");
                }
            })
            .catch(() => {
                select.disabled = false;
                document.getElementById(`spinner-${id}`)?.remove();
                showAlert("Something went wrong", "alert-danger");
            });
    });

    // escape helper
    function escapeHtml(str) { return String(str || '').replace(/[&<>"'`=\/]/g, function (s) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '\\/' }[s]; }); }

    // expose statuses into global so renderTable can use them
});
