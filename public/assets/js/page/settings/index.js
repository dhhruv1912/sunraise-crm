function loadSettings(group) {
    const wrap = document.getElementById('settingsContent');
    wrap.innerHTML = `<div class="crm-loader-overlay"><div class="crm-spinner"></div></div>`;

    fetch(SETTINGS_LOAD_URL.replace(':group', group))
        .then(r => r.text())
        .then(html => wrap.innerHTML = html)
        .catch(() => {
            wrap.innerHTML = `<div class="text-muted">Failed to load settings</div>`;
        });
}
document.addEventListener('DOMContentLoaded',() => {
    loadSettings('genaral')
})
async function saveSettings(form) {
    const data = new FormData(form);

    try {
        const res = await fetch(SETTINGS_SAVE_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
            body: data
        });

        const json = await res.json();

        if (!res.ok) {
            Object.values(json.message).forEach(m => showToast('error', m));
            return;
        }

        showToast('success', 'Settings updated');

    } catch {
        showToast('error', 'Save failed');
    }
}
// async function saveSettings(form){
//     const data = new FormData(form);
//     console.log(data);
    
//     fetch(SETTINGS_SAVE_URL,{
//         method:'POST',
//         headers:{'X-CSRF-TOKEN':window.CSRF_TOKEN},
//         body:data
//     })
//     .then(r=>r.json())
//     .then(()=>showToast('success','Settings updated'));
// }
function addJsonRow(name) {
    const wrap = document.querySelector(`.json-fields[data-name="${name}"]`);
    wrap.insertAdjacentHTML('beforeend', `
        <div class="row mb-2 json-row">
            <div class="col">
                <input type="text" class="form-control json-key">
            </div>
            <div class="col-1 text-center align-self-center">=</div>
            <div class="col">
                <input type="text" class="form-control json-value">
            </div>
        </div>
    `);
}

document.addEventListener('submit', e => {
    if (!e.target.closest('form')) return;

    e.target.querySelectorAll('.json-fields').forEach(wrap => {
        const name = wrap.dataset.name;
        const data = {};

        wrap.querySelectorAll('.json-row').forEach(row => {
            const k = row.querySelector('.json-key').value;
            const v = row.querySelector('.json-value').value;
            if (k) data[k] = v;
        });

        const hidden = wrap.parentElement.querySelector('.json-output');
        hidden.value = JSON.stringify(data);
    });
});
function openSettingModal() {
    document.getElementById('settingForm').reset();
    document.getElementById('settingId').value = '';
    document.getElementById('settingModalTitle').innerText = 'Add Setting';
    new bootstrap.Modal('#settingModal').show();
}

function editSetting(id) {
    openSettingModal();
    document.getElementById('settingModalTitle').innerText = 'Edit Setting';
    s = document.getElementById(id).dataset.setting
    s = JSON.parse(s)
    console.log(s);
    
    Object.keys(s).forEach(k => {
        const el = document.querySelector(`[name="${k}"]`);
        if (el) el.value = s[k];
    });

    document.getElementById('settingId').value = s.id;
}

document.getElementById('settingForm').addEventListener('submit', async e => {
    e.preventDefault();
    let option 
    try {
        option= JSON.stringify(JSON.parse(document.querySelector('textarea[name="option"]').value))
    } catch (error) {
    }
    const form = e.target;
    const id = form.id.value;
    formData = new FormData(form)
    formData.append("options" , option)
    const url = id
        ? `/settings/ajax/${id}/update`
        : `/settings/ajax/create`;

    const res = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
        body: formData
    });

    if (!res.ok) {
        showToast('error', 'Save failed');
        return;
    }

    showToast('success', 'Setting saved');
    bootstrap.Modal.getInstance('#settingModal').hide();
    loadSettings(CURRENT_GROUP);
});

async function deleteSetting(id) {
    if (!confirm('Delete this setting?')) return;

    const res = await fetch(`/settings/ajax/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN }
    });

    if (!res.ok) {
        showToast('error', 'Delete failed');
        return;
    }

    showToast('success', 'Setting removed');
    loadSettings(CURRENT_GROUP);
}
