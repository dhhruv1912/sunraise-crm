
function on(event, selector, handler) {
    document.addEventListener(event, function (e) {
        if (e.target.closest(selector)) {
            handler(e);
        }
    });
}

on("click", ".add-emi-field", () => {
    const finalizePrice = document.querySelector("#FinalizePrice");
    finalizePrice.disabled = true;

    const fields = document.querySelector(".emi-fields");
    const count = fields.querySelectorAll("input[type='number']").length + 1;

    fields.insertAdjacentHTML(
        "beforeend",
        `
        <div class="col-4 my-1">
            <input type="number" class="form-control emi-amount" name="emi-amount" id="EMI${count}" placeholder="EMI ${count}">
        </div>
        <div class="col-4 my-1">
            <input type="date" class="form-control emi-date" id="EMIdate${count}" placeholder="EMI Date ${count}">
        </div>
        <div class="col my-1">
            <button type="button" data-id="${count}" class="delete-emi btn-sm rounded-circle my-1 btn btn-icon btn-outline-danger waves-effect">
                <span class="tf-icons mdi mdi-delete-empty"></span>
            </button>
        </div>`
    );
});

// Finalize price keyup
on("keyup", "#FinalizePrice", (e) => {
    const input = e.target;
    const val = input.value;
    input.dataset.dp = val;

    let all_emi = 0;

    document.querySelectorAll(".emi-amount").forEach(inp => {
        all_emi += parseInt(inp.value || 0);
    });
    document.getElementById("emiunsetAmount").textContent = (parseInt(val) || 0) - all_emi;
});

// Enable Finalize Price editing
on("click", ".edit-finalize-price", () => {
    document.querySelector("#FinalizePrice").disabled = false;
});

// EMI value change logic
on("keyup", ".emi-amount", (e) => {
    const dp = parseInt(document.querySelector("#FinalizePrice").dataset.dp || 0);
    const val = parseInt(e.target.value || 0);

    let all_emi = 0;

    document.querySelectorAll(".emi-amount").forEach(inp => {
        all_emi += parseInt(inp.value || 0);
    });

    all_emi = all_emi - val;

    let remaining = dp - all_emi - val;

    if (remaining >= 0) {
        document.getElementById("emiunsetAmount").textContent = remaining;
        document.getElementById("emisetAmount").textContent = dp - remaining;
    } else {
        document.getElementById("emiunsetAmount").textContent = dp - all_emi;
        document.getElementById("emisetAmount").textContent = dp;
        e.target.value = "";
    }
});

// Delete EMI
on("click", ".delete-emi", (e) => {
    const btn = e.target.closest(".delete-emi");
    const id = btn.dataset.id;

    const targetInput = document.querySelector("#EMI" + id);
    const inputCol = targetInput.parentElement;
    const targetDate = document.querySelector("#EMIdate" + id);
    const DateCol = targetDate.parentElement;


    const val = parseInt(targetInput.value || 0);
    document.getElementById("emiunsetAmount").textContent = Number(document.getElementById("emiunsetAmount").textContent) + val;
    document.getElementById("emisetAmount").textContent = Number(document.getElementById("emisetAmount").textContent) - val;
    // const emi1 = document.querySelector("#EMI1");
    // emi1.value = parseInt(emi1.value || 0) + val;

    btn.remove();
    inputCol.remove();
    DateCol.remove();
});

on('click', '#saveEmiData', async (e) => {
    const btn = e.target
    const project_id = btn.dataset.projectId
    let EMI = {};
    let EMI_DATE = {};
    let PAYLOAD = {}
    let DATES = {}
    const finalize_price = document.querySelector("#FinalizePrice").value;
    document.querySelectorAll(".emi-date").forEach((dateInput, i) => {
        EMI_DATE[i] = dateInput.value;
    });
    document.querySelectorAll(".emi-amount").forEach((amtInput, i) => {
        EMI[EMI_DATE[i]] = parseInt(amtInput.value || 0);
    });
    const data = {
        'emi': EMI,
        finalize_price,
    };
    const res = await fetch(`/projects/${project_id}/updateEmi`, {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
    });

    const json = await res.json();

    if (!json.status) {
        alert(json.message);
        return;
    }

    alert("Project created successfully!");
})