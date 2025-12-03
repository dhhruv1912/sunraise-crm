document.addEventListener("DOMContentLoaded", () => {

    const TOKEN = document.querySelector('meta[name="csrf_token"]')?.content || "";

    const modalEl = document.getElementById("addSettingModal");
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const loader = modalEl?.querySelector(".loader-line");

    // ------------------------------------------------
    // OPEN ADD SETTING MODAL
    // ------------------------------------------------
    document.getElementById("add-setting")?.addEventListener("click", () => {
        const form = document.getElementById("addSettingForm");
        form.reset();
        document.getElementById("setting_option").disabled = true;
        document.getElementById("setting_name").readOnly = false;
        document.getElementById("setting-update").dataset.id = "";
        modal.show();
    });

    // ------------------------------------------------
    // TYPE CHANGE → ENABLE OPTIONS
    // ------------------------------------------------
    document.getElementById("setting_type")?.addEventListener("change", (e) => {
        const type = Number(e.target.value);
        document.getElementById("setting_option").disabled = ![2, 3, 4].includes(type);
    });

    // ------------------------------------------------
    // SAVE / UPDATE META
    // ------------------------------------------------
    document.getElementById("setting-update")?.addEventListener("click", async () => {
        loader.classList.remove("d-none");

        const form = document.getElementById("addSettingForm");
        const formData = new FormData(form);
        console.log("formData",formData);

        const res = await fetch("/settings/save", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": TOKEN },
            body: formData
        });

        const out = await res.json();
        console.log(out,"out");

        loader.classList.add("d-none");

        if (out.status) {
            showDismissible("Setting saved!", "alert-success");
            location.reload();
        } else {
            showDismissible(out.message || "Failed", "alert-danger");
        }
    });

    // ------------------------------------------------
    // EDIT SETTING
    // ------------------------------------------------
    document.body.addEventListener("click", async (e) => {
        const btn = e.target.closest(".edit-setting");
        if (!btn) return;

        const id = btn.dataset.id;         // internal DB id

        loader.classList.remove("d-none");

        const res = await fetch(`/setting/${id}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": TOKEN,
                "X-Requested-With": "XMLHttpRequest"
            }
        });

        const out = await res.json();
        loader.classList.add("d-none");

        const data = out;

        modal.show();
        console.log(data);

        document.getElementById("setting_name").value = data.name;
        document.getElementById("setting_id").value = data.id;
        document.getElementById("setting_name").readOnly = true;
        document.getElementById("setting_label").value = data.label;
        document.getElementById("setting_type").value = data.type;
        document.getElementById("setting_attr").value = data.attr || "";
        document.getElementById("setting_option").value = data.option || "";
        document.getElementById("setting_option").disabled = ![2,3,4].includes(Number(data.type));
        document.getElementById("setting-update").dataset.id = data.id;
    });

    // ------------------------------------------------
    // SAVE VALUE (CHECKBOX / JSON / FILE / SIMPLE)
    // ------------------------------------------------
    document.body.addEventListener("click", async (e) => {
        const btn = e.target.closest(".save-setting-value");
        if (!btn) return;

        const field = btn.dataset.field;
        const type = Number(btn.dataset.type);
        console.log("btn.dataset.type",btn.dataset.type);


        let finalValue;

        // CHECKBOX
        if (type === 4) {
            finalValue = [];
            document.querySelectorAll(`input[name="${field}"]`).forEach(chk => {
                if (chk.checked) finalValue.push(chk.value.trim());
            });
            // finalValue = JSON.stringify(finalValue);
        }

        // JSON
        // else if (type === 8) {
        //     const jsonObj = {};
        //     document.querySelectorAll(`[data-json="${field}"] .json-row`)
        //         .forEach(row => {
        //             const key = row.querySelector(".json-key")?.value || "";
        //             const val = row.querySelector(".json-value")?.value || "";
        //             if (key !== "") jsonObj[key] = val;
        //         });
        //     finalValue = JSON.stringify(jsonObj);
        // }
        else if (type === 8) {

            let field_ = field + "-key";
            let val = {};

            // select all elements with class = field_
            document.querySelectorAll(`.${field_}`).forEach((el) => {
                let key = el.value;
                let valueFieldId = el.dataset.value;
                let value = document.getElementById(valueFieldId)?.value || "";
                val[key] = value;
            }); 
            finalValue = JSON.stringify(val);
        }

        // FILE
        else if (type === 6) {
            const input = document.getElementById(field);
            const fd = new FormData();
            fd.append("file", input.files[0]);

            const res = await fetch(`/settings/save-value/${field}`, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": TOKEN },
                body: fd
            });

            const out = await res.json();
            res.ok ? showDismissible("Updated!", "alert-success")
                   : showDismissible("Failed!", "alert-danger");
            return;
        }

        // SIMPLE
        else {
            finalValue = document.getElementById(field)?.value || "";
        }

        // SEND NORMAL VALUE
        const res = await fetch(`/settings/save-value/${field}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": TOKEN
            },
            body: JSON.stringify({ value: finalValue })
        });

        const out = await res.json();
        res.ok ? showDismissible("Updated!", "alert-success")
               : showDismissible(out.message || "Failed", "alert-danger");
    });

    // ------------------------------------------------
    // ADD JSON ROW
    // ------------------------------------------------
    document.body.addEventListener("click", (e) => {
        if (!e.target.classList.contains("add-json-row")) return;

        const field = e.target.dataset.field;
        const container = document.querySelector(`[data-json="${field}"]`);

        container.insertAdjacentHTML(
            "beforeend",
            `
            <div class="row json-row mb-2">
                <div class="col">
                    <input class="form-control json-key" placeholder="Key">
                </div>
                <div class="col-1 text-center">=</div>
                <div class="col">
                    <input class="form-control json-value" placeholder="Value">
                </div>
            </div>
            `
        );
    });
    document.body.addEventListener("click", (e) => {
        if (!e.target.classList.contains("add-json-row")) return;

        const field = e.target.dataset.field;
        const container = document.querySelector(`[data-json="${field}"]`);

        container.insertAdjacentHTML(
            "beforeend",
            `
            <div class="row json-row mb-2">
                <div class="col">
                    <input class="form-control json-key" placeholder="Key">
                </div>
                <div class="col-1 text-center">=</div>
                <div class="col">
                    <input class="form-control json-value" placeholder="Value">
                </div>
            </div>
            `
        );
    });

document.addEventListener("click", function (e) {
    if (e.target && e.target.id === "add_key_value_pair") {

        let field = e.target.dataset.field;
        let count = parseInt(e.target.dataset.count);

        let html = `
            <div class="row mb-3">
                <div class="col">
                    <input type="text" id="key-${field}-${count}" data-value="value-${field}-${count}" placeholder="Key" class="form-control phone-mask ${field}-key">
                </div>
                <div class="col-1 align-content-around p-0 m-0 text-center"> = </div>
                <div class="col">
                    <input type="text" id="value-${field}-${count}" placeholder="Value" class="form-control phone-mask">
                </div>
            </div>
        `;

        // update count
        e.target.dataset.count = count + 1;

        // append inside .json_fields (parent > parent > .json_fields)
        const container = e.target.closest("div").parentElement.querySelector(".json_fields");

        if (container) {
            container.insertAdjacentHTML("beforeend", html);
        }
    }
});

});
