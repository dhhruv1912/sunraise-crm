const warehouseModal = new bootstrap.Modal(document.getElementById('warehouseModal'));
console.log(warehouseModal);

function loadWarehouses(page = 1) {
    let search = document.getElementById("searchWarehouse").value;

    fetch(`/warehouse/list?search=${search}&page=${page}`)
        .then(res => res.json())
        .then(res => {
            let html = "";

            res.data.data.forEach(loc => {
                html += `
                    <tr>
                        <td>${loc.name}</td>
                        <td>${loc.code}</td>
                        <td>${loc.address}</td>
                        <td>${loc.city} - ${loc.state}<br>${loc.pincode}</td>
                        <td>
                            ${loc.location ? '<button type="button" class="btn btn-icon btn-outline-primary">' + 
                                '<a target="_blank" href="'+loc.location+'" class="icon-base mdi mdi-map-marker-right-outline icon-22px"></a>' + 
                            '</button>' : "-"}
                        </td>
                        <td>
                            <div class="mt-2">
                                <button class="btn btn-warning btn-sm" onclick="editWarehouse(${loc.id})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteWarehouse(${loc.id})">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += paginate(res.data);
            document.getElementById("warehouseList").innerHTML = html;
        });
}

// <div class="card mb-2 p-3">
//     <b>${loc.name}</b> (${loc.code ?? ''})<br>
//     <small>${loc.city ?? ''}</small>


// </div>
function paginate(data) {
    let html = `<nav><ul class="pagination">`;
    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <li class="page-item ${i == data.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadWarehouses(${i})">${i}</a>
            </li>`;
    }
    html += `</ul></nav>`;
    return html;
}

document.getElementById("searchWarehouse").addEventListener("keyup", () => loadWarehouses());

loadWarehouses();

document.getElementById("addWarehouseBtn").addEventListener("click", () => {
    document.getElementById("warehouseForm").reset();
    document.getElementById("warehouse_id").value = "";
    warehouseModal.show();
});

document.getElementById("warehouseForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let id = document.getElementById("warehouse_id").value;
    let url = id ? `/warehouse/update/${id}` : `/warehouse/store`;

    let data = {
        name: document.getElementById("name").value,
        code: document.getElementById("code").value,
        address: document.getElementById("address").value,
        city: document.getElementById("city").value,
        state: document.getElementById("state").value,
        pincode: document.getElementById("pincode").value,
        location: document.getElementById("location").value,
        cords: document.getElementById("cords").value,
    };

    fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                warehouseModal.hide();
                loadWarehouses();
            }
        });
});

function editWarehouse(id) {
    fetch(`/warehouse/edit/${id}`)
        .then(res => res.json())
        .then(loc => {
            document.getElementById("warehouse_id").value = loc.id;
            document.getElementById("name").value = loc.name;
            document.getElementById("code").value = loc.code;
            document.getElementById("address").value = loc.address;
            document.getElementById("city").value = loc.city;
            document.getElementById("state").value = loc.state;
            document.getElementById("pincode").value = loc.pincode;
            document.getElementById("location").value = loc.location,
            document.getElementById("cords").value = loc.cords,

            warehouseModal.show();
        });
}

function deleteWarehouse(id) {
    if (!confirm("Delete this location?")) return;

    fetch(`/warehouse/delete/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(res => res.json())
        .then(res => {
            if (res.success) loadWarehouses();
        });
}
