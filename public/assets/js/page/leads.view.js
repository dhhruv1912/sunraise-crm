/* =====================================================
   Lead → Project Conversion Canvas
===================================================== */

let leadCanvas;
let projectAmount = 0;

/* ================= CANVAS INIT ================= */
document.addEventListener('DOMContentLoaded', () => {
    const canvasEl = document.getElementById('leadConvertCanvas');
    if (canvasEl) {
        leadCanvas = new bootstrap.Offcanvas(canvasEl);
    }
});

/* ================= OPEN / CLOSE ================= */
function openConvertCanvas(leadId) {
    crmFetch(`/leads/${leadId}/convert`)
        .then(res => res.json())
        .then(res => {

            if (!res.status && res.code === 409) {
                showToast('warning', 'Project already exists for this lead');
                return;
            }

            document.getElementById('leadCanvasBody').innerHTML = res.html;
            leadCanvas.show();

            initProjectAmount();
            loadDefaultMilestones();
        });
}

function closeLeadCanvas() {
    leadCanvas?.hide();
}

/* ================= PROJECT SUBMIT ================= */
function submitLeadConversion(leadId) {

    if (!validateBeforeSubmit()) return;

    const loader = document.getElementById('convertLoader');
    loader.classList.remove('d-none');

    const payload = {
        finalize_price: projectAmount,
        priority: document.getElementById('priority').value,
        emis: collectEmis(),
        milestones: collectMilestones()
    };

    crmFetch(`/leads/ajax/${leadId}/convert`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(res => {
        if (!res.status) {
            showToast('danger', res.message || 'Conversion failed');
            return;
        }

        showToast('success', 'Project created successfully');
        window.location.href = res.redirect;
    })
    .finally(() => loader.classList.add('d-none'));
}

/* =====================================================
   EMI SECTION
===================================================== */

function initProjectAmount() {
    const input = document.getElementById('finalizePrice');
    projectAmount = parseFloat(input.value || 0);
    recalcEmi();
}

function addEmiRow() {
    const row = document.createElement('div');
    row.className = 'row g-2 align-items-end mb-2 emi-row';

    row.innerHTML = `
        <div class="col-md-5">
            <label class="form-label small">Due Date</label>
            <input type="date" class="form-control emi-date">
        </div>
        <div class="col-md-5">
            <label class="form-label small">Amount</label>
            <input type="number"
                   class="form-control emi-amount">
        </div>
        <div class="col-md-2 text-end">
            <button class="btn btn-sm btn-outline-danger"
                    type="button"
                    onclick="this.closest('.emi-row').remove(); recalcEmi();">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    `;

    document.getElementById('emiRows').appendChild(row);
}

function recalcEmi() {
    let total = 0;

    document.querySelectorAll('.emi-amount').forEach(i => {
        total += parseFloat(i.value || 0);
    });

    document.getElementById('totalEmi').innerText =
        '₹ ' + total.toLocaleString('en-IN');

    const remaining = projectAmount - total;

    document.getElementById('remainingAmount').innerText =
        '₹ ' + Math.max(remaining, 0).toLocaleString('en-IN');
}

document.addEventListener('input', e => {
    if (e.target.id === 'finalizePrice') {
        projectAmount = parseFloat(e.target.value || 0);
        recalcEmi();
    }
    if (e.target.classList.contains('emi-amount')) {
        recalcEmi();
    }
});

function collectEmis() {
    const emis = [];

    document.querySelectorAll('.emi-row').forEach(row => {
        const date = row.querySelector('.emi-date').value;
        const amount = row.querySelector('.emi-amount').value;

        if (date && amount) {
            emis.push({ due_date: date, amount });
        }
    });

    return emis;
}

/* =====================================================
   MILESTONES
===================================================== */

const DEFAULT_MILESTONES = [
    'Survey',
    'Installation Start',
    'Installation Complete',
    'Inspection',
    'Meter File',
    'Subsidy Claim',
    'Handover'
];

function loadDefaultMilestones() {
    const wrap = document.getElementById('milestoneRows');
    if (!wrap || wrap.children.length) return;

    DEFAULT_MILESTONES.forEach(name => addMilestoneRow(name));
}

function addMilestoneRow(name = '') {
    const row = document.createElement('div');
    row.className = 'row g-2 align-items-end mb-2 milestone-row';

    row.innerHTML = `
        <div class="col-md-6">
            <label class="form-label small">Milestone</label>
            <input type="text"
                   class="form-control milestone-name"
                   value="${name}">
        </div>
        <div class="col-md-4">
            <label class="form-label small">Expected Date</label>
            <input type="date"
                   class="form-control milestone-date">
        </div>
        <div class="col-md-2 text-end">
            <button class="btn btn-sm btn-outline-danger"
                    type="button"
                    onclick="this.closest('.milestone-row').remove()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    `;

    document.getElementById('milestoneRows').appendChild(row);
}

function collectMilestones() {
    const milestones = [];

    document.querySelectorAll('.milestone-row').forEach(row => {
        const name = row.querySelector('.milestone-name').value;
        const date = row.querySelector('.milestone-date').value;

        if (name) {
            milestones.push({
                name,
                expected_date: date || null
            });
        }
    });

    return milestones;
}

/* =====================================================
   VALIDATION + TOASTS
===================================================== */

function validateBeforeSubmit() {

    if (!projectAmount || projectAmount <= 0) {
        showToast('danger', 'Final price is required');
        return false;
    }

    let totalEmi = 0;
    let invalidEmi = false;

    document.querySelectorAll('.emi-row').forEach(row => {
        const amount = parseFloat(row.querySelector('.emi-amount').value || 0);
        const date = row.querySelector('.emi-date').value;

        if (!amount || !date) invalidEmi = true;
        totalEmi += amount;
    });

    if (invalidEmi) {
        showToast('danger', 'Each EMI must have amount and due date');
        return false;
    }

    if (totalEmi > projectAmount) {
        showToast('danger', 'EMI total exceeds project amount');
        return false;
    }

    let milestoneWarn = false;
    document.querySelectorAll('.milestone-row').forEach(row => {
        if (row.querySelector('.milestone-name').value &&
            !row.querySelector('.milestone-date').value) {
            milestoneWarn = true;
        }
    });

    if (milestoneWarn) {
        showToast('warning', 'Some milestones have no expected date');
    }

    return true;
}
function generateQuotationPdf(id) {
    crmFetch(`/quotations/ajax/${id}/generate-pdf`)
        .then(res => res.json())
        .then(res => {
            if (!res.status) {
                showToast('danger', res.message || 'PDF generation failed');
                return;
            }
            showToast('success', 'PDF generated');
            location.reload();
        });
}
