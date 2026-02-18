// public/js/batch_create.js

// ---- Configuration ----
const MIN_SERIAL_LENGTH = 8; // Rule B: minimum length for a serial-like token

// DOM refs
const invoiceInput = document.getElementById('invoiceInput');
const cropContainer = document.getElementById('cropContainer');
const cropImage = document.getElementById('cropImage');
const ocrProgress = document.getElementById('ocrProgress');
const ocrProgressWrapper = document.getElementById('ocrProgressWrapper');
const ocrResultCard = document.getElementById('ocrResultCard');
const serialCard = document.getElementById('serialCard');
const serialList = document.getElementById('serialList');
const serialList2 = document.getElementById('serialList');
const continueBtn = document.getElementById('continueBtn');

const categorySelect = document.getElementById('categorySelect');
const itemSelect = document.getElementById('itemSelect');
const warehouseSelect = document.getElementById('warehouseSelect');

// extracted field inputs
const invoiceNumberInput = document.getElementById('invoiceNumber');
const invoiceDateInput = document.getElementById('invoiceDate');
const quentityInput = document.getElementById('quentity');
const materialDescriptionInput = document.getElementById('materialDescription');
const modelNoInput = document.getElementById('modelNo');
const dimensionsInput = document.getElementById('dimensions');
const netWeightInput = document.getElementById('netWeight');
const grossWeightInput = document.getElementById('grossWeight');
const progressbar = document.getElementById('progressbar');

// internal state
let cropper = null;
let currentBlob = null;        // original or cropped image blob
let lastOCRText = '';
let currentSerials = [];

/* ---------------------------
   Helper utilities
   --------------------------- */

function show(el) { el.style.display = ''; }
function hide(el) { el.style.display = 'none'; }
function setText(el, text) { if (el) el.textContent = text; }

function toast(msg, type = 'success') {
    const bg = type === 'success' ? 'var(--arham-color-blue)' : '#dc3545';
    const el = document.createElement('div');
    el.style.position = 'fixed';
    el.style.bottom = '24px';
    el.style.right = '24px';
    el.style.background = bg;
    el.style.color = 'white';
    el.style.padding = '10px 14px';
    el.style.borderRadius = '6px';
    el.style.zIndex = 99999;
    el.textContent = msg;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

/* ---------------------------
   Category -> Items loader
   --------------------------- */
categorySelect?.addEventListener('change', () => {
    const cid = categorySelect.value;
    // fetch items by category using existing items list endpoint
    fetch(`/items/list?category_id=${cid}&page=1`)
        .then(r => r.json())
        .then(res => {
            const data = res.data.data || res.data; // handle possible boundary
            itemSelect.innerHTML = `<option value="">Select Item</option>`;
            data.forEach(i => {
                const opt = document.createElement('option');
                opt.value = i.id;
                opt.text = i.name;
                itemSelect.appendChild(opt);
            });
        }).catch(err => {
            console.error(err);
            toast('Failed to load items', 'error');
        });
});

/* ---------------------------
   File input handling
   --------------------------- */
invoiceInput?.addEventListener('change', async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // reset state
    currentBlob = null;
    lastOCRText = '';
    currentSerials = [];
    clearSerialList();
    hide(ocrResultCard);
    hide(serialCard);
    hide(continueBtn);

    const mime = file.type || '';
    const ext = (file.name || '').split('.').pop().toLowerCase();

    // if PDF -> skip client OCR (we'll send to server)
    if (mime === 'application/pdf' || ext === 'pdf') {
        // show message and set currentBlob equal to file for upload
        cropContainer.style.display = 'none';
        currentBlob = file;
        toast('PDF detected — browser OCR skipped. You can still proceed to review.', 'success');
        // but still reveal manual fields area
        show(ocrResultCard);
        show(serialCard);
        show(continueBtn);
        invoiceNumberInput.value = '';
        materialDescriptionInput.value = '';
        return;
    }

    // For images: display in cropper
    const reader = new FileReader();
    reader.onload = function (ev) {
        cropImage.src = ev.target.result;
        cropContainer.style.display = '';
        initCropper();
        // add action buttons once cropper ready
        addCropperControls();
    };
    reader.readAsDataURL(file);
});

