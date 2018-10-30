<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group{
        margin-bottom: 20px;
    }

    .container{
        width: 800px!important;
    }

    .mincontent{
        padding:15px
    }

</style>
<?php $client_info = $output['client_info'];?>
<?php $memberStateLang =  enum_langClass::getMemberStateLang() ?>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="collection-div">
        <div class="basic-info container">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
            </div>
            <div class="content">
                <div class="col-sm-6 mincontent">
                    <div class="input-group" style="width: 300px">
                        <span class="input-group-addon" style="padding: 0;border: 0;">
                            <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                <option value="855" <?php echo $client_info['phone_country'] == 855 ? 'selected' : ''?>>+855</option>
                                <option value="66" <?php echo $client_info['phone_country'] == 66 ? 'selected' : ''?>>+66</option>
                                <option value="86" <?php echo $client_info['phone_country'] == 86 ? 'selected' : ''?>>+86</option>
                            </select>
                        </span>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $client_info['phone_number'];?>" placeholder="">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                <i class="fa fa-search"></i>
                                Search
                            </button>
                        </span>
                    </div>
                    <div class="search-other">
                        <img src="resource/img/member/phone.png">
                        <img src="resource/img/member/qr-code.png">
                        <img src="resource/img/member/bank-card.png">
                    </div>
                </div>
                <div class="col-sm-6 mincontent">
                    <dl class="account-basic clearfix">
                        <dt class="pull-left">
                        <p class="account-head">
                            <img id="member-icon" src="resource/img/member/bg-member.png" class="avatar-lg">
                        </p>
                        </dt>
                        <dd class="pull-left margin-large-left">
                            <input type="hidden" id="client_id" name="client_id" value="<?php echo intval($client_info['uid'])?>">
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Login Account</span>:
                                <span class="marginleft10" id="login-account"><?php echo $client_info['login_code']?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Khmer Name</span>:
                                <span class="marginleft10" id="khmer-name"><?php echo $client_info['kh_display_name']?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Member State</span>:
                                <span class="marginleft10" id="member_state"><?php echo $memberStateLang[$output['client_info']["member_state"]] ?></span>
                            </p>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright3">Member Grade</span>:
                                <span class="marginleft10" id="member-grade"><?php echo $client_info['grade_code']?></span>
                            </p>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="basic-info container">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Function</h5>
            </div>
            <div class="content">
                <div class="col-sm-12 mincontent" style="text-align: center">
                    <div>
<!--                        <div class="form-group">-->
<!--                            <div class="col-sm-4">-->
<!--                                <button type="button" --><?php //echo $client_info['uid'] ? : 'disabled'?><!-- class="btn btn-success" onclick="changeLoginPwd()" href="#">--><?php //echo 'Change Login Password' ?><!--</button>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="form-group">
                            <div class="col-sm-4">
                                <button type="button" <?php echo $client_info['uid'] ? : 'disabled'?> class="btn btn-success" style="width: 175px!important" onclick="unlock()"><?php echo 'Unlock' ?></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <button type="button" <?php echo $client_info['uid'] ? : 'disabled'?> class="btn btn-success"  style="width: 175px!important"  onclick="changeTradePwd()" ><?php echo 'Change Trading Password' ?></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <button type="button" <?php echo $client_info['uid'] ? : 'disabled'?> class="btn btn-success" style="width: 175px!important"  onclick='changePhoneNum()'><?php echo 'Change Phone number' ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        $('#btn_search').click(function () {
            search_click()
        })
    })
    $('#phone').bind('keydown', function (event) {
        if (event.keyCode == "13") {
            search_click();
        }
    });

    function changeLoginPwd() {
        var id = $('#client_id').val();
        if (id > 0) {
            window.location.href = "<?php echo getUrl('member', 'changeLoginPwd', array(), false, ENTRY_COUNTER_SITE_URL) ?>&uid=" + id;
        }else {
            alert('Please input phone number')
        }

    }

    function unlock() {
        var id = $('#client_id').val();
        var state = $('#member_state').html();
        if(state != 'Temp Locking'){
            alert("State Inappropriate");
            return
        }
        if (id > 0) {
            window.location.href = "<?php echo getUrl('member', 'memberUnlock', array(), false, ENTRY_COUNTER_SITE_URL) ?>&uid=" + id;
        }else {
            alert('Please input phone number')
        }

    }

    function changeTradePwd() {
        var id = $('#client_id').val();
        if (id > 0) {
            window.location.href = "<?php echo getUrl('member', 'changeTradePwd', array(), false, ENTRY_COUNTER_SITE_URL) ?>&uid=" + id;
        }else {
            alert('Please input phone number')
        }
    }

    function changePhoneNum() {
        var id = $('#client_id').val();
        if (id > 0) {
            window.location.href = "<?php echo getUrl('member', 'changePhoneNum', array(), false, ENTRY_COUNTER_SITE_URL) ?>&uid=" + id;
        }else {
            alert('Please input phone number')
        }
    }

    function search_click() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.loadData({
            _c: 'member',
            _m: 'getClientInfo',
            param: {country_code: country_code, phone: phone},
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('#member-icon').attr('src', data.member_icon ? data.member_icon : 'resource/img/member/bg-member.png');
                    $('#client_id').val(data.uid);
                    $('#login-account').html(data.login_code);
                    $('#khmer-name').html(data.kh_display_name);
                    $('#member_state').html(data.member_state_text);
                    $('#member-grade').html(data.grade_code);
                    $('.btn-success').attr('disabled', false);
                } else {
                    alert(_o.MSG);
                    $('.btn-success').attr('disabled', true);
                }
            }
        });
    }
</script>



