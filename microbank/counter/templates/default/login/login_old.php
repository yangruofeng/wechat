<style>
    body {
        display: flex;
        display: -webkit-flex;
        align-items: center;
        justify-content: center;
        -webkit-align-items: center;
        -webkit-justify-content: center;
        background: url(./resource/img/login/login-bg.jpg);
        background-repeat: no-repeat;
        background-size: cover;
    }

    #login_box {
        width: 350px;
        height: 250px;
        padding: 20px 30px;
        border-radius: 10px;
        background: #FFF;
        position: absolute;
        top: 50%;
        margin-top: -120px;
        right: 100px;
    }

    #login-form h3 {
        text-align: center;
        color: #6d43b1;
        margin-top: 10px;
        margin-bottom: 20px;
    }

    #login-form .form-group {
        position: relative;
    }

    #login-form .form-group .error_msg_1 {
        position: absolute;
        top: 7px;
        right: 7px;
        color: #E32F2F;
    }

    #btn_authorization, #btn_password {
        width: 140px;
    }

    #btn_password {
        margin-left: 6px;
    }

</style>

<div class="container">
    <div class="login_box" id="login_box">
        <form id="login-form">
            <h3>Password Login</h3>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Account Name" name="user_code" value="" autofocus>
                <div class="error_msg"></div>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" name="user_password">
                <div class="error_msg"></div>
            </div>
            <div class="form-group">
                <button id="btn_authorization" class="btn btn-default" type="button" disabled>Authorize</button>
                <button id="btn_password" class="btn btn-warning" type="button" onclick="login();">Sign In</button>
            </div>
        </form>
    </div>
</div>
<script src="resource/js/application.js"></script>
<script>
    $(function(){
        document.onkeydown = function (event) {
            var e = event || window.event || arguments.callee.caller.arguments[0];
            if (e && e.keyCode == 13) {
                login();
            }
        };

        if(window.external){
            try{
                window.external.unsetFullScreen();
            }catch (ex){
                //alert(ex.Message);
            }
        }
    })


    function login(){
        if (!$("#login-form").valid()) {
            return;
        }
        var _data = $("#login-form").getValues();
        var _url = '<?php echo ENTRY_API_SITE_URL . DS . 'counter.login.php'?>';
        $.post(_url, _data, function (_o) {
            var _obj = $.parseJSON(_o);
            if (_obj.STS) {
                var data = _obj.DATA;
                window.location.href = data.new_url;
            } else {
                var msg = '<label id="user_code-error" class="error" for="user_code">' + _obj.MSG + '</label>';
                $('.login-account .error_msg').text(msg);
            }
        })
    }

    $('#login-form').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules : {
            user_code : {
                required : true
            },
            user_password : {
                required : true
            }
        },
        messages : {
            user_code : {
                required : '<?php echo 'Required'?>'
            },
            user_password : {
                required : '<?php echo 'Required'?>'
            }
        }
    });
</script>