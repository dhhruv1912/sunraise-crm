
let tallydatatable = null;

// ------------------------------
// Document Ready
// ------------------------------
document.addEventListener("DOMContentLoaded", () => {
    loadHome();

    // ESC key handler
    document.addEventListener("keyup", e => {
        if (e.key === "Escape") goBack();
    });
});

// ------------------------------
// Reload
// ------------------------------
function reload(){
    localStorage.removeItem('STOCKITEM');
    loadHome();
}

// ------------------------------
// Load Home
// ------------------------------
function loadHome(){
    document.querySelector("#table-loader").style.display = "block";

    let stored = localStorage.getItem("STOCKITEM");

    const thead = document.querySelector("#tally-datatable thead");
    thead.innerHTML = `
        <tr class="table-secondary">
            <th rowspan="2">Particulars<br>
                <input id="GroupsSearch" class="form-control form-control-sm" type="text">
            </th>
            <th>Value</th>
        </tr>
    `;

    if (stored) {
        process_stocks(JSON.parse(stored));
    } else {
        get_stocks();
    }

    document.querySelector("#table-loader").style.display = "none";
}

// ------------------------------
// GET stocks via fetch()
// ------------------------------
async function get_stocks(){
    document.querySelector("#table-loader").style.display = "block";

    const res = await fetch(`/tally/data/stocks`);
    const response = await res.json();

    let stocks = response?.data?.BODY?.DATA?.COLLECTION?.STOCKITEM;

    if (stocks) {
        localStorage.setItem("STOCKITEM", JSON.stringify(stocks));
        process_stocks(stocks);
    }

    document.querySelector("#table-loader").style.display = "none";
}

// ------------------------------
// Format Stock Data
// ------------------------------
function fotmate_data(DATA){
    const RESULT = {};
    const groups = [];

    DATA.forEach(stock => {
        const name = stock['@attributes'].NAME;
        const parent = stock.PARENT;

        let closingStock = typeof stock.CLOSINGBALANCE === "string" ? stock.CLOSINGBALANCE : "0";
        let openingStock = typeof stock.OPENINGBALANCE === "string" ? stock.OPENINGBALANCE : "0";
        let cost = typeof stock.STANDARDCOST === "string" ? stock.STANDARDCOST : "0";
        let price = typeof stock.STANDARDPRICE === "string" ? stock.STANDARDPRICE : "0";
        let closingvalue = typeof stock.CLOSINGVALUE === "string" ? stock.CLOSINGVALUE : "0";
        let openingvalue = typeof stock.OPENINGVALUE === "string" ? stock.OPENINGVALUE : "0";

        if (!groups.includes(parent)){
            groups.push(parent);
            RESULT[parent] = [];
        }

        RESULT[parent].push({
            name,
            closingStock,
            openingStock,
            cost,
            price,
            closingvalue,
            openingvalue
        });
    });

    return RESULT;
}

// ------------------------------
// Render Stock Groups
// ------------------------------
function process_stocks(DATA){
    const STOCKS = fotmate_data(DATA);
    const table = document.querySelector("#tally-datatable tbody");
    table.innerHTML = "";

    let ALL_STOCK = 0;
    let ALL_VALUE = 0;

    Object.entries(STOCKS).forEach(([GROUP, ITEMS]) => {
        const totalValue = ITEMS.reduce((sum, s) => sum + parseFloat(s.closingvalue), 0);

        ALL_STOCK += ITEMS.length;
        ALL_VALUE += totalValue;

        const tr = document.createElement("tr");
        tr.ondblclick = () => loadStockByGroup(GROUP);

        tr.innerHTML = `
            <td class="StockByGroup">${GROUP}</td>
            <td>${displayAmount(totalValue).replace("-", "")}</td>
        `;

        table.appendChild(tr);
    });

    // Add ALL row
    const tr = document.createElement("tr");
    tr.ondblclick = () => loadStockByGroup("All");
    tr.innerHTML = `
        <td class="StockByGroup">All</td>
        <td>${displayAmount(ALL_VALUE)}</td>
    `;
    table.appendChild(tr);

    changeTitle({ route: "home" });
}

