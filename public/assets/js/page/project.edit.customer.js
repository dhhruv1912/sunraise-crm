
const progress_bar_colors = [
    'secondary',
    'primary',
    'warning',
    'info',
    'success',
]
const progress_bar_per = [
    10,
    10,
    15,
    10,
    55,
]


document.addEventListener("DOMContentLoaded", () => {
    
    const customer_note_quill = new Quill('#customer_note', {
        theme: 'snow'
    });
    
});
document.addEventListener('mouseover', function (e) {
    const wrapper = e.target.closest('.document-upload-element');
    if (!wrapper || !wrapper.contains(e.target)) return;

    const addButton = wrapper.querySelector('button.upload-document-icon');
    const removeButton = wrapper.querySelector('button.remove-document-icon');
    const placeholder = wrapper.querySelector('img.placeholder_img');
    const placeholderUrl = wrapper.dataset.placeholder_url;

    if (placeholder?.src === placeholderUrl) {
        addButton?.classList.remove('d-none');
    } else {
        removeButton?.classList.remove('d-none');
    }
});

document.addEventListener('keyup', function (e) {
    if (e.target.matches('#customerNoteWrapper .ql-editor')) {
        if (e.target.classList.contains('ql-blank')) {
            document.getElementById("customer_note_hidden").value = ""
        } else {
            document.getElementById("customer_note_hidden").value = e.target.innerHTML
        }
    }
})

document.addEventListener('mouseout', function (e) {
    const wrapper = e.target.closest('.document-upload-element');
    if (!wrapper || !wrapper.contains(e.target)) return;

    const addButton = wrapper.querySelector('button.upload-document-icon');
    const removeButton = wrapper.querySelector('button.remove-document-icon');
    const placeholder = wrapper.querySelector('img.placeholder_img');
    const placeholderUrl = wrapper.dataset.placeholder_url;

    if (placeholder?.src === placeholderUrl) {
        addButton?.classList.add('d-none');
    } else {
        removeButton?.classList.add('d-none');
    }
});

document.addEventListener('click', function (e) {

    /* Upload button click */
    if (e.target.matches('button.upload-document-icon')) {
        const wrapper = e.target.closest('.document-upload-element');
        wrapper?.querySelector('input[type="file"]')?.click();
        e.target.classList.add('d-none');
    }

    /* Remove button click */
    if (e.target.matches('button.remove-document-icon')) {
        const wrapper = e.target.closest('.document-upload-element');
        const placeholder = wrapper?.querySelector('.placeholder_img');
        const placeholderUrl = wrapper?.dataset.placeholder_url;

        if (placeholder && placeholderUrl) {
            placeholder.src = placeholderUrl;
        }

        const fileInput = wrapper?.querySelector('input[type="file"]');
        if (fileInput) fileInput.value = '';

        e.target.classList.add('d-none');
    }
});

document.addEventListener('change', function (e) {
    if (!e.target.matches('.document-upload-element input[type="file"]')) return;

    const wrapper = e.target.closest('.document-upload-element');
    const placeholder = wrapper?.querySelector('.placeholder_img');

    if (e.target.files && e.target.files[0] && placeholder) {
        const reader = new FileReader();
        reader.onload = ev => placeholder.src = ev.target.result;
        reader.readAsDataURL(e.target.files[0]);
    }
});

