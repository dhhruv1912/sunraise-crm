
document.addEventListener("DOMContentLoaded", () => {
    loadHome();

    document.addEventListener("keyup", e => {
        if (e.key === "Escape") goBack();
    });
});

// ------------------------------
// Reload
// ------------------------------
function reload() {
    document.querySelector("#table-loader").classList.remove('d-none');
    localStorage.removeItem("ledgers");
    localStorage.removeItem("voucher_type_mapping");
    loadHome();
    document.querySelector("#table-loader").classList.add('d-none');
}

// ------------------------------
// Load Home
// ------------------------------
function loadHome() {
    document.querySelector("#table-loader").classList.remove('d-none');
    let ledgersLocal = localStorage.getItem("ledgers");
    let mappingLocal = localStorage.getItem("voucher_type_mapping");

    if (ledgersLocal && mappingLocal) {
        process_ledger(JSON.parse(ledgersLocal), JSON.parse(mappingLocal));
    } else {
        get_ledger();
    }
    document.querySelector("#table-loader").classList.add('d-none');
}

// ------------------------------
// AJAX → fetch()
// ------------------------------
async function get_ledger() {
    document.querySelector("#table-loader").classList.remove('d-none');
    const res = await fetch(`/tally/data/ledger`);
    const response = await res.json();

    let ledgerData = response?.data?.BODY?.DATA?.COLLECTION?.LEDGER;

    if (ledgerData) {
        localStorage.setItem("ledgers", JSON.stringify(ledgerData));
        localStorage.setItem("voucher_type_mapping", JSON.stringify(response.voucher_type_mapping));

        process_ledger(ledgerData, response.voucher_type_mapping);
    }
    document.querySelector("#table-loader").classList.add('d-none');
}

// ------------------------------
// Format Ledger Data
// ------------------------------
function fotmate_data(DATA) {
    const result = {};
    const masters = [];

    DATA.forEach(ledger => {
        const name = ledger['@attributes'].NAME;
        const parent = ledger?.PARENT;
        const balance = typeof ledger?.CLOSINGBALANCE === "string" ? ledger.CLOSINGBALANCE : "0";

        if (!masters.includes(parent)) {
            masters.push(parent);
            result[parent] = [];
        }

        result[parent].push({ name, balance });
    });

    return result;
}

// ------------------------------
// Process Ledger & Render Table
// ------------------------------
function process_ledger(DATA, voucher_type_mapping) {
    const LEDGERS = fotmate_data(DATA);

    const table = document.querySelector("#tally-datatable tbody");
    const thead = document.querySelector("#tally-datatable thead");

    table.innerHTML = "";
    thead.innerHTML = "";

    thead.innerHTML = `
        <tr class="table-secondary">
            <th>Type<br><input id="LedgerGroupSearch" class="form-control form-control-sm" type="text"></th>
            <th>Count</th>
            <th>Balance</th>
        </tr>
    `;

    document.querySelector("#table-loader").classList.remove('d-none');
    const rows = {};
    for (let index = 0; index < Object.keys(voucher_type_mapping).length; index++) {
        const key = Object.keys(voucher_type_mapping)[index];
        const val = Object.values(voucher_type_mapping)[index];
        if(key != ""){
            rows[val] = {}
        }

    }

    rows["OTHERS"] = {};

    Object.entries(LEDGERS).forEach(([MASTER, LEDGER]) => {

        const totalBalance = LEDGER.reduce((sum, i) => sum + parseFloat(i.balance), 0);

        const tr = document.createElement("tr");
        tr.ondblclick = () => loadLedgerByMaster(MASTER);

        tr.innerHTML = `
            <td class="LedgerGroup">${MASTER}</td>
            <td>${LEDGER.length}</td>
            <td>${displayAmount(totalBalance)}</td>
        `;
        if (voucher_type_mapping.hasOwnProperty(MASTER) && voucher_type_mapping[MASTER] != "") {
            rows[voucher_type_mapping[MASTER]][MASTER] = tr;
        } else {
            rows["OTHERS"][MASTER] = tr;
        }
        // if (voucher_type_mapping[MASTER]) {
        //     rows[voucher_type_mapping[MASTER]][MASTER] = tr;
        // } else {
        //  rows["OTHERS"][MASTER] = tr;
        // }
    });
    // console.log(rows);


    // Print Parent & Child Rows
    Object.entries(rows).forEach(([TYPE, masters]) => {
        const parent = document.createElement("tr");
        parent.className = "table-primary VOUCHER_TYPE";
        parent.dataset.parent = TYPE;
        parent.innerHTML = `<td colspan="3">${TYPE}</td>`;
        table.appendChild(parent);

        Object.values(masters).forEach(row => {
            row.classList.add("VOUCHER_TYPE_CHILD");
            row.dataset.parent = TYPE;
            table.appendChild(row);
        });
    });

    document.querySelector("#table-loader").classList.add('d-none');
    changeTitle({ route: "home" });
}

