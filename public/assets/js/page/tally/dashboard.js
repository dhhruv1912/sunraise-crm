document.addEventListener('DOMContentLoaded', () => {
    loadTallyDashboard();
});

/* ================= ENTRY ================= */

async function loadTallyDashboard() {
    await renderKpis();
    // await buildMonthlyCashflow();
    renderStockSnapshot();
    renderRiskSignals();


    await loadBalanceSheet();

    await loadTrialBalance();
}

async function buildMonthlyCashflow() {
    const LoadedLedger = JSON.parse(localStorage.getItem("ledgers") || "[]");
    const cached = localStorage.getItem('tally_monthly_cashflow');
    
    // if (cached) {
    //     renderCashFlowChart(JSON.parse(cached));
    //     return;
    // }
    const ledgers = []
    LoadedLedger.forEach(l => {
        const name = l['@attributes'].NAME;
        if (l['PARENT'] == "Bank Accounts") {
            ledgers.push(name)
        }
        if (l['PARENT'] == "Cash-in-Hand") {
            ledgers.push(name)
        }
        
    }); // configurable later , 'BANK'
    let monthly = {};
// for (const ledger of ledgers) {
//         const res = await fetch(
//             `/tally/data/single-cashflow?ledger=${encodeURIComponent(ledger)}`
//         );
//         const json = await res.json();
//         data = json?.data?.BODY?.DATA?.TALLYMESSAGE?.LEDGER;
//         const name = data['@attributes'].NAME;
//         const parent = data?.PARENT;
//         const balance = data?.CLOSINGBALANCE['@attributes'].BV;

        
    for (const ledger of ledgers) {
        const res = await fetch(
            `/tally/data/single-cashflow?ledger=${encodeURIComponent(ledger)}`
        );
        const json = await res.json();
        const res1 = await fetch(
            `/tally/data/cashflow?ledger=${encodeURIComponent(ledger)}`
        );
        const json1 = await res1.json();
        console.log(json);
        console.log(json1);
        
        (json.data || []).forEach(v => {
            const month1 = normalizeMonth(v.date);
            const month = v.date.split("-")[1]
            if (!monthly[month]) {
                monthly[month] = { debit: 0, credit: 0 };
            }
            
            v.credit = (typeof v.credit === 'number' || typeof v.credit === 'string') && !isNaN(v.credit) ? v.credit : 0
            v.debit = (typeof v.debit === 'number' || typeof v.debit === 'string') && !isNaN(v.debit) ? v.debit : 0
            if (v.debit) {
                monthly[month].debit += parseFloat(v.debit.replace('-', '')) || 0;
            }
            if (v.credit) {
                monthly[month].credit += parseFloat(v.credit.replace('-', '')) || 0;
            }
        });
    }

    localStorage.setItem(
        'tally_monthly_cashflow',
        JSON.stringify(monthly)
    );

    renderCashFlowChart(monthly);
}
function renderCashFlowChart(monthlyData) {
    const labels = Object.keys(monthlyData).sort();
    const debit = labels.map(m => monthlyData[m].debit);
    const credit = labels.map(m => monthlyData[m].credit);

    const ctx = document.getElementById('cashFlowChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Debit',
                    data: debit,
                    backgroundColor: 'rgba(220,53,69,.6)'
                },
                {
                    label: 'Credit',
                    data: credit,
                    backgroundColor: 'rgba(25,135,84,.6)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: ctx =>
                            `${ctx.dataset.label}: ₹ ${format(ctx.raw)}`
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: v => '₹ ' + format(v)
                    }
                }
            }
        }
    });
}
function normalizeMonth(dateStr) {
    // supports: DD-MM-YYYY / YYYY-MM-DD
    let d;

    if (dateStr.includes('-') && dateStr.split('-')[0].length === 2) {
        const [dd, mm, yyyy] = dateStr.split('-');
        d = `${yyyy}-${mm}`;
    } else {
        d = dateStr.substring(0, 7);
    }

    return d;
}
function reloadTallyCache() {
    localStorage.removeItem('ledgers');
    localStorage.removeItem('STOCKITEM');
    localStorage.removeItem('tally_monthly_cashflow');
    location.reload();
}

/* ================= KPIS ================= */

