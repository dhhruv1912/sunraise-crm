function loadLedgers() {
    const ledgers = JSON.parse(localStorage.getItem("ledgers") || "[]");
    const select = document.getElementById("ledgerSelect");

    select.innerHTML = `<option value="">Select Ledger</option>`;

    ledgers.forEach(l => {
        const name = l['@attributes'].NAME;
        select.innerHTML += `<option value="${name}">${name}</option>`;
    });
}

function showLoaders() {
    document.getElementById("monthlyWidgetsLoader").classList.remove('d-none')
    document.getElementById("monthlyChartLoader").classList.remove('d-none')
    document.getElementById("monthlyTableLoader").classList.remove('d-none')
}

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
    loadLedgers()
    document.getElementById("yearSelect").value = ""
    document.getElementById("monthlyWidgetsLoader").classList.add("d-none")
    document.getElementById("monthlyChartLoader").classList.add("d-none")
    document.getElementById("monthlyTableLoader").classList.add("d-none")
    document.getElementById("monthlyWidgets").innerHTML = ""
    document.getElementById("monthlyChart").parentElement.innerHTML = `<canvas id="monthlyChart" height="90"></canvas><div class="crm-loader-overlay d-none" id="monthlyChartLoader">
            <div class="crm-spinner"></div>
        </div>`
    document.getElementById("monthlyTable").innerHTML = ""
}
loadLedgers()
async function fetchLedgerVouchers(ledger) {
    const res = await fetch(`/tally/data/ledger_voucher?ledger=${encodeURIComponent(ledger)}`);
    const json = await res.json();
    return json.data || [];
}
function buildMonthlyReport(vouchers, year) {
    const monthly = {};
    const data = []
    vouchers.forEach(v => {
        
        if (!v.date?.endsWith(year)) return;

        const month = v.date.split('-')[1];
        
        v.credit = (typeof v.credit === 'number' || typeof v.credit === 'string') && !isNaN(v.credit) ? v.credit : 0
        v.debit = (typeof v.debit === 'number' || typeof v.debit === 'string') && !isNaN(v.debit) ? v.debit : 0
        if (!monthly[month]) {
            monthly[month] = {
                debit: 0,
                credit: 0,
                count: 0,
                data : {}
            };
        }
        
        monthly[month].debit += Math.abs(parseFloat(v.debit || 0));
        monthly[month].credit += Math.abs(parseFloat(v.credit || 0));
        monthly[month].count++;
        data.push(v)
    });
    
    Object.keys(monthly).forEach(m => {
        monthly[m].net = monthly[m].credit - monthly[m].debit;
    });
    
    monthly['data'] = data;
    return monthly;
}
function renderWidgets(data) {
    let totalDebit = 0, totalCredit = 0, totalCount = 0;

    Object.keys(data).forEach((m) => {
        if (m != "data") {
            ms = data[m]
            totalDebit += ms.debit;
            totalCredit += ms.credit;
            totalCount += ms.count;
        }
    });

    document.getElementById("monthlyWidgets").innerHTML = `
        <div class="col-md-3">
            <div class="crm-stat text-danger">
                <div class="small">Total Debit</div>
                <div class="fs-5 fw-bold">₹ ${displayAmount(totalDebit)}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="crm-stat text-success">
                <div class="small">Total Credit</div>
                <div class="fs-5 fw-bold">₹ ${displayAmount(totalCredit)}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="crm-stat">
                <div class="small">Net Position</div>
                <div class="fs-5 fw-bold">
                    ₹ ${displayAmount(totalCredit - totalDebit)}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="crm-stat">
                <div class="small">Total Vouchers</div>
                <div class="fs-5 fw-bold">${totalCount}</div>
            </div>
        </div>
    `;
    document.getElementById("monthlyWidgetsLoader").classList.add('d-none')
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
    // const labels = Object.keys(data);
    
    const debit = labels.map(m => data[m]?.debit);
    const credit = labels.map(m => data[m]?.credit);
    vaCharts = {};
    document.getElementById("monthlyChart").parentElement.innerHTML = `<canvas id="monthlyChart" height="90"></canvas><div class="crm-loader-overlay d-none" id="monthlyChartLoader">
            <div class="crm-spinner"></div>
        </div>`
    if (vaCharts.monthlyChart){
        vaCharts.monthlyChart.destroy();
    }

    vaCharts.monthlyChart = new Chart(
        document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    { label: 'Debit', data: debit, borderWidth: 2 },
                    { label: 'Credit', data: credit, borderWidth: 2 }
                ]
            }
        }
    );

    document.getElementById("monthlyChartLoader").classList.add('d-none')
}
function renderTable(data) {
    const tbody = document.getElementById("monthlyTable");
    tbody.innerHTML = "";

    Object.entries(data.data).forEach(([m, r]) => {
        tbody.innerHTML += `
            <tr>
                <td>${r.date}</td>
                <td class="text-end">${r.account}</td>
                <td class="text-end">${r.type}</td>
                <td class="text-end">${displayAmount(r.debit)}</td>
                <td class="text-end">${displayAmount(r.credit)}</td>
            </tr>
        `;
    });
    document.getElementById("monthlyTableLoader").classList.add('d-none')
}
async function generateReport() {
    showLoaders()
    const ledger = ledgerSelect.value;
    const year = yearSelect.value;

    if (!ledger || !year) return;

    const vouchers = await fetchLedgerVouchers(ledger);
    const monthly = buildMonthlyReport(vouchers, year);
    

    renderWidgets(monthly);
    renderChart(monthly);
    renderTable(monthly);
}
