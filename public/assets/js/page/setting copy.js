document.addEventListener("DOMContentLoaded", () => {

    // Read CSRF token correctly
    const TOKEN = document.querySelector('meta[name="csrf_token"]')?.content || "";

    const modalEl = document.getElementById("addSettingModal");
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const loader = modalEl?.querySelector(".loader-line");

    // ------------------------------------------------
    // OPEN ADD SETTING MODAL
    // ------------------------------------------------
    document.getElementById("add-setting")?.addEventListener("click", () => {
        const form = document.getElementById("addSettingForm");
        if (form) form.reset();

        // Disable option box unless type is select/radio/checkbox
        document.getElementById("setting_option").disabled = true;

        modal?.show();
    });

    // ------------------------------------------------
    // ENABLE OPTION BOX WHEN TYPE == SELECT/RADIO/CHECKBOX
    // ------------------------------------------------
    document.getElementById("setting_type")?.addEventListener("change", (e) => {
        const type = Number(e.target.value);
        const optionBox = document.getElementById("setting_option");
        optionBox.disabled = ![2, 3, 4].includes(type);
    });

    // ------------------------------------------------
    // SAVE NEW/EDIT META SETTING (modal form submit)
    // ------------------------------------------------
    document.getElementById("setting-update")?.addEventListener("click", async () => {
        loader?.classList.remove("d-none");

        const form = document.getElementById("addSettingForm");
        const formData = new FormData(form);

        const res = await fetch("/settings/save", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": TOKEN },
            body: formData,
        });

        const out = await res.json();
        loader?.classList.add("d-none");

        if (out.status) {
            showDismissible("Setting saved!", "alert-success");
            location.reload();
        } else {
            showDismissible(out.message || "Unable to save setting", "alert-danger");
        }
    });

    // ------------------------------------------------
    // SAVE SETTING VALUE  (COMPATIBLE WITH OLD LOGIC)
    // ------------------------------------------------
    document.body.addEventListener("click", async (e) => {

        if (!e.target.classList.contains("save-setting-value")) return;

        const field = e.target.dataset.field; // example: website_title
        const type = Number(e.target.dataset.type);

        let finalValue = null;

        // -------------------------------
        // 1. CHECKBOX GROUP  (OLD LOGIC)
        // -------------------------------
        if (type === 4) {
            finalValue = [];
            document.querySelectorAll(`input[name="${field}"]`).forEach(chk => {
                if (chk.checked) finalValue.push(chk.value);
            });
            // finalValue = JSON.stringify(finalValue);
        }

        // -------------------------------
        // 2. JSON KEY-VALUE (OLD LOGIC)
        // -------------------------------
        else if (type === 8) {
            const jsonObj = {};

            document.querySelectorAll(`[data-json="${field}"] .json-row`).forEach(row => {
                const key = row.querySelector(".json-key")?.value ?? "";
                const val = row.querySelector(".json-value")?.value ?? "";
                if (key !== "") jsonObj[key] = val;
            });

            finalValue = JSON.stringify(jsonObj);
        }

        // -------------------------------
        // 3. FILE UPLOAD
        // -------------------------------
        else if (type === 6) {
            const input = document.getElementById(field);
            const formData = new FormData();
            formData.append("file", input.files[0]);

            const res = await fetch(`/settings/save-value/${field}`, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": TOKEN },
                body: formData
            });

            const out = await res.json();
            if (res.ok) showDismissible("Value updated!", "alert-success");
            else showDismissible("Failed to update value.", "alert-danger");

            return;
        }

        // -------------------------------
        // 4. NORMAL INPUT
        // -------------------------------
        else {
            const input = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
            finalValue = input?.value ?? "";
        }

        // -------------------------------
        // SEND (JSON FORMAT LIKE OLD SYSTEM)
        // -------------------------------
        const payload = {
            value: finalValue
        };

        const res = await fetch(`/settings/save-value/${field}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": TOKEN,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify(payload)
        });

        const out = await res.json();

        if (res.ok) {
            showDismissible("Value updated!", "alert-success");
        } else {
            showDismissible(out.message || "Failed to update value.", "alert-danger");
        }
    });

    // ------------------------------------------------
    // ADD JSON ROW (dynamic)
    // ------------------------------------------------
    document.body.addEventListener("click", (e) => {
        if (!e.target.classList.contains("add-json-row")) return;

        const field = e.target.dataset.field;
        const container = document.querySelector(`[data-json="${field}"]`);

        if (!container) return;

        container.insertAdjacentHTML(
            "beforeend",
            `
            <div class="row json-row mb-2">
                <div class="col">
                    <input class="form-control json-key" placeholder="Key" />
                </div>
                <div class="col-1 text-center">=</div>
                <div class="col">
                    <input class="form-control json-value" placeholder="Value" />
                </div>
            </div>
        `
        );
    });

    // ------------------------------------------------------------
    // EDIT SETTING (OPEN MODAL + LOAD EXISTING META)
    // ------------------------------------------------------------
    document.body.addEventListener("click", async (e) => {
        const btn = e.target.closest(".edit-setting");
        if (!btn) return;

        const field = btn.dataset.field;   // <- CORRECT FIELD ID
        const id = btn.dataset.id;         // internal DB id

        if (!id) {
            console.error("Missing data-id on edit-setting button");
            return;
        }

        const loader = document.querySelector("#addSettingModal .loader-line");
        loader?.classList.remove("d-none");

        try {
            // fetch meta
            const res = await fetch(`/setting/${id}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": TOKEN,
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            const out = await res.json();
            loader?.classList.add("d-none");
            const data = out;
            console.log("data",out);

            // open modal
            const modal = new bootstrap.Modal(document.getElementById("addSettingModal"));
            modal.show();

            // fill form fields
            document.getElementById("addSettingModalLabel").innerHTML = "Edit Setting";
            document.getElementById("setting-update").dataset.id = data.id;

            document.getElementById("setting_name").value = data.name;
            document.getElementById("setting_name").readOnly = true;

            document.getElementById("setting_label").value = data.label;
            document.getElementById("setting_type").value = data.type;

            // enable types 2,3,4
            if ([2, 3, 4].includes(Number(data.type))) {
                document.getElementById("setting_option").disabled = false;
            } else {
                document.getElementById("setting_option").disabled = true;
            }

            document.getElementById("setting_attr").value = data.attr || "";
            document.getElementById("setting_option").value = data.option || "";

        } catch (err) {
            loader?.classList.add("d-none");
            console.error(err);
            showDismissible("Error loading setting", "alert-danger");
        }
    });


});