async function renderKpis() {
    const wrap = document.getElementById('tallyKpis');
    let ledgers = JSON.parse(localStorage.getItem('ledgers') || null);
    
    let stocks  = JSON.parse(localStorage.getItem('STOCKITEM') || null);
    if (!ledgers) {
        const res = await fetch(`/tally/data/ledger`);
        const response = await res.json();

        ledgers = response?.data?.BODY?.DATA?.COLLECTION?.LEDGER;

        if (ledgers) {
            localStorage.setItem("ledgers", JSON.stringify(ledgers));
        }
    }
    if (!stocks) {
        const res = await fetch(`/tally/data/stocks`);
        const response = await res.json();

        stocks = response?.data?.BODY?.DATA?.COLLECTION?.STOCKITEM;

        if (stocks) {
            localStorage.setItem("STOCKITEM", JSON.stringify(stocks));
        }
    }
    
    let stockValue = 0;
    stocks.forEach(s => {
        stockValue += parseFloat(s.CLOSINGVALUE || 0);
    });

    wrap.innerHTML = `
        <div class="col-md-3">
            <div class="crm-stat text-primary">
                <div class="small">Ledgers</div>
                <div class="fs-4 fw-bold">${ledgers.length}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="crm-stat text-success">
                <div class="small">Stock Items</div>
                <div class="fs-4 fw-bold">${stocks.length}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="crm-stat text-warning">
                <div class="small">Stock Value</div>
                <div class="fs-4 fw-bold">₹ ${format(stockValue)}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="crm-stat text-info">
                <div class="small">Cache Status</div>
                <div class="fs-6 fw-semibold">
                    ${ledgers.length && stocks.length ? 'Loaded' : 'Not Loaded'}
                </div>
            </div>
        </div>
    `;
}

/* ================= STOCK SNAPSHOT ================= */

function renderStockSnapshot() {
    const wrap = document.getElementById('stockSnapshot');
    const stocks = JSON.parse(localStorage.getItem('STOCKITEM') || '[]');

    let dead = stocks.filter(s => parseFloat(s.CLOSINGBALANCE) === 0).length;

    wrap.innerHTML = `
        <div class="col-6">
            <div class="crm-stat">
                <div class="small">Active Items</div>
                <div class="fw-bold">${stocks.length - dead}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="crm-stat text-danger">
                <div class="small">Dead Stock</div>
                <div class="fw-bold">${dead}</div>
            </div>
        </div>
    `;
}

/* ================= RISK ================= */

function renderRiskSignals() {
    const wrap = document.getElementById('riskList');
    const ledgers = JSON.parse(localStorage.getItem('ledgers') || '[]');

    let risks = [];

    ledgers.forEach(l => {
        let bal = parseFloat(l.CLOSINGBALANCE || 0);
        if (bal < 0) {
            risks.push(`Negative balance: ${l['@attributes'].NAME}`);
        }
    });

    if (!risks.length) {
        wrap.innerHTML = `<li class="text-muted">No risks detected</li>`;
        return;
    }

    wrap.innerHTML = risks
        .slice(0, 5)
        .map(r => `<li>${r}</li>`)
        .join('');
}

function reloadTallyCache() {
    localStorage.removeItem('ledgers');
    localStorage.removeItem('STOCKITEM');
    location.reload();
}

function format(num) {
    return Number(num || 0).toLocaleString('en-IN');
}

async function loadBalanceSheet(force = false) {
    const widget = document.getElementById('balanceSheetWidget');
    const loader = widget.querySelector('.bs-loader');
    const container = document.getElementById('balanceSheetList');
    const containerLeft = document.getElementById('balanceSheetListLeft');
    const containerRight = document.getElementById('balanceSheetListRight');

    loader.classList.remove('d-none');
    // container.innerHTML = '';
    containerLeft.innerHTML = '';
    containerRight.innerHTML = '';
    let balanceSheet
    let balanceSheetLocal = localStorage.getItem("balanceSheet");
    if (balanceSheetLocal && !force && JSON.parse(balanceSheetLocal)?.data && JSON.parse(balanceSheetLocal)?.data.length > 0) {
        balanceSheet = JSON.parse(balanceSheetLocal)
    } else {
        res = await fetch('/tally/data/balance-sheet'); // your existing endpoint
        balanceSheet = await res.json()
        localStorage.setItem("balanceSheet", JSON.stringify(balanceSheet));
    }
    
    try {
        const json = balanceSheet.data;

        renderBalanceSheet(json || []);
    } finally {
        loader.classList.add('d-none');
    }
}

