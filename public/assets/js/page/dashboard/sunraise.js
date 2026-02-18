function hideLoader(el){
    el.querySelector('.crm-loader-overlay')?.remove();
}

function format(n){
    return Number(n || 0).toLocaleString('en-IN');
}
const DASHBOARD_REFRESH_INTERVAL =  60 * 1000 * 5; // minutes
let dashboardRefreshTimer = null;

function startDashboardAutoRefresh() {
    if (dashboardRefreshTimer) return;

    dashboardRefreshTimer = setInterval(() => {
        refreshLightWidgets();
    }, DASHBOARD_REFRESH_INTERVAL);
}

function refreshLightWidgets() {
    loadTop();
    loadEmi();
    loadProjects();
    loadTimeline('overdue', 'overdueList');
    loadTimeline('upcoming', 'upcomingList');
    loadActivity();
    loadAiInsights();
}

async function loadTop() {
    const wrap = document.getElementById('dashTop');

    try {
        const res = await crmFetch('/dashboard/sunraise/ajax/top');
        const d = await res.json();

        wrap.innerHTML = `
            ${stat('Projects', d.projects, 'primary')}
            ${stat('Active', d.active, 'success')}
            ${stat('Invoice Value', '₹ ' + format(d.invoiced), 'info')}
            ${stat('Outstanding', '₹ ' + format(d.due), 'danger')}
        `;
    } catch {
        showToast('error','Failed to load dashboard KPIs');
    } finally {
        hideLoader(wrap);
    }
}

function stat(label, value, color){
    return `
    <div class="col-md-3">
        <div class="crm-stat crm-section text-${color}">
            <div class="small">${label}</div>
            <div class="fs-4 fw-bold">${value}</div>
        </div>
    </div>`;
}
async function loadFinance() {
    const wrap = document.getElementById('invoiceChart').closest('.position-relative');

    try {
        const res = await crmFetch('/dashboard/sunraise/ajax/invoice-trend');
        const d = await res.json();

        new Chart(invoiceChart, {
            type:'line',
            data:{
                labels:Object.keys(d),
                datasets:[{
                    data:Object.values(d),
                    tension:.4
                }]
            },
            options:{ plugins:{ legend:{ display:false } } }
        });
    } catch {
        showToast('error','Invoice trend failed');
    } finally {
        hideLoader(wrap);
    }
}
async function loadEmi() {
    const wrap = document.getElementById('emiSummary').closest('.position-relative');

    try {
        const res = await crmFetch('/dashboard/sunraise/ajax/emi');
        const d = await res.json();

        emiSummary.innerHTML = `
            <div class="d-flex justify-content-between">
                <div><div class="small">Paid</div><b class="text-success">₹ ${format(d.paid)}</b></div>
                <div><div class="small">Upcoming</div><b class="text-warning">₹ ${format(d.upcoming)}</b></div>
                <div><div class="small">Overdue</div><b class="text-danger">₹ ${format(d.overdue)}</b></div>
            </div>
        `;
    } finally {
        hideLoader(wrap);
    }
}
async function loadProjects() {
    const wrap = document.getElementById('projectHealth').closest('.position-relative');

    try {
        const res = await crmFetch('/dashboard/sunraise/ajax/projects');
        const rows = await res.json();

        projectHealth.innerHTML = rows.map(p => `
            <div class="col-md-3">
                <div class="crm-section">
                    <div class="fw-semibold">${p.code}</div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-${p.color}"
                             style="width:${p.progress}%">
                            ${p.progress}%
                        </div>
                    </div>
                    <div class="small text-muted">${p.status}</div>
                </div>
            </div>
        `).join('');
    } finally {
        hideLoader(wrap);
    }
}
async function loadTimeline(type, el) {
    const wrap = document.getElementById(el).closest('.position-relative');

    try {
        const res = await crmFetch(`/dashboard/sunraise/ajax/${type}`);
        const rows = await res.json();

        document.getElementById(el).innerHTML =
            rows.map(r => `
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <div class="fw-semibold">${r.title}</div>
                        <div class="small text-muted">${r.sub}</div>
                    </div>
                    <b class="text-${r.color}">₹ ${format(r.amount)}</b>
                </div>
            `).join('');
    } finally {
        hideLoader(wrap);
    }
}
async function loadWorkload() {
    const wrap = document.getElementById('workloadChart').closest('.position-relative');

    try {
        const res = await crmFetch('/dashboard/sunraise/ajax/workload');
        const rows = await res.json();

        new Chart(workloadChart, {
            type:'bar',
            data:{
                labels:rows.map(r=>r.name),
                datasets:[{ data:rows.map(r=>r.total) }]
            },
            options:{ plugins:{ legend:{ display:false } } }
        });
    } finally {
        hideLoader(wrap);
    }
}
async function loadActivity() {
    const wrap = document.getElementById('activityTimeline').closest('.position-relative');

    try {
        const res = await crmFetch('/dashboard/sunraise/ajax/activity');
        const rows = await res.json();

        activityTimeline.innerHTML = rows.map(a => `
            <div class="mb-2">
                <span class="badge bg-${a.color}">${a.type}</span>
                ${a.text}
                <div class="small text-muted">${a.time}</div>
            </div>
        `).join('');
    } finally {
        hideLoader(wrap);
    }
}
document.addEventListener('DOMContentLoaded', () => {
    loadTop();
    loadFinance();
    loadEmi();
    loadProjects();
    loadTimeline('overdue','overdueList');
    loadTimeline('upcoming','upcomingList');
    loadWorkload();
    loadActivity();
    loadAiInsights();

    startDashboardAutoRefresh();
});
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(dashboardRefreshTimer);
        dashboardRefreshTimer = null;
    } else {
        startDashboardAutoRefresh();
    }
});
async function loadAiInsights() {
    const wrap = document.getElementById('aiInsights');

    try {
        const res = await crmFetch('/dashboard/sunraise/ajax/insights');
        const rows = await res.json();

        aiInsightList.innerHTML = rows.map(i => `
            <div class="d-flex align-items-start mb-2">
                <span class="badge bg-${i.color} me-2">${i.type}</span>
                <div>${i.text}</div>
            </div>
        `).join('');
    } catch {
        showToast('error', 'Insight engine failed');
    } finally {
        hideLoader(wrap);
    }
}

