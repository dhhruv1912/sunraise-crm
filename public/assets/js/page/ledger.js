
    // $(function () {
    //     $.ajaxSetup({
    //          headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    //     let inventoryTable = new DataTable('#inventory-datatable');
    //     loadHome()
    // });

    // function reload(){
    //     localStorage.removeItem('ledgers')
    //     localStorage.removeItem('voucher_type_mapping')
    //     loadHome()
    // }
    // $(document).on('keyup',function(e){
    //     if(e.keyCode == 27 && e.key == "Escape"){
    //         goBack()
    //     }
    // })

    // function loadHome(){
    //     local_storage_has_ledger = localStorage.getItem('ledgers')
    //     local_storage_has_voucher_type_mapping = localStorage.getItem('voucher_type_mapping')
    //     if(local_storage_has_ledger && local_storage_has_voucher_type_mapping){
    //         process_ledger(JSON.parse(local_storage_has_ledger), JSON.parse(local_storage_has_voucher_type_mapping))
    //     }else{
    //         get_ledger()
    //     }
    // }

    // function get_ledger(){
    //     $.ajax({
    //         type: "GET",
    //         url: "{{ url('/SRI/tally/data/ledger') }}",
    //         dataType: "json",
    //         success: function (response) {
    //             if(response?.data?.BODY?.DATA?.COLLECTION?.LEDGER){
    //                 localStorage.setItem('ledgers',JSON.stringify(response?.data?.BODY?.DATA?.COLLECTION?.LEDGER))
    //                 localStorage.setItem('voucher_type_mapping',JSON.stringify(response?.voucher_type_mapping))
    //                 process_ledger(response?.data?.BODY?.DATA?.COLLECTION?.LEDGER, response?.voucher_type_mapping)
    //             }
    //         },
    //     })
    // }

    // function process_ledger(DATA, voucher_type_mapping){
    //     LEDGERS = fotmate_data(DATA);

    //     table = $('#tally-datatable tbody').empty()
    //     thead = $('#tally-datatable thead').empty()
    //     var tableHead = $('<tr class="table-secondary">')
    //         // tableHead.append('<th>Name</th>')
    //         tableHead.append('<th>Type<br><input id="LedgerGroupSearch" class="form-control form-control-sm" type="text" ></th>')
    //         tableHead.append('<th style="align-content:baseline">Count</th>')
    //         tableHead.append('<th style="align-content:baseline">Balance</th>')
    //     thead.html(tableHead)
    //     let ALL_LEDGER = 0
    //     let ALL_Balance = 0
    //     rows = {}

    //     $.each(voucher_type_mapping,function(ms,key){
    //         if(key != ""){
    //             rows[key] = {}
    //         }
    //     })
    //     rows["OTHERS"] = {}
    //     $.each(LEDGERS,function(MASTER,LEDGER){
    //     console.log(MASTER);
    //         const totalBalance = LEDGER.reduce((sum, item) => {
    //             return sum + parseFloat(item.balance);
    //         }, 0);
    //         ALL_LEDGER = ALL_LEDGER + LEDGER.length
    //         ALL_Balance = ALL_Balance + totalBalance
    //         var row = $(`<tr ondblclick="loadLedgerByMaster('${MASTER}')">`)
    //             row.append('<td class="LedgerGroup">'+MASTER+'</td>')
    //             row.append('<td>'+LEDGER.length+'</td>')
    //             row.append('<td>'+displayAmount(totalBalance)+'</td>')
    //         if (voucher_type_mapping.hasOwnProperty(MASTER) && voucher_type_mapping[MASTER] != "") {
    //             rows[voucher_type_mapping[MASTER]][MASTER] = row;
    //         } else {
    //             rows["OTHERS"][MASTER] = row;
    //         }
    //     })
    //     $.each(rows,function(VOUCHER_TYPE,Masters){
    //         var row = $(`<tr class="table-primary VOUCHER_TYPE" data-parent="${VOUCHER_TYPE}">`)
    //             row.append(`<td colspan="3">${VOUCHER_TYPE}</td>`)
    //         table.append(row)
    //         $.each(Masters,function(aa,bb){
    //             $(bb).addClass('VOUCHER_TYPE_CHILD').data('parent',VOUCHER_TYPE)
    //             table.append(bb)
    //         })
    //     })
    //     // var row = $(`<tr ondblclick="loadLedgerByMaster('All')">`)
    //     //     row.append('<td class="LedgerGroup">All</td>')
    //     //     row.append('<td class="ALL_LEDGER">'+ALL_LEDGER+'</td>')
    //     //     row.append('<td class="ALL_Balance">'+displayAmount(ALL_Balance)+'</td>')
    //     // table.append(row)
    //     changeTitle({
    //         route : 'home'
    //     })
    // }

    // function fotmate_data(DATA){
    //     RETURN_DATA = {};
    //     master = [];
    //     $.each(DATA, function(key, ledger){
    //         name = ledger['@attributes'].NAME
    //         parent = ledger?.PARENT
    //         balance = typeof ledger?.CLOSINGBALANCE == 'string' ? ledger?.CLOSINGBALANCE : '0'
    //         if($.inArray(parent, master) === -1){
    //             RETURN_DATA[parent] = []
    //             master.push(parent)
    //         }
    //         RETURN_DATA[parent].push({
    //             name : name,
    //             balance : balance
    //         });
    //     })
    //     return RETURN_DATA

    // }

    // function loadLedgerByMaster(MASTER){
    //     changeTitle({
    //         route : 'master',
    //         master : MASTER
    //     })
    //     ledgers = localStorage.getItem('ledgers')
    //     if(ledgers){
    //         ledgers = JSON.parse(ledgers)
    //         LEDGERS = fotmate_data(ledgers)
    //         table = $('#tally-datatable tbody').empty()
    //         thead = $('#tally-datatable thead').empty()
    //         var tableHead = $('<tr class="table-secondary">')
    //             tableHead.append('<th>Name<br><input id="LedgerNameSearch" class="form-control form-control-sm" type="text" ></th>')
    //             tableHead.append('<th style="align-content:baseline">Closing Balance</th>')
    //         thead.html(tableHead)
    //         table.empty();
    //         if(MASTER == 'All'){
    //             $.each(LEDGERS, function(key, value){
    //                 $.each(value, function(key, ledger){
    //                     row = $(`<tr ondblclick="loadVoucherOfLedger('${ledger.name}')">`)
    //                     row.append('<td class="LedgerName">'+ledger.name+'</td>')
    //                     row.append('<td>'+displayAmount(ledger.balance)+'</td>')
    //                     table.append(row)
    //                 })
    //             })
    //         }else{
    //             $.each(LEDGERS[MASTER],function(i,LEDGER){
    //                 var row = $(`<tr ondblclick="loadVoucherOfLedger('${LEDGER.name}')">`)
    //                     row.append('<td class="LedgerName">'+LEDGER.name+'</td>')
    //                     row.append('<td>'+displayAmount(LEDGER.balance)+'</td>')
    //                 table.append(row)
    //             })
    //         }
    //         // localStorage.setItem('set_ledger',MASTER)
    //     }else{
    //         ledgers = {}
    //     }



    // }

    // function loadVoucherOfLedger(LEDGER){
    //     changeTitle({
    //         route : 'ledger',
    //         ledger : LEDGER
    //     })
    //     let vouchers = null
    //     $.ajax({
    //         type: "GET",
    //         url: "{{ url('/SRI/tally/data/ledger_voucher') }}?ledger="+encodeURIComponent(LEDGER),
    //         dataType: "json",
    //         success: function (response) {
    //             vouchers = response.data
    //             balance = response.balance
    //             ledger_enties = response.ledger_enties
    //             $('#tally-datatable thead').empty()
    //             $('#tally-datatable tbody').empty()
    //             var tableHead = $('<tr class="table-secondary">')
    //                 tableHead.append('<th>Transaction Date<br><input id="LedgerTransactionDateSearch" class="form-control form-control-sm" type="text" ></th>')
    //                 tableHead.append('<th>Transaction Type<br><input id="LedgerTransactionTypeSearch" class="form-control form-control-sm" type="text" ></th>')
    //                 tableHead.append('<th style="align-content:baseline">Debit</th>')
    //                 tableHead.append('<th style="align-content:baseline">Credit</th>')
    //             $('#tally-datatable thead').append(tableHead)
    //             balance.current_debit = 0
    //             balance.current_credit = 0
    //             $.each(vouchers, function(key, voucher){
    //                 if(ledger_enties == 'credit'){
    //                     debit = ((typeof voucher?.debit) == 'string') ? parseFloat(voucher?.debit.replace('-','')) : ''
    //                     credit = ((typeof voucher?.credit) == 'string') ? parseFloat(voucher?.credit) : ''
    //                 }else{
    //                     debit = ((typeof voucher?.debit) == 'string') ? parseFloat(voucher?.debit) : ''
    //                     credit = ((typeof voucher?.credit) == 'string') ? parseFloat(voucher?.credit.replace('-','')) : ''
    //                 }
    //                 balance.current_debit = (debit != '') ? balance.current_debit + parseFloat(debit) : balance.current_debit
    //                 balance.current_credit = (credit != '') ? balance.current_credit + parseFloat(credit) : balance.current_credit
    //                 var row = $('<tr>')
    //                     row.append('<td class="LedgerTransactionDate">'+voucher.date+'</td>')
    //                     row.append('<td class="LedgerTransactionType">'+voucher.type+'</td>')
    //                     row.append('<td>'+displayAmount(debit)+'</td>')
    //                     row.append('<td>'+displayAmount(credit)+'</td>')
    //                 $('#tally-datatable tbody').append(row)
    //             })
    //             var row = $('<tr>')
    //                 row.append('<td></td>')
    //                 row.append('<td></td>')
    //                 row.append('<td></td>')
    //                 row.append('<td></td>')
    //             $('#tally-datatable tbody').append(row)

    //             var row = $('<tr>')
    //                 row.append('<td></td>')
    //                 row.append('<td>Opening Balance</td>')
    //                 if(parseFloat(balance.opening) > 0){
    //                     row.append('<td></td>')
    //                     row.append('<td>'+displayAmount(balance.opening)+'</td>')
    //                 }else{
    //                     row.append('<td>'+displayAmount(balance.opening.replace('-',''))+'</td>')
    //                     row.append('<td></td>')
    //                 }
    //             $('#tally-datatable tbody').append(row)
    //             var row = $('<tr>')
    //                 row.append('<td></td>')
    //                 row.append('<td>Current Total</td>')
    //                 row.append('<td>'+displayAmount(balance.current_debit)+'</td>')
    //                 row.append('<td>'+displayAmount(balance.current_credit)+'</td>')
    //             $('#tally-datatable tbody').append(row)
    //             var row = $('<tr>')
    //                 row.append('<td></td>')
    //                 row.append('<td>Closing Balance</td>')
    //                 if(parseFloat(balance.closing) > 0){
    //                     row.append('<td></td>')
    //                     row.append('<td>'+displayAmount(balance.closing)+'</td>')
    //                 }else{
    //                     row.append('<td>'+displayAmount(balance.closing.replace('-',''))+'</td>')
    //                     row.append('<td></td>')
    //                 }
    //             $('#tally-datatable tbody').append(row)
    //         }
    //     });
    // }

    // function changeTitle(data = {}){
    //     tagData = $('#table-title').data()
    //     titleTag = $('#table-title');
    //     breadcrumb = $('#tally-breadcrumb .breadcrumb').empty();

    //     switch (data.route) {
    //         case 'home':
    //             $('#table-title').data('master','')
    //             $('#table-title').data('ledger','')
    //             $('#table-title').data('back','home')
    //             $('#table-title').data('path','home')
    //             tag = $('<li>')
    //                 tag.addClass('breadcrumb-item active')
    //                 tag.text('Masters')
    //             breadcrumb.append(tag)
    //         break;

    //         case 'master':
    //             $('#table-title').data('master',data.master)
    //             $('#table-title').data('back','home')
    //             $('#table-title').data('path','master')
    //             li = $('<li>')
    //                 li.addClass('breadcrumb-item')
    //                 a = $('<a>')
    //                     a.attr('ondblclick','goHome()')
    //                     a.text("Master")
    //                 li.append(a)
    //             breadcrumb.append(li)
    //             tag = $('<li>')
    //                 tag.addClass('breadcrumb-item active')
    //                 tag.text(data.master)
    //             breadcrumb.append(tag)
    //             break;

    //         case 'ledger':
    //             $('#table-title').data('ledger',data.ledger)
    //             $('#table-title').data('back','master')
    //             $('#table-title').data('path','ledger')
    //             li = $('<li>')
    //                 li.addClass('breadcrumb-item')
    //                 a = $('<a>')
    //                     a.attr('ondblclick','goHome()')
    //                     a.attr('href','javascript:void(0);')
    //                     a.text("Master")
    //                 li.append(a)
    //             breadcrumb.append(li)
    //             li2 = $('<li>')
    //                 li2.addClass('breadcrumb-item')
    //                 a = $('<a>')
    //                     a.attr('ondblclick',`loadLedgerByMaster("${tagData.master}")`)
    //                     a.attr('href','javascript:void(0);')
    //                     a.text(tagData.master)
    //                 li2.append(a)
    //             breadcrumb.append(li2)
    //             tag = $('<li>')
    //                 tag.addClass('breadcrumb-item active')
    //                 tag.text(data.ledger)
    //             breadcrumb.append(tag)
    //             // $('#table-title').text('Tally > '+ tagData.master + ' > ' + data.ledger)
    //             break;

    //         default:
    //             break;
    //     }
    // }
    // function goBack(){
    //     tagData = $('#table-title').data()
    //     switch (tagData.back) {
    //         case 'home':
    //             if(tagData.path != 'home'){
    //                 goHome()
    //             }
    //             break

    //         case 'master':
    //             loadLedgerByMaster(tagData.master)
    //         default:
    //             break
    //     }

    // }
    // function goHome(TITLE){
    //     loadHome()
    // }
    // function displayAmount(Amount){
    //     if(Amount == ''){return Amount}
    //     if (typeof Amount !== 'string') {
    //         Amount = Amount.toString();
    //     }
    //     is_nagetive = ''
    //     if(Amount.indexOf('-') > -1){
    //         is_nagetive = '-'
    //         Amount = Amount.replace('-','')
    //     }
    //     Amount = parseFloat(Amount).toFixed(2)
    //     let parts = Amount.split('.');
    //     let rs = parts[0];
    //     let ps = parts[1] || '';
    //     numArray = [];
    //     count = 0;
    //     spliter = 0;
    //     $.each(rs.split('').reverse(),function(a,d){
    //         numArray.push(d)
    //         if(count == 2 && spliter == 0){
    //             spliter = 1;
    //             count = 0;
    //             if(rs.split('').length != (a+1)){
    //                 numArray.push(',')
    //             }
    //         }
    //         if(spliter == 1 && count == 2){
    //             count = 0;
    //             if(rs.split('').length != (a+1)){
    //                 numArray.push(',')
    //             }
    //         }
    //         count++;
    //     })
    //     rs = numArray.reverse().join('')
    //     return `${is_nagetive}${rs}.${ps}`;
    // }

    // $(document).on('keyup','#LedgerGroupSearch',function(){
    //     var search = $('#LedgerGroupSearch').val().trim()
    //     if(search == ''){
    //         $('.LedgerGroup').each(function(aa,ss){
    //             $(ss).parent().show()
    //         })
    //         $('.VOUCHER_TYPE').each(function(aa,ss){
    //             $(ss).show()
    //         })
    //     }else{
    //         $('.VOUCHER_TYPE').each(function(aa,ss){
    //             $(ss).hide()
    //         })
    //         $('.LedgerGroup').each(function(aa,ss){
    //             filterCount = search.split(' ').length
    //             matchCount = 0
    //             $.each(search.split(' '),function(){
    //                 if($(ss).text().toLowerCase().indexOf(this.toLowerCase()) > -1){
    //                     matchCount++;
    //                 }
    //             })
    //             if(matchCount == filterCount){
    //                 $(`tr[data-parent="${$(ss).parent().data('parent')}"].VOUCHER_TYPE`).show()
    //                 $(ss).parent().show()
    //             }else{
    //                 $(ss).parent().hide()
    //             }
    //         })
    //     }
    // })
    // $(document).on('keyup','#LedgerNameSearch',function(){
    //     var search = $('#LedgerNameSearch').val().trim()
    //     if(search == ''){
    //         $('.LedgerName').each(function(aa,ss){
    //             $(ss).parent().show()
    //         })
    //     }else{
    //         $('.LedgerName').each(function(aa,ss){
    //             filterCount = search.split(' ').length
    //             matchCount = 0
    //             $.each(search.split(' '),function(){
    //                 if($(ss).text().toLowerCase().indexOf(this.toLowerCase()) > -1){
    //                     matchCount++;
    //                 }
    //             })
    //             if(matchCount == filterCount){
    //                 $(ss).parent().show()
    //             }else{
    //                 $(ss).parent().hide()
    //             }
    //         })
    //     }
    // })
    // $(document).on('keyup','#LedgerTransactionDateSearch',function(){
    //     var search = $('#LedgerTransactionDateSearch').val().trim()
    //     if(search == ''){
    //         $('.LedgerTransactionDate').each(function(aa,ss){
    //             $(ss).parent().show()
    //         })
    //     }else{
    //         $('.LedgerTransactionDate').each(function(aa,ss){
    //             filterCount = search.split(' ').length
    //             matchCount = 0
    //             $.each(search.split(' '),function(){
    //                 if($(ss).text().toLowerCase().indexOf(this.toLowerCase()) > -1){
    //                     matchCount++;
    //                 }
    //             })
    //             if(matchCount == filterCount){
    //                 $(ss).parent().show()
    //             }else{
    //                 $(ss).parent().hide()
    //             }
    //         })
    //     }
    // })
    // $(document).on('keyup','#LedgerTransactionTypeSearch',function(){
    //     var search = $('#LedgerTransactionTypeSearch').val().trim()
    //     if(search == ''){
    //         $('.LedgerTransactionType').each(function(aa,ss){
    //             $(ss).parent().show()
    //         })
    //     }else{
    //         $('.LedgerTransactionType').each(function(aa,ss){
    //             filterCount = search.split(' ').length
    //             matchCount = 0
    //             $.each(search.split(' '),function(){
    //                 if($(ss).text().toLowerCase().indexOf(this.toLowerCase()) > -1){
    //                     matchCount++;
    //                 }
    //             })
    //             if(matchCount == filterCount){
    //                 $(ss).parent().show()
    //             }else{
    //                 $(ss).parent().hide()
    //             }
    //         })
    //     }
    // })
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
    localStorage.removeItem("ledgers");
    localStorage.removeItem("voucher_type_mapping");
    loadHome();
}

