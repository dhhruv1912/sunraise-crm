const LEAD_WIDGET_URL = "/leads/ajax/widgets";
const LEAD_ALERT_URL  = "/leads/ajax/alerts";

document.addEventListener('DOMContentLoaded', () => {
    loadLeadWidgets();
    loadLeadAlerts();
});

function loadLeadWidgets() {
    crmFetch(LEAD_WIDGET_URL)
        .then(res => res.text())
        .then(html => {
            document.getElementById('leadWidgets').innerHTML = html;

            const overdueWidget = document.getElementById('leadOverdueWidget');
            if (overdueWidget) {
                overdueWidget.onclick = () => {
                    document.getElementById('filterStatus').value = '';
                    document.getElementById('filterFollowup').value = 'overdue';
                    loadLeads();
                };
            }
        });
}

function loadLeadAlerts() {
    crmFetch(LEAD_ALERT_URL)
        .then(res => res.json())
        .then(data => {
            if (data.overdue > 0) {
                showToast(
                    'warning',
                    `${data.overdue} leads have overdue follow-ups`
                );
            }
        });
}
