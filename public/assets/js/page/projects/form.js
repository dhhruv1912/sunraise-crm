let emi = { ...EXISTING_EMI };

document.addEventListener('DOMContentLoaded', renderEmi);
let quill;

document.addEventListener('DOMContentLoaded', () => {
    quill = new Quill('#projectNoteEditor', {
        theme: 'snow',
        placeholder: 'Add internal notes about this project...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });

    renderEmi();
});

function renderEmi() {
    const wrap = document.getElementById('emiRows');
    wrap.innerHTML = '';

    Object.keys(emi).forEach(date => {
        wrap.innerHTML += `
            <div class="row g-2 mb-1">
                <div class="col-md-4">
                    <input type="date"
                           class="form-control"
                           value="${date}"
                           onchange="updateEmiDate('${date}', this.value)">
                </div>
                <div class="col-md-4">
                    <input type="number"
                           class="form-control"
                           value="${emi[date]}"
                           onchange="emi['${date}'] = Number(this.value)">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="removeEmi('${date}')">
                        ✕
                    </button>
                </div>
            </div>
        `;
    });
}

function addEmiRow() {
    const today = new Date().toISOString().slice(0,10);
    emi[today] = 0;
    renderEmi();
}

function updateEmiDate(oldDate, newDate) {
    if (!newDate || emi[newDate]) return;
    emi[newDate] = emi[oldDate];
    delete emi[oldDate];
    renderEmi();
}

function removeEmi(date) {
    delete emi[date];
    renderEmi();
}

async function submitForm() {
    const form = document.getElementById('projectForm');
    const data = new FormData(form);

    data.append('emi', JSON.stringify(emi));
    data.set('project_note', quill.root.innerHTML);

    try {
        const res = await fetch(UPDATE_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.CSRF_TOKEN
            },
            body: data
        });

        const json = await res.json();

        if (!res.ok) {
            Object.values(json.message).forEach(m =>
                showToast('error', m)
            );
            return;
        }

        showToast('success', 'Project updated');

    } catch {
        showToast('error', 'Update failed');
    }
}
function openHoldModal() {
    document.getElementById('holdReason').value = '';
    new bootstrap.Modal('#holdModal').show();
}
async function confirmHold() {
    const reason = document.getElementById('holdReason').value.trim();

    if (!reason) {
        showToast('error', 'Hold reason is required');
        return;
    }

    await toggleHold(true, reason);
}
async function resumeProject() {
    await toggleHold(false, null);
}
async function toggleHold(isHold, reason) {

    const data = new FormData();
    data.append('is_on_hold', isHold ? 1 : 0);
    if (reason) data.append('hold_reason', reason);

    try {
        const res = await fetch(UPDATE_URL, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.CSRF_TOKEN
            },
            body: data
        });

        const json = await res.json();

        if (!res.ok) {
            Object.values(json.message).forEach(m =>
                showToast('error', m)
            );
            return;
        }

        showToast('success', isHold
            ? 'Project put on hold'
            : 'Project resumed');

        location.reload(); // reflect state safely

    } catch {
        showToast('error', 'Action failed');
    }
}