/* ---------------------------
   Cropper setup + controls
   --------------------------- */
function initCropper() {
    // destroy previous cropper
    if (cropper) {
        try { cropper.destroy(); } catch (e) { }
        cropper = null;
        document.getElementById('cropControls')?.remove();
    }

    cropper = new Cropper(cropImage, {
        viewMode: 1,
        autoCropArea: 0.9,
        movable: true,
        zoomable: true,
        rotatable: true,
        scalable: false,
        aspectRatio: NaN // free crop
    });
}

function addCropperControls() {
    // ensure not adding twice
    if (document.getElementById('cropControls')) return;

    const controls = document.createElement('div');
    controls.id = 'cropControls';
    controls.style.marginTop = '8px';
    controls.innerHTML = `
        <button class="btn btn-sm btn-primary me-2" id="runOcrBtn">Run OCR on Crop</button>
        <button class="btn btn-sm btn-outline-secondary me-2" id="useFullBtn">Use Full Image (no crop)</button>
        <button class="btn btn-sm btn-outline-warning" id="rotateBtn">Rotate 90°</button>
    `;
    cropContainer.insertAdjacentElement('afterend', controls);

    document.getElementById('runOcrBtn').addEventListener('click', () => runOCRFromCrop());
    document.getElementById('useFullBtn').addEventListener('click', () => useFullImageForOCR());
    document.getElementById('rotateBtn').addEventListener('click', () => {
        if (cropper) cropper.rotate(90);
    });
}

async function runOCRFromCrop() {
    if (!cropper) return toast('Cropper not initialized', 'error');

    // get cropped canvas and convert to blob
    const canvas = cropper.getCroppedCanvas({
        maxWidth: 3000,
        maxHeight: 3000,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });

    if (!canvas) return toast('Failed to get cropped area', 'error');

    show(ocrProgressWrapper);
    hide(ocrResultCard);
    hide(serialCard);

    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.9));
    currentBlob = blob;

    try {
        const { text } = await doTesseractOCR(blob);
        lastOCRText = text;
        populateFieldsFromOCR(text);
        // const serials = extractSerialsFromText(text);
        // currentSerials = serials;
        // renderSerialList();
        show(ocrResultCard);
        show(serialCard);
        show(continueBtn);
        toast('OCR complete — please review extracted data.');
    } catch (err) {
        console.error(err);
        toast('OCR failed. See console for details.', 'error');
    } finally {
        hide(ocrProgressWrapper);
    }
}

async function useFullImageForOCR() {
    // get full image from <img> src
    if (!cropImage || !cropImage.src) return toast('Image not loaded', 'error');

    show(ocrProgressWrapper);
    hide(ocrResultCard);
    hide(serialCard);

    // convert src to blob
    const blob = await dataUrlToBlob(cropImage.src);
    currentBlob = blob;

    try {
        const { text } = await doTesseractOCR(blob);
        lastOCRText = text;
        populateFieldsFromOCR(text);
        // const serials = extractSerialsFromText(text);
        // currentSerials = serials;
        // renderSerialList();
        show(ocrResultCard);
        show(serialCard);
        show(continueBtn);
        toast('OCR complete — please review extracted data.');
    } catch (err) {
        console.error(err);
        toast('OCR failed', 'error');
    } finally {
        hide(ocrProgressWrapper);
    }
}
async function doTesseractOCR(blobOrDataUrl) {
    // Show an initial progress message (caller toggles UI)
    if (ocrProgress) {
        ocrProgress.innerHTML = `<p>Loading OCR engine...</p>`;
    }

    // Prepare input for recognize: accept Blob or dataURL
    let input = blobOrDataUrl;
    if (blobOrDataUrl instanceof Blob) {
        // fine — Tesseract accepts Blob directly
        input = blobOrDataUrl;
    } else if (typeof blobOrDataUrl === 'string' && blobOrDataUrl.startsWith('data:')) {
        input = blobOrDataUrl;
    } // otherwise assume it's a URL/dataUrl string

    // Use Tesseract.recognize (works with the CDN bundle)
    // Provide a logger to show progress
    const logger = m => {
        if (ocrProgress) {
            const pct = m.progress ? Math.round(m.progress * 100) : 0;
            progressbar.style.width = pct + '%'
            ocrProgress.innerHTML = `<p>OCR status: ${m.status || ''} (${pct}%)</p>`;
        }
    };

    try {
        const { data } = await Tesseract.recognize(input, 'eng', { logger });
        return { text: data.text || '' };
    } catch (err) {
        console.error('Tesseract.recognize error', err);
        throw err;
    }
}

