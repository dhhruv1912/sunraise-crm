function loadProfileWidgets() {
    Promise.all([
        crmFetch(TIMELINE_URL).then(r => r.text()),
    ]).then(([timeline]) => {
        document.getElementById('profileTimeline').innerHTML =
            timeline;
    });
}
