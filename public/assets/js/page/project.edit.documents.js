let bsDocModal;

document.addEventListener('DOMContentLoaded', function () {
    bsDocModal = new bootstrap.Modal(document.getElementById('docModal'))
});
document.getElementById('fileInput').addEventListener('change', function () {
    const file = this.files[0];
    const files = this.files;

    const previewBox = document.getElementById('previewBox');
    const previewContent = document.getElementById('previewContent');

    previewContent.innerHTML = '';
    previewBox.classList.add('d-none');

    if (!file) return;

    previewBox.classList.remove('d-none');
    Array.from(files).forEach((file) => {
        const label = document.createElement('div');
        const imgDiv = document.createElement('div');
        const table = document.createElement('table');
        const tbody = document.createElement('tbody');

        label.className = 'col-md-3';
        imgDiv.className = 'col-md-9';

        // ---------- Table Rows ----------
        const addRow = (title, value) => {
            const tr = document.createElement('tr');
            const td1 = document.createElement('td');
            const td2 = document.createElement('td');
            td1.textContent = title;
            td2.textContent = value;
            tr.append(td1, td2);
            tbody.appendChild(tr);
        };

        addRow('File Name', file.name);
        addRow('File Type', file.type || 'N/A');
        addRow('File Size', `${(file.size / 1024 / 1024).toFixed(2)} MB`);

        table.className = 'table table-sm';
        table.appendChild(tbody);
        label.appendChild(table);

        // ---------- Preview ----------
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.className = 'img-fluid rounded border border-2 p-1 rounded-3';
            img.style.maxHeight = '250px';
            img.src = URL.createObjectURL(file);

            imgDiv.appendChild(img);

        } else if (file.type === 'application/pdf') {
            imgDiv.innerHTML = `
                <div class="text-center">
                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size:4rem"></i>
                    <p class="fw-semibold mb-0">${file.name}</p>
                </div>
            `;
        } else {
            imgDiv.innerHTML = `<p class="text-danger">Unsupported file type</p>`;
        }

        previewContent.append(label, imgDiv);

        hr = document.createElement('hr')
        previewContent.appendChild(hr);
    });
});
function loadDocCard(doc) {
    container = document.getElementById("container")
    let preview
    if (doc.mime_type.startsWith('image/')) {
        preview = document.createElement('img')
        preview.classList = "img-fluid rounded viewDoc"
        preview.src = doc.file_path
        preview.style.maxHeight = "120px"
    } else {
        preview = document.createElement('i')
        preview.classList = "bi bi-file-earmark-pdf text-danger viewDoc"
        preview.style.fontSize = "3rem"
        preview.src = doc.file_path
    }
    preview.style.cursor = "pointer"
    preview.dataset.id = doc.id
    preview.dataset.file_name = doc.file_name
    preview.dataset.mime_type = doc.mime_type
    preview.dataset.file_path = doc.file_path

    const fileName = document.createElement("p")
    fileName.classList = "small mt-2 mb-1 text-truncate"
    fileName.textContent = doc.file_name

    const deleteBtn = document.createElement('button')
    deleteBtn.classList = "btn btn-sm btn-outline-danger delete-project-doc"
    deleteBtn.dataset.id = doc.id
    deleteBtn.textContent = "Delete"

    const cardBody = document.createElement('div')
    cardBody.classList = "card-body text-center"
    cardBody.append(preview, fileName, deleteBtn)

    const card = document.createElement('div')
    card.classList = "card shadow-sm"
    card.appendChild(cardBody)

    const root = document.createElement('div')
    root.classList = "col-md-3 doc-item"
    root.appendChild(card)

    container.append(root)
}
function uploadWithProgress(files) {
    const xhr = new XMLHttpRequest();
    const formData = new FormData();
    const project_id = document.getElementById("projectId").value
    // Append multiple files
    Array.from(files).forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });

    // Extra fields
    formData.append('project_id', project_id);
    formData.append('type', 'project_documents');

    // CSRF token (Laravel)
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    const progressWrapper = document.getElementById('progressWrapper');
    const progressBar = document.getElementById('progressBar');
    const status = document.getElementById('status');
    const previewBox = document.getElementById('previewBox');

    xhr.open('POST', '/documents/upload');

    // ---- Progress ----
    xhr.upload.onprogress = function (e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            progressWrapper.classList.remove('d-none');
            progressBar.style.width = percent + '%';
            progressBar.textContent = percent + '%';
        }
    };

    // ---- Success / Error ----
    xhr.onload = function () {
        if (xhr.status === 200) {
            progressBar.classList.add('bg-success');
            status.innerHTML = '<span class="text-success">Upload successful</span>';
            previewBox.innerHTML = ""
            JSON.parse(xhr.response).data.forEach(re => {
                loadDocCard(re)
            });
            setTimeout(() => {
                progressWrapper.classList.add('d-none');
                status.innerHTML = ""
            }, 1500);
        } else {
            progressBar.classList.add('bg-danger');
            status.innerHTML = '<span class="text-danger">Upload failed</span>';
            console.error(xhr.responseText);
        }
    };

    xhr.onerror = function () {
        progressBar.classList.add('bg-danger');
        status.innerHTML = '<span class="text-danger">Network error</span>';
    };

    xhr.send(formData);
}

// ---- Button Click ----
document.getElementById('fileInputUpload').addEventListener('click', function () {
    const fileInput = document.getElementById('fileInput');
    const status = document.getElementById('status');

    if (!fileInput.files.length) {
        status.innerHTML = '<span class="text-danger">Please select a file</span>';
        return;
    }

    status.innerHTML = 'Uploading...';
    uploadWithProgress(fileInput.files);
});
document.addEventListener('click', function (e) {

    const btn = e.target.closest('.viewDoc');
    if (!btn) return;
    // bsDocModal = new bootstrap.Modal(document.getElementById('docModal'))

    const data = btn.dataset;
    document.getElementById('model-file').innerHTML = ""
    document.getElementById("model-file-name").textContent = data.file_name
    if (data.mime_type.startsWith('image/')) {
        const preview = document.createElement('img')
        preview.classList = "img-fluid rounded"
        preview.src = data.file_path
        document.getElementById('model-file').appendChild(preview)
    }
    if (data.mime_type.startsWith('application/pdf')) {
        const preview = document.createElement('iframe')
        preview.width = "100%"
        preview.height = "500"
        preview.src = data.file_path
        document.getElementById('model-file').appendChild(preview)
    }
    bsDocModal.show()
});
document.addEventListener('click', async function (e) {

    const btn = e.target.closest('.delete-project-doc');
    if (!btn) return;
    if (!confirm("Are you sure to delete this doc?")) {
        return
    }
    const docId = btn.dataset.id;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    btn.disabled = true;
    btn.textContent = 'Deleting...';

    try {
        const response = await fetch(`/documents/destroy/${docId}`, {
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
        btn.closest('.doc-item')?.remove();

    } catch (err) {
        console.error(err);
        alert('Failed to delete document');

        btn.disabled = false;
        btn.textContent = 'Delete';
    }
});