/* ---------------------------
   Text parsing & extraction
   --------------------------- */

function normalizeToken(tok) {
    // upper-case, remove surrounding punctuation
    return tok.replace(/[^A-Za-z0-9-]/g, '').toUpperCase();
}

function looksLikeSerial(tok) {
    if (!tok) return false;
    const t = normalizeToken(tok);
    if (t.length < MIN_SERIAL_LENGTH) return false;
    // must contain at least one digit
    if (!/[0-9]/.test(t)) return false;
    // disallow pure dates or pure integers of length short
    // ensure no spaces
    if (/\s/.test(tok)) return false;
    // allow alnum and hyphen only
    if (!/^[A-Z0-9-]+$/.test(t)) return false;
    // avoid tokens like "INVOICE" or "QTY"
    const blacklist = ['INVOICE', 'DATE', 'QTY', 'QTY.', 'TOTAL', 'GST', 'ITEM', 'SR', 'NO', 'DESCRIPTION'];
    if (blacklist.includes(t)) return false;
    return true;
}

function extractSerialsFromText(text) {
    if (!text || typeof text !== 'string') return [];
    const lines = text.split(/\r?\n/);
    const results = new Set();

    for (let raw of lines) {
        // split by spaces and punctuation
        const parts = raw.split(/[\s,;|]+/);
        for (let p of parts) {
            const token = normalizeToken(p);
            if (looksLikeSerial(token)) {
                // apply simple character corrections common in OCR
                let fixed = token
                    .replace(/O/g, '0')
                    .replace(/I/g, '1')
                    .replace(/B/g, '8')
                    .replace(/S/g, '5')
                    .replace(/L/g, '1');
                if (looksLikeSerial(fixed)) results.add(fixed);
            }
        }

        // Also try to find longer continuous alnum strings
        const regex = /[A-Za-z0-9-]{8,}/g;
        let m;
        while ((m = regex.exec(raw)) !== null) {
            const token = normalizeToken(m[0]);
            if (looksLikeSerial(token)) {
                let fixed = token.replace(/O/g, '0').replace(/I/g, '1').replace(/B/g, '8');
                if (looksLikeSerial(fixed)) results.add(fixed);
            }
        }
    }

    return Array.from(results);
}

/* ---------------------------
   Serial list UI
   --------------------------- */

// function renderSerialList() {
//     serialList.innerHTML = '';
//     currentSerials.forEach((s, idx) => {
//         const li = document.createElement('li');
//         li.className = 'list-group-item d-flex gap-2 align-items-center';

//         li.innerHTML = `
//             <input type="text" class="form-control form-control-sm serial-input" data-idx="${idx}" value="${s}">
//             <button class="btn btn-sm btn-danger ms-2 remove-serial-btn">Remove</button>
//         `;
//         serialList.appendChild(li);

//         li.querySelector('.remove-serial-btn').addEventListener('click', () => {
//             currentSerials.splice(idx, 1);
//             renderSerialList();
//         });

//         li.querySelector('.serial-input').addEventListener('input', (e) => {
//             currentSerials[idx] = e.target.value.trim().toUpperCase();
//         });
//     });

