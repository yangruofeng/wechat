<style>
    body {
        display: flex;
        display: -webkit-flex;
        align-items: center;
        justify-content: center;
        -webkit-align-items: center;
        -webkit-justify-content: center;
        background: url(./resource/img/bg.jpg);
        background-repeat: no-repeat;
        /*background-position: center;*/
        min-width: 1200px;

    }

    .logo-wrapper{
        position: absolute;
        top:20px;
        left:10%;
    }

    .login_box {
        box-sizing: border-box;
        width: 400px;
        position: absolute;
        top: 25%;
        left: 55%;
        margin-left: -20px;
        color: #444;

    }

    .login_box_error {

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
        margin-left: 0;
        margin-right: 0;
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



    #btn_signin{
        background: #41d1ec;
        border-radius: 0;
        font-size: 18px;
        border-color: #252328;
        height: 45px;
        border: none;
    }

    #btn_signin:hover{
        background: #22baec;
    }

    .form-signin {
        width: 390px;
        margin: 0 auto;
    }

    .login_box .login-title{
        text-align: center;
        color: #737070;
    }

    .form-signin .form-control {
        width: 100%;
        border: 1px solid #615c5c;
        font-size: 14px;
        font-weight: 500;
        box-shadow: none;
        height: 40px;
        line-height: 40px;
        outline: none;
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
        width: 390px;
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
        display: inline-block;
        line-height: 40px;
        margin-left: 10px;
        margin-top: 3px;
    }

    .app-wrapper{
        margin-top: 80px;
        font-size: 14px;
    }

    .app-wrapper .app-tip{
        font-size: 18px;
        color: #41d1ec;s
    }

    .app-wrapper .app-tip img{
        vertical-align: baseline;
        margin-right: 5px;
    }

    .app-wrapper .app-link{
        margin-top: 10px;
        margin-left: 30px;
    }

    .app-wrapper .app-link a{

    }

    .qrcode-img{
        position: relative;
        z-index: 99;
    }

    .qrcode-img:hover{
        transform:scale(3);//设置缩放比例
        -ms-transform:scale(3);
        -webkit-transform:scale(3);
        -o-transform:scale(3);
        -moz-transform:scale(3);
        z-index: 9999;
    }




</style>

<div class="container">


    <div class="logo-wrapper">
        <div class="logo-img">
            <img src="resource/img/c-logo.png" alt="">
        </div>
    </div>

    <div class="login_box" id="login_box">
        <form class="form-signin form-horizontal" id="login-form">
            <h2 class="login-title">
                Log in
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
                <button id="btn_signin" class="btn btn-lg btn-primary btn-block" type="button" onclick="login();">Sign In</button>
            </div>
        </form>
<?php if(getConf("debug")==true){?>
    <div class="app-wrapper">
        <div class="app-tip">
            <img src="resource/img/download.png" alt=""> Download
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="app-link">
                    <?php $member_qrcode = ENTRY_API_SITE_URL.'/qrcode.image.create.php?content='.urlencode($output['member_app']['download_url']); ?>
                    <a href="<?php echo $output['member_app']['download_url']?:'#'; ?>">MEMBER APP</a>
                    <div >
                        <a href="<?php echo $member_qrcode; ?>" target="_blank">
                            <img class="qrcode-img" style="width: 150px;height: 150px;" src="<?php echo $member_qrcode; ?>" alt="">
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
                            <img class="qrcode-img" style="width: 150px;height: 150px;" src="<?php echo $co_qrcode; ?>" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>


    </div>
<?php }else{?>

<?php }?>

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