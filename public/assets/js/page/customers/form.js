document.getElementById('customerForm')
    ?.addEventListener('submit', submitCustomerForm);

async function submitCustomerForm(e) {
    e.preventDefault();

    const data = Object.fromEntries(
        new FormData(e.target).entries()
    );

    try {
        // loader(true);

        const res = await fetch(UPDATE_URL, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.CSRF_TOKEN
            },
            body: JSON.stringify(data)
        });

        const json = await res.json();

        if (!res.ok) {
            showToast('error',json.message || 'Validation failed');
            return;
        }

        showToast('success','Customer updated successfully');

        // setTimeout(() => {
        //     window.location.href = `/customers/${ENTITY_ID}`;
        // }, 500);

    } finally {
        // loader(false);
    }
}

/* ================= DOCUMENT UPLOAD ================= */

function openDocUpload(type) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*,.pdf';

    input.onchange = async () => {
        const file = input.files[0];
        if (!file) return;

        const data = new FormData();
        data.append('entity_type', 'customer');
        data.append('entity_id', ENTITY_ID);
        data.append('type', type);
        data.append('file', file);

        try {
            const res = await fetch(DOC_UPLOAD_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.CSRF_TOKEN
                },
                body: data
            });
            if (!res.ok) {
                const er = await res.json();

                if (er.errors) {
                    Object.values(er.errors).forEach(messages => {
                        messages.forEach(msg => {
                            showToast('danger', msg);
                        });
                    });
                } else if (er.message) {
                    showToast('danger', er.message);
                }
                return;
            }

            const doc = await res.json();
            showToast('success', 'Document uploaded');

            renderDocPreview(type, doc);

        } catch (e) {
            console.error(e);
            
            showToast('danger', 'Upload error');
        }
    };

    input.click();
}
function renderDocPreview(type, doc) {
    const slot = document.querySelector(
        `#preview-${type}`
    );
    const slotWrapper = document.querySelector(
        `#wrapper-${type}`
    );
    if (!slot) return;

    
    // let previewHtml = '';

    // if (doc.mime.startsWith('image/')) {
    //     previewHtml = `
    //         <div class="crm-doc-preview-box">
    //             <img src="${doc.url}" class="img-fluid rounded mb-2"
    //                  style="max-height:120px; object-fit:contain;">
    //             <button class="btn btn-sm btn-outline-primary w-100"
    //                     onclick="openDocUpload('${type}')">
    //                 Replace
    //             </button>
    //         </div>
    //     `;
    // } else {
    //     previewHtml = `
    //         <div class="crm-doc-preview-box text-center">
    //             <i class="fa-solid fa-file-pdf fa-2x text-danger mb-2"></i>
    //             <a href="${doc.url}" target="_blank"
    //                class="btn btn-sm btn-outline-success w-100 mb-1">
    //                 Preview
    //             </a>
    //             <button class="btn btn-sm btn-outline-primary w-100"
    //                     onclick="openDocUpload('${type}')">
    //                 Replace
    //             </button>
    //         </div>
    //     `;
    // }

    // slot.innerHTML = previewHtml;
    slot.src = doc.url
    slotWrapper.classList.remove('crm-badge-missing')
}
