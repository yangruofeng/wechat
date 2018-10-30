<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px !important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    .voting-list .fa {
        font-size: 18px;
        margin-left: 10px;
        color: #666666;
    }

    .voting-list .fa-check {
        color: #008000 !important;
    }

    .voting-list .fa-close {
        color: red !important;
    }

    .contract-img {
        padding: 3px 5px 3px 0;
    }

</style>
<?php
$cert_detail = $output['detail'];
$verify_field = enum_langClass::getCertificationTypeEnumLang();
$asset_evaluate = $data['asset_evaluate'];
$asset_rental = $data['asset_rental'];
$storage_list = $data['storage_list'];
$loan_list = $data['loan_list'];
$asset_arr = array(certificationTypeEnum::HOUSE,certificationTypeEnum::CAR,certificationTypeEnum::LAND,certificationTypeEnum::MOTORBIKE,certificationTypeEnum::STORE);
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Certification</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('data_center_certification', 'index', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Index</span></a>
                </li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="">
        <?php $client_info = $output['client_info']; ?>
        <div class="col-sm-6">
            <div class="col-sm-12">
                <?php require_once template('widget/item.member.summary'); ?>
            </div>
            <div class="col-sm-12" style="margin-top: 10px;">
                <div class="basic-info">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-list-ul"></i>Check Info</h5>
                    </div>

                    <div class="content voting-list">
                        <table class="table">
                            <tr>
                                <td><label for="">Certification Type</label></td>
                                <td><?php echo $verify_field[$cert_detail['cert_type']]; ?></td>
                            </tr>
                            <tr>
                                <td><label for="">Verify State</label></td>
                                <td><?php echo $lang['cert_state_' . $cert_detail['verify_state']];?></td>
                            </tr>
                            <tr>
                                <td><label for="">Create Time</label></td>
                                <td><?php echo timeFormat($cert_detail['create_time']);?></td>
                            </tr>
                            <?php if ($cert_detail['verify_state'] || $cert_detail['verify_state'] == -1) { ?>
                            <tr>
                                <td><label class="control-label">Auditor Name</label></td>
                                <td><?php echo $cert_detail['auditor_name'] ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Auditor Time</label></td>
                                <td><?php echo timeFormat($cert_detail['auditor_time']) ?></td>
                            </tr>
                            <?php }?>
                            <?php  if(!in_array($cert_detail['cert_type'],$asset_arr)) {?>
                            <tr>
                                <td><label for="">Certification Images</label></td>
                                <td>
                                    <?php
                                    $image_list = array();
                                    foreach($cert_detail['cert_images'] as $img_item){
                                        $image_list[] = array(
                                            'url' => $img_item['image_url'],
                                            'image_source' => $img_item['image_source'],
                                        );
                                    }
                                    include(template(":widget/item.image.viewer.list"));
                                    ?>
                                </td>
                            </tr>
                            <?php }?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php  if(in_array($cert_detail['cert_type'],$asset_arr)) {?>
        <div class="col-sm-6">
            <div class="business-content" style="padding-top: 10px;">
                <?php include(template("widget/item.asset.reference"))?>
            </div>
        </div>
        <?php }?>
    </div>
</div>
<script>
    function deleteCreditGrant(_uid) {
        if (!_uid) {
            return;
        }
        $.messager.confirm("<?php echo $lang['common_delete']?>", "<?php echo $lang['common_confirm_delete']?>", function (_r) {
            if (!_r) return;
            $(".page").waiting();
            yo.loadData({
                _c: "loan_committee",
                _m: "deleteCreditGrant",
                param: {uid: _uid},
                callback: function (_o) {
                    $(".page").unmask();
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.href = '<?php echo getUrl('loan_committee', 'grantCreditHistory', array(), false, BACK_OFFICE_SITE_URL) ?>';
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }
</script>