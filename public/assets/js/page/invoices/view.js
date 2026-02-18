let paymentModal;
let EMI_MAP = {};
document.addEventListener('DOMContentLoaded', () => {
    loadPayments();
    loadWidgets()
    const btn = document.getElementById('addPaymentBtn');
    if (btn) {
        paymentModal = new bootstrap.Modal('#paymentModal');
        btn.onclick = () => paymentModal.show();
    }

    document.getElementById('paymentForm')?.addEventListener('submit', submitPayment);
});

document.getElementById('generatePdfBtn')?.addEventListener('click', async () => {

    try {
        // loader(true);

        const res = await fetch(GENERATE_PDF_URL, {
            method: 'POST',
            headers: {
                // 'Content-Type': 'application/json' ,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
        });

        const json = await res.json();

        if (!res.ok) {
            showToast(json.message || 'Failed to generate PDF', 'error');
            return;
        }

        showToast(json.message, 'success');

        // reload to refresh button + link
        setTimeout(() => location.reload(), 800);

    } catch {
        showToast('Server error', 'error');
    } finally {
        // loader(false);
    }
});

document.getElementById('sendEmailBtn')?.addEventListener('click', async () => {
    try {
        // loader(true);
        const res = await fetch(`/invoices/ajax/${INVOICE_ID}/send`, { method: 'POST' });
        const json = await res.json();
        showToast(json.message, 'success');
        location.reload();
    } catch {
        showToast('Email send failed', 'error');
    } finally {
        // loader(false);
    }
});
document.getElementById('emiSelect')
    .addEventListener('change', e => {

        const emiDate = e.target.value;

        const amt = document.getElementById('payAmount');
        const ref = document.getElementById('payRef');

        if (!emiDate) {
            amt.readOnly = false;
            ref.readOnly = false;
            return;
        }

        const emi = EMI_MAP[emiDate];

        amt.value = emi.amount;
        ref.value = `EMI ${emi.date}`;

        amt.readOnly = true;
        ref.readOnly = true;
    });

async function submitPayment() {
    const amount = Number(document.getElementById('payAmount').value);

    if (!amount) {
        showToast('Enter valid payment amount', 'error');
        return;
    }

    try {
        // loader(true);

        const res = await fetch(PAYMENTS_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                amount,
                method: document.getElementById('payMethod').value,
                reference: document.getElementById('payRef').value,
                paid_at: document.getElementById('payDate').value,
                emi_date: document.getElementById('emiSelect').value || null
            })
        });

        const json = await res.json();

        if (!res.ok) {
            showToast(json.message || 'Payment failed', 'error');
            return;
        }

        showToast(json.message, 'success');

        // reset form
        document.getElementById('payAmount').value = '';
        document.getElementById('payRef').value = '';

        loadPayments();

    } finally {
        // loader(false);
    }
}


async function submitPayment(e) {
    e.preventDefault();

    const payload = {
        amount: document.getElementById('pay_amount').value,
        method: document.getElementById('pay_method').value,
        reference: document.getElementById('pay_reference').value,
        paid_at: document.getElementById('pay_date').value,
    };

    try {
        // loader(true);

        const res = await fetch(INVOICE_PAYMENTS_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!res.ok) {
            const err = await res.json();
            showToast(err.message || 'Validation error', 'error');
            return;
        }

        showToast('Payment added', 'success');
        paymentModal.hide();
        loadPayments();
        location.reload(); // status + totals refresh

    } catch {
        showToast('Server error', 'error');
    } finally {
        // loader(false);
    }
}


function loadWidgets() {

    crmFetch(UPCOMING_PAYMENTS_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById('invoiceWidgets').innerHTML = html;
        });
}

async function loadPayments() {
    try {
        const res = await crmFetch(PAYMENTS_URL);
        const json = await res.json();

        document.getElementById('invTotal').innerText = Number(json.total).toFixed(2);
        document.getElementById('invPaid').innerText = Number(json.paid).toFixed(2);
        document.getElementById('invRemaining').innerText = Number(json.remaining).toFixed(2);
        const loader  = document.getElementById('paymentTableLoader');

        loader.classList.add('d-none');

        const tbody = document.getElementById('paymentTable');
        tbody.innerHTML = '';

        if (!json.payments.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        No payments recorded
                    </td>
                </tr>`;
            return;
        }

        json.payments.forEach(p => {
            tbody.innerHTML += `
                <tr>
                    <td>${p.date}</td>
                    <td>${p.method}</td>
                    <td>${p.ref ?? '—'}</td>
                    <td>₹ ${Number(p.amount).toFixed(2)}</td>
                    <td>${p.emi_date ?? '—'}</td>
                    <td>${p.by}</td>
                </tr>`;
        });

        const emiSel = document.getElementById('emiSelect');
        emiSel.innerHTML = `<option value="">Manual Payment</option>`;
        EMI_MAP = {};

        json.emis.forEach(e => {
            EMI_MAP[e.date] = e;

            emiSel.innerHTML += `
                <option value="${e.date}"
                        ${e.paid ? 'disabled' : ''}>
                    ${e.date} — ₹ ${e.amount}
                    ${e.paid ? '(Paid)' : ''}
                </option>`;
        });

    } catch (e) {
        showToast('Failed to load payments', 'error');
    }
}