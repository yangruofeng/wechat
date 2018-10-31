<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/client.css?v=4" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Member Setting</h3>
        </div>
    </div>
    <div class="container">
        <div class="col-sm-12 col-md-10 col-lg-9">
            <div class="basic-info block-model">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Basic Information</h5>
                </div>
                <div class="content">
                    <div class="col-sm-6">
                        <div class="input-group search-group" style="width: 300px">
                            <span class="input-group-addon" style="padding: 0;border: 0;">
                                <select class="form-control" name="country_code" style="min-width: 80px;">
                                    <option <?php if($_GET['country_code'] == 855){ echo 'selected';}?> value="855">+855</option>
                                    <option <?php if($_GET['country_code'] == 66){ echo 'selected';}?> value="66">+66</option>
                                    <option <?php if($_GET['country_code'] == 86){ echo 'selected';}?> value="86">+86</option>
                                </select>
                            </span>
                            <input type="text" class="form-control phone" id="s_phone" name="s_phone" value="<?php echo $_GET['phone']; ?>" placeholder="">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" id="btn_search">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                            </span>
                        </div>
                        <div class="search-other clearfix">
                            <img src="resource/image/phone.png">
                            <img src="resource/image/qr-code.png">
                            <img src="resource/image/bank-card.png">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <dl class="account-basic clearfix">
                            <dt class="pull-left">
                            <p class="account-head">
                                <img id="member-icon" src="resource/image/bg-member.png" class="avatar-lg">
                            </p>
                            </dt>
                            <dd class="pull-left margin-large-left">
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
                                    <span class="marginleft10" id="member-grade"><?php echo $client_info['member_grade']?></span>
                                </p>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-9">
            <div class="setting-handle block-model">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Setting</h5>
                </div>
                <div class="content">
                    <div class="setting-info">
                        <div class="no-search">please search member.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#btn_search').click(function () {
        search_click();
    });
    function search_click() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#s_phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.loadData({
            _c: 'dev',
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
                    handle(data.uid);
                } else {
                    $('#member-icon').attr('src','resource/image/bg-member.png');
                    $('#client_id').val('');
                    $('#login-account').html('');
                    $('#khmer-name').html('');
                    $('#english-name').html('');
                    $(".setting-info").html('<div class="no-search">please search member.</div>');
                    alert(_o.MSG);
                }
            }
        });

        
    }

    function handle(id){
        yo.dynamicTpl({
            tpl: "dev/member.setting.handle",
            dynamic: {
                api: "dev",
                method: "getClientCreditInfo",
                param: { id: id}
            },
            callback: function (_tpl) {
                $(".setting-info").html(_tpl);
            }
        });
    }
</script>