function renderBalanceSheet(rows) {
    const container = document.getElementById('balanceSheetList');
    const containerLeft = document.getElementById('balanceSheetListLeft');
    const containerRight = document.getElementById('balanceSheetListRight');
    const balanceSheetTotalLeft = document.getElementById('balanceSheetTotalLeft');
    const balanceSheetTotalRight = document.getElementById('balanceSheetTotalRight');
    let balanceSheetTotalLeftAmount = 0
    let balanceSheetTotalRightAmount = 0
    rows.forEach((row, index) => {
        const hasChildren = row.children && row.children.length;

        const parent = document.createElement('div');
        parent.className = 'bs-row';

        parent.innerHTML = `
            <div class="bs-row-header" onclick="toggleBS(${index})">
                <div class="bs-name">
                    ${hasChildren ? `<i class="mdi mdi-chevron-right bs-toggle" id="icon-${index}"></i>` : ''}
                    ${row.name}
                </div>
                <div class="bs-amount ${row.amount >= 0 ? 'bs-positive' : 'bs-negative'}"
                     data-counter="${row.amount}">
                    0
                </div>
            </div>
            <div class="bs-children" id="children-${index}"></div>
        `;
        if(row.amount >= 0){
            containerLeft.appendChild(parent);
            balanceSheetTotalLeftAmount = balanceSheetTotalLeftAmount + row.amount
        }else{
            containerRight.appendChild(parent);
            balanceSheetTotalRightAmount = balanceSheetTotalRightAmount + row.amount
        }

        if (hasChildren) {
            const childBox = parent.querySelector(`#children-${index}`);

            row.children.forEach(c => {
                const child = document.createElement('div');
                child.className = 'bs-child';

                child.innerHTML = `
                    <div>${c.name}</div>
                    <div class="${c.amount >= 0 ? 'bs-positive' : 'bs-negative'}">
                        ₹ ${formatAmount(c.amount)}
                    </div>
                `;

                childBox.appendChild(child);
            });
        }
        balanceSheetTotalRight.innerHTML = balanceSheetTotalRightAmount 
        balanceSheetTotalRight.dataset.counter = balanceSheetTotalRightAmount 
        balanceSheetTotalLeft.innerHTML = balanceSheetTotalLeftAmount 
        balanceSheetTotalLeft.dataset.counter = balanceSheetTotalLeftAmount 
    });
    let diff
    if (balanceSheetTotalRightAmount > balanceSheetTotalLeftAmount) {
        diff = Math.abs(balanceSheetTotalRightAmount) - Math.abs(balanceSheetTotalLeftAmount)
        const Diffparent = document.createElement('div');
        Diffparent.className = 'bs-row';

        Diffparent.innerHTML = `
            <div class="bs-row-header">
                <div class="bs-name">
                Difference in opening balance
                </div>
                <div class="bs-amount bs-positive"
                    data-counter="${diff}">
                    0
                </div>
            </div>
        `;
        containerLeft.appendChild(Diffparent);
        balanceSheetTotalLeftAmount = Math.abs(balanceSheetTotalLeftAmount) + diff
        balanceSheetTotalLeft.innerHTML = balanceSheetTotalLeftAmount 
        balanceSheetTotalLeft.dataset.counter = balanceSheetTotalLeftAmount 
    } else if (balanceSheetTotalRightAmount < balanceSheetTotalLeftAmount){
        diff = Math.abs(balanceSheetTotalLeftAmount) - Math.abs(balanceSheetTotalRightAmount)
        const Diffparent = document.createElement('div');
        Diffparent.className = 'bs-row';

        Diffparent.innerHTML = `
            <div class="bs-row-header">
                <div class="bs-name">
                Difference in opening balance
                </div>
                <div class="bs-amount bs-negative"
                    data-counter="${diff}">
                    0
                </div>
            </div>
        `;
        containerRight.appendChild(Diffparent);
        balanceSheetTotalRightAmount = Math.abs(balanceSheetTotalRightAmount) + diff
        
        balanceSheetTotalRight.innerHTML = balanceSheetTotalRightAmount 
        balanceSheetTotalRight.dataset.counter = balanceSheetTotalRightAmount 
    }

    animateCounters();
}

function toggleBS(index) {
    const box = document.getElementById(`children-${index}`);
    const icon = document.getElementById(`icon-${index}`);

    if (!box) return;

    const open = box.style.display === 'block';
    box.style.display = open ? 'none' : 'block';
    icon.className = open
        ? 'mdi mdi-chevron-right bs-toggle'
        : 'mdi mdi-chevron-down bs-toggle';
}

/* Animated counters */
function animateCounters() {
    
    document.querySelectorAll('[data-counter]').forEach(el => {
        const target = parseFloat(el.dataset.counter);
        let current = 0;
        const step = target / 40;

        const timer = setInterval(() => {
            current += step;
            if (Math.abs(current) >= Math.abs(target)) {
                current = target;
                clearInterval(timer);
            }
            el.innerText = '₹ ' + formatAmount(current);
        }, 20);
    });
}

