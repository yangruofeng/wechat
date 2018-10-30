<style>
    body {

        background-image: url(./resource/img/login_bg.png);
        background-repeat: no-repeat;
        background-size: 100% 100%;
        -moz-background-size: 100% 100%;
        background-attachment: fixed;
    }

    #login_div {
        width: 500px;
        height: 400px;
        background-color: #FFF;
        border-radius: 10px;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -200px;
        margin-left: -250px;
    }

    #login_top {
        width: 100%;
        height: 210px;
        border-bottom: 1px solid #DDD;
    }

    #login_bottom {
        width: 100%;
        height: 190px;
        padding: 15px 45px;
    }

    #login_top_left {
        width: 180px;
        height: 100%;
        float: left;
        text-align: center;
    }

    #login_top_left img {
        width: 120px;
        height: 120px;
        margin-top: 55px;
    }

    #login_top_right {
        width: 320px;
        height: 100%;
        float: right;
        padding: 10px 30px 10px 0;
    }

    #login_top_right .login-title {
        margin-top: 10px;
        font-size: 20px;
        font-weight: 600;
        color: #542890;
        text-align: center;
    }

    #login_top_right .login-account, #login_top_right .login-pwd {
        margin-top: 10px;
        position: relative;
    }

    #login_top_right .login-account input, #login_top_right .login-pwd input {
        border-radius: 0px;
    }

    #login_top_right .login-submit {
        margin-top: 15px;
    }

    #login_top_right .login-remember {
        margin-top: 0px;
        margin-bottom: 5px;
    }

    #btn_signin {
        border-radius: 0px;
    }

    .app-link {
        text-align: center;
    }

    .app-link a {
        color: #542890;
    }

    .app-link img {
        margin-top: 5px;
        border: 1px solid #DDD;
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
    <div id="login_div">
        <div id="login_top">
            <div id="login_top_left">
                <img src="resource/img/login_logo.png">
            </div>
            <div id="login_top_right">
                <form class="form-signin form-horizontal" id="login-form">
                    <div class="login-title">Password Login</div>
                    <div class="login-account">
                        <input type="text" class="form-control" placeholder="Account Name" name="user_code" value="" autofocus>
                        <div class="error_msg"></div>
                    </div>
                    <div class="login-pwd">
                        <input type="password" class="form-control" placeholder="Password" name="user_password">
                        <div class="error_msg"></div>
                    </div>
<!--                    <div class="login-remember">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="remember_me" value="1" <?php echo $output['client_id'] ? 'checked' : ''?>>
                            <span>Remember me</span>
                        </label>
<!--                    </div>-->
                    <div class="login-submit">
                        <button id="btn_signin" class="btn btn-warning btn-block" type="button" onclick="login();">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="login_bottom">
            <div class="col-sm-6">
                <div class="app-link">
                    <?php $member_qrcode = ENTRY_API_SITE_URL.'/qrcode.image.create.php?content='.urlencode($output['member_app']['download_url']); ?>
                    <a href="<?php echo $output['member_app']['download_url']?:'#'; ?>">MEMBER APP</a>
                    <div >
                        <a href="<?php echo $member_qrcode; ?>" target="_blank">
                            <img class="qrcode-img" style="width: 130px;height: 130px;" src="<?php echo $member_qrcode; ?>" alt="">
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="app-link">
                    <?php $co_qrcode = ENTRY_API_SITE_URL.'/qrcode.image.create.php?content='.urlencode($output['operator_app']['download_url']); ?>

                    <a href="<?php echo $output['operator_app']['download_url']?:'#'; ?>">OPERATOR APP</a>
                    <div >
                        <a href="<?php echo $co_qrcode; ?>" target="_blank">
                            <img class="qrcode-img" style="width: 130px;height: 130px;" src="<?php echo $co_qrcode; ?>" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="resource/js/application.js"></script>
<script>
    document.onkeydown = function (event) {
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 13) {
            login();
        }
    };

    function login() {
        if (!$("#login-form").valid()) {
            return;
        }
        var _data = $("#login-form").getValues();
        var _url = '<?php echo ENTRY_API_SITE_URL . DS . 'login.php'?>';
        $.post(_url, _data, function (_o) {
            var _obj = $.parseJSON(_o);
            if (_obj.STS) {
                var data = _obj.DATA;
                window.location.href = data.new_url;
            } else {
                $('.login-account .error_msg').text(_obj.MSG);
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