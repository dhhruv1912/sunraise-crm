document.addEventListener('DOMContentLoaded', loadDashboard);

async function loadDashboard() {
    const wrap = document.getElementById('projectDashboard');

    try {
        const res = await fetch(DASHBOARD_URL);
        wrap.innerHTML = await res.text();
    } catch (e) {
        showToast('error', 'Failed to load dashboard');
    }
}
async function moveToNextStatus() {
    try {
        const res = await fetch(DASHBOARD_STATUS_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.CSRF_TOKEN
            }
        });

        if (!res.ok) {
            showToast('error', 'Status update failed');
            return;
        }

        showToast('success', 'Project moved to next stage');
        loadDashboard();

    } catch (e) {
        showToast('error', 'Status error');
    }
}

async function completeMilestone(key) {
    try {
        const res = await fetch(
            `/projects/ajax/${PROJECT_ID}/milestone/${key}`,
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                }
            }
        );

        if (!res.ok) {
            showToast('error', 'Milestone update failed');
            return;
        }

        showToast('success', 'Milestone completed');
        loadDashboard();

    } catch (e) {
        console.log(e);

        showToast('error', 'Milestone error');
    }
}

function uploadDoc(type, entity, entity_id, type_, multiple = false) {

    const URL = type_ === 'customer'
        ? DOC_UPLOAD_URL_CUSTOMER
        : DOC_UPLOAD_URL_PROJECT;

    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*,.pdf';
    input.multiple = multiple;

    input.onchange = async () => {

        if (!input.files || input.files.length === 0) return;

        const data = new FormData();

        if (multiple) {
            Array.from(input.files).forEach((file, index) => {
                data.append(`file[${index}]`, file);
            });
        } else {
            data.append('file', input.files[0]);
        }

        data.append('entity_type', entity);
        data.append('entity_id', entity_id);
        data.append('type', type);
        data.append('multiple', multiple);

        try {
            const res = await fetch(URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                },
                body: data
            });

            const json = await res.json();

            if (!res.ok) {
                if (json.message) {
                    Object.values(json.message).flat().forEach(msg =>
                        showToast('error', msg)
                    );
                } else {
                    showToast('error', 'Upload failed');
                }
                return;
            }

            showToast('success', multiple
                ? 'Documents uploaded'
                : 'Document uploaded'
            );

            loadDashboard();

        } catch (e) {
            showToast('error', 'Upload failed');
        }
    };

    input.click();
}

function previewDoc(url) {
    window.open(url, '_blank');
}

document.addEventListener('change', e => {
    if (e.target.id === 'emiDate') {
        const opt = e.target.selectedOptions[0];
        if (!opt) return;

        document.getElementById('emiAmount').value =
            opt.dataset.amount || '';
        document.getElementById('emiRef').value =
            'EMI ' + opt.text.trim();
    }
});

async function submitEmiPayment() {
    const emiDate = document.getElementById('emiDate').value;
    const amount  = document.getElementById('emiAmount').value;
    const ref     = document.getElementById('emiRef').value;

    if (!emiDate || !amount) {
        showToast('error', 'Select EMI date');
        return;
    }

    const data = new FormData();
    data.append('amount', amount);
    data.append('method', 'emi');
    data.append('reference', ref);
    data.append('emi_date', emiDate);

    try {
        const res = await fetch(EMI_PAY_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
            body: data
        });

        const json = await res.json();

        if (!res.ok) {
            showToast('error', json.message || 'Payment failed');
            return;
        }

        showToast('success', 'EMI recorded');
        loadDashboard();

    } catch {
        showToast('error', 'EMI payment error');
    }
}
