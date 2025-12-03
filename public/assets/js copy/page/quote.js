
$(function () {
    $("#QuoteExcelModal").click(function () {
        $("#QuoteExcelUploaderModal").modal("show");
    });
});
$(document).on('click','#QupteExcelUpload',function (e) {
    var fileInput = $('#quoteFile')[0].files[0];
    if (fileInput) {
        var formData = new FormData();
        formData.append('_token', TOKEN);
        formData.append('quoteFile', fileInput);
        $.ajax({
            type: "POST",
            url: BASE_URL + "/SRI/quote/upload",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            beforeSend : function(){
                show_fs_loader()
            },
            success: function (response) {
                hide_fs_loader()
                $("#QuoteExcelUploaderModal").modal('hide')
            },
        });
    } else {
        console.error("No file selected");
    }
});
$(document).on('click','.quote-save',function (e) {
    id = $(this).attr('id')
    $.ajax({
        type: "POST",
        url: BASE_URL + "/SRI/quote/save",
        data: {
            _token          : TOKEN,
            SKU             : id,
            Module          : $("#Module").val(),
            KW              : $("#KW").val(),
            Module_Count    : $("#Module_Count").val(),
            Value           : $("#Value").val(),
            Taxes           : $("#Taxes").val(),
            Metering_Cost   : $("#Metering_Cost").val(),
            MCB             : $("#MCB").val(),
            Payable         : $("#Payable").val(),
            Subsidy         : $("#Subsidy").val(),
            Projected       : $("#Projected").val(),
        },
        dataType: "json",
        beforeSend : function(){
            show_fs_loader()
        },
        success: function (response) {
            hide_fs_loader()
        },
    });
});
$(document).on('click','#sendQuote',function(){
    $.ajax({
        type: "POST",
        url: BASE_URL + '/SRI/quote/send/' + $(this).data('id'),
        data: {
            _token  : TOKEN
        },
        dataType: "json",
        beforeSend : function(){
            show_fs_loader()
        },
        success: function (response) {
            hide_fs_loader()
        }
    });
})


function getActionCell(member) {
    let $anchor = $("<a>", {
        class: " btn btn-outline-primary waves-effect px-2",
    }).attr("href", BASE_URL + "/SRI/quote/edit/" + member.id);
    let $icon = $("<span>", { class: "mdi mdi-pen" });
    $anchor.append($icon)
    return $anchor;
}
function getActionCellQuoteRequest(member) {
    let $anchor = $("<a>", {
        class: "btn btn-icon btn-outline-primary waves-effect",
    }).attr("href", BASE_URL + "/SRI/quote/request/" + member.id);
    let $icon = $("<span>", { class: "tf-icons mdi mdi-eye-arrow-right" });
    $anchor.append($icon);
    return $anchor;
}
function getActionCellCallRequest(member) {
    let $anchor = $("<a>", {
        class: "btn btn-icon btn-outline-primary waves-effect",
    }).attr("href", "#");
    // }).attr("href", BASE_URL + "/SRI/quote/request/" + member.id);
    let $icon = $("<span>", { class: "tf-icons mdi mdi-eye-arrow-right" });
    $anchor.append($icon);
    return $anchor;
}
function renderPagination(response,ele,func=null) {
    let paginationHtml = `<nav><ul class="pagination justify-content-center">`;
    if (response.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-func="${func}" data-page="${
                    response.current_page - 1
                }" data->Previous</a>
            </li>`;
    } else {
        paginationHtml += `
            <li class="page-item disabled">
                <span class="page-link">Previous</span>
            </li>`;
    }
    for (let i = 1; i <= response.last_page; i++) {
        let activeClass = i === response.current_page ? "active" : "";
        paginationHtml += `
            <li class="page-item ${activeClass}">
                <a href="#" class="page-link page-btn" data-func="${func}" data-page="${i}">${i}</a>
            </li>`;
    }
    if (response.current_page < response.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-func="${func}" data-page="${
                    response.current_page + 1
                }">Next</a>
            </li>`;
    } else {
        paginationHtml += `
            <li class="page-item disabled">
                <span class="page-link">Next</span>
            </li>`;
    }
    paginationHtml += `</ul></nav>`;
    $(ele).html(paginationHtml);
}

