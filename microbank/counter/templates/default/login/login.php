<style>
    body {
        background-image: url(./resource/img/login/login-bg.png);
        background-repeat: no-repeat;
        background-size: 100% 100%;
        -moz-background-size: 100% 100%;
        background-attachment: fixed;
    }

    #login_div {
        width: 350px;
        height: 230px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 7px;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -100px;
        margin-left: -175px;
        padding: 20px;
    }

    #login_logo {
        width: 130px;
        height: 130px;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -250px;
        margin-left: -65px;
    }

    #login_logo img {
        width: 130px;
        height: 130px;
    }

    #login_div .login-title {
        margin-top: 10px;
        font-size: 20px;
        font-weight: 600;
        color: #542890;
        text-align: center;
    }

    #login_div .login-account, #login_div .login-pwd {
        margin-top: 10px;
        position: relative;
    }

    #login_div .login-account input, #login_div .login-pwd input {
        border-radius: 0px;
    }

    #login_div .login-submit {
        margin-top: 15px;
    }

    #btn_authorization, #btn_password {
        width: 148px;
        border-radius: 0;
    }

    #btn_authorization {
        margin-right: 5px;
    }

    #btn_password {
        margin-left: 5px;
    }

    .error_msg {
        position: absolute;
        right: 10px;
        top: 0;
        z-index: 999;
        line-height: 34px;
        color: #d9534f;
        font-size: 12px;
        font-weight: 400!important;
    }

    .error_msg label {
        font-weight: 400!important;
        font-size: 12px;
    }

</style>

<div class="container">
    <div id="login_logo">
        <img src="resource/img/login/login-logo.png">
    </div>
    <div id="login_div">
        <form class="form-signin form-horizontal" id="login-form">
            <div class="login-title">Password Login</div>
            <div class="login-account">
                <input type="text" class="form-control" placeholder="Account Name" name="user_code" value="" autofocus>
                <div class="error_msg" id="error_msg"></div>
            </div>
            <div class="login-pwd">
                <input type="password" class="form-control" placeholder="Password" name="user_password">
                <div class="error_msg"></div>
            </div>
            <div class="login-submit">
                <button id="btn_authorization" class="btn btn-default" type="button" disabled>Authorize</button>
                <button id="btn_password" class="btn btn-warning" type="button" onclick="login();">Sign In</button>
            </div>
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

    function login() {
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
                $('#error_msg').text(_obj.MSG);
            }
        })
    }

    $('#login-form').validate({
        errorPlacement: function (error, element) {
            element.next().html(error);
        },
        rules: {
            user_code: {
                required: true
            },
            user_password: {
                required: true
            }
        },
        messages: {
            user_code: {
                required: '<?php echo 'Required'?>'
            },
            user_password: {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>