document.addEventListener('click', function (e) {

    const btn = e.target.closest(
        '.get_aadhar_number, .get_pan_number, .get_bank_account_number, .get_bank-mirc-number, .get_bank-ifsc-code, .get_lightbill_number'
    );
    if (!btn) return;

    /* ---------------- GROUP REGEX HANDLING ---------------- */
    const groupRegexes = {};

    if (btn.classList.contains('group-regex')) {
        let groupClass = '';

        for (const cls of btn.classList) {
            if (cls.startsWith('grouped-')) {
                groupClass = cls;
                break;
            }
        }

        if (groupClass) {
            const groupItems = document.getElementsByClassName(groupClass);

            for (const item of groupItems) {
                if (item.dataset.field && item.dataset.regex) {
                    groupRegexes[item.dataset.field] = new RegExp(item.dataset.regex);
                }
            }
        }
    }

    /* ---------------- SINGLE REGEX ---------------- */
    let regex = btn.dataset.regex;
    if (regex) regex = new RegExp(regex);

    btn.innerHTML = '<i class="mdi mdi-robot-angry"></i>';

    const fileInput = document.querySelector(btn.dataset.file);
    const file = fileInput?.files?.[0];
    if (!file) return;

    /* ---------------- PROGRESS BAR ---------------- */
    let progress_count = 0;
    let width = 0;

    const progressBarWrapper = document.querySelector(btn.dataset.progress_bar);
    const progressBar = progressBarWrapper?.querySelector('.progress-bar');

    progressBarWrapper?.style.setProperty('display', 'block');

    /* ---------------- FILE READER ---------------- */
    const reader = new FileReader();

    reader.onload = function (ev) {
        const img = new Image();

        img.onload = function () {
            Tesseract.recognize(img, 'eng', {
                logger: m => {

                    /* reset colors */
                    progress_bar_colors.forEach(color => {
                        progressBarWrapper?.classList.remove(`bg-label-${color}`);
                        progressBar?.classList.remove(`bg-${color}`);
                    });

                    /* apply current color */
                    const color = progress_bar_colors[progress_count] || progress_bar_colors.at(-1);
                    progressBarWrapper?.classList.add(`bg-label-${color}`);
                    progressBar?.classList.add(`bg-${color}`);

                    const currentProcess = Math.round(m.progress * 100);
                    width = 0;

                    for (let i = 0; i < progress_count; i++) {
                        width += progress_bar_per[i] || 0;
                    }

                    const processCount = progress_bar_per[progress_count] || 0;
                    width += (currentProcess * processCount) / 100;

                    progressBar.style.width = `${width}%`;
                    progressBar.textContent = m.status;

                    if (m.progress === 1) progress_count++;
                }
            }).then(({ data: { text } }) => {

                progressBar.textContent = '';
                progressBarWrapper.style.display = 'none';

                const cleanedText = text
                    .toUpperCase()
                    .replace(/[^A-Z0-9\s\-]/g, ' ')
                    .replace(/\s+/g, ' ')
                    .trim()
                // const cleanedText = text.replace(/\n/g, '');

                /* ---------------- GROUP MATCH ---------------- */
                if (Object.keys(groupRegexes).length > 0) {

                    Object.entries(groupRegexes).forEach(([fieldSelector, reg]) => {
                        const match = cleanedText.match(reg);
                        const field = document.querySelector(fieldSelector);

                        if (!field) return;

                        if (match) {
                            field.value = match[0];
                            btn.innerHTML = '<i class="mdi mdi-robot-happy"></i>';
                        } else {
                            const prevVal = field.value || '';
                            let placeholder = "Can't find number, enter manually.";
                            if (prevVal) placeholder += ` Prev Value : ${prevVal}`;

                            field.value = '';
                            field.placeholder = placeholder;
                            btn.innerHTML = '<i class="mdi mdi-robot-dead"></i>';
                        }
                    });

                } else if (regex) {

                    /* ---------------- SINGLE MATCH ---------------- */
                    const match = cleanedText.match(regex);
                    const field = document.querySelector(btn.dataset.field);

                    if (!field) return;

                    if (match) {
                        field.value = match[0];
                        btn.innerHTML = '<i class="mdi mdi-robot-happy"></i>';
                    } else {
                        const prevVal = field.value || '';
                        let placeholder = "Can't find number, enter manually.";
                        if (prevVal) placeholder += ` Prev Value : ${prevVal}`;

                        field.value = '';
                        field.placeholder = placeholder;
                        btn.innerHTML = '<i class="mdi mdi-robot-dead"></i>';
                    }
                }

                setTimeout(() => {
                    btn.innerHTML = '<i class="mdi mdi-robot"></i>';
                }, 2000);
            });
        };

        img.src = ev.target.result;
    };

    reader.readAsDataURL(file);
});
