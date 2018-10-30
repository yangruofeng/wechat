<?php
if(!$data) $data=$output['data'];
$detail = $data['detail'];$client_authorized_history = $data['client_authorized_history'];
$asset_enum=(new certificationTypeEnum())->Dictionary();
?>
<div>
    <?php if($detail){ ?>
        <div class="record-info">
            <?php include(template("member_credit/client.credit.part.summary"))?>
            <div class="panel-tab custom-panel-tab">
                <ul class="nav nav-tabs record-tabs" role="tablist">
                    <li role="presentation" class="authorize-li active">
                        <a href="#tab_authorize" aria-controls="tab_authorize" role="tab" data-toggle="tab"><?php echo 'Authorize';?></a>
                    </li>

                    <li role="presentation" class="draft-li">
                        <a href="#tab_interest" aria-controls="tab_interest" role="tab" data-toggle="tab"><?php echo 'Interest & Currency';?></a>
                    </li>
                    <li role="presentation" class="history-li">
                        <a href="#tab_history" aria-controls="tab_history" role="tab" data-toggle="tab"><?php echo 'Contract List';?></a>
                    </li>
                    <li role="presentation" class="tab-detail-li" style="display: none;">
                        <a href="#tab_detail" aria-controls="tab_detail" role="tab" data-toggle="tab"><?php echo 'Authorized Contract Detail';?></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <?php include(template("member_credit/client.credit.part.authorize"))?>
                    <?php include(template("member_credit/client.credit.part.currency.interest"))?>
                    <?php include(template("member_credit/client.credit.part.history"))?>
                    <div role="tabpanel" class="tab-pane tab-detail-pane" id="tab_detail" style="display: none;">
                        <div class="contract-detail"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php }else{ ?>
        <div class="no-record">No Credit Record</div>
    <?php } ?>
</div>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>
    function callWin_snapshot_slave() {
        /*var img = 'avator/05747074622691598.jpg';
         $("#img_slave").attr("src", getUPyunImgUrl(img, "180x120"));
         $('#member_image').val(img);
         return*/
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#img_slave").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#member_image').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }

</script>