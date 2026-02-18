document.addEventListener("DOMContentLoaded", () => {
    const searchBox = document.getElementById('searchBox');
    const perPage = document.getElementById('perPage');
    const dataBody = document.getElementById('dataBody');
    const pagination = document.getElementById('pagination');

    let currentPage = 1;

    async function load(page = 1) {
        currentPage = page;
        const q = encodeURIComponent(searchBox.value || '');
        const per = perPage.value || 20;
        let url = ""
        if(window.REQ_ID != ""){
            url = `${window.QUOTE_ROUTES.ajax}/${window.REQ_ID}?search=${q}&per_page=${per}&page=${page}`;
        }else{
            url = `${window.QUOTE_ROUTES.ajax}?search=${q}&per_page=${per}&page=${page}`;
        }
        const res = await fetch(url);
        const json = await res.json();

        renderRows(json.data || json.data === undefined ? (json.data || json) : []);
        renderPagination(json);
    }

    function renderRows(rows) {
        let html = '';
        rows.forEach(row => {
            const rq = row.lead || {};
            html += `<tr>
                <td>${row.id}</td>
                <td>${row.quotation_no}</td>
                <td>${rq.customer.name || '—'}</td>
                <td>${row.base_price ?? '-'}</td>
                <td>${row.discount ?? '-'}</td>
                <td>${row.final_price ?? '-'}</td>
                <td>${row.sent_at ? (new Date(row.sent_at)).toLocaleString() : '-'}</td>
                <td>
                    <a class="btn btn-sm btn-primary" href="/quotations/${row.id}/edit">Edit</a>
                    <button class="btn btn-sm btn-secondary" onclick="generatePdf(${row.id})">PDF</button>
                    <button class="btn btn-sm btn-success" onclick="sendEmail(${row.id})">Send</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteQuotation(${row.id})">Delete</button>
                </td>
            </tr>`;
        });
        dataBody.innerHTML = html || '<tr><td colspan="8" class="text-center">No records</td></tr>';
    }

    function renderPagination(json) {
        if(!json || !json.links) {
            pagination.innerHTML = '';
            return;
        }
        // simple pagination UI
        let html = `<div class="d-flex justify-content-between align-items-center">
            <div>Showing ${json.from || 0} — ${json.to || 0} of ${json.total || 0}</div>
            <ul class="pagination mb-0">`;
        json.links.forEach((link, idx) => {
            if(!link.url) {
                html += `<li class="page-item ${link.active ? 'active' : 'disabled'}"><span class="page-link">${link.label.replace(/&laquo;|&raquo;/g,'')}</span></li>`;
            } else {
                html += `<li class="page-item ${link.active ? 'active' : ''}"><a class="page-link" href="#" data-page="${idx+1}">${link.label.replace(/&laquo;|&raquo;/g,'')}</a></li>`;
            }
        });
        html += `</ul></div>`;
        pagination.innerHTML = html;

        // attach click listeners
        pagination.querySelectorAll('a.page-link[data-page]').forEach(a => {
            a.addEventListener('click', (ev) => {
                ev.preventDefault();
                const page = parseInt(a.dataset.page);
                load(page);
            });
        });
    }

    // initial load
    load();

    // events
    if (searchBox && searchBox.length > 0) {
        searchBox.addEventListener('input', () => load(1));
    }
    if (perPage && perPage.length > 0) {
        perPage.addEventListener('change', () => load(1));
    }

    // expose some actions globally
    window.generatePdf = async function(id) {
        const res = await fetch(`/quotations/${id}/generate-pdf`);
        const json = await res.json();
        if (json.status) {
            alert(json.message || 'PDF generated');
            // open in new tab
            if (json.pdf_url) window.open(json.pdf_url, '_blank');
            load(currentPage);
        } else {
            alert('Failed to generate PDF');
        }
    };

    window.sendEmail = async function(id) {
        const res = await fetch(`/quotations/${id}/send-email`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': (window.TOKEN || document.querySelector('meta[name="csrf-token"]')?.content) }
        });
        const json = await res.json();
        alert(json.message || (json.status ? 'Sent' : 'Failed'));
        load(currentPage);
    };

    window.deleteQuotation = async function(id) {
        if(!confirm('Delete quotation?')) return;
        const res = await fetch(`/quotations/${id}`, {
            method: 'DELETE',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': (window.TOKEN || document.querySelector('meta[name="csrf-token"]')?.content) }
        });
        const json = await res.json();
        alert(json.message || 'Deleted');
        load(currentPage);
    };
document.getElementById("create-project").addEventListener("click",createProject)

async function createProject() {
    if (!confirm("Create project from this lead?")) return;
    leadId = document.getElementById("create-project").dataset.id

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
});
