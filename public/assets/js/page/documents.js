// documents.js
document.addEventListener('DOMContentLoaded', function () {
    window.DOC = {
        page: 1
    };

    // init
    loadDocuments();

    document.getElementById('searchBox').addEventListener('input', () => {
        DOC.page = 1;
        loadDocuments();
    });

    document.getElementById('filterType').addEventListener('change', () => {
        DOC.page = 1;
        loadDocuments();
    });

    document.getElementById('perPage').addEventListener('change', () => {
        DOC.page = 1;
        loadDocuments();
    });

    document.getElementById('uploadBtn').addEventListener('click', uploadFiles);

    // click delegate for dynamic rows
    document.body.addEventListener('click', function (e) {
        if (e.target.classList.contains('doc-view-btn')) {
            const id = e.target.dataset.id;
            viewDocument(id);
        }
        if (e.target.classList.contains('doc-delete-btn')) {
            const id = e.target.dataset.id;
            deleteDocument(id);
        }
        // if (e.target.classList.contains('paginate-link')) {
        //     e.preventDefault();
        //     const p = e.target.dataset.page;
        //     DOC.page = parseInt(p);
        //     loadDocuments();
        // }
    });
});

async function loadDocuments() {
    const search = encodeURIComponent(document.getElementById('searchBox').value || '');
    const type = document.getElementById('filterType').value || '';
    const perPage = document.getElementById('perPage').value || 20;

    const res = await fetch(`/documents/ajax?search=${search}&filter_type=${type}&per_page=${perPage}&page=${window.DOC.page || 1}`);
    const json = await res.json();

    const body = document.getElementById('documentsBody');
    body.innerHTML = '';

    if (!json.data || json.data.length === 0) {
        body.innerHTML = '<tr><td colspan="8" class="text-center">No documents</td></tr>';
    } else {
        json.data.forEach(d => {
            const preview = d.mime_type && d.mime_type.startsWith('image') ? `<img src="${d.url}" class="rounded" style="height:48px;object-fit:cover" />` : '<i class="mdi mdi-file-outline" style="font-size:24px"></i>';
            const project = d.project_id ? (d.project ? d.project.project_code ?? d.project_id : d.project_id) : '—';
            const uploader = d.uploader.fname + " " + d.uploader.lname || '—';
            const size = d.human_size || (d.size ? (d.size/1024).toFixed(2)+' KB' : '—');
            const created = new Date(d.created_at).toLocaleString();

            body.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>${preview}</td>
                    <td>${escapeHtml(d.file_name)}</td>
                    <td>${escapeHtml(d.type || '—')}</td>
                    <td>${escapeHtml(project)}</td>
                    <td>${escapeHtml(uploader)}</td>
                    <td>${escapeHtml(size)}</td>
                    <td>${created}</td>
                    <td>
                        <button class="btn btn-sm btn-info doc-view-btn" data-id="${d.id}">View</button>
                        <button class="btn btn-sm btn-success" onclick="window.open('/documents/download/${d.id}','_blank')">Download</button>
                        <button class="btn btn-sm btn-danger doc-delete-btn" data-id="${d.id}">Delete</button>
                    </td>
                </tr>
            `);
        });
    }

    // pagination
    renderPagination(json);
}

function renderPagination(json) {
    const pagination = document.getElementById('documentsPagination');
    if(!json || !json.links) {
            pagination.innerHTML = '';
            return;
        }
        // simple pagination UI
        let html = `<div class="d-flex justify-content-between align-items-center">
            <div>Showing ${json.from || 0} — ${json.to || 0} of ${json.total || 0}</div>
            <ul class="pagination mb-0">`;
        json.links.forEach((link, idx) => {
            console.log(link, idx);

            if(!link.url) {
                html += `<li class="page-item ${link.active ? 'active' : 'disabled'}"><span class="page-link">${link.label.replace(/&laquo;|&raquo;/g,'')}</span></li>`;
            } else {
                html += `<li class="page-item ${link.active ? 'active' : ''}"><a class="page-link" href="#" data-page="${idx-1}">${link.label.replace(/&laquo;|&raquo;/g,'')}</a></li>`;
            }
        });
        html += `</ul></div>`;
        pagination.innerHTML = html;

        // attach click listeners
        pagination.querySelectorAll('a.page-link[data-page]').forEach(a => {
            a.addEventListener('click', (ev) => {
                ev.preventDefault();
                const page = parseInt(a.dataset.page);
                window.DOC.page = page
                loadDocuments();
            });
        });
}

async function viewDocument(id) {
    const res = await fetch(`/documents/view/${id}`);
    const json = await res.json();
    if (!json.status) return alert('Failed to load');

    const d = json.data;
    const modal = new bootstrap.Modal(document.getElementById('docViewModal'));
    document.getElementById('docModalTitle').textContent = d.file_name;
    document.getElementById('docFileName').textContent = d.file_name;
    document.getElementById('docTypeText').textContent = d.type || '—';
    document.getElementById('docProject').textContent = d.project ? (d.project.project_code || d.project.id) : '—';
    document.getElementById('docUploader').textContent = d.uploader ? d.uploader.name : (d.uploaded_by_name || '—');
    document.getElementById('docSize').textContent = d.human_size || (d.size ? (d.size/1024).toFixed(2) + ' KB' : '—');
    document.getElementById('docTagsText').textContent = (d.tags && d.tags.length) ? d.tags.join(', ') : '—';
    document.getElementById('docDescriptionText').textContent = d.description || '—';
    document.getElementById('docUploadedAt').textContent = new Date(d.created_at).toLocaleString();
    document.getElementById('docDownloadBtn').href = `/documents/download/${d.id}`;

    // preview
    const preview = document.getElementById('docPreview');
    preview.innerHTML = '';
    if (d.mime_type && d.mime_type.startsWith('image')) {
        preview.innerHTML = `<img src="${d.url}" class="img-fluid rounded-3" />`;
    } else if (d.mime_type && d.mime_type.includes('pdf')) {
        preview.innerHTML = `<embed src="${d.url}" type="application/pdf" width="100%" height="500px" />`;
    } else {
        preview.innerHTML = `<a target="_blank" href="${d.url}" class="btn btn-outline-primary">Open file</a>`;
    }

    // attach / detach buttons
    const attachBtn = document.getElementById('docAttachBtn');
    const detachBtn = document.getElementById('docDetachBtn');
    if (d.project_id) {
        detachBtn.classList.remove('d-none');
        attachBtn.classList.add('d-none');
    } else {
        attachBtn.classList.remove('d-none');
        detachBtn.classList.add('d-none');
    }

    attachBtn.onclick = async () => {
        const pid = prompt('Enter project ID to attach:');
        if (!pid) return;
        const resp = await fetch(`/documents/attach/${d.id}`, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': TOKEN},
            body: JSON.stringify({project_id: pid})
        });
        const j = await resp.json();
        alert(j.message || 'Done');
        modal.hide();
        loadDocuments();
    };

    detachBtn.onclick = async () => {
        const resp = await fetch(`/documents/detach/${d.id}`, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': TOKEN},
        });
        const j = await resp.json();
        alert(j.message || 'Done');
        modal.hide();
        loadDocuments();
    };

    modal.show();
}

async function deleteDocument(id) {
    if (!confirm('Delete this file?')) return;
    const res = await fetch(`/documents/delete/${id}`, {
        method: 'DELETE',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': TOKEN}
    });
    const j = await res.json();
    alert(j.message || 'Deleted');
    loadDocuments();
}

async function uploadFiles() {
    const files = document.getElementById('filesInput').files;
    if (!files.length) return alert('Select files');
    const fd = new FormData();
    for (let f of files) fd.append('files[]', f);
    fd.append('type', document.getElementById('docType').value || '');
    fd.append('description', document.getElementById('docDescription').value || '');
    fd.append('tags', document.getElementById('docTags').value || '');
    fd.append('tags', document.getElementById('docTags').value || '');
    if (document.getElementById('uploadProjectId').value) fd.append('project_id', document.getElementById('uploadProjectId').value);

    const res = await fetch('/documents/upload', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': TOKEN},
        body: fd
    });
    const j = await res.json();
    if (j.status) {
        alert(j.message || 'Uploaded');
        document.getElementById('docUploadForm').reset();
        bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
        loadDocuments();
    } else {
        alert('Upload failed');
        console.error(j);
    }
}

function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) {
        return ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;',
            "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;'
        })[s];
    });
}

document.addEventListener("DOMContentLoaded", () => {

    const input = document.getElementById("uploadProjectSearch");
    const hidden = document.getElementById("uploadProjectId");
    const dropdown = document.getElementById("projectSearchDropdown");

    let timer = null;

    input.addEventListener("input", function () {
        clearTimeout(timer);
        const query = this.value.trim();

        if (!query) {
            dropdown.style.display = "none";
            return;
        }

        timer = setTimeout(() => searchProjects(query), 300);
    });

    function searchProjects(query) {
        fetch(`/ajax/projects/search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(list => {
                dropdown.innerHTML = "";
                // dropdown.className = "bg-white"

                if (list.length === 0) {
                    dropdown.style.display = "none";
                    return;
                }

                list.forEach(project => {
                    const item = document.createElement("a");
                    item.href = "javascript:void(0)";
                    item.className = "list-group-item list-group-item-action";

                    item.innerHTML = `
                        <strong>#${project.id}</strong> —
                        ${project.project_code ?? '-'} <br>
                        <small>${project.customer_name ?? ''}</small>
                    `;

                    item.addEventListener("click", () => {
                        // Set values
                        input.value = `${project.project_code ?? project.id}`;
                        hidden.value = project.id;

                        dropdown.style.display = "none";
                    });

                    dropdown.appendChild(item);
                });

                dropdown.style.display = "block";
            })
            .catch(err => console.error("Project search error", err));
    }

    // Hide dropdown when clicking outside
    document.addEventListener("click", function (e) {
        if (!input.contains(e.target)) {
            dropdown.style.display = "none";
        }
    });

});

