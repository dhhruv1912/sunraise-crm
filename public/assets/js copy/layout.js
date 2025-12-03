window.BASE_URL = "{{ url('/') }}";
window.BASE_URL = document.querySelector('meta[name="base_url"]')?.content || "{{ url('/') }}";
window.TOKEN    = document.querySelector('meta[name="csrf_token"]')?.content || "{{ csrf_token() }}";

// Global Page Loader Logic
function show_fs_loader() {
    $('.loader-img').removeClass('d-none');
}

function hide_fs_loader() {
    $('.loader-img').addClass('d-none');
}

$(document).ready(function () {
    hide_fs_loader();
});


// SIMPLE ALERT
window.showAlert = function (message, type = 'alert-primary') {
    const el = document.getElementById('alert-simple');
    el.className = `alert ${type}`;
    el.textContent = message;
    el.classList.remove('d-none');

    setTimeout(() => el.classList.add('d-none'), 3000);
};

// DISMISSIBLE ALERT
window.showDismissible = function (message, type = 'alert-success') {
    const el = document.getElementById('alert-dismissible');
    el.className = `alert alert-dismissible fade show ${type}`;
    el.querySelector('.alert-text').textContent = message;
    el.classList.remove('d-none');
};

// CONFIRM ALERT (OK / Cancel)
window.showConfirm = function (message, type = 'alert-warning', onYes = null, onNo = null) {
    const el = document.getElementById('alert-confirm');

    el.className = `alert d-flex align-items-center ${type}`;
    el.querySelector('.alert-text').textContent = message;
    el.classList.remove('d-none');

    // Button binding
    el.querySelector('.btn-confirm-true').onclick = () => {
        el.classList.add('d-none');
        if (onYes) onYes();
    };

    el.querySelector('.btn-confirm-false').onclick = () => {
        el.classList.add('d-none');
        if (onNo) onNo();
    };
};
