<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="collection-div">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
                </div>
                <div class="content">
                    <div class="col-sm-6">
                        <div class="input-group" style="width: 300px">
                            <span class="input-group-addon" style="padding: 0;border: 0;">
                                <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                    <option value="855" <?php echo $output['phone_arr'][0] == 855 ? 'selected' : ''?>>+855</option>
                                    <option value="66" <?php echo $output['phone_arr'][0] == 66 ? 'selected' : ''?>>+66</option>
                                    <option value="86" <?php echo $output['phone_arr'][0] == 86 ? 'selected' : ''?>>+86</option>
                                </select>
                            </span>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $output['phone_arr'][1];?>" placeholder="">
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
                    <div class="col-sm-6">
                        <dl class="account-basic clearfix">
                            <dt class="pull-left">
                                <p class="account-head">
                                    <img id="member-icon" src="resource/img/member/bg-member.png" class="avatar-lg">
                                </p>
                            </dt>
                            <dd class="pull-left margin-large-left">
                                <input type="hidden" id="client_id" name="client_id" value="">
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Login Account</span>:
                                    <span class="marginleft10" id="login-account"><?php echo $client_info['login_account']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Khmer Name</span>:
                                    <span class="marginleft10" id="khmer-name"><?php echo $client_info['kh_display_name']?></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">English Name</span>:
                                    <span class="marginleft10" id="english-name"><?php echo $client_info['display_name']?></span>
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
            <div class="authentication-information">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Authentication Information</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal">
                        <div class="personal-info">
                            <?php $identity_type = memberIdentityClass::getIdentityType(); ?>
                            <?php $i = 0;foreach ($identity_type as $key => $val) { ++$i?>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label"><?php echo $i == 1 ? 'Personal Information:' : ''?> </label>
                                    <div class="col-sm-8" id="identity_<?php echo $key?>">
                                        <?php if ($member_info['identity_list'][$key]) { ?>
                                            <i class="fa fa-check-square-o"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-square-o"></i>
                                        <?php } ?>
                                        <span><?php echo $val?></span>
                                        <a class="function" type="<?php echo $key?>">【Collect】</a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        search_click();

        $('#btn_search').click(function () {
            search_click()
        })

        $('.personal-info .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            var _type = $(this).attr('type');
            window.location.href = '<?php echo getUrl('member', 'uploadMemberCertificationPage', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id + '&type=' + _type;
        })

        $('#working_certificate .function').click(function () {
            var client_id = $('#client_id').val();
            if (!client_id) {
                return;
            }
            window.location.href = '<?php echo getUrl('member', 'workAuthentication', array('nav_op' => 'documentCollection'), false, ENTRY_COUNTER_SITE_URL);?>&client_id=' + client_id;
        })

    })

    $('#phone').bind('keydown',function(event){
        if(event.keyCode == "13") {
            search_click();
        }
    });

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
                    $('#english-name').html(data.display_name);
                    $('#member-grade').html(data.grade_code);
                    var identity_list = data.identity_list;
                    for (var i in identity_list) {
                        if(identity_list[i] > 0){
                            $('#identity_' + i + ' i').removeClass('fa-square-o').addClass('fa-check-square-o');
                        } else {
                            $('#identity_' + i + ' i').removeClass('fa-check-square-o').addClass('fa-square-o');
                        }
                    }

                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>