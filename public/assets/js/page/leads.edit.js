document.addEventListener('DOMContentLoaded', () => {

    const form   = document.getElementById('leadEditForm');
    const loader = document.getElementById('leadEditLoader');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        loader.classList.remove('d-none');

        crmFetch(LEAD_UPDATE_URL, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(res => res.json())
        .then(res => {
            if (res.status) {
                showToast('success', res.message);

                setTimeout(() => {
                    window.location.href = LEAD_VIEW_URL;
                }, 600);
            } else {
                showToast('danger', res.message || 'Update failed');
            }
        })
        .catch(() => {
            showToast('danger', 'Something went wrong');
        })
        .finally(() => {
            loader.classList.add('d-none');
        });
    });

});
