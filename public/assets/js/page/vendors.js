const vendorModal = new bootstrap.Modal(document.getElementById('vendorModal'));

function loadVendors(page = 1) {
    let search = document.getElementById("searchVendor").value;

    fetch(`/vendors/list?search=${search}&page=${page}`)
        .then(res => res.json())
        .then(res => {
            let html = "";

            res.data.data.forEach(v => {
                html += `
                        <tr>
                            <td>${v.name}</td>
                            <td>${v.company_name}</td>
                            <td>${v.pan_number}</td>
                            <td>${v.gst_number}</td>
                            <td>${v.phone}</td>
                            <td>${v.email}</td>
                            <td>
                                <div class="mt-2">
                                    <button class="btn btn-warning btn-sm" onclick="editVendor(${v.id})">Edit</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteVendor(${v.id})">Delete</button>
                                </div>
                            </td>
                        </tr>
                `;
            });

            paginate(res.data);
            document.getElementById("vendorList").innerHTML = html;
        });
}
function paginate(data) {
    let html = `<nav><ul class="pagination">`;
    for (let i = 1; i <= data.last_page; i++) {
        html += `
            <li class="page-item ${i == data.current_page ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadVendors(${i})">${i}</a>
            </li>`;
    }
    html += `</ul></nav>`;
    document.getElementById("vendorPaginate").innerHTML = html;
}

document.getElementById("searchVendor").addEventListener("keyup", () => loadVendors());

// Load initial list
loadVendors();

document.getElementById("addVendorBtn").addEventListener("click", () => {
    document.getElementById("vendorForm").reset();
    document.getElementById("vendor_id").value = "";
    vendorModal.show();
});

// Submit form
document.getElementById("vendorForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let id = document.getElementById("vendor_id").value;
    let url = id ? `/vendors/update/${id}` : `/vendors/store`;

    let data = {
        name: document.getElementById("name").value,
        company_name: document.getElementById("company_name").value,
        email: document.getElementById("email").value,
        phone: document.getElementById("phone").value,
        gst_number: document.getElementById("gst_number").value,
        pan_number: document.getElementById("pan_number").value,
        address: document.getElementById("address").value,
        type: document.getElementById("type").value,
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
                vendorModal.hide();
                loadVendors();
            }
        });
});

// Edit vendor
function editVendor(id) {
    fetch(`/vendors/edit/${id}`)
        .then(res => res.json())
        .then(v => {
            document.getElementById("vendor_id").value = v.id;
            document.getElementById("name").value = v.name;
            document.getElementById("company_name").value = v.company_name;
            document.getElementById("email").value = v.email;
            document.getElementById("phone").value = v.phone;
            document.getElementById("gst_number").value = v.gst_number;
            document.getElementById("pan_number").value = v.pan_number;
            document.getElementById("address").value = v.address;
            document.getElementById("type").value = v.type;

            vendorModal.show();
        });
}

// Delete vendor
function deleteVendor(id) {
    if (!confirm("Delete this vendor?")) return;

    fetch(`/vendors/delete/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(res => res.json())
        .then(res => {
            if (res.success) loadVendors();
        });
}
