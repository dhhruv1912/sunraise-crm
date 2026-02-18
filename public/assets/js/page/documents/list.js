let DOC_PAGE = 1;
let deleteModal;
document.addEventListener('DOMContentLoaded', () => {
    loadDocuments();
    loadDocumentWidgets();
    loadDocumentAdvancedWidgets()
    deleteModal = new bootstrap.Modal(
        document.getElementById('deleteDocModal')
    );

    document
        .getElementById('confirmDeleteBtn')
        .addEventListener('click', confirmDelete);
});
function loadDocumentWidgets() {
    fetch(DOC_WIDGET_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById('documentWidgets').innerHTML = html;
        });
}
function loadDocumentAdvancedWidgets() {
    fetch(DOC_ADV_WIDGET_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById(
                'documentAdvancedWidgets'
            ).innerHTML = html;
        });
}

async function loadDocuments(page = 1) {
    DOC_PAGE = page;

    const params = new URLSearchParams({
        page,
        per_page: 12,
        entity_type: document.getElementById('filterEntity').value,
        type: document.getElementById('filterType').value,
        search: document.getElementById('searchBox').value
    });

    const loader = document.getElementById('documentLoader');
    loader.classList.remove('d-none');

    try {
        const res = await fetch(DOC_LIST_URL + '?' + params.toString());
        const json = await res.json();

        renderDocumentGrid(json.data,json.canEdit);
        renderPagination(json.meta);

    } finally {
        loader.classList.add('d-none');
    }
}

function renderDocumentGrid(rows,canEdit) {
    const grid = document.getElementById('documentGrid');
    grid.innerHTML = '';

    if (!rows.length) {
        grid.innerHTML = `
            <div class="col-12 text-center text-muted py-4">
                No documents found
            </div>`;
        return;
    }

    rows.forEach(d => {
        grid.innerHTML += `
            <div class="col-md-3 col-sm-4 col-6">
                <div class="crm-doc-card">

                    <div class="crm-doc-preview">
                        ${
                            d.mime.startsWith('image/')
                            ? `<img src="${d.url}">`
                            : `<i class="fa-solid fa-file-pdf"></i>`
                        }
                    </div>

                    <div class="crm-doc-body">
                        <div class="fw-semibold text-truncate"
                             title="${d.file_name}">
                            ${d.file_name}
                        </div>

                        <div class="small text-muted">
                            ${humanize(d.type)} · ${humanize(d.entity)}
                        </div>

                        <div class="small text-muted">
                            ${d.created_at}
                        </div>
                    </div>

                    <div class="crm-doc-actions">
                        <a href="/documents/${d.id}"
                           class="btn btn-sm btn-light">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        ${canEdit ? `
                            <button class="btn-sm btn btn-outline-danger"
                                    onclick="openDeleteModal('${d.id}')">
                                <i class="fa-solid fa-trash me-1"></i>
                            </button>
                            <a href="${d.url}"
                               target="_blank"
                               class="btn btn-sm btn-light">
                                <i class="fa-solid fa-download"></i>
                            </a>
                            
                        `
                        : ""}
                    </div>

                </div>
            </div>
        `;
    });
}

function renderPagination(meta) {
    const wrap = document.getElementById('documentPagination');
    wrap.innerHTML = '';

    if (meta.last_page <= 1) return;

    for (let i = 1; i <= meta.last_page; i++) {
        wrap.innerHTML += `
            <button class="btn btn-sm ${
                meta.current_page === i
                    ? 'btn-primary'
                    : 'btn-outline-secondary'
            } me-1"
            onclick="loadDocuments(${i})">
                ${i}
            </button>
        `;
    }
}

function humanize(text) {
    return text
        ?.replaceAll('_', ' ')
        .replace(/\b\w/g, l => l.toUpperCase()) || '—';
}

function openDeleteModal(id=null) {
    if(id){
        document.getElementById("confirmDeleteBtn").dataset.id = id
    }
    deleteModal.show();
}

async function confirmDelete() {
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Deleting`;

    try {
        const res = await fetch("/documents/ajax/" + document.getElementById("confirmDeleteBtn").dataset.id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!res.ok) {
            showToast('error', 'Delete failed');
            return;
        }

        showToast('success', 'Document deleted');

        setTimeout(() => {
            window.location.href = '/documents';
        }, 600);

    } catch (e) {
        showToast('error', 'Delete error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `
            <i class="fa-solid fa-trash me-1"></i> Delete
        `;
        deleteModal.hide();
    }
}