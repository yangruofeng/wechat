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
                                    <option value="855">+855</option>
                                    <option value="66">+66</option>
                                    <option value="86">+86</option>
                                </select>
                            </span>
                            <input type="text" class="form-control" id="phone" name="phone" value="" placeholder="">
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
            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Fingerprint Information</h5>
                </div>
                <div class="content">
                    <span style="margin-right: 5px;">Certification Status: </span>
                    <span id="status" style="font-weight: 600;margin-right: 20px"></span>
                    <span style="margin-right: 5px;">Certification Time: </span>
                    <span id="time" style="font-weight: 600;margin-right: 20px"></span>
                    <form class="form-horizontal" id="basic-info">
                        <input type="hidden" id="client_id" name="client_id" value="">
                        <input type="hidden" id="obj_uid" name="obj_uid" value="">
                    </form>
                    <div class="snapshot_div" id="feature_img" style="height: 140px;width: 120px" onclick="callWin_registerFinger('feature_img');">
                        <img src="resource/img/member/photo.png" style="width: 100px;height: 100px">
                        <div>Fingermark</div>
                    </div>
                </div>
            </div>
            <div class="operation" style="margin-bottom: 40px">
                <button class="btn btn-default" onclick="btn_reset()">Reset</button>
            </div>
        </div>
    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>
    $(function () {
        $('#btn_search').click(function () {
            search_click()
        })
    })

    function callWin_registerFinger(id) {
        var uid = $('input[name="obj_uid"]').val();
        if (!uid) {
            alert('Please select the client first.');
        }
        if (window.external) {
            try {
                var _img_path = window.external.registerFingerPrint(uid, "0");
                if (_img_path != "" && _img_path != null) {
                    $("#" + id + " img").attr("src", getUPyunImgUrl(_img_path));
                    $('#status').html('Registered');
                    $('#time').html('<?php echo timeFormat(Now())?>');
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }

    function btn_reset() {
        window.location.reload();
    }

    function search_click() {
        var country_code = $('select[name="country_code"]').val();
        var phone = $('#phone').val();
        if (!$.trim(phone)) {
            return;
        }

        yo.loadData({
            _c: 'member',
            _m: 'getClientFingermark',
            param: {country_code: country_code, phone: phone},
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('#member-icon').attr('src', data.member_icon ? data.member_icon : 'resource/img/member/bg-member.png');
                    $('#client_id').val(data.uid);
                    $('#obj_uid').val(data.obj_guid);
                    $('#login-account').html(data.login_code);
                    $('#khmer-name').html(data.kh_display_name);
                    $('#english-name').html(data.display_name);
                    $('#member-grade').html(data.grade_code);

                    $('#status').html(data.certification_status);
                    $('#time').html(data.certification_time);
                    $("#feature_img img").attr("src", upyun_url + data.feature_img);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#phone').bind('keydown',function(event){
        if(event.keyCode == "13") {
            search_click();
        }
    });
</script>