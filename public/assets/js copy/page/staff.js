$(function () {
    $("#close-emp-modal").click(function(){
        $('#addEmployeeModal').modal('hide')
        $('#addEmployeeForm')[0].reset()
    })
    $('#add-employee').click(function(){
        $("#addEmployeeModal").modal('show')
        $('#employee-update').addClass('d-none');
        $('#employee-save').removeClass('d-none');
    })
});

$(document).on("click", "#employee-update,#employee-save",function(){
    if(validate_staff_form()){
        $.ajax({
            type: "post",
            url: BASE_URL + "/SRI/staff/update",
            data: {
                _token      : TOKEN,
                id          : $(this).data('id'),
                fname       : $('#firstname').val(),
                lname       : $('#lastname').val(),
                password    : $('#password').val(),
                role        : $('#role').val(),
                status      : $('#status').is(":checked") ? 1 : 0,
                mobile      : $('#mobile').val(),
                email       : $('#email').val(),
                salary       : $('#salary').val(),
            },
            dataType: "json",
            beforeSend : function(){
                show_fs_loader()
            },
            success: function (response) {
                hide_fs_loader()
                $('.loader-line').hide()
                show_alert('dismissible', 'success', {"alert-dismissible-text" : response.message});
                setTimeout(() => {
                    hideAlertBox('dismissible', 'success')
                },2000);
            },
            error : function(err) {
                $('.loader-line').hide()
                if(err.responseJSON != undefined && err.responseJSON.errors != undefined){
                    $.each(err.responseJSON.errors,function(aa,bb){
                        show_alert('dismissible', 'danger', {"alert-dismissible-text" : bb});
                    })
                }
            }
        });
    }
});
$(document).on("click", "#employee-save",function(){
    if(validate_staff_form()){
        $.ajax({
            type: "post",
            url: BASE_URL + "/register",
            data: {
                _token      : TOKEN,
                fname       : $('#firstname').val(),
                lname       : $('#lastname').val(),
                password    : $('#password').val(),
                role        : $('#role').val(),
                status      : $('#status').is(":checked") ? 1 : 0,
                mobile      : $('#mobile').val(),
                email       : $('#email').val(),
            },
            dataType: "json",
            beforeSend : function(){
                show_fs_loader()
            },
            success: function (response) {
                hide_fs_loader()
                $('.loader-line').hide()
                show_alert('dismissible', 'success', {"alert-dismissible-text" : response.message});
                setTimeout(() => {
                    hideAlertBox('dismissible', 'success')
                },2000);
            },
            error : function(err) {
                $('.loader-line').hide()
                if(err.responseJSON != undefined && err.responseJSON.errors != undefined){
                    $.each(err.responseJSON.errors,function(aa,bb){
                        show_alert('dismissible', 'danger', {"alert-dismissible-text" : bb});
                    })
                }
            }
        });
    }
});
$(document).on('click','.delete-employee',function(){
    id = $(this).attr('id');
    Ele = $(this)
    $.ajax({
        type: "delete",
        url:  BASE_URL + "/SRI/staff/delete",
        data: {
            _token  : TOKEN,
            id      :  id,
        },
        dataType: "json",
        beforeSend : function(){
            show_fs_loader()
        },
        success: function (response) {
            hide_fs_loader()
            if(response.status){
                Ele.parent().parent().parent().remove()
            }
        },
        error : function(){

        }
    });
})
$(document).on('click','.edit-employee',function(){
    id = $(this).attr('id');
    $.ajax({
        type: "post",
        url:  BASE_URL + "/SRI/staff/get",
        data: {
            _token  : TOKEN,
            id      :  id,
        },
        dataType: "json",
        beforeSend : function(){
            show_fs_loader()
        },
        success: function (response) {
            hide_fs_loader()
            if(response.status){
                $('#employee-update').removeClass('d-none');
                $('#employee-save').addClass('d-none');
                $('#addEmployeeModal').modal('show')
                $('#employee-update').data('id',response.data.id)
                $('#firstname').val(response.data.fname)
                $('#lastname').val(response.data.lname)
                $('#mobile').val(response.data.mobile)
                $('#email').val(response.data.email)
                $('#salary').val(response.data.salary)
                $('#password').val(response.data.password_d)
                $('#confirm-password').val(response.data.password_d)
                $('#role option[value="'+response.data.role+'"]').attr('selected',true)
                $('#status').attr('checked',(response.data.status)  ? true : false )
            }
        },
        error : function(){

        }
    });
})

