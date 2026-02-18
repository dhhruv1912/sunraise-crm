document.addEventListener('DOMContentLoaded', () => {
    loadReport('execution');
});

async function loadReport(type) {
    const wrap = document.getElementById('reportContainer');
    wrap.innerHTML = `
        <div class="crm-loader-overlay">
            <div class="crm-spinner"></div>
        </div>
    `;

    document.querySelectorAll('.nav-link').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');

    try {
        const res = await fetch(REPORT_URLS[type]);
        wrap.innerHTML = await res.text();
    } catch {
        showToast('error', 'Failed to load report');
    }
}
