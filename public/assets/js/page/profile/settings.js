let CURRENT_USER_GROUP = 'appearance';
document.addEventListener('DOMContentLoaded', () => {
    loadUserSettings(CURRENT_USER_GROUP);
});

function loadUserSettings(group) {
    CURRENT_USER_GROUP = group;

    const wrap = document.getElementById('userSettingsContent');
    wrap.innerHTML = `<div class="crm-loader-overlay"><div class="crm-spinner"></div></div>`;
    document.getElementById("SettingBtn" + group).classList.remove('btn-outline-secondary')
    const activeItems = document.getElementsByClassName('active-setting-btn');

    for (const item of activeItems) {
        item.classList.remove('btn-outline-primary', 'active-setting-btn')
        item.classList.add('btn-outline-secondary')
    }
    document.getElementById("SettingBtn" + group).classList.add('active-setting-btn', 'btn-outline-primary')
    fetch(USER_SETTINGS_URL + '?group=' + group)
        .then(r => r.text())
        .then(html => wrap.innerHTML = html)
        .catch(() => {
            wrap.innerHTML = `<div class="text-muted">Failed to load settings</div>`;
        });
}

async function saveUserSettings(form) {
    const res = await fetch(
        "{{ route('profile.ajax.settings.save') }}",
        {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN },
            body: new FormData(form)
        }
    );

    if (!res.ok) {
        showToast('error', 'Failed to save settings');
        return;
    }

    showToast('success', 'Settings saved');
}
async function resetUserSettings() {
    const res = await fetch(
        "{{ route('profile.ajax.settings.reset') }}",
        {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN }
        }
    );

    if (!res.ok) {
        showToast('error', 'Reset failed');
        return;
    }

    showToast('success', 'Settings reset to default');
    loadUserSettings(CURRENT_USER_GROUP);
}