function validate_staff_form(){
    var isValid = true;
    $(".required").each(function(){
        if($(this).val()===""){
            isValid=false;
            $(this).addClass("is-invalid");
            $(this).parent().find('.text-danger').html("This field is required.");
		}else {
             $(this).removeClass("is-invalid");
             $(this).parent().find('.text-danger').html("")
         }
    });
    if(isValid == false){
        return false;
    }
    if ($("#mobile").val().length < 10) {
        isValid = false;
        $(".invalid-feedback-mobile").html("Please enter a valid mobile number");
        $("#mobile").addClass("is-invalid");
        return false;
    }else{
        $(".invalid-feedback-mobile").html("");
        $("#mobile").removeClass("is-invalid");
    }
    if ($("#email").val().match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) < 10) {
        isValid = false;
        $(".invalid-feedback-email").html("Please enter a valid email");
        $("#email").addClass("is-invalid");
        return false;
    }else{
        $(".invalid-feedback-email").html("");
        $("#email").removeClass("is-invalid");
    }
    if ($("#password").val().length < 8) {
        isValid = false;
        $(".invalid-feedback-password").html("Password must be at least 8 characters long");
        $("#password").addClass("is-invalid");
        return false;
    } else {
        $(".invalid-feedback-mobile").html("");
        $("#mobile").removeClass("is-invalid");
    }
    if ($("#password").val() != $("#confirm-password").val()) {
        isValid = false;
        $(".invalid-feedback-confirm-password").html("The password and confirmation password do not match");
        $("#confirm-password").addClass("is-invalid");
        return false;
    } else {
        $(".invalid-feedback-mobile").html("");
        $("#mobile").removeClass("is-invalid");
    }
    return isValid

}

function show_alert(type,cls,params = {}){
    // alert_html.removeClass('d-none')
    $('.alert-'+type).addClass('alert-'+cls)
    $.each(params,function(aa,bb){
        $('.alert-'+type).find('.'+aa).html(bb)
    })
    alert_html = $('#alert-'+type).html()
    $('#inline-alerts').append(alert_html)
}

