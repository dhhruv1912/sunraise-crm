
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
$(document).on("click", "#employee-update",function(){
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
            },
            dataType: "json",
            beforeSend : function(){
                show_fs_loader()
            },
            success: function (response) {
                hide_fs_loader()
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
    if($("#password").length){
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
