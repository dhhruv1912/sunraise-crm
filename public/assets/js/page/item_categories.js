document.addEventListener("DOMContentLoaded", () => {
    loadCategories();

    document.getElementById("searchCategory").addEventListener("input", loadCategories);
});

function loadCategories(page = 1) {
    const search = document.getElementById("searchCategory").value;

    fetch(`/item-categories/list?search=${search}&page=${page}`)
        .then(res => res.json())
        .then(res => renderTable(res.data));
}

function renderTable(data) {
    let html = `
        <table class="table table-hover">
            <thead>
                <tr style="color: var(--arham-text-heading);">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
    `;

    if (data.data.length === 0) {
        html += `<tr><td colspan="4" class="text-center text-muted">No results</td></tr>`;
    }

    data.data.forEach(row => {
        html += `
            <tr>
                <td>${row.id}</td>
                <td>${row.name}</td>
                <td>${row.description ?? ''}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editCategory(${row.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCategory(${row.id})">Delete</button>
                </td>
            </tr>
        `;
    });

    html += `
        </tbody>
        </table>
    `;

    // Pagination
    html += `<nav><ul class="pagination justify-content-center">`;
    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" href="javascript:loadCategories(${i})">${i}</a>
            </li>
        `;
    }
    html += `</ul></nav>`;

    document.getElementById("categoryTable").innerHTML = html;
}

// Modal open for create
function openCreateCategoryModal() {
    document.getElementById("categoryModalTitle").innerText = "Add Category";
    document.getElementById("cat_id").value = "";
    document.getElementById("cat_name").value = "";
    document.getElementById("cat_description").value = "";

    new bootstrap.Modal(document.getElementById("categoryModal")).show();
}

// Edit category
function editCategory(id) {
    fetch(`/item-categories/${id}`)
        .then(res => res.json())
        .then(res => {
            const c = res.data;

            document.getElementById("categoryModalTitle").innerText = "Edit Category";
            document.getElementById("cat_id").value = c.id;
            document.getElementById("cat_name").value = c.name;
            document.getElementById("cat_description").value = c.description ?? '';

            new bootstrap.Modal(document.getElementById("categoryModal")).show();
        });
}

// Submit form
document.getElementById("categoryForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const id = document.getElementById("cat_id").value;
    const url = id ? `/item-categories/${id}/update` : "/item-categories/store";

    const formData = new FormData();
    formData.append("name", document.getElementById("cat_name").value);
    formData.append("description", document.getElementById("cat_description").value);

    fetch(url, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
        .then(res => res.json())
        .then(res => {
            loadCategories();
            bootstrap.Modal.getInstance(document.getElementById("categoryModal")).hide();
        });
});

// Delete
function deleteCategory(id) {
    if (!confirm("Are you sure?")) return;

    fetch(`/item-categories/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(res => res.json())
        .then(() => loadCategories());
}
