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
                        <div class="input-group" style="width: 320px">
                            <span class="input-group-addon" style="padding: 0;border: 0;">
                                <select class="form-control" name="search_type" style="min-width: 95px;height: 30px">
                                    <option value="id_card">Id-Card</option>
                                    <option value="cid">CID</option>
                                </select>
                            </span>
                            <input type="text" class="form-control" name="search_text" id="search_text" value="" placeholder="">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                    <i class="fa fa-search"></i>
                                    Search
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <dl class="account-basic clearfix">
                            <dt class="pull-left">
                            <p class="account-head">
                                <img id="staff_icon" src="resource/img/member/bg-member.png" class="avatar-lg">
                            </p>
                            </dt>
                            <dd class="pull-left margin-large-left">
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">CID</span>:
                                    <span class="marginleft10" id="obj_guid"></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Display Name</span>:
                                    <span class="marginleft10" id="display_name"></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Id-Card</span>:
                                    <span class="marginleft10" id="id_card_number"></span>
                                </p>
                                <p class="text-small">
                                    <span class="show pull-left base-name marginright3">Status</span>:
                                    <span class="marginleft10" id="staff_status"></span>
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
                    <span id="status" style="font-weight: 600;margin-right: 20px">--</span>
                    <span style="margin-right: 5px;">Certification Time: </span>
                    <span id="time" style="font-weight: 600;margin-right: 20px">--</span>
                    <form class="form-horizontal" id="basic-info">
                        <input type="hidden" name="staff_id" value="">
                        <input type="hidden" name="obj_uid" value="">
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

    function search_click() {
        var search_type = $('select[name="search_type"]').val();
        var search_text = $('#search_text').val();
        if (!$.trim(search_text)) {
            return;
        }

        yo.loadData({
            _c: 'staff',
            _m: 'getStaffFingermark',
            param: {search_type: search_type, search_text: search_text},
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('#staff_icon').attr('src', data.staff_icon ? data.staff_icon : 'resource/img/member/bg-member.png');
                    $('#obj_guid').text(data.obj_guid);
                    $('#display_name').text(data.display_name);
                    $('#id_card_number').text(data.id_card_number);
                    $('#staff_status').text(data.staff_status);
                    $('input[name="staff_id"]').val(data.uid);
                    $('input[name="obj_uid"]').val(data.obj_guid);

                    $('#status').html(data.certification_status);
                    $('#time').html(data.certification_time);
                    $("#feature_img img").attr("src", data.feature_img ? (upyun_url + data.feature_img) : 'resource/img/member/photo.png');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function btn_reset() {
        window.location.reload();
    }

    function callWin_registerFinger(id) {
        var uid = $('input[name="obj_uid"]').val();
        if (!uid) {
            alert('Please select the staff first.');
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

    $('#search_text').bind('keydown',function(event){
        if(event.keyCode == "13") {
            search_click();
        }
    });
</script>