function loadQuoteMasterData(page = 1, perPage = 0) {
    if (perPage === 0) {
        const val = $("#per_page").val();
        perPage = val != null ? val : 10;
    }
    $.get(
        BASE_URL + "/SRI/quote/list?page=" + page + "&per_page=" + perPage,
        function (response) {
            const tbody = $("#quote-datatable tbody");
            tbody.empty();
            response.data.forEach((qMaster) => {
                tr = $("<tr>");
                td1 = $("<td>").text(qMaster.sku);
                td2 = $("<td>").text(qMaster.module);
                td3 = $("<td>").text(qMaster.KW);
                td4 = $("<td>").text(qMaster.module_count);
                td5 = $("<td>").text(qMaster.value);
                td6 = $("<td>").text(qMaster.taxes);
                td7 = $("<td>").text(qMaster.metering_cost);
                td8 = $("<td>").text(qMaster.mcb_ppa);
                td9 = $("<td>").text(qMaster.payable);
                td10 = $("<td>").text(qMaster.subsidy);
                td11 = $("<td>").text(qMaster.projected);
                td12 = $("<td>").html(getActionCell(qMaster));
                tr.append(td1);
                tr.append(td2);
                tr.append(td3);
                tr.append(td4);
                tr.append(td5);
                tr.append(td6);
                tr.append(td7);
                tr.append(td8);
                tr.append(td9);
                tr.append(td10);
                tr.append(td11);
                tr.append(td12);
                tbody.append(tr);
            });
            renderPagination(response,"#pagination","loadQuoteMasterData");
        }
    );
}
function loadQuoteRequestData(page = 1, perPage = 0) {
    if (perPage === 0) {
        const val = $("#per_page").val();
        perPage = val != null ? val : 10;
    }
    const tbody = $("#quote-request-datatable tbody");
    tbody.empty();
    $.get(
        BASE_URL +
            "/SRI/quote/requests/list/quote?page=" +
            page +
            "&per_page=" +
            perPage,
        function (response) {

            response.data.forEach((qMaster) => {

                tr = $("<tr>");
                td1 = $("<td>").text(qMaster.name);
                td2 = $("<td>").text(qMaster.module);
                td3 = $("<td>").text(qMaster.kw);
                td4 = $("<td>").text(qMaster.status);
                td5 = $("<td>").html(getActionCellQuoteRequest(qMaster));
                tr.append(td1);
                tr.append(td2);
                tr.append(td3);
                tr.append(td4);
                tr.append(td5);

                tbody.append(tr);
            });

            // Render basic pagination
            renderPagination(response, "#QuoteRequestPagination","loadQuoteRequestData");
        }
    );
}
function loadQuoteCallData(page = 1, perPage = 0) {
    if (perPage === 0) {
        const val = $("#per_page").val();
        perPage = val != null ? val : 10;
    }
    $.get(
        BASE_URL +
            "/SRI/quote/requests/list/call?page=" +
            page +
            "&per_page=" +
            perPage,
        function (response) {
            const tbody = $("#call-request-datatable tbody");
                tbody.empty();

            response.data.forEach((qMaster) => {
                tr = $("<tr>");
                td1 = $("<td>").text(qMaster.name);
                td2 = $("<td>").text(qMaster.email);
                td3 = $("<td>").text(qMaster.number);
                td4 = $("<td>").text(qMaster.status);
                td5 = $("<td>").html(getActionCellCallRequest(qMaster));
                tr.append(td1);
                tr.append(td2);
                tr.append(td3);
                tr.append(td4);
                tr.append(td5);
                tbody.append(tr);
            });

            // Render basic pagination
            renderPagination(response, "#CallRequestPagination","loadQuoteCallData");
        }
    );
}

function changeValue(value,field){
    $(field).val(value)
    loadQuoteMasterData(1,$(field).val())
    loadQuoteRequestData(1,$(field).val())
    loadQuoteCallData(1,$(field).val())
}
$(document).on("click", ".page-btn", function (e) {
    e.preventDefault();
    let page = $(this).data("page");
    let func = $(this).data("func");
    switch (func) {
        case "loadQuoteMasterData":
            loadQuoteMasterData(page)
            break;
        case "loadQuoteRequestData":
            loadQuoteRequestData(page)
            break;
        case "loadQuoteCallData":
            loadQuoteCallData(page)
            break;

        default:
            break;
    }
});
