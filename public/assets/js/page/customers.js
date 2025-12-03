document.addEventListener("DOMContentLoaded", () => {
    loadList();

    document.getElementById("searchBox").addEventListener("input", () => loadList());
    document.getElementById("perPage").addEventListener("change", () => loadList());
});

async function loadList(page = 1) {
    const search = document.getElementById("searchBox").value;
    const perPage = document.getElementById("perPage").value;

    const res = await fetch(`/customers/ajax?search=${search}&page=${page}&per_page=${perPage}`);
    const json = await res.json();

    let html = "";
    json.data.forEach(row => {
        html += `
            <tr>
                <td>${row.name}</td>
                <td>${row.email ?? '-'}</td>
                <td>${row.mobile ?? '-'}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewCustomer(${row.id})">View</button>
                    <a href="/customers/edit/${row.id}" class="btn btn-sm btn-primary">Edit</a>
                    <button class="btn btn-sm btn-danger" onclick="delCustomer(${row.id})">Delete</button>
                </td>
            </tr>
        `;
    });

    document.getElementById("dataBody").innerHTML = html;

    renderPagination(json);
}

/* VIEW */
async function viewCustomer(id) {
    const res = await fetch(`/customers/view-json/${id}`);
    const row = await res.json();

    document.getElementById("m-name").textContent = row.name;
    document.getElementById("m-email").textContent = row.email ?? '-';
    document.getElementById("m-mobile").textContent = row.mobile ?? '-';
    document.getElementById("m-alt").textContent = row.alternate_mobile ?? '-';
    document.getElementById("m-address").textContent = row.address ?? '-';
    document.getElementById("m-note").textContent = row.note ?? '-';

    new bootstrap.Modal(document.getElementById("customerViewModal")).show();
}

/* DELETE */
async function delCustomer(id) {
    if (!confirm("Delete?")) return;

    const res = await fetch("/customers/delete", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": TOKEN
        },
        body: JSON.stringify({ id })
    });

    loadList();
}

/* PAGINATION */
function renderPagination(json) {
    let html = `<div class="mt-3">`;

    json.links.forEach(link => {
        html += `<button class="btn btn-sm btn-light ${link.active ? 'btn-primary' : ''}"
                    onclick="loadList(${link.url ? link.url.split('page=')[1] : 1})"
                >${link.label}</button>`;
    });

    html += `</div>`;
    document.getElementById("pagination").innerHTML = html;
}