// ------------------------------
// Load Stock By Group
// ------------------------------
function loadStockByGroup(GROUP){
    changeTitle({ route: "master", master: GROUP });

    let stored = localStorage.getItem("STOCKITEM");
    if (!stored) return;

    const LEDGERS = fotmate_data(JSON.parse(stored));

    const table = document.querySelector("#tally-datatable tbody");
    const thead = document.querySelector("#tally-datatable thead");

    table.innerHTML = "";
    thead.innerHTML = `
        <tr class="table-secondary">
            <th>Particulars<br><input id="StocksSearch" class="form-control form-control-sm"></th>
            <th>Quantity</th>
            <th>Cost</th>
            <th>Price</th>
            <th>Value</th>
        </tr>
    `;

    document.querySelector("#table-loader").style.display = "block";

    if (GROUP === "All"){
        Object.values(LEDGERS).forEach(items => {
            items.forEach(stock => {
                appendStockRow(stock, table);
            });
        });
    } else {
        LEDGERS[GROUP]?.forEach(stock => {
            appendStockRow(stock, table);
        });
    }

    document.querySelector("#table-loader").style.display = "none";
}

function appendStockRow(stock, table){
    const tr = document.createElement("tr");
    tr.ondblclick = () => loadVoucherOfStock(
        encodeURIComponent(stock.name),
        encodeURIComponent(JSON.stringify(stock))
    );

    tr.innerHTML = `
        <td class="StocksItem">${stock.name}</td>
        <td>${stock.closingStock}</td>
        <td>${stock.cost}</td>
        <td>${stock.price}</td>
        <td>${displayAmount(stock.closingvalue)}</td>
    `;

    table.appendChild(tr);
}

// ------------------------------
// Load Voucher of Stock
// ------------------------------
async function loadVoucherOfStock(NAME, OPENING){
    console.log("NAME",NAME);
    console.log("OPENING",OPENING);

    NAME = decodeURIComponent(NAME);
    OPENING = JSON.parse(decodeURIComponent(OPENING));
    console.log("NAME",NAME);

    console.log("OPENING",OPENING);

    changeTitle({ route : "ledger", ledger: NAME });

    document.querySelector("#table-loader").style.display = "block";

    const res = await fetch(`/tally/data/stock_voucher?stock=${encodeURIComponent(NAME)}`);
    const response = await res.json();
    const vouchers = response.data;

    const table = document.querySelector("#tally-datatable tbody");
    const thead = document.querySelector("#tally-datatable thead");

    table.innerHTML = "";
    thead.innerHTML = `
        <tr class="table-secondary">
            <th rowspan="2" class="text-center">Date</th>
            <th rowspan="2" class="text-center">Type</th>
            <th rowspan="2" class="text-center">Account</th>
            <th colspan="2" class="text-center">Inwards</th>
            <th colspan="2" class="text-center">Outwards</th>
            <th colspan="2" class="text-center">Closing</th>
        </tr>
        <tr class="table-secondary">
            <th>QTY</th><th>Amount</th>
            <th>QTY</th><th>Amount</th>
            <th>QTY</th><th>Amount</th>
        </tr>
    `;

    // Opening row
    const opening_amount = displayAmount(OPENING.openingvalue);
    const opening_amount_fmt = (parseFloat(opening_amount) > 0 ? "-" : "") + opening_amount;

    table.innerHTML += `
        <tr>
            <td></td>
            <td>Opening Balance</td>
            <td></td>
            <td>${OPENING.openingStock}</td>
            <td>${opening_amount_fmt}</td>
            <td></td><td></td>
            <td>${OPENING.openingStock}</td>
            <td>${opening_amount_fmt}</td>
        </tr>
        <tr><td colspan="9"></td></tr>
    `;

    let totalInQty = parseFloat(OPENING.openingStock);
    let totalInAmt = parseFloat(OPENING.openingvalue);
    let totalOutQty = 0;
    let totalOutAmt = 0;

    vouchers.forEach(v => {
        const tr = document.createElement("tr");

        const inAmt = displayAmount(v.in.amount);
        const outAmt = displayAmount(v.out.amount);
        const closeAmt = displayAmount(v.closing.amount);

        if (v.in.quentity) {
            totalInQty += parseFloat(v.in.quentity);
            totalInAmt += parseFloat(v.in.amount);
        }

        if (v.out.quentity) {
            totalOutQty += parseFloat(v.out.quentity);
            totalOutAmt += parseFloat(v.out.amount);
        }

        tr.innerHTML = `
            <td>${v.date}</td>
            <td>${v.account}</td>
            <td>${v.type}</td>
            <td>${v.in.quentity}</td>
            <td>${inAmt}</td>
            <td>${v.out.quentity}</td>
            <td>${outAmt}</td>
            <td>${v.closing.quentity}</td>
            <td>${closeAmt}</td>
        `;
        table.appendChild(tr);
    });

    // Grand total
    table.innerHTML += `
        <tr><td colspan="9"></td></tr>
        <tr>
            <td></td><td>Grand Total</td><td></td>
            <td>${totalInQty}</td>
            <td>${displayAmount(totalInAmt)}</td>
            <td>${totalOutQty}</td>
            <td>${displayAmount(totalOutAmt)}</td>
            <td>${OPENING.closingStock}</td>
            <td>${displayAmount(OPENING.closingvalue)}</td>
        </tr>
    `;

    document.querySelector("#table-loader").style.display = "none";
}