function hideAlertBox(type,cls){
    $('.alert.alert-'+type).addClass('d-none')
}
function getActionCell(member){
// Create outer div
    let $dropdown = $('<div>', { class: 'dropdown' });

    // Edit button
    let $editBtn = $('<button>', {
        id: member.id,
        class: 'edit-employee btn btn-outline-primary waves-effect px-2 mx-1'
    }).append($('<span>', { class: 'mdi mdi-account-edit' }));

    $dropdown.append($editBtn);

    // Verification buttons
    if (member.varify === false || member.varify === 0) {
        let $verifyLink = $('<a>', {
            href: '/varify-employee/' + member.id, // adjust route if needed
            class: 'btn btn-outline-secondary waves-effect px-2 mx-1'
        }).append($('<span>', { class: 'mdi mdi-shield-alert' }));

        $dropdown.append($verifyLink);
    } else {
        let $verifiedBtn = $('<button>', {
            class: 'btn btn-outline-success waves-effect px-2 mx-1'
        }).append($('<span>', { class: 'mdi mdi-shield-check' }));

        $dropdown.append($verifiedBtn);
    }

    let $deleteBtn = $('<button>', {
        id: member.id,
        class: 'delete-employee btn btn-outline-danger waves-effect px-2 mx-1'
    }).append($('<span>', { class: 'mdi mdi-delete' }));

    $dropdown.append($deleteBtn);
    return $dropdown;
}
function renderPagination(response) {
    let paginationHtml = `<nav><ul class="pagination justify-content-center">`;

    // Previous button
    if (response.current_page > 1) {
        paginationHtml += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${response.current_page - 1}">Previous</a>
            </li>`;
    } else {
        paginationHtml += `
            <li class="page-item disabled">
                <span class="page-link">Previous</span>
            </li>`;
    }

    // Page numbers
    for (let i = 1; i <= response.last_page; i++) {
        let activeClass = (i === response.current_page) ? 'active' : '';
        paginationHtml += `
            <li class="page-item ${activeClass}">
                <a href="#" class="page-link page-btn" data-page="${i}">${i}</a>
            </li>`;
    }

    // Next button
    if (response.current_page < response.last_page) {
        paginationHtml += `
            <li class="page-item">
                <a href="#" class="page-link page-btn" data-page="${response.current_page + 1}">Next</a>
            </li>`;
    } else {
        paginationHtml += `
            <li class="page-item disabled">
                <span class="page-link">Next</span>
            </li>`;
    }

    paginationHtml += `</ul></nav>`;

    $('#pagination').html(paginationHtml);
}

function loadStaffData(page = 1, perPage = 2) {
    $.get(BASE_URL + '/SRI/staff/list?page=' + page + '&per_page=' + perPage, function(response) {
        const tbody = $('#staff-datatable tbody');
        tbody.empty();

        response.data.forEach(member => {
            tr = $('<tr>')
            td1 = $('<td>').text(member.fname + " " + member.lname)
            td2 = $('<td>').text(member.role)
            td3 = $('<td>').text(member.status)
            td4 = $('<td>').text(member.mobile)
            td5 = $('<td>').text(member.email)
            td6 = $('<td>').html(getActionCell(member))
            tr.append(td1)
            tr.append(td2)
            tr.append(td3)
            tr.append(td4)
            tr.append(td5)
            tr.append(td6)
            tbody.append(tr);
        });

        // Render basic pagination
        renderPagination(response)
    });
}
loadStaffData()
$(document).on('click', '.page-btn', function(e) {
    e.preventDefault();
    let page = $(this).data('page');
    loadStaffData(page); // reload data for selected page
});


function loadDatatableFunctionalities(TableEle) {
    const $table = $(TableEle);
    const tableId = $table.attr("id"); // Use ID for reference in data-attribute

    const $theadRow = $table.find('thead tr').addClass("bg-label-secondary");

    $theadRow.find('th').each(function(index, element) {
        if (!$(element).hasClass('searchable')) {
            $(element).addClass("align-baseline");
        }
    });

    $theadRow.find('th.searchable').each(function(index, element) {
        const $th = $(element);
        const thIndex = $th.index();

        const columnTitle = $th.text().trim();

        const $container = $("<div>").addClass("d-flex flex-column gap-2");
        const $titleDiv = $("<div>").html($th.html());
        const $searchInput = $("<input>")
            .addClass("form-control form-control-sm rounded-full border-0 custom-searchable")
            .attr({
                placeholder: `Search ${columnTitle}`,
                type: "text",
                name: `search_${columnTitle.replace(/\s+/g, '_')}`,
                'data-index': thIndex,
                'data-table-id': tableId
            });

        $container.append($titleDiv).append($searchInput);
        $th.attr("rowspan", "2").html($container);
    });
}

$(document).on("keyup", ".custom-searchable", function () {
    const index = $(this).data('index');
    const tableId = $(this).data('table-id');
    const $table = $("#" + tableId);
    const search = $(this).val().trim().toLowerCase();

    $table.find('tbody tr').each(function () {
        const $row = $(this);
        const cellText = $row.find('td').eq(index).text().toLowerCase();

        let match = true;

        if (search !== "") {
            const keywords = search.split(" ");
            match = keywords.every(word => cellText.includes(word));
        }

        $row.toggle(match);
    });
});


loadDatatableFunctionalities("#staff-datatable")
