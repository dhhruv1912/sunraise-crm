function loadStocks() {
    const stocks = JSON.parse(localStorage.getItem("STOCKITEM") || "[]");
    const select = document.getElementById("stockSelect");

    select.innerHTML = `<option value="">Select Item</option>`;

    stocks.forEach(s => {
        const name = s['@attributes'].NAME;
        select.innerHTML += `<option value="${name}">${name}</option>`;
    });
}

function showLoaders() {
    document.getElementById("stockWidgetsLoader").classList.remove('d-none')
    document.getElementById("stockChartLoader").classList.remove('d-none')
    document.getElementById("stockMonthlyTableLoader").classList.remove('d-none')
}
async function fetchStockVouchers(stock) {
    const res = await fetch(`/tally/data/stock_voucher?stock=${encodeURIComponent(stock)}`);
    const json = await res.json();
    return json.data || [];
}
function buildMonthlyStockReport(vouchers, year) {
    const monthly = {};

    vouchers.forEach(v => {
        if (!v.date?.endsWith(year)) return;

        const month1 = v.date.substring(0, 7);
        const month = v.date.split("-")[1]
        if (!monthly[month]) {
            monthly[month] = {
                inQty: 0,
                outQty: 0,
                inAmt: 0,
                outAmt: 0,
                closingQty: 0
            };
        }

        monthly[month].inQty += parseFloat(v.in?.quentity || 0);
        monthly[month].outQty += parseFloat(v.out?.quentity || 0);
        monthly[month].inAmt += parseFloat(v.in?.amount || 0);
        monthly[month].outAmt += parseFloat(v.out?.amount || 0);
        monthly[month].closingQty = parseFloat(v.closing?.quentity || monthly[month].closingQty);
    });

    return monthly;
}
function renderWidgets(data) {
    let inQty = 0, outQty = 0, inAmt = 0, outAmt = 0;

    Object.values(data).forEach(m => {
        inQty += m.inQty;
        outQty += m.outQty;
        inAmt += m.inAmt;
        outAmt += m.outAmt;
    });

    document.getElementById("stockWidgets").innerHTML = `
        <div class="col-md-3">
            <div class="crm-stat text-success">
                <div class="small">Total In Qty</div>
                <div class="fs-5 fw-bold">${inQty}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="crm-stat text-danger">
                <div class="small">Total Out Qty</div>
                <div class="fs-5 fw-bold">${outQty}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="crm-stat">
                <div class="small">In Value</div>
                <div class="fs-5 fw-bold">₹ ${displayAmount(inAmt)}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="crm-stat">
                <div class="small">Out Value</div>
                <div class="fs-5 fw-bold">₹ ${displayAmount(outAmt)}</div>
            </div>
        </div>
    `;
    document.getElementById("stockWidgetsLoader").classList.add('d-none')
}
function renderChart(data) {
    const labels = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
    ];
    // console.log(label,data);
    
    // const labels = Object.keys(data);
    const inQty = labels.map(m => data[m]?.inQty);
    const outQty = labels.map(m => data[m]?.outQty);
    document.getElementById("stockChart").parentElement.innerHTML = `<canvas id="stockChart" height="90"></canvas><div class="crm-loader-overlay d-none" id="stockChartLoader">
            <div class="crm-spinner"></div>
        </div>`

    stockChart = new Chart(
        document.getElementById("stockChart"), {
        type: "bar",
        data: {
            labels,
            datasets: [
                { label: "In Qty", data: inQty },
                { label: "Out Qty", data: outQty }
            ]
        }
    }
    );
}
function renderTable(data) {
    const tbody = document.getElementById("stockMonthlyTable");
    tbody.innerHTML = "";

    Object.entries(data).forEach(([m, r]) => {
        tbody.innerHTML += `
            <tr>
                <td>${m}</td>
                <td class="text-end">${r.inQty}</td>
                <td class="text-end">${r.outQty}</td>
                <td class="text-end">${displayAmount(r.inAmt)}</td>
                <td class="text-end">${displayAmount(r.outAmt)}</td>
                <td class="text-end">${r.closingQty}</td>
            </tr>
        `;
    });
    document.getElementById("stockMonthlyTableLoader").classList.add('d-none')
}
async function generateReport() {
    showLoaders()
    const stock = stockSelect.value;
    const year = yearSelect.value;
    if (!stock || !year) return;

    const vouchers = await fetchStockVouchers(stock);
    const monthly = buildMonthlyStockReport(vouchers, year);

    renderWidgets(monthly);
    renderChart(monthly);
    renderTable(monthly);
}
document.addEventListener("DOMContentLoaded", () => {
    loadStocks();
    // populateYearSelect();
});

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

function reloadData() {
    showLoaders()
    loadStocks()
    document.getElementById("yearSelect").value = ""
    document.getElementById("stockWidgetsLoader").classList.add("d-none")
    document.getElementById("stockChartLoader").classList.add("d-none")
    document.getElementById("stockMonthlyTableLoader").classList.add("d-none")
    document.getElementById("stockWidgets").innerHTML = ""
    document.getElementById("stockChart").parentElement.innerHTML = `<canvas id="stockChart" height="90"></canvas>
        <div class="crm-loader-overlay d-none" id="stockChartLoader">
            <div class="crm-spinner"></div>
        </div>`
    document.getElementById("stockMonthlyTable").innerHTML = ""
}