$(function () {
    $('#loginAuth').click(function(){
        $.ajax({
            type: "post",
            url: BASE_URL + "login",
            data: {
                _token : TOKEN,
                email : $("#email").val(),
                password : $("#password").val()
            },
            dataType: "json",
            beforeSend : function(){
                show_fs_loader()
            },
            success: function (response) {
                hide_fs_loader()
                if(response && response.status){
                    window.location = response.url
                }else{
                    $('#login-alert').html(`
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        ${response.message}!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    `)
                }
            },
            error: function(error){
                hide_fs_loader()
                html = ""
                // if()
                $.each(error.responseJSON?.errors,function(key,msg){
                    html += `<div class="alert alert-danger alert-dismissible" role="alert">
                        ${msg.join('!<br>')}!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`
                })
                console.log(error);$('#login-alert').html(html)

            }
        });
    })
});
