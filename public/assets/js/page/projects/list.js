document.addEventListener('DOMContentLoaded', () => {
    loadWidgets();
    loadProjects();
});

async function loadWidgets() {
    const wrap = document.getElementById('projectWidgets');
    const res = await fetch(PROJECT_WIDGET_URL);
    wrap.innerHTML = await res.text();
}

async function loadProjects(page = 1) {

    const params = new URLSearchParams({
        page,
        per_page: 10,
        status: document.getElementById('filterStatus').value,
        priority: document.getElementById('filterPriority').value,
        search: document.getElementById('searchBox').value,
    });

    const loader = document.getElementById('projectLoader');
    loader.classList.remove('d-none');

    try {
        const res = await fetch(PROJECT_LIST_URL + '?' + params.toString());
        const json = await res.json();

        renderRows(json.data);
        renderPagination(json.meta);

    } finally {
        loader.classList.add('d-none');
    }
}

function renderRows(rows) {
    const tbody = document.getElementById('projectTable');
    tbody.innerHTML = '';

    if (!rows.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    No projects found
                </td>
            </tr>`;
        return;
    }

    rows.forEach(p => {

        /* ===== STATUS CHIP ===== */
        let statusChip = `<span class="badge bg-light text-dark">${p.status}</span>`;
        if (p.on_hold) statusChip = `<span class="badge bg-danger">On Hold</span>`;
        else if (p.is_delayed) statusChip = `<span class="badge bg-warning text-dark">Delayed</span>`;

        /* ===== NEXT ACTION CHIP ===== */
        let actionChip = `
            <span class="crm-chip-action ${p.on_hold ? 'danger' :
                p.doc_blocked ? 'secondary' :
                    p.is_delayed ? 'warning' :
                        p.next_emi ? 'info' : 'success'
            }">
                ${p.next_action}
            </span>
        `;

        /* ===== IDLE WARNING ===== */
        let idleNote = '';
        if (p.idle_days && p.idle_days >= 5) {
            idleNote = `
                <div class="small text-danger">
                    ⚠ No activity for ${p.idle_days} days
                </div>
            `;
        }

        /* ===== EMI INFO ===== */
        let emiInfo = p.next_emi
            ? `<div class="small text-muted">Next EMI: ${p.next_emi}</div>`
            : `<div class="small text-success">No EMI pending</div>`;

        tbody.innerHTML += `
            <tr>
                <td>
                    <div class="fw-semibold">${p.project_code}</div>
                    ${emiInfo}
                    ${idleNote}
                </td>

                <td>${p.customer ?? '—'}</td>

                <td>${statusChip}</td>

                <td>
                    <span class="badge ${p.priority === 'high'
                ? 'bg-danger'
                : p.priority === 'medium'
                    ? 'bg-warning text-dark'
                    : 'bg-secondary'
            }">
                        ${p.priority}
                    </span>
                </td>

                <td>${actionChip}</td>

                <td class="text-end">
                    <a href="/projects/${p.id}"
                       class="btn btn-sm btn-light"
                       title="View Project">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                </td>
            </tr>
        `;
    });
}


function renderPagination(meta) {
    const wrap = document.getElementById('projectPagination');
    wrap.innerHTML = '';

    if (meta.last_page <= 1) return;

    for (let i = 1; i <= meta.last_page; i++) {
        wrap.innerHTML += `
            <button class="btn btn-sm ${meta.current_page === i
                ? 'btn-primary'
                : 'btn-outline-secondary'
            } me-1"
            onclick="loadProjects(${i})">
                ${i}
            </button>
        `;
    }
}