//     // allow bulk paste row
//     const bulkRow = document.createElement('li');
//     bulkRow.className = 'list-group-item';
//     bulkRow.innerHTML = `
//         <label class="form-label">Bulk paste (one per line)</label>
//         <textarea id="bulkSerials" class="form-control" rows="4" placeholder="Paste serials here..."></textarea>
//         <div class="mt-2 text-end">
//             <button class="btn btn-sm btn-secondary" id="applyBulk">Add All</button>
//         </div>
//     `;
//     serialList.appendChild(bulkRow);
//     document.getElementById('applyBulk').addEventListener('click', () => {
//         const txt = document.getElementById('bulkSerials').value || '';
//         const lines = txt.split(/\r?\n/).map(l => l.trim()).filter(l => l.length);
//         lines.forEach(l => {
//             const normalized = normalizeToken(l);
//             if (normalized && !currentSerials.includes(normalized)) currentSerials.push(normalized);
//         });
//         renderSerialList();
//     });
// }

function clearSerialList() {
    // serialList.innerHTML = '';
}

/* ---------------------------
   Auto-fill fields from OCR text
   (best-effort using regex)
   --------------------------- */

function firstMatch(regex, text) {
    const m = text.match(regex);
    if (m && m[1]) return m[1].trim();
    return null;
}
function allMatch(regex, text) {
    const m = text.match(regex);
    if (m) return m;
    return null;
}
function firstMatchWeight(regex, text) {
    const m = text.match(regex);
    if (m.length === 2) {
        if (m && m[1]) return m[1].trim();
    } else if (m.length > 2) {
        if (m && m[1] && m[2]) {
            return `${Number(m[2]).toFixed(2)} ${m[1]}`;
        }
    }
}
function normalizeText(t) {
    return t
        .replace(/\r\n/g, '\n')
        .replace(/[\u2018\u2019\u201C\u201D]/g, "'")
        .replace(/[\[\]]/g, ' ')          // convert brackets to spaces
        .replace(/[\"“”\\\/\u00A0]/g, ' ')// more stray chars -> space
        .replace(/[^ -~\n]/g, ' ')        // non-ascii to space (helps OCR garbage)
        .replace(/\s+\|/g, ' |')          // tidy pipe spacing
        .replace(/\|\s+/g, '|')           // tidy pipe spacing
        .replace(/\s+/g, ' ')             // collapse whitespace (keeps newlines collapsed too)
        .trim();
}


function populateFieldsFromOCR(text) {
    if (!text) return;

    // invoice number: look for 'invoice' followed by colon or whitespace then token
    // const invNo = firstMatch(/invoice(?:\s*no|number)?\D*([A-Z0-9-\/\s]{4,80})/i, text) ||
    //               firstMatch(/inv(?:\.)?\s*[:#\-]?\s*([A-Z0-9-\/\s]{4,80})/i, text);
    // if (invNo) invoiceNumberInput.value = invNo.split('\n')[0];
    const DescriptionRegEx = /Material Description\s*:\s*(.+)/i
    const DimentionsRegEx = /(\d+\s*X\s*\d+\s*X\s*\d+\s*[A-Za-z]+)/
    const ModelNoRegEx = /Model No\s*:\s*([A-Z][A-Z0-9]*)/
    const GrossWeightRegEx1 = /Gross Weight\s*\(([A-Z]{1,3})\)\s*:\s*([\d.]+)/
    const GrossWeightRegEx2 = /Weight\s*\(([A-Z]{1,3})\)\s*:\s*([\d.]+)/
    const NetWeightRegEx = /Net Weight\s*\(([A-Z]{1,3})\)\s*:\s*([\d.]+)/
    const QuentityRegEx = /[QJ]uantity\s*\([A-Z]{1,3}\)\s*:\s*(\d+)/
    const DateRegEx1 = /(\d{1,2}[.\-\/]\d{1,2}[.\-\/]\d{2,4})/
    const DateRegEx2 = /(\d{2,4}[.\-\/]\d{1,2}[.\-\/]\d{1,2})/
    const Description = firstMatch(DescriptionRegEx, text)
    const Dimentions = firstMatch(DimentionsRegEx, text)
    const ModelNo = firstMatch(ModelNoRegEx, text)
    const GrossWeight = firstMatchWeight(GrossWeightRegEx1, text) || firstMatchWeight(GrossWeightRegEx2, text)
    const NetWeight = firstMatchWeight(NetWeightRegEx, text)
    const Quentity = firstMatchWeight(QuentityRegEx, text)

    // date: try several date formats
    const dateCandidate = firstMatch(DateRegEx1, text) || firstMatch(DateRegEx2, text);
    console.log("dateCandidate",dateCandidate);
    
    if (dateCandidate) {
        // normalize to YYYY-MM-DD if possible
        const d = new Date(dateCandidate);
        if (!isNaN(d)) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            invoiceDateInput.value = `${y}-${m}-${day}`;
        }
    }

    [
        DescriptionRegEx,
        DimentionsRegEx,
        ModelNoRegEx,
        GrossWeightRegEx1,
        GrossWeightRegEx2,
        NetWeightRegEx,
        QuentityRegEx,
        DateRegEx1,
        DateRegEx2
    ].forEach(element => {
        text = text.replace(element, '');
    });
    let rows = allMatch(/([A-Za-z0-9]{8,})[\|\]]?\s*([^\|\r\n]+)/g, text)
    // ocrText = normalizeText(text)
    // console.log("ocrText",ocrText);
    let extra = []
    rows.forEach((row, i) => {
        const subrow = allMatch(/([A-Za-z0-9]{8,})/g, row) || [];

        // Helper: safe extract number + desc from a part
        function parsePart(part) {
            if (!part) return { number: "", desc: "" };

            const parts = part.split(/[|\]\[\|]/).map(s => s.trim()).filter(Boolean);

            const number = parts[0] || "";
            const desc = (parts[1] || "").split(" ").splice(0, 2).join(" ");

            return { number, desc };
        }

        // If row contains 2 serial numbers → split in half
        if (subrow.length > 1) {

            const words = row.split(" ").filter(Boolean);
            const half = Math.floor(words.length / 2);

            const col1 = words.slice(0, half).join(" ");
            const col2 = words.slice(half).join(" ");

            const col1Parsed = parsePart(col1);
            const col2Parsed = parsePart(col2);

            rows[i] = col1Parsed;
            extra.push(col2Parsed);

        } else {
            // Single row
            const parsed = parsePart(row);
            rows[i] = parsed;
        }
    });

    rows.concat(extra)
    console.log(rows);
    if (ModelNo) modelNoInput.value = ModelNo;
    if (NetWeight) netWeightInput.value = NetWeight;
    if (GrossWeight) grossWeightInput.value = GrossWeight;
    if (Dimentions) dimensionsInput.value = Dimentions;
    if (Description) materialDescriptionInput.value = Description.split('\n')[0];
    if (Quentity) quentityInput.value = Quentity;

    rows.forEach((s, idx) => {
        const li = document.createElement('li');
        currentSerials.push(s)
        li.className = 'list-group-item d-flex gap-2 align-items-center';

        li.innerHTML = `
            <span>${idx + 1}</span>
            <input type="text" class="form-control form-control-sm serial-no-input" data-idx="${idx}" value="${s.number}">
            <input type="text" class="form-control form-control-sm serial-desc-input" data-idx="${idx}" value="${s.desc}">
            <button class="btn btn-sm btn-danger ms-2 remove-serial-btn">Remove</button>
        `;
        serialList2.appendChild(li);

        li.querySelector('.remove-serial-btn').addEventListener('click', () => {
            rows.splice(idx, 1);
            // renderSerialList();
        });

        li.querySelector('.serial-no-input').addEventListener('input', (e) => {
            rows[idx] = e.target.value.trim().toUpperCase();
        });
        li.querySelector('.serial-desc-input').addEventListener('input', (e) => {
            rows[idx] = e.target.value.trim().toUpperCase();
        });
    });

    // set OCR text hidden field if needed
    // NOTE: lastOCRText is set earlier
}

