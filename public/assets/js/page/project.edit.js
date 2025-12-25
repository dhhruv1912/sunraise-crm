document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab');

    const tabList = document.getElementById('projectTabList');
    if (!tabList || typeof bootstrap === 'undefined') return;

    // Find tab button by tab param
    const triggerEl = document.querySelector(
        `#projectTabList button[data-bs-target="#navs-pills-justified-${tab}"]`
    );

    if (tab && triggerEl) {
        // Create instance if not exists, then show
        const tabInstance = bootstrap.Tab.getOrCreateInstance(triggerEl);
        tabInstance.show();
    } else {
        // Fallback to first tab
        const firstTabEl = tabList.querySelector('button[data-bs-toggle="tab"]');
        if (firstTabEl) {
            bootstrap.Tab.getOrCreateInstance(firstTabEl).show();
        }
    }
    /* ---------------------------------
     |  Populate Assignee / Reporter / Status
     |----------------------------------*/
    const users = window.__PROJECT_USERS || [];
    const statuses = window.__PROJECT_STATUSES || {};

    function fillSelect(selector, list, selectedId) {
        const el = document.querySelector(selector);
        if (!el) return;

        list.forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.textContent = `${u.fname} ${u.lname}`;
            if (selectedId === u.id) opt.selected = true;
            el.appendChild(opt);
        });

        el.dataset.old = el.value;
    }

    fillSelect('#subMenuBar .assignee-select', users, PROJECT.assignee);
    fillSelect('#subMenuBar .reporter-select', users, PROJECT.reporter);

    const statusSelect = document.querySelector('#subMenuBar .status-select');
    if (statusSelect) {
        Object.keys(statuses).forEach(key => {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = statuses[key];
            if (PROJECT.status === key) opt.selected = true;
            statusSelect.appendChild(opt);
        });
        statusSelect.dataset.old = statusSelect.value;
    }

    /* ---------------------------------
     |  Generic Select Updater
     |----------------------------------*/
    function bindSelectUpdate(selector, urlKey, payloadKey) {
        document.querySelectorAll(selector).forEach(select => {

            select.addEventListener('change', async () => {
                const id = select.dataset.id;
                const value = select.value;
                select.disabled = true;

                try {
                    const res = await fetch(`/projects/${id}/${urlKey}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': TOKEN
                        },
                        body: JSON.stringify({ [payloadKey]: value })
                    });

                    const data = await res.json();
                    if (!data.status) throw new Error();

                    select.dataset.old = value;

                } catch {
                    alert('Update failed');
                    select.value = select.dataset.old;
                } finally {
                    select.disabled = false;
                }
            });
        });
    }

    bindSelectUpdate('.assignee-select', 'assign', 'assignee');
    bindSelectUpdate('.reporter-select', 'reporter', 'reporter');
    bindSelectUpdate('.status-select', 'status', 'status');
    bindSelectUpdate('.priority-select', 'priority', 'priority');

    /* ---------------------------------
     |  Hold Project Logic
     |----------------------------------*/
    const holdCheckbox = document.getElementById("HoldProject");
    const holdModalEl = document.getElementById("holdProjectModal");
    const holdModal = holdModalEl ? new bootstrap.Modal(holdModalEl) : null;

    if (holdCheckbox) {
        holdCheckbox.addEventListener('change', async (e) => {
            if (e.target.checked) {
                holdModal.show();
            } else {
                const ok = await updateProjectHold(false);
                if (!ok) holdCheckbox.checked = true;
            }
        });
    }

    document.getElementById("saveHold")?.addEventListener('click', async () => {
        const ok = await updateProjectHold(true);
        if (!ok) holdCheckbox.checked = false;
    });

    async function updateProjectHold(isHold) {
        const projectId = document.getElementById("project_id").value;

        try {
            const res = await fetch(`/projects/${projectId}/hold`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    is_hold: isHold,
                    reason: isHold ? document.getElementById("hold_reason")?.value : null
                })
            });

            const data = await res.json();
            if (!data.success) throw new Error();

            holdModal?.hide();
            return true;

        } catch {
            alert('Hold update failed');
            return false;
        }
    }

    /* ---------------------------------
     |  Steps Modal Logic
     |----------------------------------*/
    const editNextStep = document.getElementById("editNextStep");
    const saveSteps = document.getElementById("saveSteps");
    const addStepItem = document.getElementById("addStepItem");
    const completeCurrentStep = document.getElementById("completeCurrentStep");
    const stepWrapper = document.getElementById("stepWrapper");

    const nextStepModalEl = document.getElementById("nextStepModal");
    const nextStepModal = nextStepModalEl ? new bootstrap.Modal(nextStepModalEl) : null;

    editNextStep?.addEventListener('click', () => nextStepModal.show());

    addStepItem?.addEventListener('click', () => {
        const stepID = document.querySelectorAll('.steps-field').length + 1;

        const idDiv = document.createElement('div');
        idDiv.className = 'col-1 stepID';
        idDiv.textContent = stepID;

        const stepDiv = document.createElement('div');
        stepDiv.className = 'col-9';
        const input = document.createElement('input');
        input.className = 'form-control steps-field';
        input.value = `Step ${stepID}`;
        stepDiv.appendChild(input);

        const actionDiv = document.createElement('div');
        actionDiv.className = 'col-2';
        actionDiv.innerHTML = `
            <button class="btn btn-sm btn-outline-danger deleteStepItem">
                <span class="mdi mdi-delete"></span>
            </button>
        `;

        stepWrapper.append(idDiv, stepDiv, actionDiv);
    });

    stepWrapper?.addEventListener('click', (e) => {
        const btn = e.target.closest('.deleteStepItem');
        if (!btn) return;

        btn.closest('.col-2').previousElementSibling.remove();
        btn.closest('.col-2').previousElementSibling.remove();
        btn.closest('.col-2').remove();
        reindexSteps();
    });

    function reindexSteps() {
        document.querySelectorAll('.stepID').forEach((el, i) => el.textContent = i + 1);
    }

    saveSteps?.addEventListener('click', async () => {
        const steps = [...document.querySelectorAll('.steps-field')]
            .map(i => i.value.trim())
            .filter(Boolean);

        if (!steps.length) {
            alert('Add at least one step');
            return;
        }

        const projectId = document.getElementById("project_id").value;

        const res = await fetch(`/projects/${projectId}/step/edit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ steps })
        });

        const data = await res.json();
        if (data.status) {
            updateStepUI(data.current_step, data.next_step);
            updateStepEditModal(data.next_step)
            nextStepModal.hide();
        }
    });

    completeCurrentStep?.addEventListener('click', async () => {
        if (!confirm('Complete current step?')) return;

        const projectId = document.getElementById("project_id").value;
        const res = await fetch(`/projects/${projectId}/step/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await res.json();
        if (data.status) {
            updateStepUI(data.current_step, data.next_step);
            updateStepEditModal(data.next_step); // 🔥 FIX
        }
        if (!data.next_step.length) {
            completeCurrentStep.disabled = true;
        }
    });

    function updateStepUI(currentStep, nextSteps = []) {
        const currentEl = document.getElementById("currentStepText");
        const list = document.getElementById("stepsList");

        currentEl.textContent = currentStep || 'All steps completed 🎉';
        list.innerHTML = '';

        nextSteps.forEach(step => {
            const a = document.createElement('a');
            a.className = 'list-group-item list-group-item-action';
            a.textContent = step;
            list.appendChild(a);
        });
    }

    const saveProjectBtn = document.getElementById("saveProjectData")
    saveProjectBtn.addEventListener('click', async (e) => {
        const fd = new FormData();
        // for (let f of files) fd.append('files[]', f);
        const projectId = document.getElementById('project_id').value
        if (projectId) fd.append('project_id', projectId);
        fd.append('survey_date', document.getElementById('survey_date').value || '');
        fd.append('installation_start_date', document.getElementById('installation_start_date').value || '');
        fd.append('installation_end_date', document.getElementById('installation_end_date').value || '');
        fd.append('inspection_date', document.getElementById('inspection_date').value || '');
        fd.append('handover_date', document.getElementById('handover_date').value || '');
        fd.append('estimated_complete_date', document.getElementById('estimated_complete_date').value || '');
        fd.append('subsidy_status', document.getElementById('subsidy_status').value || '');
        fd.append('ajax', true);
        console.log("fd", fd);
        const res = await fetch(`/projects/${projectId}/update`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': TOKEN },
            body: fd
        });
        const j = await res.json();
        console.log(j);

    })

    /* ------------------------
       UPLOAD DOCUMENT
    ------------------------ */
    document.querySelectorAll('.upload-document').forEach(btn => {
        btn.addEventListener('click', () => {

            const fileInput = document.getElementById(btn.dataset.file);
            const hidden = document.getElementById(btn.dataset.hidden);
            const preview = document.getElementById(btn.dataset.preview);
            const progress = document.getElementById(btn.dataset.progress);
            const progressWrap = document.getElementById(btn.dataset.progressWrap);
            const docType = btn.dataset.doc_type;
            const project_id = document.getElementById('project_id').value;

            if (!fileInput.files.length) {
                showError(progressWrap, 'Please select a file');
                return;
            }

            const file = fileInput.files[0];

            // UI lock
            btn.disabled = true;
            fileInput.disabled = true;
            progressWrap.classList.remove('d-none');
            progress.style.width = '0%';
            progress.className = 'progress-bar progress-bar-striped progress-bar-animated';

            const formData = new FormData();
            formData.append('file', file);
            formData.append('project_id', project_id);
            formData.append('type', docType);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', `/projects/${project_id}/attach-document`);

            xhr.upload.onprogress = e => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progress.style.width = percent + '%';
                    progress.textContent = percent + '%';
                }
            };

            xhr.onload = () => {
                progressWrap.classList.add('d-none');
                btn.disabled = false;

                if (xhr.status !== 200) {
                    fileInput.disabled = false;
                    showError(null, 'Upload failed');
                    return;
                }

                const res = JSON.parse(xhr.responseText);

                // ✅ Preview handling
                if (res.doc.mime_type.startsWith('image/')) {
                    preview.outerHTML = `
                        <img src="${res.url}" id="${btn.dataset.preview}"
                            class="img-fluid rounded viewDoc"
                            style="max-height:120px;cursor:pointer"
                            data-id="${res.doc.id}"
                            data-file_name="${res.doc.file_name}"
                            data-mime_type="${res.doc.mime_type}"
                            data-file_path="${res.url}">
                    `;
                } else {
                    preview.outerHTML = `
                        <i id="${btn.dataset.preview}"
                        class="bi bi-file-earmark-pdf text-danger viewDoc"
                        style="font-size:3rem;cursor:pointer"
                        data-id="${res.doc.id}"
                        data-file_name="${res.doc.file_name}"
                        data-mime_type="${res.doc.mime_type}"
                        data-file_path="${res.url}">
                        </i>
                    `;
                }

                hidden.value = res.doc.id;
                fileInput.value = '';

                showSuccess(null, 'Upload successful');
            };

            xhr.onerror = () => {
                btn.disabled = false;
                fileInput.disabled = false;
                progressWrap.classList.add('d-none');
                showError(null, 'Network error');
            };

            xhr.send(formData);
        });
    });


    /* ------------------------
       DELETE DOCUMENT
    ------------------------ */
    document.querySelectorAll('.delete-document').forEach(btn => {
        btn.addEventListener('click', async () => {

            if (!confirm('Delete this document?')) return;

            const hidden = document.getElementById(btn.dataset.hidden);
            const preview = document.getElementById(btn.dataset.preview);
            const fileInputId = btn.closest('tr')
                .querySelector('.upload-document')?.dataset.file;
            const fileInput = document.getElementById(fileInputId);

            const docId = hidden.value;
            if (!docId) return;

            const project_id = document.getElementById('project_id').value;

            const formData = new FormData();
            formData.append('document_id', docId);

            const res = await fetch(`/projects/${project_id}/detech-document`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const data = await res.json();
            if (!data.status) {
                showError(null, 'Delete failed');
                return;
            }

            // ✅ Reset UI
            hidden.value = '';
            fileInput.disabled = false;
            fileInput.value = '';

            preview.outerHTML = `
                <img src="/assets/img/placeholder/user.jpg"
                    id="${btn.dataset.preview}"
                    class="img-fluid rounded"
                    style="max-height:120px;cursor:pointer">
            `;

            showSuccess(null, 'Document deleted');
        });
    });


});

function updateStepEditModal(steps = []) {
    const stepWrapper = document.getElementById('stepWrapper');
    if (!stepWrapper) return;

    // Remove all rows except header
    stepWrapper.querySelectorAll(
        '.stepID, .steps-field, .deleteStepItem'
    ).forEach(el => el.closest('.col-1, .col-9, .col-2')?.remove());

    // Rebuild steps
    steps.forEach((step, index) => {
        const stepID = index + 1;

        const idDiv = document.createElement('div');
        idDiv.className = 'col-1 stepID';
        idDiv.textContent = stepID;

        const stepDiv = document.createElement('div');
        stepDiv.className = 'col-9';
        stepDiv.innerHTML = `
            <input class="form-control steps-field" value="${step}">
        `;

        const actionDiv = document.createElement('div');
        actionDiv.className = 'col-2';
        actionDiv.innerHTML = `
            <button class="btn btn-sm btn-outline-danger deleteStepItem">
                <span class="mdi mdi-delete"></span>
            </button>
        `;

        stepWrapper.append(idDiv, stepDiv, actionDiv);
    });
}

function showSuccess(el, message) {
    console.log('%c✔ ' + message, 'color:green;font-weight:bold');
}

function showError(el, message) {
    console.error('✖ ' + message);
    alert(message);
}


document.getElementById('ProjectPhotoUpload').addEventListener('click', function () {
    const projectPhotos = document.getElementById('projectPhotos');
    const projectPhotosStatus = document.getElementById('projectPhotosStatus');

    if (!projectPhotos.files.length) {
        projectPhotosStatus.innerHTML = '<span class="text-danger">Please select a file</span>';
        return;
    }

    projectPhotosStatus.innerHTML = 'Uploading...';
    uploadProjectPhotosWithProgress(projectPhotos.files);
});


function uploadProjectPhotosWithProgress(files) {
    const xhr = new XMLHttpRequest();
    const formData = new FormData();
    const project_id = document.getElementById("projectId").value
    // Append multiple files
    Array.from(files).forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });

    // Extra fields
    formData.append('project_id', project_id);
    formData.append('type', 'site_photos');

    // CSRF token (Laravel)
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    const projectPhotosProgressWrapper = document.getElementById('projectPhotosProgressWrapper');
    const projectPhotosProgressBar = document.getElementById('projectPhotosProgressBar');
    const projectPhotosStatus = document.getElementById('projectPhotosStatus');
    const projectPhotosPreviewBox = document.getElementById('projectPhotosPreviewBox');

    xhr.open('POST', `/projects/${project_id}/attach-photos`);

    // ---- Progress ----
    xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            projectPhotosProgressWrapper.classList.remove('d-none');
            projectPhotosProgressBar.style.width = percent + '%';
            projectPhotosProgressBar.textContent = percent + '%';
        }
    };

    // ---- Success / Error ----
    xhr.onload = function () {
        if (xhr.status === 200) {
            projectPhotosProgressBar.classList.add('bg-success');
            projectPhotosStatus.innerHTML = '<span class="text-success">Upload successful</span>';
            projectPhotosPreviewBox.innerHTML = ""
            document.getElementById('projectPhotos').value = ""
            JSON.parse(xhr.response).docs.forEach(re => {
                loadPhotoCard(re)
            });
            setTimeout(() => {
                projectPhotosProgressWrapper.classList.add('d-none');
                projectPhotosStatus.innerHTML = ""
            }, 1500);
        } else {
            projectPhotosProgressBar.classList.add('bg-danger');
            projectPhotosStatus.innerHTML = '<span class="text-danger">Upload failed</span>';
            console.error(xhr.responseText);
        }
    };

    xhr.onerror = function () {
        projectPhotosProgressBar.classList.add('bg-danger');
        projectPhotosStatus.innerHTML = '<span class="text-danger">Network error</span>';
    };

    xhr.send(formData);
}


function loadPhotoCard(doc) {
    SitePhotoContainer = document.getElementById("SitePhotoContainer")
    let preview = document.createElement('img')
    preview.classList = "img-fluid rounded viewPhoto"
    preview.src = doc.file_path
    preview.style.maxHeight = "120px"
    preview.style.cursor = "pointer"
    preview.dataset.id = doc.id
    preview.dataset.file_name = doc.file_name
    preview.dataset.mime_type = doc.mime_type
    preview.dataset.file_path = doc.file_path

    const fileName = document.createElement("p")
    fileName.classList = "small mt-2 mb-1 text-truncate"
    fileName.textContent = doc.file_name

    const deleteBtn = document.createElement('button')
    deleteBtn.classList = "btn btn-sm btn-outline-danger delete-site-photo"
    deleteBtn.dataset.id = doc.id
    deleteBtn.textContent = "Delete"

    const cardBody = document.createElement('div')
    cardBody.classList = "card-body text-center"
    cardBody.append(preview, fileName, deleteBtn)

    const card = document.createElement('div')
    card.classList = "card shadow-sm"
    card.appendChild(cardBody)

    const root = document.createElement('div')
    root.classList = "col-md-3 photo-item"
    root.appendChild(card)

    SitePhotoContainer.append(root)
}

document.addEventListener('click', async function (e) {

    const btn = e.target.closest('.delete-site-photo');
    if (!btn) return;
    if (!confirm("Are you sure to delete this doc?")) {
        return
    }
    const docId = btn.dataset.id;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    btn.disabled = true;
    btn.textContent = 'Deleting...';

    try {
        const response = await fetch(`/projects/detech-photos/${docId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw await response.json();
        }

        // Remove card / row from DOM
        btn.closest('.photo-item')?.remove();

    } catch (err) {
        console.error(err);
        alert('Failed to delete document');

        btn.disabled = false;
        btn.textContent = 'Delete';
    }
});