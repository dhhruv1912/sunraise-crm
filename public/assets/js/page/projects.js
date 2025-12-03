/* public/assets/js/page/projects.js */
document.addEventListener('DOMContentLoaded', () => {
    window.projectsState = { page: 1 };
    loadProjects();

    document.getElementById('applyFilters').addEventListener('click', () => {
        window.projectsState.page = 1;
        loadProjects();
    });
    document.getElementById('clearFilters').addEventListener('click', () => {
        document.getElementById('searchBox').value = '';
        document.getElementById('filter_status').value = '';
        document.getElementById('filter_assignee').value = '';
        window.projectsState.page = 1;
        loadProjects();
    });

    document.getElementById('perPage').addEventListener('change', () => {
        window.projectsState.page = 1;
        loadProjects();
    });

    document.getElementById('searchBox').addEventListener('keyup', delay((e) => {
        window.projectsState.page = 1;
        loadProjects();
    }, 400));
});

function loadProjects(page = 1) {
    const perPage = document.getElementById('perPage').value;
    const search = encodeURIComponent(document.getElementById('searchBox').value || '');
    const status = document.getElementById('filter_status').value || '';
    const assignee = document.getElementById('filter_assignee').value || '';

    fetch(`/projects/ajax?search=${search}&filter_status=${status}&filter_assigned=${assignee}&per_page=${perPage}&page=${page}`)
        .then(r => r.json())
        .then(json => renderList(json))
        .catch(err => console.error(err));
}

function renderList(payload) {
    const body = document.getElementById('projectsBody');
    body.innerHTML = '';

    payload.data.forEach(p => {
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td>${escapeHtml(p.project_code || '')}</td>
            <td>${escapeHtml(p.customer_name || '')}</td>
            <td>${escapeHtml(p.mobile || '')}</td>
            <td>${escapeHtml(p.kw ?? '—')}</td>
            <td>
                <select class="form-select assignee-select" data-id="${p.id}">
                    <option value="">—</option>
                </select>
            </td>
            <td>
                <select class="form-select status-select" data-id="${p.id}">
                </select>
            </td>
            <td>${new Date(p.created_at).toISOString().slice(0,10)}</td>
            <td>
                <button class="btn btn-sm btn-info view-btn" data-id="${p.id}">View</button>
                <a href="/projects/${p.id}/edit" class="btn btn-sm btn-primary">Edit</a>
                <button class="btn btn-sm btn-danger delete-btn" data-id="${p.id}">Delete</button>
            </td>
        `;
        body.appendChild(tr);

        // populate assignee select (fetch from global window.__PROJECT_USERS if available)
        const assigneeSelect = tr.querySelector('.assignee-select');
        const users = window.__PROJECT_USERS || [];
        users.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.text = u.fname + " " + u.lname;
            if (p.assignee === u.id) opt.selected = true;
            assigneeSelect.appendChild(opt);
        });

        const statusSelect = tr.querySelector('.status-select');
        const statuses = window.__PROJECT_STATUSES || {};
        for (const key in statuses) {
            const opt = document.createElement('option');
            opt.value = key;
            opt.text = statuses[key];
            if (p.status === key) opt.selected = true;
            statusSelect.appendChild(opt);
        }
    });

    // pagination
    const pagination = document.getElementById('paginationWrap');
    pagination.innerHTML = renderPagination(payload);

    // bind events
    document.querySelectorAll('.assignee-select').forEach(el => {
        el.addEventListener('change', (e) => {
            const id = e.currentTarget.dataset.id;
            const assignee = e.currentTarget.value;
            fetch(`/projects/${id}/assign`, {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':TOKEN},
                body: JSON.stringify({assignee})
            }).then(r => r.json()).then(j => {
                if(!j.status) alert('Assign failed');
            });
        });
    });

    document.querySelectorAll('.status-select').forEach(el => {
        el.addEventListener('change', (e) => {
            const id = e.currentTarget.dataset.id;
            const status = e.currentTarget.value;
            fetch(`/projects/${id}/status`, {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':TOKEN},
                body: JSON.stringify({status})
            }).then(r => r.json()).then(j => {
                if(!j.status) alert('Status update failed');
            });
        });
    });

    document.querySelectorAll('.view-btn').forEach(b => {
        b.addEventListener('click', (e) => onView(e.currentTarget || e.target));
    });

    document.querySelectorAll('.delete-btn').forEach(b => {
        b.addEventListener('click', (e) => {
            const id = e.currentTarget.dataset.id;
            if (!confirm('Delete project?')) return;
            fetch(`/projects/${id}/delete`, {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':TOKEN},
            }).then(r => r.json()).then(j => {
                if (j.status) loadProjects();
            });
        });
    });
}

/* render pagination simple */
function renderPagination(payload) {
    if (!payload || !payload.meta) return '';
    const meta = payload.meta;
    let html = '<ul class="pagination">';
    for (let i=1;i<=meta.last_page;i++){
        html += `<li class="page-item ${meta.current_page===i? 'active':''}"><a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
    }
    html += '</ul>';
    return html;
}

/* pagination click */
document.addEventListener('click', (e) => {
    if (e.target.matches('.page-link')) {
        e.preventDefault();
        const page = parseInt(e.target.dataset.page);
        if (!isNaN(page)) loadProjects(page);
    }
});

/* view modal */
async function onView(target) {
    const id = target.dataset.id;
    const res = await fetch(`/projects/${id}/view-json`);
    const data = await res.json();
    populateModal(data);
    const modalEl = new bootstrap.Modal(document.getElementById('projectViewModal'));
    modalEl.show();
}

function populateModal(data) {
    console.log("data",data);

    document.getElementById('modal-project-code').textContent = data.project_code;
    document.getElementById('modal-customer').textContent = data.customer_name;
    document.getElementById('modal-mobile').textContent = data.mobile;
    document.getElementById('modal-address').textContent = data.address || '—';
    document.getElementById('modal-kw').textContent = data.kw || '—';
    document.getElementById('modal-assignee').textContent = data.assignee_user.fname + " " + data.assignee_user.lname || '—';
    document.getElementById('modal-status').textContent = (window.__PROJECT_STATUSES||{})[data.status] || data.status;
    document.getElementById('modal-notes').textContent = data.project_note || '—';

    const docsWrap = document.getElementById('modal-documents');
    docsWrap.innerHTML = '';
    (data.documents || []).forEach(d => {
        const a = document.createElement('a');
        a.href = `/storage/${d.file_path}`;
        a.target = '_blank';
        a.textContent = d.type || 'file';
        const div = document.createElement('div');
        div.className = 'mb-2';
        div.appendChild(a);
        docsWrap.appendChild(div);
    });

    // fetch history and render
    fetch(`/projects/${data.id}/history`).then(r=>r.json()).then(hist=>{
        const wrap = document.getElementById('modal-history');
        wrap.innerHTML = '';
        hist.forEach(h=>{
            const node = document.createElement('div');
            node.className = 'mb-2';
            node.innerHTML = `<div class="small text-muted">${h.created_at} — ${h.changed_by ?? 'System'}</div><div>${h.notes}</div>`;
            wrap.appendChild(node);
        });
    });

    document.getElementById('modal-edit-link').href = `/projects/${data.id}/edit`;
}

/* helper functions */
function escapeHtml(unsafe) {
    return unsafe === null ? '' : String(unsafe).replace(/[&<"'>]/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#039;"}[m];});
}

function delay(fn, ms) {
    let timer = null;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), ms);
    };
}
