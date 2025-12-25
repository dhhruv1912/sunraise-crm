class OCRManager {
    constructor() {
        this.cache = new Map();              // fileHash → OCR text
        this.worker = null;                  // Tesseract worker
        this.currentJob = null;
    }

    init() {
        document.addEventListener('click', e => this.handleClick(e));
        document.addEventListener('change', e => this.handleFileChange(e));
        this.initDragDrop();
    }

    /* ------------------ EVENTS ------------------ */

    handleClick(e) {
        const btn = e.target.closest('.get_aadhar_number, .get_pan_number, .get_bank-acc-number_number, .get_bank-mirc-number, .get_bank-ifsc-code');
        const cancelBtn = e.target.closest('.cancel-ocr');

        if (btn) this.startOCR(btn);
        if (cancelBtn) this.cancelOCR(cancelBtn);
    }

    handleFileChange(e) {
        if (!e.target.matches('.document-upload-element input[type="file"]')) return;
        const wrapper = e.target.closest('.document-upload-element');
        const img = wrapper.querySelector('.placeholder_img');
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = ev => img.src = ev.target.result;
        reader.readAsDataURL(file);
    }

    /* ------------------ DRAG & DROP ------------------ */

    initDragDrop() {
        document.querySelectorAll('.ocr-drop-zone').forEach(zone => {
            zone.addEventListener('dragover', e => {
                e.preventDefault();
                zone.classList.add('border-primary');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('border-primary');
            });

            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.classList.remove('border-primary');

                const file = e.dataTransfer.files[0];
                if (!file) return;

                const input = zone.querySelector('input[type="file"]');
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            });
        });
    }

    /* ------------------ OCR CORE ------------------ */

    async startOCR(btn) {
        const regex = new RegExp(btn.dataset.regex);
        const fileInput = document.querySelector(btn.dataset.file);
        const field = document.querySelector(btn.dataset.field);
        const progressBar = document.querySelector(btn.dataset.progress_bar);
        const cancelBtn = btn.parentElement.querySelector('.cancel-ocr');

        const file = fileInput?.files?.[0];
        if (!file) return;

        btn.innerHTML = '<i class="mdi mdi-robot-angry"></i>';
        cancelBtn.classList.remove('d-none');
        progressBar.classList.remove('d-none');

        const hash = await this.hashFile(file);

        // ✅ CACHE HIT
        if (this.cache.has(hash)) {
            this.applyResult(this.cache.get(hash), regex, field, btn);
            return;
        }

        const imgURL = URL.createObjectURL(file);

        this.worker = Tesseract.createWorker({
            logger: m => this.updateProgress(progressBar, m)
        });

        this.currentJob = this.worker;

        await this.worker.load();
        await this.worker.loadLanguage('eng');
        await this.worker.initialize('eng');

        const { data: { text } } = await this.worker.recognize(imgURL);

        this.cache.set(hash, text);
        this.applyResult(text, regex, field, btn);

        await this.worker.terminate();
        cancelBtn.classList.add('d-none');
        progressBar.classList.add('d-none');
    }

    /* ------------------ CANCEL OCR ------------------ */

    async cancelOCR(btn) {
        if (this.worker) {
            await this.worker.terminate();
            this.worker = null;
        }
        btn.classList.add('d-none');
    }

    /* ------------------ RESULT HANDLING ------------------ */

    applyResult(text, regex, field, btn) {
        const clean = text.replace(/\s+/g, '');
        const matches = clean.match(new RegExp(regex, 'g')) || [];

        const best = this.bestMatch(matches);

        if (best) {
            field.value = best.value;
            btn.innerHTML = `<i class="mdi mdi-robot-happy"></i>`;
        } else {
            btn.innerHTML = `<i class="mdi mdi-robot-dead"></i>`;
            field.value = '';
            field.placeholder = 'OCR failed — enter manually';
        }

        setTimeout(() => btn.innerHTML = `<i class="mdi mdi-robot"></i>`, 2000);
    }

    /* ------------------ CONFIDENCE SCORING ------------------ */

    bestMatch(matches) {
        if (!matches.length) return null;

        let best = { value: matches[0], score: 0 };

        matches.forEach(m => {
            let score = 0;
            score += m.length * 2;               // length confidence
            if (/^\d+$/.test(m)) score += 10;    // numeric confidence
            if (!/[A-Z]/.test(m)) score += 5;    // noise reduction

            if (score > best.score) {
                best = { value: m, score };
            }
        });

        return best;
    }

    /* ------------------ UTIL ------------------ */

    updateProgress(barWrap, m) {
        const bar = barWrap.querySelector('.progress-bar');
        bar.style.width = `${Math.round(m.progress * 100)}%`;
        bar.textContent = m.status;
    }

    async hashFile(file) {
        const buf = await file.arrayBuffer();
        const hash = await crypto.subtle.digest('SHA-1', buf);
        return Array.from(new Uint8Array(hash)).map(b => b.toString(16).padStart(2, '0')).join('');
    }
}
