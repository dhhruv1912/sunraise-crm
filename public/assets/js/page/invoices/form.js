document.addEventListener('DOMContentLoaded', () => {

    if (typeof CREATE_URL !== 'undefined') {
        loadProjects();
    }

    if (typeof UPDATE_URL !== 'undefined') {
        if (!items.length || !items[0].is_quote_master) {
            loadQuoteMaster(document.getElementById('project_id').value);
        } else {
            renderItems();
            enableItems();
        }
    }

    document.getElementById('invoiceForm')
        .addEventListener('submit', submitForm);

    document.getElementById('project_id')
        ?.addEventListener('change', onProjectChange);

    document.getElementById('discount')
        ?.addEventListener('input', recalculateTotals);
});

/* ================= PROJECT ================= */

async function loadProjects() {
    try {
        const res = await crmFetch(PROJECTS_URL);
        const rows = await res.json();

        const sel = document.getElementById('project_id');
        rows.forEach(p => {
            sel.innerHTML += `
                <option value="${p.id}"
                        data-customer="${p.customer_id}">
                    ${p.project_code}
                </option>`;
        });
    } catch {
        showToast('Failed to load projects', 'error');
    }
}

function onProjectChange(e) {
    const opt = e.target.selectedOptions[0];
    if (!opt) return;

    document.getElementById('customer_id').value = opt.dataset.customer;
    document.getElementById('customer_id').disabled = false;

    loadQuoteMaster(e.target.value);
}

/* ================= QUOTE MASTER ================= */

async function loadQuoteMaster(projectId) {
    try {
        // loader(true);

        const res = await crmFetch(`/invoices/ajax/quote-master/${projectId}`);
        const qm  = await res.json();

        items = [{
            is_quote_master : true,
            quote_master_id: qm.quote_master_id,
            description    : qm.description,
            unit_price     : Number(qm.unit_price),
            quantity       : Number(qm.quantity),
            tax            : Number(qm.tax || 0)
        }];

        renderItems();
        enableItems();

    } catch {
        showToast('Failed to load quote master', 'error');
    } finally {
        // loader(false);
    }
}

/* ================= ITEMS ================= */

function enableItems() {
    document.getElementById('addItemBtn')?.removeAttribute('disabled');
}

function addItem() {
    items.push({
        is_quote_master : false,
        quote_master_id: null,
        description    : '',
        unit_price     : 0,
        quantity       : 1,
        tax            : 0
    });
    renderItems();
}

function removeItem(i) {
    if (items[i].is_quote_master) return;
    items.splice(i, 1);
    renderItems();
}

/* ================= RENDER ================= */

function renderItems() {
    const body = document.getElementById('itemsBody');

    body.innerHTML = items.map((r, i) => {
        const subtotal = (Number(r.unit_price) * Number(r.quantity)) + Number(r.tax);
        
        return `
        <tr>
            <td>
                <input class="form-control"
                       value="${r.description}"
                       ${r.is_quote_master ? 'readonly' : ''}
                       id="description${i}"
                       data-key="${i}-description"
                       oninput="updateItem(${i},'description',this.value)">
            </td>

            <td>
                <input type="number"
                       class="form-control"
                       value="${r.unit_price}"
                       id="unit_price${i}"
                       data-key="${i}-unit_price"
                       oninput="updateItem(${i},'unit_price',this.value)">
            </td>

            <td>
                <input type="number"
                       class="form-control"
                       value="${r.quantity}"
                       ${r.is_quote_master ? 'readonly' : ''}
                       id="quantity${i}"
                       data-key="${i}-quantity"
                       oninput="updateItem(${i},'quantity',this.value)">
            </td>

            <td>
                <input type="number"
                       class="form-control"
                       value="${r.tax}"
                       id="tax${i}"
                       data-key="$tax{i}-"
                       oninput="updateItem(${i},'tax',this.value)">
            </td>

            <td class="fw-semibold">
                ₹ <span id="subtotal-${i}">${subtotal?.toFixed(2)}</span>
            </td>

            <td>
                ${
                    r.is_quote_master ? '' :
                    `<button type="button"
                             class="btn btn-sm btn-light"
                             onclick="removeItem(${i})">
                        <i class="fa-solid fa-trash"></i>
                     </button>`
                }
            </td>
        </tr>`;
    }).join('');

    recalculateTotals();
}

function updateItem(i, field, value) {

    items[i][field] =
        field === 'description'
            ? value
            : Number(value) || 0;

    // update line subtotal only
    const r = items[i];
    const subtotal =
        (r.unit_price * r.quantity) + (r.tax || 0);

    const cell = document.getElementById(`subtotal-${i}`);
    if (cell) {
        cell.innerText = subtotal.toFixed(2);
    }

    // recalc bottom totals only
    recalculateTotals();
}


/* ================= TOTAL CALC ================= */

function recalculateTotals() {
    let totalPrice = 0;
    let totalTax   = 0;

    items.forEach(r => {
        totalPrice += (Number(r.unit_price) * Number(r.quantity));
        totalTax   += (Number(r.tax) || 0);
    });

    const discount = Number(
        document.getElementById('discount')?.value || 0
    );

    const grandTotal = Math.max(
        (totalPrice + totalTax) - discount,
        0
    );

    document.getElementById('totalPrice').innerText = totalPrice.toFixed(2);
    document.getElementById('totalTax').innerText   = totalTax.toFixed(2);
    document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
}

/* ================= SUBMIT ================= */

async function submitForm(e) {
    e.preventDefault();

    const payload = {
        project_id  : document.getElementById('project_id').value,
        customer_id : document.getElementById('customer_id').value,
        invoice_date: document.getElementById('invoice_date').value,
        due_date    : document.getElementById('due_date').value,
        discount    : document.getElementById('discount').value,
        items       : items
    };

    const url = typeof UPDATE_URL !== 'undefined'
        ? UPDATE_URL
        : CREATE_URL;

    try {
        // loader(true);

        const res = await crmFetch(url, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json' ,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(payload)
        });

        const json = await res.json();

        if (!res.ok) {
            showToast(json.message || 'Validation error', 'error');
            return;
        }

        showToast(json.message || 'Invoice saved', 'success');
        window.location.href = `/invoices/${json.id ?? ''}`;

    } catch {
        showToast('Server error', 'error');
    } finally {
        // loader(false);
    }
}
