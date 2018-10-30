<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container">
        <div class="collection-div">
            <div class="basic-info">
                <?php include(template("widget/item.member.summary.v2"))?>
            </div>

            <div class="scene-photo">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Fingerprint Information</h5>
                </div>
                <div class="content">
                    <span style="margin-right: 5px;">Certification Status:</span>
                    <span id="status" style="font-weight: 600;margin-right: 20px"><?php echo $output['client_info']['certification_status']?></span>
                    <span style="margin-right: 5px;">Certification Time:</span>
                    <span id="time" style="font-weight: 600;margin-right: 20px"><?php echo $output['client_info']['certification_time']?:'N/A'; ?> </span>
                    <form class="form-horizontal" id="basic-info">
                        <input type="hidden" id="client_id" name="client_id" value="<?php echo $output['member_id']?>">
                        <input type="hidden" id="obj_uid" name="obj_uid" value="<?php echo $output['client_info']['obj_guid']?>">
                    </form>
                    <div class="snapshot_div" id="feature_img" style="height: 140px;width: 120px" onclick="callWin_registerFinger('feature_img');">
                        <img src="<?php echo getImageUrl($output['client_info']['feature_img'])?: 'resource/img/member/photo.png'?>" style="width: 100px;height: 100px">
                        <div>Fingermark</div>
                    </div>
                </div>
            </div>
            <div class="operation" style="margin-bottom: 40px">
                <a  class="btn btn-default"  href="<?php echo getUrl('member_index', 'index', array('member_id'=>$output['member_id']), false, ENTRY_COUNTER_SITE_URL) ?>"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
            </div>
        </div>

    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>

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



</script>