// ------------------------------
// Load Ledger by MASTER
// ------------------------------
function loadLedgerByMaster(MASTER) {
    changeTitle({ route: "master", master: MASTER });

    let ledgers = JSON.parse(localStorage.getItem("ledgers") || "[]");

    const LEDGERS = fotmate_data(ledgers);
    const table = document.querySelector("#tally-datatable tbody");
    const thead = document.querySelector("#tally-datatable thead");

    document.querySelector("#table-loader").classList.remove('d-none');
    table.innerHTML = "";
    thead.innerHTML = `
        <tr class="table-secondary">
            <th>Name<br><input id="LedgerNameSearch" class="form-control form-control-sm" type="text"></th>
            <th>Closing Balance</th>
        </tr>
    `;

    if (MASTER === "All") {
        Object.values(LEDGERS).forEach(group => {
            group.forEach(l => {
                const tr = document.createElement("tr");
                tr.ondblclick = () => loadVoucherOfLedger(l.name);

                tr.innerHTML = `
                    <td class="LedgerName">${l.name}</td>
                    <td>${displayAmount(l.balance)}</td>
                `;

                table.appendChild(tr);
            });
        });
    } else {
        LEDGERS[MASTER]?.forEach(l => {
            const tr = document.createElement("tr");
            tr.ondblclick = () => loadVoucherOfLedger(l.name);

            tr.innerHTML = `
                <td class="LedgerName">${l.name}</td>
                <td>${displayAmount(l.balance)}</td>
            `;

            table.appendChild(tr);
        });
    }
    document.querySelector("#table-loader").classList.add('d-none');
}