// ------------------------------
// Load Home
// ------------------------------
function loadHome() {
    let ledgersLocal = localStorage.getItem("ledgers");
    let mappingLocal = localStorage.getItem("voucher_type_mapping");

    if (ledgersLocal && mappingLocal) {
        process_ledger(JSON.parse(ledgersLocal), JSON.parse(mappingLocal));
    } else {
        get_ledger();
    }
}

// ------------------------------
// AJAX → fetch()
// ------------------------------
async function get_ledger() {
    const res = await fetch(`/tally/data/ledger`);
    const response = await res.json();

    let ledgerData = response?.data?.BODY?.DATA?.COLLECTION?.LEDGER;

    if (ledgerData) {
        localStorage.setItem("ledgers", JSON.stringify(ledgerData));
        localStorage.setItem("voucher_type_mapping", JSON.stringify(response.voucher_type_mapping));

        process_ledger(ledgerData, response.voucher_type_mapping);
    }
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
}

// ------------------------------
// Load Ledger Vouchers
// ------------------------------
async function loadVoucherOfLedger(LEDGER) {
    changeTitle({ route: "ledger", ledger: LEDGER });

    const res = await fetch(`/tally/data/ledger_voucher?ledger=${encodeURIComponent(LEDGER)}`);
    const response = await res.json();

    const vouchers = response.data;
    const balance = response.balance;
    const ledger_enties = response.ledger_enties;

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
    const tagData = document.querySelector("#table-title").dataset;

    if (tagData.back === "home" && tagData.path !== "home") {
        goHome();
    } else if (tagData.back === "master") {
        loadLedgerByMaster(tagData.master);
    }
}

function goHome() {
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
