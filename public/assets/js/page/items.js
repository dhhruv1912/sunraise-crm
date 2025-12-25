document.addEventListener("DOMContentLoaded", () => {
    loadItems();

    document.getElementById("itemSearch").addEventListener("input", loadItems);
    document.getElementById("filterCategory").addEventListener("change", loadItems);
});

function loadItems(page = 1) {
    const params = {
        search: document.getElementById("itemSearch").value,
        category_id: document.getElementById("filterCategory").value,
        page
    };

    fetch(`/items/list?` + new URLSearchParams(params))
        .then(res => res.json())
        .then(res => renderItemsTable(res.data));
}

function renderItemsTable(data) {
    let html = `
        <table class="table table-hover">
            <thead>
                <tr style="color: var(--arham-text-heading);">
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>SKU</th>
                    <th>Model</th>
                    <th>Watt</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
    `;

    if (data.data.length === 0) {
        html += `<tr><td colspan="7" class="text-center text-muted">No items found</td></tr>`;
    }

    data.data.forEach(i => {
        html += `
            <tr>
                <td>
                    ${i.image ? `<img src="/storage/${i.image}" style="width:50px;">` : '-'}
                </td>
                <td>${i.name}</td>
                <td>${i.category?.name ?? ''}</td>
                <td>${i.sku ?? ''}</td>
                <td>${i.model ?? ''}</td>
                <td>${i.watt ?? ''}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editItem(${i.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteItem(${i.id})">Delete</button>
                </td>
            </tr>
        `;
    });

    html += `</tbody></table>`;

    // Pagination
    html += `<nav><ul class="pagination justify-content-center">`;
    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <a class="page-link" href="javascript:loadItems(${i})">${i}</a>
            </li>
        `;
    }
    html += `</ul></nav>`;

    document.getElementById("itemsTable").innerHTML = html;
}

// Create item
function openCreateItemModal() {
    document.getElementById("itemModalTitle").innerText = "Add Item";

    document.getElementById("item_id").value = "";
    document.getElementById("item_category").value = "";
    document.getElementById("item_name").value = "";
    document.getElementById("item_sku").value = "";
    document.getElementById("item_model").value = "";
    document.getElementById("item_watt").value = "";
    document.getElementById("item_description").value = "";
    document.getElementById("itemImagePreview").style.display = "none";

    new bootstrap.Modal(document.getElementById("itemModal")).show();
}

// Edit item
function editItem(id) {
    fetch(`/items/${id}`)
        .then(res => res.json())
        .then(res => {
            const i = res.data;

            document.getElementById("itemModalTitle").innerText = "Edit Item";

            document.getElementById("item_id").value = i.id;
            document.getElementById("item_category").value = i.category_id;
            document.getElementById("item_name").value = i.name;
            document.getElementById("item_sku").value = i.sku ?? '';
            document.getElementById("item_model").value = i.model ?? '';
            document.getElementById("item_watt").value = i.watt ?? '';
            document.getElementById("item_description").value = i.description ?? '';

            if (i.image) {
                document.getElementById("itemImagePreview").src = `/storage/${i.image}`;
                document.getElementById("itemImagePreview").style.display = "block";
            }

            new bootstrap.Modal(document.getElementById("itemModal")).show();
        });
}

// Submit form
document.getElementById("itemForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const id = document.getElementById("item_id").value;
    const url = id ? `/items/${id}/update` : "/items/store";

    const formData = new FormData();
    formData.append("category_id", document.getElementById("item_category").value);
    formData.append("name", document.getElementById("item_name").value);
    formData.append("sku", document.getElementById("item_sku").value);
    formData.append("model", document.getElementById("item_model").value);
    formData.append("watt", document.getElementById("item_watt").value);
    formData.append("description", document.getElementById("item_description").value);

    const file = document.getElementById("item_image").files[0];
    if (file) formData.append("image", file);

    fetch(url, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
        .then(res => res.json())
        .then(res => {
            loadItems();
            bootstrap.Modal.getInstance(document.getElementById("itemModal")).hide();
        });
});

// Delete item
function deleteItem(id) {
    if (!confirm("Delete this item?")) return;

    fetch(`/items/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(res => res.json())
        .then(() => loadItems());
}
