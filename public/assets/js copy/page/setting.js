$(function () {
    $("#close-set-modal").click(function(){
        $('#addSettingModal').modal('hide')
    })
    $('#add-setting').click(function(){
        $("#addSettingModal").modal('show')
    })
});

$(document).on('change','#setting_type',function(){
    if($(this).val() == '2' || $(this).val() == '3' || $(this).val() == '4'){
        $('#setting_option').attr('disabled',false)
    }else{
        $('#setting_option').attr('disabled',true)
    }
})
$(document).on('click','#add_key_value_pair',function(){
    field = $(this).data('field')
    count = $(this).data('count')
    html = `<div class="row mb-3">
                <div class="col">
                    <input type="text" id="key-${field}-${count}" data-value="value-${field}-${count}" placeholder="Key" class="form-control phone-mask ${field}-key">
                </div>
                <div class="col-1 align-content-around p-0 m-0 text-center"> = </div>
                <div class="col">
                    <input type="text" id="value-${field}-${count}" placeholder="Value" class="form-control phone-mask">
                </div>
            </div>`
    $(this).data('count',parseInt(count) + 1)
    $(this).parent().parent().find('.json_fields').append(html)
})

$(document).on("click", ".save-setting-value",function(){
    name = $(this).attr('id');
    if($('#'+name).hasClass('required') && $('#'+name).val() == ''){
        return false;
    }
    val = ""
    if($(`[name="${name}"]`).attr('type') == 'Checkbox'){
        val = []
        $(`[name="${name}"]`).each(function(a,b){
            if($(b).is(':checked')){
                val.push($(b).val())
            }
        })
        val = JSON.stringify(val)
    }else if($(this).data('type') == "8"){
        field = $(this).data('field') + '-key'
        val = {}

        $(`.${field}`).each(function(aa,bb){
            val[$(bb).val()] = $("#" + $(bb).data('value')).val()
        })
        val = JSON.stringify(val)
    }else{
        val = $(`[name="${name}"]`).val()
    }
    $.ajax({
        type: "post",
        url: BASE_URL + "/SRI/setting/save/" + name,
        data: {
            _token      : TOKEN,
            value       : val,
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
});
$(document).on("click", "#setting-update",function(){
    if(validate_setting_form()){
        $.ajax({
            type: "post",
            url: BASE_URL + "/SRI/setting/update",
            data: {
                _token              : TOKEN,
                id                  : $(this).data('id'),
                setting_name        : $("#setting_name").val(),
                setting_label       : $("#setting_label").val(),
                setting_type        : $("#setting_type").val(),
                setting_attr        : $("#setting_attr").val(),
                setting_option      : $("#setting_option").val(),
                setting_module      : $(this).data('module'),
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
                $('#addSettingModal').modal('hide')
            },
            error : function(err) {
                $('.loader-line').hide()
                $('#addSettingModal').modal('hide')
                if(err.responseJSON != undefined && err.responseJSON.errors != undefined){
                    $.each(err.responseJSON.errors,function(aa,bb){
                        show_alert('dismissible', 'danger', {"alert-dismissible-text" : bb});
                    })
                }
            }
        });
    }
});

$(document).on('click','.edit-setting',function(){
    name = $(this).attr('id');
    $.ajax({
        type: "post",
        url:  BASE_URL + "/SRI/setting/get/" + name,
        data: {
            _token  : TOKEN,
        },
        dataType: "json",
        beforeSend : function(){
            show_fs_loader()
        },
        success: function (response) {
            console.log(response);
            hide_fs_loader()
            if(response.status){
                $("#addSettingModal").modal('show')
                $('#setting-update').data('id',response.data.id)
                $('#setting_name').val(response.data.name)
                $('#setting_name').attr('readonly',true)
                $('#setting_label').val(response.data.label)
                $('#setting_type').find('option[value="'+response.data.type+'"]').attr('selected',true)
                if(response.data.type == '2' || response.data.type == '3' || response.data.type == '4'){
                    $('#setting_option').attr('disabled',false)
                }else{
                    $('#setting_option').attr('disabled',true)
                }
                $('#setting_attr').val(response.data.attr)
                $('#setting_option').val(response.data.option)
            }
        },
        error : function(){

        }
    });
})

function validate_setting_form(){
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
