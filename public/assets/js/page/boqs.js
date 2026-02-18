document.addEventListener("DOMContentLoaded", () => {
    loadBoqs();
    const EditItemModal = new bootstrap.Modal(document.getElementById("EditItemModal"))

    document.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.edit-boq-item');
        const deleteBtn = e.target.closest('.delete-boq-item');

        if (editBtn) {
            e.preventDefault();
            const id = editBtn.dataset.id;
            editItem(id)
        }

        if (deleteBtn) {
            e.preventDefault();
            const id = deleteBtn.dataset.id;
            deleteItem(id);
        }
    });
    document.querySelector('.add-boq-item').addEventListener('click', (e) => {
        populateEditModal({})
    })

    document.getElementById("itemDropper").addEventListener('change', (e) => {
        const [name, unit] = e.target.value.split('-')
        console.log(name, unit);
        if (name) {
            document.getElementById("itemName").value = name
        } else {
            document.getElementById("itemName").value = ""
        }
        if (unit) {
            document.getElementById("itemUnit").value = unit
        } else {
            document.getElementById("itemUnit").value = ""
        }
    })
    const rateInput = document.getElementById('itemRate');
    const qtyInput = document.getElementById('itemQuentity');

    ['input', 'change'].forEach(evt => {
        rateInput?.addEventListener(evt, recalculateAmount);
        qtyInput?.addEventListener(evt, recalculateAmount);
    });

    document.getElementById("saveItem").addEventListener("click", function (e) {
        e.preventDefault();

        const id = document.getElementById("item_id").value;
        const project_id = document.getElementById("project_id").value
        const boq_id = document.getElementById("boq_id").value
        const url = id ? `/projects/boq/${project_id}/${boq_id}/save/${id}` : `/projects/boq/${project_id}/${boq_id}/save`;
        const formData = new FormData();
        formData.append("name", document.getElementById("itemName").value);
        formData.append("unit", document.getElementById("itemUnit").value);
        formData.append("rate", document.getElementById("itemRate").value);
        formData.append("quentity", document.getElementById("itemQuentity").value);
        formData.append("amount", document.getElementById("itemAmount").value);
        formData.append("specification", document.getElementById("specification").value);

        fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
            .then(res => res.json())
            .then(res => {
                loadBoqs();
                EditItemModal.hide();
                // bootstrap.Modal.getInstance(document.getElementById("categoryModal")).hide();
            });
    });


    function editItem(id) {
        project_id = document.getElementById("project_id").value
        boq_id = document.getElementById("boq_id").value
        fetch(`/projects/boq/${project_id}/${boq_id}/edit/${id}`)
            .then(res => res.json())
            .then(res => {
                populateEditModal(res.boq)
            });
    }

    function populateEditModal(params) {
        const modalTitel = document.getElementById("modalTitel")
        const item_id = document.getElementById("item_id")
        const itemName = document.getElementById("itemName")
        const itemUnit = document.getElementById("itemUnit")
        const itemRate = document.getElementById("itemRate")
        const itemQuentity = document.getElementById("itemQuentity")
        const itemAmount = document.getElementById("itemAmount")
        const specification = document.getElementById("specification")
        item_id.value = params.boq_id || ""
        itemName.value = params.item || ""
        itemUnit.value = params.unit || ""
        itemRate.value = params.rate || ""
        itemQuentity.value = params.quantity || ""
        itemAmount.value = params.amount || ""
        specification.value = params.specification || ""
        modalTitel.value = params.boq_id ? "Edit" : "Add"

        EditItemModal.show();
    }

});

function recalculateAmount() {
    const itemRate = document.getElementById("itemRate")
    const itemQuentity = document.getElementById("itemQuentity")
    const itemAmount = document.getElementById("itemAmount")
    itemAmount.value = itemQuentity.value * itemRate.value
}

function loadBoqs(page = 1) {
    project_id = document.getElementById("project_id").value
    boq_id = document.getElementById("boq_id").value

    fetch(`/projects/boq/${project_id}/${boq_id}/list`)
        .then(res => res.json())
        .then(res => renderTable(res.boq));
}

function renderTable(data) {
    console.log("data", data);

    let html = ``;

    if (data.length === 0) {
        html += `<tr><td colspan="7" class="text-center text-muted">No results</td></tr>`;
    }

    data.forEach((row,id) => {
        html += `
            <tr>
                <td>${id+1}</td>
                <td>${row.item}</td>
                <td>${row.quantity} ${row.unit}</td>
                <td>${row.rate ?? ''}</td>
                <td>${row.amount ?? ''}</td>
                <td>${row.specification ?? ''}</td>
                <td>
                    <button type="button" class="btn btn-sm icon-base btn-outline-warning edit-boq-item" data-id="${row.id}">Edit</button>
                    <button type="button" class="btn btn-sm icon-base btn-outline-danger delete-boq-item" data-id="${row.id}">Delete</button>
                </td>
            </tr>
        `;
    });



    document.getElementById("boqItems").innerHTML = html;
}


function deleteItem(id) {
    if (!confirm("Are you sure?")) return;
    project_id = document.getElementById("project_id").value
    boq_id = document.getElementById("boq_id").value
    fetch(`/projects/boq/${project_id}/${boq_id}/delete/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(res => res.json())
        .then(() => loadBoqs());
}
