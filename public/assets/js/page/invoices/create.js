let items = [];

document.addEventListener('DOMContentLoaded', () => {
    addItem();
    document.getElementById('addItem').onclick = addItem;
    document.getElementById('invoiceForm').onsubmit = submitForm;
});

function addItem() {
    items.push({ description: '', unit_price: 0, quantity: 1, tax: 0 });
    renderItems();
}

function removeItem(i) {
    items.splice(i, 1);
    renderItems();
}

function renderItems() {
    const body = document.getElementById('itemsBody');
    let total = 0;

    body.innerHTML = items.map((r, i) => {
        const line = (r.unit_price * r.quantity) + (+r.tax || 0);
        total += line;
        return `
        <tr>
            <td><input class="form-control" value="${r.description}"
                oninput="items[${i}].description=this.value"></td>
            <td><input type="number" class="form-control" value="${r.unit_price}"
                oninput="items[${i}].unit_price=+this.value"></td>
            <td><input type="number" class="form-control" value="${r.quantity}"
                oninput="items[${i}].quantity=+this.value"></td>
            <td><input type="number" class="form-control" value="${r.tax}"
                oninput="items[${i}].tax=+this.value"></td>
            <td>₹ ${line.toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="removeItem(${i})">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
    }).join('');

    const discount = +document.getElementById('discount').value || 0;
    document.getElementById('grandTotal').innerText = (total - discount).toFixed(2);
}

async function submitForm(e) {
    e.preventDefault();

    const payload = {
        customer_id: document.getElementById('customer_id').value,
        invoice_date: document.getElementById('invoice_date').value,
        due_date: document.getElementById('due_date').value,
        discount: document.getElementById('discount').value,
        items: items
    };

    try {
        loader(true);
        const res = await fetch(INVOICE_STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const json = await res.json();
        showToast(json.message, 'success');
        window.location.href = `/invoices/${json.id}`;

    } catch {
        showToast('Validation or server error', 'error');
    } finally {
        loader(false);
    }
}