/* Utils */
function formatAmount(v) {
    return Math.abs(v).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function reloadBalanceSheet() {
    loadBalanceSheet(true);
}
function reloadTrialBalance() {
    loadTrialBalance(true);
}


async function loadTrialBalance(force = false) {
    const widget = document.getElementById('trialBalanceWidget');
    const loader = widget.querySelector('.tb-loader');
    const body = document.getElementById('trialBalanceBody');

    loader.classList.remove('d-none');
    body.innerHTML = '';

    try {

        let trialBalance 
        let TrialBalanceLocal = localStorage.getItem("trial_balance");
        if (TrialBalanceLocal && !force && JSON.parse(TrialBalanceLocal)?.data){
            trialBalance = JSON.parse(TrialBalanceLocal)
            trialBalance = trialBalance.data
        }else{
            const res = await fetch('/tally/data/trial-balance');
            const json = await res.json();
            localStorage.setItem("trial_balance", JSON.stringify(json));
            trialBalance = json.data
        }
        // const res = await fetch('/tally/data/trial-balance');
        // const json = await res.json();

        const names = trialBalance.DSPACCNAME || [];
        const infos = trialBalance.DSPACCINFO || [];

        let totalDebit = 0;
        let totalCredit = 0;
        let debitAc = 0;
        let creditAc = 0;
        names.forEach((n, i) => {
            const info = infos[i] || {};
            
            const name = n.DSPDISPNAME ?? '—';

            const debit =
                parseFloat(info?.DSPCLDRAMT?.DSPCLDRAMTA || 0) || 0;

            const credit =
                parseFloat(info?.DSPCLCRAMT?.DSPCLCRAMTA || 0) || 0;

            if (!debit && !credit) return;
            const tr = document.createElement('tr');
            if (debitAc == 0 && creditAc == 0) {
                tr.classList.add('bg-info-subtle')
                totalDebit += Math.abs(debit);
                totalCredit += Math.abs(credit);
            }
            tr.innerHTML = `
                <td class="tb-name ${debitAc == 0 && creditAc == 0 ? "fw-semibold" : ""}">${name}</td>
                <td class="text-end text-danger">
                    ${debit ? formatAmount2(Math.abs(debit)) : ''}
                </td>
                <td class="text-end text-success">
                    ${credit ? formatAmount2(Math.abs(credit)) : ''}
                </td>
            `;
            body.appendChild(tr);
            if(debitAc == 0 && creditAc == 0){
                debitAc = Math.abs(debit)
                creditAc = Math.abs(credit)
            }else{
                if(debit){
                    debitAc = (Math.abs(debitAc) - Math.abs(debit)).toFixed(2)
                }
                if(credit){
                    creditAc = (Math.abs(creditAc) - Math.abs(credit)).toFixed(2)
                }
            }
            
        });
        let diff
        let prof = 0
        if (totalDebit > totalCredit) {
            prof = 1
            diff = Math.abs(totalDebit) - Math.abs(totalCredit)
        }else if(totalDebit < totalCredit){
            prof = -1
            diff = Math.abs(totalCredit) - Math.abs(totalDebit)
        }
        const tr = document.createElement('tr');
        // if (debitAc == 0 && creditAc == 0) {
            tr.classList.add('bg-info-subtle')
        //     totalDebit += Math.abs(debit);
        //     totalCredit += Math.abs(credit);
        // }
        tr.innerHTML = `
            <td class="tb-name ${debitAc == 0 && creditAc == 0 ? "fw-semibold" : ""}">Difference in opening balance</td>
            <td class="text-end text-danger">
                ${prof < 0 ?  formatAmount2(Math.abs(diff)) : ''}
            </td>
            <td class="text-end text-success">
                ${prof > 0 ? formatAmount2(Math.abs(diff)) : ''}
            </td>
        `;
        body.appendChild(tr);
        animateTotal('tbTotalDebit', totalDebit);
        animateTotal('tbTotalCredit', totalCredit);

    } finally {
        loader.classList.add('d-none');
    }
}

/* Counter animation */
function animateTotal(id, value) {
    const el = document.getElementById(id);
    let current = 0;
    const step = value / 40;

    const timer = setInterval(() => {
        current += step;
        if (current >= value) {
            current = value;
            clearInterval(timer);
        }
        el.innerText = formatAmount2(current);
    }, 20);
}

/* Search */
document.addEventListener('keyup', e => {
    if (e.target.id !== 'tbSearch') return;

    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('#trialBalanceBody tr').forEach(tr => {
        const name = tr.querySelector('.tb-name').innerText.toLowerCase();
        tr.style.display = name.includes(q) ? '' : 'none';
    });
});

/* Amount formatter */
function formatAmount2(v) {
    return '₹ ' + Number(v).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
