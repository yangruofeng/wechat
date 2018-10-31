<style>
    body {
        display: flex;
        display: -webkit-flex;
        align-items: center;
        justify-content: center;
        -webkit-align-items: center;
        -webkit-justify-content: center;
        overflow: hidden;
        background-size: 70%;
        background-position: center;
        background-attachment: fixed;
        background-color: rgb(0, 0, 0);
    }

    #particles-js {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
    }

    .login_box {
        box-sizing: border-box;
        width: 380px;
        padding: 50px 20px 10px;
        border-radius: 3px;
        background: rgba(0, 0, 0, .5);
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -190px;
        margin-top: -150px;
    }

    .login_box_error {
        margin-top: -175px!important;
    }

    .login_box .form-signin-heading {
        width: 100%;
        border-radius: 3px 3px 0 0;
        background: none;
    }

    .login_box .form-signin-heading {
        position: absolute;
        left: 0;
        top: 0;
        margin: 0;
        display: block;
        width: 480px;
        height: 60px;
        line-height: 60px;
        text-indent: 40px;
        background: #4196f5;
        border-radius: 5px 5px 0 0;
    }

    .login_box .form-group {
        margin-bottom: 0;
    }

    .login_box .input-group {
        width: 100%;
        margin-bottom: 10px;
        display: flex;
        flex-direction: row;
    }

    .login_box .input-group-addon {
        display: none;
    }

    .login_box .btn-primary {
        background: #252328;
        border-radius: 3px;
        font-size: 14px;
        border-color: #252328;
        height: 42px;
    }

    .form-signin {
        max-width: 400px;
        padding: 15px;
        margin: 0 auto;
    }

    .form-signin .form-control {
        width: 100%;
        border-radius: 3px!important;
        border: 1px solid #dfe1e8;
        font-size: 14px;
        font-weight: 500;
        box-shadow: none;
    }

    .form-signin .form-control:focus {
        z-index: 2;
    }

    .login_box .form-signin-heading {
        width: 100%;
        border-radius: 3px 3px 0 0;
        background: none;
    }

    .bottom_links {
        position: fixed;
        bottom: 0;
        left: 0;
        padding: 20px 20px 0;
        width: 100%;
        background: #fff;
        text-align: right;
        margin-top: 100px;
    }

    .bottom_links ul li {
        display: inline;
        margin: 0 0 0 20px;
    }

    .bottom_links ul li a {
        color: #555;
    }

    #lgLogo{
        width: 270px;
    }

    .error_msg_1{
        position: absolute;
        right: 10px;
        top: 0;
        z-index: 999;
        line-height: 34px;
        color: #d9534f;
    }

    #login_error{
        width: 340px;
        padding: 8px 12px;
        display: none;
    }

    #login_error .close {
        top: 0 !important;
        right: 0 !important;
    }

    .remember-me {
        margin-top: -5px;
        margin-bottom: -5px;
    }

    .remember-me >div > div:first-child {
        padding-left: 0;
    }

    .remember-me >div > div:first-child input{
        width: 15px;
    }

    .remember-me >div > div:first-child span{
        line-height: 38px;
        margin-left: 10px;
        color: #FFF;
    }

    /*.remember-me > div > div:last-child {*/
        /*line-height: 38px;*/
    /*}*/

    .temporary {
        color: #fff;
    }
</style>
<div id="particles-js">
    <canvas style="width: 100%; height: 100%;"></canvas>
</div>
<div class="container">
    <div class="login_box" id="login_box">
        <form class="form-signin form-horizontal" id="login-form">
            <h2 class="form-signin-heading">
                <img src="resource/img/login-logo.png" id="lgLogo">
            </h2>
<!--            <div class="form-group">-->
<!--                <div class="input-group">-->
<!--                    <span class="input-group-addon"><i class="icon iconfont"></i></span>-->
<!--                    <input type="text" class="form-control" placeholder="Client ID" name="client_id" value="--><?php //echo $output['client_id']?><!--" --><?php //echo $output['client_id'] ? '' : 'autofocus'?><!-- maxlength="6">-->
<!--                    <div class="error_msg_1"></div>-->
<!--                </div>-->
<!--            </div>-->
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="icon iconfont"></i></span>
                    <input type="text" class="form-control" placeholder="Account Name" name="user_code" value="<?php echo $output['user_code']?>" <?php echo $output['client_id'] ? 'autofocus' : ''?>>
                    <div class="error_msg_1"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="icon iconfont"></i></span>
                    <input type="password" class="form-control" placeholder="Password" name="user_password">
                    <div class="error_msg_1"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="alert alert-danger alert-dismissible fade in" role="alert" id="login_error">
                    <button type="button" class="close" onclick="hidediv(this);" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <div><strong></strong></div>
                </div>
            </div>
            <div class="form-group remember-me">
                <div class="input-group">
                    <div class="col-sm-6">
                        <input type="checkbox" class="form-control" name="remember_me" value="1" <?php echo $output['client_id'] ? 'checked' : ''?>>
                        <span>Remember me</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-lg btn-primary btn-block" type="button" onclick="login();">Sign In</button>
            </div>
        </form>
    </div>
    <div class="bottom_links" style="background:rgba(0,0,0,.5);padding-top:0px;">
        <ul style="color:#ffffff;margin-bottom:0;">
            <li><a href="javascript:;"><b>KHBuy™</b>© <span id="copyright">2015-2017 pos.khbuy.com</span></a></li>
            <li><a href="javascript:;"><span id="beian"></span></a></li>
            <li><a href="javascript:changeBgimg();">Background</a></li>
        </ul>
    </div>
</div>
<script src="resource/js/application.js"></script>
<script src="resource/js/particles.js"></script>
<script>
    document.onkeydown = function (event) {
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 13) {
            login();
        }
    };

    $(function () {
        changeBgimg();
    });

    function changeBgimg() {
        var n = Math.floor(Math.random() * 11) + 1;
        $("body").css("backgroundImage", 'url(resource/img/login-bg-' + n + '.png)');
        $("body").css("background-repeat", 'no-repeat');

        if (n == 10) {
//            $("body").css("background-size", 'auto 900px');
            $("body").css("background-size", 'auto 650px');
        } else if (n == 11) {
            $("body").css("background-size", 'auto 100%');
        } else {
            $("body").css("background-size", 'auto 650px');
        }
    }

    function hidediv(obj) {
        $(obj).parent().hide();
        $('.login_box_error').removeClass('login_box_error');
    }

    function login(){
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
                $('#login_error strong').html(_obj.MSG);
                $('#login_error').show();
                $('#login_box').addClass('login_box_error');
            }
        })
    }

    $('#login-form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
//            client_id : {
//                required : true,
//                digits: true
//            },
            user_code : {
                required : true
            },
            user_password : {
                required : true
            }
        },
        messages : {
//            client_id : {
//                required : '<?php //echo $lang['common_required']?>//',
//                digits: '<?php //echo $lang['entry_index_login_tips_1']?>//'
//            },
            user_code : {
                required : '<?php echo 'Required'?>'
            },
            user_password : {
                required : '<?php echo 'Required'?>'
            }
        }
    });

</script>