// ------------------------------
// Load Ledger Vouchers
// ------------------------------
async function loadVoucherOfLedger(LEDGER) {
    changeTitle({ route: "ledger", ledger: LEDGER });
    document.querySelector("#table-loader").classList.remove('d-none');
    const res = await fetch(`/tally/data/ledger_voucher?ledger=${encodeURIComponent(LEDGER)}`);
    const response = await res.json();

    const vouchers = response.data;
    const balance = response.balance;
    const ledger_enties = response.ledger_enties;
    renderVoucherAnalytics(vouchers);
    const table = document.querySelector("#tally-datatable tbody");
    const thead = document.querySelector("#tally-datatable thead");

    table.innerHTML = "";
    thead.innerHTML = `
        <tr class="table-secondary">
            <th>Transaction Date<br><input id="LedgerTransactionDateSearch" class="form-control form-control-sm"></th>
            <th>Transaction Type<br><input id="LedgerTransactionTypeSearch" class="form-control form-control-sm"></th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    `;

    balance.current_debit = 0;
    balance.current_credit = 0;

    vouchers.forEach(v => {
        let debit = typeof v.debit === "string" ? parseFloat(v.debit.replace("-", "")) : "";
        let credit = typeof v.credit === "string" ? parseFloat(v.credit.replace("-", "")) : "";

        if (ledger_enties === "credit") {
            debit = isNaN(debit) ? "" : debit;
        } else {
            credit = isNaN(credit) ? "" : credit;
        }

        if (debit) balance.current_debit += debit;
        if (credit) balance.current_credit += credit;

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td class="LedgerTransactionDate">${v.date}</td>
            <td class="LedgerTransactionType">${v.type}</td>
            <td>${displayAmount(debit)}</td>
            <td>${displayAmount(credit)}</td>
        `;

        table.appendChild(tr);
    });

    // Extra rows (opening, closing)
    table.innerHTML += `
        <tr><td colspan="4"></td></tr>
    `;

    table.innerHTML += `
        <tr>
            <td></td><td>Opening Balance</td>
            ${balance.opening > 0
                ? `<td></td><td>${displayAmount(balance.opening)}</td>`
                : `<td>${displayAmount(balance.opening)}</td><td></td>`
            }
        </tr>
    `;

    table.innerHTML += `
        <tr>
            <td></td><td>Current Total</td>
            <td>${displayAmount(balance.current_debit)}</td>
            <td>${displayAmount(balance.current_credit)}</td>
        </tr>

        <tr>
            <td></td><td>Closing Balance</td>
            ${balance.closing > 0
                ? `<td></td><td>${displayAmount(balance.closing)}</td>`
                : `<td>${displayAmount(balance.closing)}</td><td></td>`
            }
        </tr>
    `;
    document.querySelector("#table-loader").classList.add('d-none');
}
let vaCharts = {};

function renderVoucherAnalytics(vouchers) {

    if (!vouchers || !vouchers.length) return;

    document.getElementById('voucherAnalytics').classList.remove('d-none');

    let totalDebit = 0;
    let totalCredit = 0;
    let typeMap = {};
    let dailyMap = {};

    vouchers.forEach(v => {
        const debit = parseFloat(v.debit || 0) || 0;
        const credit = parseFloat(v.credit || 0) || 0;

        totalDebit += debit;
        totalCredit += credit;

        // Voucher type
        typeMap[v.type] = (typeMap[v.type] || 0) + 1;

        // Daily net
        const date = v.date;
        dailyMap[date] = (dailyMap[date] || 0) + (credit - debit);
    });

    // KPIs
    document.getElementById('vaTotalDebit').innerText =
        '₹ ' + displayAmount(totalDebit);

    document.getElementById('vaTotalCredit').innerText =
        '₹ ' + displayAmount(totalCredit);

    document.getElementById('vaNetFlow').innerText =
        '₹ ' + displayAmount(totalCredit - totalDebit);

    document.getElementById('vaCount').innerText = vouchers.length;

    // Destroy old charts
    Object.values(vaCharts).forEach(c => c.destroy());
    vaCharts = {};

    // Donut: Debit vs Credit
    vaCharts.dc = new Chart(
        document.getElementById('vaDebitCreditChart'),
        {
            type: 'doughnut',
            data: {
                labels: ['Debit', 'Credit'],
                datasets: [{
                    data: [totalDebit, totalCredit]
                }]
            }
        }
    );

    // Bar: Voucher types
    vaCharts.type = new Chart(
        document.getElementById('vaTypeChart'),
        {
            type: 'bar',
            data: {
                labels: Object.keys(typeMap),
                datasets: [{
                    data: Object.values(typeMap)
                }]
            }
        }
    );

    // Line: Daily movement
    const dates = Object.keys(dailyMap).sort();
    vaCharts.daily = new Chart(
        document.getElementById('vaDailyChart'),
        {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    data: dates.map(d => dailyMap[d])
                }]
            }
        }
    );
}


// ------------------------------
// Change Breadcrumb Title
// ------------------------------
function changeTitle(data) {
    const titleTag = document.querySelector("#table-title");
    const breadcrumb = document.querySelector("#tally-breadcrumb .breadcrumb");

    breadcrumb.innerHTML = "";

    switch (data.route) {
        case "home":
            titleTag.dataset.master = "";
            titleTag.dataset.ledger = "";
            titleTag.dataset.back = "home";
            titleTag.dataset.path = "home";

            breadcrumb.innerHTML += `<li class="breadcrumb-item active">Masters</li>`;
            break;

        case "master":
            titleTag.dataset.master = data.master;
            titleTag.dataset.back = "home";
            titleTag.dataset.path = "master";

            breadcrumb.innerHTML += `
                <li class="breadcrumb-item"><a ondblclick="goHome()">Master</a></li>
                <li class="breadcrumb-item active">${data.master}</li>
            `;
            break;

        case "ledger":
            titleTag.dataset.ledger = data.ledger;
            titleTag.dataset.back = "master";
            titleTag.dataset.path = "ledger";

            breadcrumb.innerHTML += `
                <li class="breadcrumb-item"><a ondblclick="goHome()">Master</a></li>
                <li class="breadcrumb-item"><a ondblclick="loadLedgerByMaster('${titleTag.dataset.master}')">${titleTag.dataset.master}</a></li>
                <li class="breadcrumb-item active">${data.ledger}</li>
            `;
            break;
    }
}

// ------------------------------
// Navigation
// ------------------------------
function goBack() {
    document.querySelector("#table-loader").classList.remove('d-none');
    const tagData = document.querySelector("#table-title").dataset;
    document.getElementById("voucherAnalytics").classList.add("d-none")
    if (tagData.back === "home" && tagData.path !== "home") {
        goHome();
    } else if (tagData.back === "master") {
        loadLedgerByMaster(tagData.master);
    }
    document.querySelector("#table-loader").classList.add('d-none');
}

function goHome() {
    document.getElementById("voucherAnalytics").classList.add("d-none")
    loadHome();
}

// ------------------------------
// Amount Formatter
// ------------------------------
function displayAmount(Amount) {
    if (!Amount) return Amount;

    let negative = Amount.toString().includes("-") ? "-" : "";
    Amount = Amount.toString().replace("-", "");

    Amount = parseFloat(Amount).toFixed(2);

    let [rs, ps] = Amount.split(".");
    let result = "";
    let rev = rs.split("").reverse();

    for (let i = 0; i < rev.length; i++) {
        if (i === 3 || (i > 3 && (i - 1) % 2 === 0)) result = "," + result;
        result = rev[i] + result;
    }

    return `${negative}${result}.${ps}`;
}

// ------------------------------
// Search Filters
// ------------------------------
document.addEventListener("keyup", e => {
    const id = e.target.id;
    const value = e.target.value?.trim()?.toLowerCase();

    if (!value) {
        // show all rows when empty
        document.querySelectorAll("tbody tr").forEach(tr => tr.style.display = "");
        return;
    }

    if (id === "LedgerGroupSearch") filterRows(".LedgerGroup", value, true);
    if (id === "LedgerNameSearch") filterRows(".LedgerName", value);
    if (id === "LedgerTransactionDateSearch") filterRows(".LedgerTransactionDate", value);
    if (id === "LedgerTransactionTypeSearch") filterRows(".LedgerTransactionType", value);
});

function filterRows(selector, search, alsoShowParent = false) {
    const words = search.split(" ");

    document.querySelectorAll(selector).forEach(cell => {
        const text = cell.innerText.toLowerCase();
        const match = words.every(w => text.includes(w));

        if (match) {
            cell.parentElement.style.display = "";
            if (alsoShowParent) {
                // document.querySelector(`tr[data-parent="${cell.parentElement.dataset.parent}"]`)?.style.display = "";
                console.log(document.querySelector(`tr[data-parent="${cell.closest("tr")?.dataset.parent ?? ""}"]`),"sss");

                // document.querySelector(`tr[data-parent="${cell.closest("tr")?.dataset.parent ?? ""}"]`)?.style.display = "";
            }
        } else {
            // cell.parentElement.style.display = "none";
        }
    });
}
