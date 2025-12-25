document.addEventListener("DOMContentLoaded", () => {

    var offcanvasWithBothOptions = document.getElementById('offcanvasWithBothOptions')
    var bsOffcanvas = new bootstrap.Offcanvas(offcanvasWithBothOptions)
    document.getElementById("create-project").addEventListener("click", createProject)

    async function createProject() {
        // if (!confirm("Create project from this lead?")) return;
        leadId = document.getElementById("create-project").dataset.id
        showConvertToLeadCanvas()
    }
    document.getElementById("view-lead-quotation").addEventListener("click", showQuotations)

    async function showQuotations() {
        // if (!confirm("Create project from this lead?")) return;
        leadId = document.getElementById("view-lead-quotation").dataset.id
        showQuotationsCanvas(leadId)
    }
    document.getElementById("view-lead-history").addEventListener("click", showHistory)

    async function showHistory() {
        // if (!confirm("Create project from this lead?")) return;
        leadId = document.getElementById("view-lead-history").dataset.id
        showHistoryCanvas(leadId)
        // quoteOffcanvas.show()
    }

    function closeCanvas() {
        document.getElementById("offcanvasWithBothOptionsLabel").textContent = ""
        document.getElementById("offcanvasWithBothOptionsBody").textContent = ""

    }

    function readbleDate(date) {
        if (!date) return ""
        const d = new Date(date);

        const formatted = d.toLocaleString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        return formatted
    }
    async function createProjectPost() {
        let EMI = {};
        let EMI_DATE = {};
        let PAYLOAD = {}
        let DATES = {}
        const finalize_price = document.querySelector("#FinalizePrice").value;
        const priority = document.querySelector("input[name='priority']:checked").value;
        document.querySelectorAll(".emi-date").forEach((dateInput, i) => {
            EMI_DATE[i] = dateInput.value;
        });
        document.querySelectorAll(".emi-amount").forEach((amtInput, i) => {
            EMI[EMI_DATE[i]] = parseInt(amtInput.value || 0);
        });
        document.querySelectorAll("input[type='hidden']").forEach((hiddenInput, i) => {
            PAYLOAD[hiddenInput.name] = hiddenInput.value;
        });
        document.querySelectorAll(".dates-input").forEach((hiddenInput, i) => {
            DATES[hiddenInput.name] = hiddenInput.value;
        });
        const data = {
            'emi': EMI,
            finalize_price,
            priority,
            ...PAYLOAD,
            ...DATES,
        };
        if (!confirm("Create project from this lead?")) return;
        leadId = document.getElementById("create-project").dataset.id

        const res = await fetch(`/marketing/${leadId}/create-project`, {
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

        // Redirect to project edit page
        window.location.href = json.project_url;
    }
    generatePdf = async function(id,ele) {
        confirm("Aa")
        const res = await fetch(`/quotations/${id}/generate-pdf`);
        const json = await res.json();
        if (json.status) {
            const pdfTd = document.getElementById(ele);
            if (!pdfTd) return;

            const embed = document.createElement('embed');
            embed.src = json.pdf_url;
            embed.width = '100%';
            embed.style.minHeight = '800px';
            embed.className = 'border border-3 rounded-3 h-100';

            // Clear old content (if any)
            pdfTd.innerHTML = '';
            pdfTd.appendChild(embed);
        } else {
            alert('Failed to generate PDF');
        }
    };
    async function showConvertToLeadCanvas() {
        document.getElementById("offcanvasWithBothOptionsLabel").textContent = "Convert to Lead"
        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")

        leadId = document.getElementById("create-project").dataset.id

        const res = await fetch(`/marketing/${leadId}/create-project`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const json = await res.json();
        canvasBody.textContent = ""
        if (json.code && json.code == 409) {
            loadMessage(json.project)
        } else {
            loadPayload(json)
            loadInfo(json)
            loadEmiOptions()
            loadPriorityOptions()
            loadDates()
            loadActionButtons()
        }
        bsOffcanvas.show()
    }
    async function showHistoryCanvas(leadId) {
        document.getElementById("offcanvasWithBothOptionsLabel").textContent = "Convert to Lead"
        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")


        const res = await fetch(`/marketing/${leadId}/view/history`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const json = await res.json();
        canvasBody.textContent = ""
        loadHistory(json)
        bsOffcanvas.show()
    }
    async function showQuotationsCanvas(leadId) {
        document.getElementById("offcanvasWithBothOptionsLabel").textContent = "Convert to Lead"
        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")


        const res = await fetch(`/marketing/${leadId}/view/quotations`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const json = await res.json();
        loadQuotations(json)
        bsOffcanvas.show()
    }
    offcanvasWithBothOptions.addEventListener('hidden.bs.offcanvas', function () {
        closeCanvas()
    })




    // Helper: delegate events (like $(document).on)
    function on(event, selector, handler) {
        document.addEventListener(event, function (e) {
            if (e.target.closest(selector)) {
                handler(e);
            }
        });
    }

    function renderQuotation(quotation, index = 0) {

        const row = document.createElement('div');
        row.className = 'row bg-label-secondary mx-0 px-3 mb-3';

        /* HEADER */
        const header = document.createElement('div');
        header.className = 'col-12 my-2 pt-2 d-flex align-items-baseline justify-content-between';

        const title = document.createElement('h6');
        title.textContent = `${index + 1}. ${quotation.quotation_no}`;

        const link = document.createElement('a');
        link.href = `/quotations/${quotation.id}`;
        link.className = 'btn btn-sm btn-info';
        link.textContent = 'Go to Quotations';

        header.append(title, link);

        const hr = document.createElement('hr');

        /* TABLE */
        const table = document.createElement('table');
        table.className = 'table table-borderless mb-0';

        const tbody = document.createElement('tbody');

        // Sent
        tbody.appendChild(createRow('Sent', quotation.sent_by ? 'Yes' : 'No'));

        // Sent By
        if (quotation.sent_by) {
            tbody.appendChild(createRow('Sent By', quotation.sent_by.fname + " " + quotation.sent_by.lname));
            tbody.appendChild(createRow('Sent At', readbleDate(quotation.sent_at)));
        }

        // File row (collapse)
        const fileRow = document.createElement('tr');
        fileRow.className = 'row';

        const toggleTd = document.createElement('td');
        toggleTd.className = 'col-12 collapsed d-flex justify-content-between';
        toggleTd.setAttribute('data-bs-toggle', 'collapse');
        toggleTd.setAttribute('data-bs-target', `#pdf${quotation.id}`);
        toggleTd.setAttribute('aria-expanded', 'false');
        toggleTd.setAttribute('aria-controls', `pdf${quotation.id}`);

        toggleTd.innerHTML = `
        <span>File</span>
        <span class="mdi mdi-chevron-down"></span>
    `;

        const pdfTd = document.createElement('td');
        pdfTd.className = 'col-12 collapse';
        pdfTd.id = `pdf${quotation.id}`;

        if(quotation.pdf_path){
            const embed = document.createElement('embed');
            embed.src = `/storage/${quotation.pdf_path}`;
            embed.width = '100%';
            embed.style.minHeight = '800px';
            embed.className = 'border border-3 rounded-3 h-100';
    
            pdfTd.appendChild(embed);
        }else{
            const embed = document.createElement('div');
            embed.classList = `w-100 h-px-400 d-flex flex-column justify-content-center align-items-center border border-3 rounded-3`;
            embed.textContent = 'PDF Is not cretaed yet. click this button to generate PDF';
    
            const pdf_btn = document.createElement('div');
            pdf_btn.classList = `btn btn-sm btn-secondary`;
            pdf_btn.textContent = 'PDF';
            pdf_btn.addEventListener('click', () => {
                generatePdf(quotation.id,`pdf${quotation.id}`);
            });
            embed.appendChild(pdf_btn);
    
            pdfTd.appendChild(embed);
        }
        fileRow.append(toggleTd, pdfTd);
        tbody.appendChild(fileRow);

        table.appendChild(tbody);

        row.append(header, hr, table);
        return row
    }

    /* helper to create table row */
    function createRow(label, value) {
        const tr = document.createElement('tr');
        tr.className = 'row';

        const td1 = document.createElement('td');
        td1.className = 'col-4';
        td1.textContent = label;

        const td2 = document.createElement('td');
        td2.className = 'col-6';
        td2.textContent = value ?? '';

        tr.append(td1, td2);
        return tr;
    }


    function loadQuotations(json) {
        const container = document.getElementById('offcanvasWithBothOptionsBody');

        if (Array.isArray(json.quotations) && json.quotations.length > 0) {
            quotations = json.quotations
            quotations.forEach((quotation, index) => {
                row = renderQuotation(quotation, index)
                container.appendChild(row);
            });

        }

    }
    function loadHistory(json) {
        const container = document.getElementById('offcanvasWithBothOptionsBody');

        if (Array.isArray(json.history) && json.history.length > 0) {
            historyList = json.history
            historyList.forEach((hs, index) => {
                row = renderLeadHistory(hs, index)
                container.appendChild(row);
            });

        }
    }
    function renderLeadHistory(item, index) {
        // if (!Array.isArray(historyList) || historyList.length === 0) return;

        // historyList.forEach(item => {

        const wrapper = document.createElement('div');
        wrapper.className = 'lead-history-item border rounded-3 p-3 mb-2 bg-white';

        /* TOP ROW */
        const top = document.createElement('div');
        top.className = 'd-flex justify-content-between align-items-start';

        const left = document.createElement('div');

        const badge = document.createElement('span');
        badge.className = 'badge bg-info text-uppercase mb-1';
        badge.textContent = item.action.replace('_', ' ');

        const title = document.createElement('div');
        title.className = 'fw-semibold mt-1';
        title.textContent = 'Status updated';

        const message = document.createElement('div');
        message.className = 'text-muted small';
        message.textContent = item.message;

        left.append(badge, title, message);

        const right = document.createElement('div');
        right.className = 'text-end text-muted small';
        right.innerHTML = `
            <div>${readbleDate(item.created_at).split(',')[0]}</div>
            <div>${readbleDate(item.created_at).split(',')[1]}</div>
        `;

        top.append(left, right);

        /* FOOTER */
        const footer = document.createElement('div');
        footer.className = 'mt-2 text-muted small';
        footer.innerHTML = `
            <i class="mdi mdi-account-outline me-1"></i>
            by <span class="fw-medium">${item.user.fname} ${item.user.lname}</span>
        `;

        wrapper.append(top, footer);
        return wrapper
        // container.appendChild(wrapper);
        // });
    }

    function loadMessage(project) {
        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")
        const placeholderWrapper = document.createElement('div')
        const placeholder = document.createElement('div')
        placeholder.textContent = "Project already exists for this lead."
        const project_btn = document.createElement('a')
        project_btn.textContent = "Open"
        project_btn.classList = "btn btn-warning"
        project_btn.href = "/projects/" + project.id + "/view"
        placeholderWrapper.classList = "border border-warning border-dashed border-3 w-100 bg-label-warning h-100 rounded-3 d-flex flex-column align-items-center justify-content-center gap-2"

        placeholderWrapper.appendChild(placeholder)
        placeholderWrapper.appendChild(project_btn)
        canvasBody.appendChild(placeholderWrapper)
    }

    function loadPayload(json) {
        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")
        Object.entries(json.payload).map((val) => {
            const input_sd = document.createElement("input");
            input_sd.setAttribute("type", "hidden");
            input_sd.setAttribute("name", val[0]);
            input_sd.setAttribute("value", val[1]);
            input_sd.setAttribute("id", val[0] + "-input");
            canvasBody.appendChild(input_sd)
        })
    }
    function loadInfo(json) {
        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")
        const tableWrapper = document.createElement('div')
        tableWrapper.classList = 'table-responsive text-nowrap'
        const table = document.createElement('table')
        table.classList = 'table table-striped'
        const tableHead = document.createElement('thead')
        const headTr = document.createElement('tr')
        const headTh1 = document.createElement('th')
        headTh1.textContent = "Name"
        headTh1.classList = "col-4"
        const headTh2 = document.createElement('th')
        headTh2.textContent = "Value"

        headTr.appendChild(headTh1)
        headTr.appendChild(headTh2)
        tableHead.appendChild(headTr)
        table.appendChild(tableHead)

        const tableBody = document.createElement('tbody')
        tableBody.classList = "table-border-bottom-0"

        const leadCodeTr = document.createElement('tr')
        const leadCodeTD1 = document.createElement('td')
        leadCodeTD1.textContent = "Lead Code"
        const leadCodeTD2 = document.createElement('td')
        leadCodeTD2.textContent = json.lead.lead_code;

        leadCodeTr.appendChild(leadCodeTD1)
        leadCodeTr.appendChild(leadCodeTD2)
        tableBody.appendChild(leadCodeTr)

        const CustomerTr = document.createElement('tr')
        const CustomerTD1 = document.createElement('td')
        CustomerTD1.textContent = "Customer"
        const CustomerTD2 = document.createElement('td')
        CustomerTD2.textContent = json.lead?.customer ? json.lead.customer.name : "";

        CustomerTr.appendChild(CustomerTD1)
        CustomerTr.appendChild(CustomerTD2)
        tableBody.appendChild(CustomerTr)

        const MasterTr = document.createElement('tr')
        const MasterTD1 = document.createElement('td')
        MasterTD1.textContent = "Quote Master"
        const MasterTD2 = document.createElement('td')
        MasterTD2.textContent = json.lead.quote_master.sku

        MasterTr.appendChild(MasterTD1)
        MasterTr.appendChild(MasterTD2)
        tableBody.appendChild(MasterTr)

        const RequestTr = document.createElement('tr')
        const RequestTD1 = document.createElement('td')
        RequestTD1.textContent = "Quote Request"
        const RequestTD2 = document.createElement('td')
        RequestTD2.textContent = json.lead.quote_request.module + " " + json.lead.quote_request.kw + " (" + json.lead.quote_request.status + ")"

        RequestTr.appendChild(RequestTD1)
        RequestTr.appendChild(RequestTD2)
        tableBody.appendChild(RequestTr)

        const AssigneeTr = document.createElement('tr')
        const AssigneeTD1 = document.createElement('td')
        AssigneeTD1.textContent = "Assignee"
        const AssigneeTD2 = document.createElement('td')
        AssigneeTD2.textContent = json.lead?.assigned_user ? json.lead.assigned_user.fname + " " + json.lead.assigned_user.lname : ""

        AssigneeTr.appendChild(AssigneeTD1)
        AssigneeTr.appendChild(AssigneeTD2)
        tableBody.appendChild(AssigneeTr)

        const ReporterTr = document.createElement('tr')
        const ReporterTD1 = document.createElement('td')
        ReporterTD1.textContent = "Reporter"
        const ReporterTD2 = document.createElement('td')
        ReporterTD2.textContent = window?.auth ? window.auth.fname + " " + window.auth.lname : ""

        ReporterTr.appendChild(ReporterTD1)
        ReporterTr.appendChild(ReporterTD2)
        tableBody.appendChild(ReporterTr)


        table.appendChild(tableBody)
        tableWrapper.appendChild(table)
        canvasBody.appendChild(tableWrapper)
    }
    function loadEmiOptions() {

        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")
        // ----------------------
        // ROW 1 (Finalize Price)
        // ----------------------
        const row1 = document.createElement("div");
        row1.className = "row mx-0 my-1";

        // --- Col 1 (Finalize Price input) ---
        const col1 = document.createElement("div");
        col1.className = "col-4 my-1";

        const labelFP = document.createElement("label");
        labelFP.className = "form-label";
        labelFP.setAttribute("for", "FinalizePrice");
        labelFP.textContent = "Finalize Price";

        const inputFP = document.createElement("input");
        inputFP.type = "number";
        inputFP.className = "form-control";
        inputFP.name = "FinalizePrice";
        inputFP.id = "FinalizePrice";
        inputFP.placeholder = "Finalize Price";

        col1.appendChild(labelFP);
        col1.appendChild(inputFP);


        // --- Col 2 (Edit button) ---
        const col2 = document.createElement("div");
        col2.className = "col-3";

        const labelEdit = document.createElement("label");
        labelEdit.className = "form-label";
        labelEdit.setAttribute("for", "FinalizePrice");
        labelEdit.textContent = "Edit";

        const btnEdit = document.createElement("button");
        btnEdit.type = "button";
        btnEdit.className = "d-block edit-finalize-price btn-sm rounded-circle my-2 my-1 btn btn-icon btn-outline-primary waves-effect";

        const iconEdit = document.createElement("span");
        iconEdit.className = "tf-icons mdi mdi-pen";

        btnEdit.appendChild(iconEdit);
        col2.appendChild(labelEdit);
        col2.appendChild(btnEdit);


        // Append row1
        row1.appendChild(col1);
        row1.appendChild(col2);




        // ----------------------
        // ROW 2 (EMI Fields)
        // ----------------------
        const row2 = document.createElement("div");
        row2.className = "row emi-fields mx-0 my-1";


        // --- Col EMI Amount ---
        const col3 = document.createElement("div");
        col3.className = "col-4 my-1";

        const labelEmiAmt = document.createElement("label");
        labelEmiAmt.className = "form-label";
        labelEmiAmt.setAttribute("for", "EMI1");
        labelEmiAmt.textContent = "EMI Amounts";

        const inputEmiAmt = document.createElement("input");
        inputEmiAmt.type = "number";
        inputEmiAmt.className = "form-control emi-amount";
        inputEmiAmt.id = "EMI1";
        inputEmiAmt.placeholder = "EMI 1";

        col3.appendChild(labelEmiAmt);
        col3.appendChild(inputEmiAmt);


        // --- Col EMI Date ---
        const col4 = document.createElement("div");
        col4.className = "col-4 my-1";

        const labelEmiDate = document.createElement("label");
        labelEmiDate.className = "form-label";
        labelEmiDate.setAttribute("for", "EMI1");
        labelEmiDate.textContent = "EMI Dates";

        const inputEmiDate = document.createElement("input");
        inputEmiDate.type = "date";
        inputEmiDate.className = "form-control emi-date";
        inputEmiDate.id = "EMIdate1";
        inputEmiDate.placeholder = "EMI 1 Date";

        // const field_sd = document.createElement("div");
        // field_sd.className = "form-floating form-floating-outline";

        // const input_sd = document.createElement("input");
        // input_sd.className = "form-control";
        // input_sd.setAttribute("type", "date");
        // input_sd.setAttribute("name", "survey_date");
        // input_sd.setAttribute("id", "EMIdate1");

        // const label_sd = document.createElement("label");
        // label_sd.setAttribute("for", "EMIdate1");
        // label_sd.textContent = "EMI 1 Date"

        // field_sd.appendChild(input_sd)
        // field_sd.appendChild(label_sd)

        col4.appendChild(labelEmiDate);
        // col4.appendChild(field_sd)
        col4.appendChild(inputEmiDate);


        // --- Col Add Button ---
        const col5 = document.createElement("div");
        col5.className = "col";

        const labelAction = document.createElement("label");
        labelAction.className = "form-label";
        labelAction.setAttribute("for", "EMI1");
        labelAction.textContent = "Action";

        const btnAdd = document.createElement("button");
        btnAdd.type = "button";
        btnAdd.className = "d-block btn-sm rounded-circle my-2 my-1 add-emi-field btn btn-icon btn-outline-info waves-effect";

        const iconAdd = document.createElement("span");
        iconAdd.className = "tf-icons mdi mdi-plus";

        btnAdd.appendChild(iconAdd);
        col5.appendChild(labelAction);
        col5.appendChild(btnAdd);


        // Append row2
        row2.appendChild(col3);
        row2.appendChild(col4);
        row2.appendChild(col5);



        const row3 = document.createElement("div");
        row3.className = "row emi-fields mx-0 my-1";

        const col7 = document.createElement("div");
        col7.className = "col-4 my-1";
        const col8 = document.createElement("div");
        col8.className = "col-6 my-1";
        const col9 = document.createElement("div");
        col9.className = "col-4 my-1";
        const col10 = document.createElement("div");
        col10.className = "col-6 my-1";

        const emiset = document.createElement("label");
        emiset.className = "form-label";
        emiset.textContent = "Total EMI Amount";

        const emiunset = document.createElement("label");
        emiunset.className = "form-label";
        emiunset.textContent = "EMI Amount Remaining";


        row3.appendChild(col7);

        const emisetAmount = document.createElement("div");
        emisetAmount.className = "text-success";
        emisetAmount.id = "emisetAmount"
        emisetAmount.textContent = "0";

        const emiunsetAmount = document.createElement("div");
        emiunsetAmount.className = "text-warning";
        emiunsetAmount.id = "emiunsetAmount"
        emiunsetAmount.textContent = "0";

        col7.appendChild(emiset);
        col8.appendChild(emisetAmount);
        col9.appendChild(emiunset);
        col10.appendChild(emiunsetAmount);


        row3.appendChild(col7);
        row3.appendChild(col8);
        row3.appendChild(col9);
        row3.appendChild(col10);
        // ----------------------
        // Append everything to canvasBody
        // ----------------------
        canvasBody.appendChild(row1);
        canvasBody.appendChild(row2);
        // canvasBody.appendChild(row3);
        canvasBody.appendChild(row3);
    }
    function loadPriorityOptions() {

        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")
        // ----------------------
        // ROW 1 (Finalize Price)
        // ----------------------
        const row = document.createElement("div");
        row.className = "row mx-0 my-1 mb-2";

        // --- Col 1 (Finalize Price input) ---
        const col1 = document.createElement("div");
        col1.className = "col-4 my-1";

        const labelFP = document.createElement("label");
        labelFP.className = "form-label";
        labelFP.textContent = "Priority";

        col1.appendChild(labelFP);

        // --- Col 2 (Edit button) ---
        const col2 = document.createElement("div");
        col2.className = "col-6";

        const inputgroup = document.createElement("div");
        inputgroup.className = "input-group";

        const lowBtn = document.createElement("input");
        lowBtn.className = "btn-check";
        lowBtn.setAttribute("name", "priority");
        lowBtn.setAttribute("type", "radio");
        lowBtn.setAttribute("id", "low");
        lowBtn.setAttribute("value", "low");
        const lowLabel = document.createElement("label");
        lowLabel.className = "btn btn-outline-success";
        lowLabel.textContent = "Low";
        lowLabel.setAttribute("for", "low");
        inputgroup.appendChild(lowBtn);
        inputgroup.appendChild(lowLabel);

        const mediumBtn = document.createElement("input");
        mediumBtn.className = "btn-check";
        mediumBtn.setAttribute("name", "priority");
        mediumBtn.setAttribute("type", "radio");
        mediumBtn.setAttribute("checked", true);
        mediumBtn.setAttribute("id", "medium");
        mediumBtn.setAttribute("value", "medium");
        const mediumLabel = document.createElement("label");
        mediumLabel.className = "btn btn-outline-warning";
        mediumLabel.textContent = "Medium";;
        mediumLabel.setAttribute("for", "medium");
        inputgroup.appendChild(mediumBtn);
        inputgroup.appendChild(mediumLabel);

        const highBtn = document.createElement("input");
        highBtn.className = "btn-check";
        highBtn.setAttribute("name", "priority");
        highBtn.setAttribute("type", "radio");
        highBtn.setAttribute("id", "high");
        highBtn.setAttribute("value", "high");
        const highLabel = document.createElement("label");
        highLabel.className = "btn btn-outline-danger";
        highLabel.textContent = "High";
        highLabel.setAttribute("for", "high");
        inputgroup.appendChild(highBtn);
        inputgroup.appendChild(highLabel);
        col2.appendChild(inputgroup)
        // Append row
        row.appendChild(col1);
        row.appendChild(col2);
        canvasBody.appendChild(row);
    }
    function loadDates() {

        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")
        // ----------------------
        // ROW 1 (Finalize Price)
        // ----------------------
        const row1 = document.createElement("div");
        row1.className = "row mx-0 my-2 align-items-center";

        // --- Col 1 (Finalize Price input) ---
        const col1 = document.createElement("div");
        col1.className = "col-4";
        col1.textContent = "Survey Date";

        // --- Col 2 (Edit button) ---
        const col2 = document.createElement("div");
        col2.className = "col-6 my-2";

        const field_sd = document.createElement("div");
        field_sd.className = "form-floating form-floating-outline";

        const input_sd = document.createElement("input");
        input_sd.className = "form-control dates-input";
        input_sd.setAttribute("type", "date");
        input_sd.setAttribute("name", "survey_date");
        input_sd.setAttribute("id", "survey-date-input");

        const label_sd = document.createElement("label");
        label_sd.setAttribute("for", "survey-date-label");
        label_sd.textContent = "Survey Date"

        field_sd.appendChild(input_sd)
        field_sd.appendChild(label_sd)

        col2.appendChild(field_sd)
        // Append row1
        row1.appendChild(col1);
        row1.appendChild(col2);

        const col3 = document.createElement("div");
        col3.className = "col-4";
        col3.textContent = "Installation Start Date";

        // --- Col 2 (Edit button) ---
        const col4 = document.createElement("div");
        col4.className = "col-6 my-1";

        const field_isd = document.createElement("div");
        field_isd.className = "form-floating form-floating-outline";

        const input_isd = document.createElement("input");
        input_isd.className = "form-control dates-input";
        input_isd.setAttribute("type", "date");
        input_isd.setAttribute("name", "installation_start_date");
        input_isd.setAttribute("id", "installation_start_date-input");

        const label_isd = document.createElement("label");
        label_isd.setAttribute("for", "installation_start_date-label");
        label_isd.textContent = "Installation Start Date"

        field_isd.appendChild(input_isd)
        field_isd.appendChild(label_isd)

        col4.appendChild(field_isd)
        // Append row1
        row1.appendChild(col3);
        row1.appendChild(col4);

        const col5 = document.createElement("div");
        col5.className = "col-4";
        col5.textContent = "Installation Date";

        // --- Col 2 (Edit button) ---
        const col6 = document.createElement("div");
        col6.className = "col-6 my-1";

        const field_id = document.createElement("div");
        field_id.className = "form-floating form-floating-outline";

        const input_id = document.createElement("input");
        input_id.className = "form-control dates-input";
        input_id.setAttribute("type", "date");
        input_id.setAttribute("name", "inspection_date");
        input_id.setAttribute("id", "installation_date-input");

        const label_id = document.createElement("label");
        label_id.setAttribute("for", "installation_date-label");
        label_id.textContent = "Installation Date"

        field_id.appendChild(input_id)
        field_id.appendChild(label_id)

        col6.appendChild(field_id)
        // Append row1
        row1.appendChild(col5);
        row1.appendChild(col6);

        const col7 = document.createElement("div");
        col7.className = "col-4";
        col7.textContent = "Estimated Complete Date";

        // --- Col 2 (Edit button) ---
        const col8 = document.createElement("div");
        col8.className = "col-6 my-1";

        const field_ecd = document.createElement("div");
        field_ecd.className = "form-floating form-floating-outline";

        const input_ecd = document.createElement("input");
        input_ecd.className = "form-control dates-input";
        input_ecd.setAttribute("type", "date");
        input_ecd.setAttribute("name", "estimated_complete_date");
        input_ecd.setAttribute("id", "estimated_complete_date-input");

        const label_ecd = document.createElement("label");
        label_ecd.setAttribute("for", "estimated_complete_date-label");
        label_ecd.textContent = "Estimated Complete Date"

        field_ecd.appendChild(input_ecd)
        field_ecd.appendChild(label_ecd)

        col8.appendChild(field_ecd)
        // Append row1
        row1.appendChild(col7);
        row1.appendChild(col8);
        canvasBody.appendChild(row1);
    }
    function loadActionButtons() {

        const canvasBody = document.getElementById("offcanvasWithBothOptionsBody")
        // ----------------------
        // ROW 1 (Finalize Price)
        // ----------------------
        const row = document.createElement("div");
        row.className = "d-flex gap-2 justify-content-center mb-2 mt-5 mx-0";

        // --- Col 2 (Edit button) ---
        // const col2 = document.createElement("div");
        // col2.className = "col";


        const Create = document.createElement("button");
        Create.className = "btn btn-success";
        Create.setAttribute("name", "submit");
        Create.setAttribute("id", "createProject");
        // Create.setAttribute("onclick", "createProjectPost()");
        Create.addEventListener("click", createProjectPost);
        Create.textContent = "Create";

        const Cancel = document.createElement("button");
        Cancel.className = "btn btn-outline-danger";
        Cancel.setAttribute("id", "cancelProject");
        Cancel.textContent = "Cancel";

        row.appendChild(Cancel);
        row.appendChild(Create);
        // Append row
        // row.appendChild(col2);
        canvasBody.appendChild(row);
    }

    // Add EMI field
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

    on("click", "#cancelProject", (e) => {
        bsOffcanvas.hide()
    })


});
