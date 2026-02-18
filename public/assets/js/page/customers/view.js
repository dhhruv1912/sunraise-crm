document.addEventListener('DOMContentLoaded', () => {
    loadActivities();
    loadDocuments();
    const quill = new Quill('#customerNote', {
        theme: 'snow'
    });
});


/* ================= ACTIVITIES ================= */

function loadActivities() {
    crmFetch(ACTIVITY_URL)
        .then(r => r.text())
        .then(html => {
            document.getElementById('customerActivities').innerHTML = html;
        });
}

/* ================= DOCUMENTS ================= */

function loadDocuments() {
    crmFetch(DOCUMENTS_URL)
        .then(r => r.text())
        .then(html => {
            document.getElementById('customerDocuments').innerHTML = html;
        });
}