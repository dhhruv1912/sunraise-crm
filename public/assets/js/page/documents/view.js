let deleteModal;

document.addEventListener('DOMContentLoaded', () => {
    deleteModal = new bootstrap.Modal(
        document.getElementById('deleteDocModal')
    );

    document
        .getElementById('confirmDeleteBtn')
        .addEventListener('click', confirmDelete);
});

function openDeleteModal() {
    deleteModal.show();
}

async function confirmDelete() {
    const btn = document.getElementById('confirmDeleteBtn');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Deleting`;

    try {
        const res = await fetch(DOC_DELETE_URL, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!res.ok) {
            showToast('error', 'Delete failed');
            return;
        }

        showToast('success', 'Document deleted');

        setTimeout(() => {
            window.location.href = '/documents';
        }, 600);

    } catch (e) {
        showToast('error', 'Delete error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `
            <i class="fa-solid fa-trash me-1"></i> Delete
        `;
        deleteModal.hide();
    }
}