/* ---------------------------
   Convert dataURL to Blob
   --------------------------- */
async function dataUrlToBlob(dataUrl) {
    if (dataUrl.startsWith('data:')) {
        const res = await fetch(dataUrl);
        return await res.blob();
    }
    // fallback: fetch
    const res = await fetch(dataUrl);
    return await res.blob();
}

/* ---------------------------
   Add serial row (manual)
   --------------------------- */
function addSerialRow() {
    // currentSerials.push('');
    // renderSerialList();
}

/* ---------------------------
   Submit For Review
   Build FormData and POST to /batches/review
   --------------------------- */
async function submitForReview() {

    // Validation
    if (!categorySelect.value) return toast('Select item category', 'error');
    if (!itemSelect.value) return toast('Select item', 'error');
    if (!warehouseSelect.value) return toast('Select warehouse', 'error');
    if (!currentBlob && !(invoiceInput.files && invoiceInput.files[0])) return toast('Choose invoice file', 'error');
    if (!currentSerials || currentSerials.length === 0) return toast('Add at least one serial', 'error');

    // Collect data
    const form = new FormData();
    form.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    form.append('item_id', itemSelect.value);
    form.append('warehouse_id', warehouseSelect.value);

    form.append('invoice_number', invoiceNumberInput.value || '');
    form.append('fields[invoice_number]', invoiceNumberInput.value || '');
    form.append('invoice_date', invoiceDateInput.value || '');
    form.append('fields[invoice_date]', invoiceDateInput.value || '');
    form.append('material_description', materialDescriptionInput.value || '');
    form.append('fields[material_description]', materialDescriptionInput.value || '');
    form.append('model_no', modelNoInput.value || '');
    form.append('fields[model_no]', modelNoInput.value || '');
    form.append('net_weight', netWeightInput.value || '');
    form.append('fields[net_weight]', netWeightInput.value || '');
    form.append('gross_weight', grossWeightInput.value || '');
    form.append('fields[gross_weight]', grossWeightInput.value || '');
    form.append('dimensions', dimensionsInput.value || '');
    form.append('fields[dimensions]', dimensionsInput.value || '');

    form.append('ocr_text', lastOCRText || '');

    // serials (send as array)
    currentSerials.forEach((s,i) => {
        form.append(`serialNumbers[${i}]`, s.number);
        form.append(`serialDescs[${i}]`, s.desc);
        form.append(`serials[]`, s.number);
    });

    // invoice file: if we have a cropped blob (currentBlob) use that, else fallback to selected file
    let fileToSend = currentBlob;
    if (!fileToSend) fileToSend = invoiceInput.files[0];

    // If it's still a dataURL string (unlikely), convert
    if (typeof fileToSend === 'string' && fileToSend.startsWith('data:')) {
        fileToSend = await dataUrlToBlob(fileToSend);
    }

    // append invoice_file to form
    form.append('invoice_file', fileToSend, 'invoice_upload.jpg');
    console.log([...form.entries()]);
    // show progress
    show(ocrProgressWrapper);
    ocrProgress.innerHTML = `<p>Preparing review... uploading data</p>`;

    try {
        const res = await fetch('/batches/review', {
            method: 'POST',
            body: form,
        });
        
        const data = await res.json();

        if (data.redirected && data.url) {
            window.location.href = data.url;
            return;
        }

        const html = await res.text();

        // Write returned HTML (review page) to document
        document.open();
        document.write(html);
        document.close();

    } catch (err) {
        console.error(err);
        toast('Failed to send data to server', 'error');
    } finally {
        hide(ocrProgressWrapper);
    }
}

/* ---------------------------
   Init: show/hide pieces
   --------------------------- */
(function init() {
    hide(ocrResultCard);
    hide(serialCard);
    hide(continueBtn);
    hide(ocrProgressWrapper);
})();