// ------------------------------
// Title / Breadcrumb Manager
// ------------------------------
function changeTitle(data){
    const title = document.querySelector("#table-title");
    const breadcrumb = document.querySelector("#tally-breadcrumb .breadcrumb");

    const tagData = title.dataset;
    breadcrumb.innerHTML = "";

    if (data.route === "home"){
        title.dataset.master = "";
        title.dataset.ledger = "";
        title.dataset.back = "home";
        title.dataset.path = "home";

        breadcrumb.innerHTML = `<li class="breadcrumb-item active">Groups</li>`;
    }

    if (data.route === "master"){
        title.dataset.master = data.master;
        title.dataset.back = "home";
        title.dataset.path = "master";

        breadcrumb.innerHTML = `
            <li class="breadcrumb-item"><a ondblclick="goHome()">Stock</a></li>
            <li class="breadcrumb-item active">${data.master}</li>
        `;
    }

    if (data.route === "ledger"){
        title.dataset.ledger = data.ledger;
        title.dataset.back = "master";
        title.dataset.path = "ledger";

        breadcrumb.innerHTML = `
            <li class="breadcrumb-item"><a ondblclick="goHome()">Group</a></li>
            <li class="breadcrumb-item">
                <a ondblclick="loadStockByGroup('${title.dataset.master}')">
                    ${title.dataset.master}
                </a>
            </li>
            <li class="breadcrumb-item active">${data.ledger}</li>
        `;
    }
}

// ------------------------------
// Navigation
// ------------------------------
function goBack(){
    document.querySelector("#table-loader").style.display = "block";

    const tags = document.querySelector("#table-title").dataset;

    if (tags.back === "home" && tags.path !== "home"){
        goHome();
    } else if (tags.back === "master"){
        loadStockByGroup(tags.master);
    }

    document.querySelector("#table-loader").style.display = "none";
}

function goHome(){
    loadHome();
}

// ------------------------------
// Display Amount
// ------------------------------
function displayAmount(Amount){
    if (!Amount) return Amount?.toString() ?? "";

    let negative = Amount.toString().includes("-") ? "-" : "";
    Amount = Amount.toString().replace("-", "");

    Amount = parseFloat(Amount).toFixed(2);
    let [rs, ps] = Amount.split(".");

    let out = "";
    let rev = rs.split("").reverse();

    for (let i = 0; i < rev.length; i++){
        if (i === 3 || (i > 3 && (i - 1) % 2 === 0)){
            out = "," + out;
        }
        out = rev[i] + out;
    }

    return `${negative}${out}.${ps}`;
}

// ------------------------------
// Search Filter: GROUPS
// ------------------------------
document.addEventListener("keyup", e => {
    if (e.target.id === "GroupsSearch"){
        const search = e.target.value.toLowerCase().trim();

        document.querySelectorAll(".StockByGroup").forEach(cell => {
            const txt = cell.innerText.toLowerCase();
            cell.parentElement.style.display =
                txt.includes(search) ? "" : "none";
        });
    }

    if (e.target.id === "StocksSearch"){
        const search = e.target.value.toLowerCase().trim();

        document.querySelectorAll(".StocksItem").forEach(cell => {
            const txt = cell.innerText.toLowerCase();
            cell.parentElement.style.display =
                txt.includes(search) ? "" : "none";
        });
    }
});
