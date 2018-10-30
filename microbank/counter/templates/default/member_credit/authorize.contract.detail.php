<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>
<style>
    .record-base-table>tbody>tr>td{
        padding: 2px;
    }
    .image-item{
        position: relative;
        display: inline;
    }
    .image-item img{
        position: relative;
    }
    .image-item .a-delete{
        right: -10px;top: -65px;position: absolute;width: 20px;height: 20px;border-radius: 60%;background-color: red;color: #ffffff
    }
    .image-item .a-delete .fa-close{
        top:3px;position: absolute;left: 6px
    }
    .authorize-detail-table .img{
        margin-bottom: 0px;
    }
    .text-small {
        margin-bottom: 0;
    }
</style>

<?php
$member_image = $output['contract']['member_img']?:$output['member_scene_image'];
?>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container" style="max-width: 1000px">
        <div >
            <?php require_once template('widget/item.member.summary.v2'); ?>
        </div>
        <?php $detail = $output['contract'];?>
        <form action="<?php echo getUrl('member_credit', 'editAuthorizeContract', array(), false, ENTRY_COUNTER_SITE_URL) ?>" method="post" id="authorizeForm">
            <input type="hidden" name="contract_id" value="<?php echo $detail['uid']?>">
            <input type="hidden" name="contract_images" id="contract_images">
            <input type="hidden" name="received_list" id="received_list">
            <input type="hidden" name="member_image" id="member_image" value="<?php echo $member_image ?>">
        </form>
        <div>
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Contract Information</h5>
            </div>
            <div class="content">

                <table class="table table-bordered authorize-detail-table">
                    <tr>
                        <td>
                            <label class="control-label">Contract SN</label>
                        </td>
                        <td>
                            <?php echo $detail['contract_no']; ?>
                        </td>
                        <td>
                            Contract State
                        </td>
                        <td>
                            <?php $enum_state=(new authorizedContractStateEnum())->Dictionary();
                                echo $enum_state[$detail['state']];
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Loan Fee</label>
                        </td>
                        <td>
                            <?php echo ncPriceFormat($detail['fee']); ?>
                        </td>
                        <td>
                            <label>Received Date</label>
                        </td>
                        <td>
                            <?php if($detail['is_paid']){?>
                                <i class="fa fa-ok"></i> <span><?php echo $detail['pay_time']?></span>
                            <?php }else{?>
                                <i class="fa fa-close"></i>
                            <?php }?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Teller</label>
                        </td>
                        <td>
                            <label><?php echo $detail['officer_name']?></label>
                            <em><?php echo $detail['create_time']?></em>
                        </td>
                        <td>
                            <label>Grant ID</label>
                        </td>
                        <td>
                            <label><?php echo $detail['grant_info']['uid']?></label>
                            <em><?php echo $detail['grant_info']['grant_time']?></em>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Scene Photo</label>
                        </td>
                        <td>


                            <?php if( $member_image ){?>
                                <?php
                                    $image_item=$member_image;
                                    include(template(":widget/item.image.viewer.item"))
                                ?>
                            <?php }else{if($detail['state']!=authorizedContractStateEnum::COMPLETE){?>
                                <div class="snapshot_div" onclick="callWin_snapshot_slave();">
                                    <img id="img_slave" src="resource/img/member/photo.png">
                                </div>
                            <?php }}?>
                        </td>
                        <td>
                            Contract Images
                        </td>
                        <td>
                            <div class="image-list contract-image-list clearfix" id="div_contract_images">
                                <?php
                                    $image_list=$detail['contract_images'];
                                    $viewer_width=240;
                                    include(template(":widget/item.image.viewer.list"));
                                ?>

                                <?php if($detail['state']!=authorizedContractStateEnum::COMPLETE){?>
                                    <div class="image-item snapshot_div" onclick="callWin_snapshot_contract();">
                                        <img src="resource/img/member/photo.png">
                                    </div>
                                <?php }?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="control-label">Mortgage List</label>
                        </td>
                        <td colspan="4" id="td_asset_list">
                            <?php if($detail['mortgages']){
                                    $cert_enum=(new certificationTypeEnum())->Dictionary();
                                ?>
                                <?php foreach ($detail['mortgages'] as $ck => $cv) { ?>
                                    <div class="asset-info-wrap clearfix">
                                        <div class="col-sm-12 form-group">
                                            <div class="col-sm-12 control-label">
                                                <div class="ck_wrap">
                                                    <span style="margin-right: 20px">Type:<b><?php  echo $cert_enum[$cv['asset_type']]; ?></b></span>
                                                    <span style="margin-right: 20px">Cert-Name:<b><?php echo $cv['asset_name']?></b></span>
                                                    <span style="margin-right: 20px">Cert-Id:<b><?php echo $cv['asset_sn']?></b></span>
                                                    <span style="margin-right: 20px">Cert-Type:<b><?php echo $cv['asset_cert_type'];?></b></span>
                                                    <span  style="margin-right: 20px">Owner:
                                                        <?php foreach ($cv['relative_list'] as $relative) { ?>
                                                            <label style="display: inline-block;margin: 0 15px 0 0;<?php echo $relative['relative_id'] == 0 ? 'font-style: italic' : ''?>">
                                                                <?php echo $relative['relative_id'] == 0 ? 'Own' : $relative['relative_name'];?>
                                                            </label>
                                                        <?php } ?>
                                                    </span>
                                                    <?php if(!$cv['is_received']){?>
                                                        <input type="checkbox" class="chk-received" uid="<?php echo $cv['uid']?>"/> Received
                                                    <?php }else{?>
                                                        <code>Already Received</code>
                                                    <?php }?>
                                                    <input type="checkbox" class="chk-print" value="<?php echo $cv['uid']?>"/> Print
                                                    <a onclick="print_contract_1(<?php echo $cv['uid']?>)" style="margin-left: 20px;background-color: #f9f2f4;padding: 3px 7px" >Print</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 form-group">
                                            <div class="col-sm-12">
                                                <div class="image-list clearfix">
                                                    <?php if($cv['image_path']){ ?>
                                                        <?php
                                                        $image_list=$cv['image_path'];
                                                        $viewer_width = 360;
                                                        include(template(":widget/item.image.viewer.list"));
                                                        ?>
                                                    <?php } ?>
                                                    <?php if(!$cv['is_received']){?>
                                                        <div class="image-item snapshot_div"
                                                             onclick="callWin_snapshot_asset(this);" >
                                                            <img class="asset-img" src="resource/img/member/photo.png">
                                                        </div>
                                                    <?php }?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php }else{ ?>
                                Not Mortgaged
                            <?php } ?>
                        </td>
                    </tr>
                </table>
                <?php if( !$output['is_can_cancel'] ){ ?>
                    <div class="alert alert-warning" style="margin: 5px 0;">
                        <h4>This credit contract can not cancel!</h4>
                    </div>
                <?php } ?>
                <div class="col-sm-12 form-group">
                    <div class="operation">
                        <a class="btn btn-default" href="<?php echo getUrl('member_credit', 'showClientCreditMain', array('member_id'=>$client_info['uid']), false, ENTRY_COUNTER_SITE_URL);?>" target="_self">Back</a>
                        <?php if($detail['state']!=authorizedContractStateEnum::COMPLETE){?>
                            <a class="btn btn-primary" onclick="updateAuthorize();">Update</a>
                        <?php }?>
                        <a class="btn btn-default" onclick="print_contract(<?php echo $detail['uid']?>);">Print Contract</a>
                        <a class="btn btn-default" onclick="print_collateral(<?php echo $detail['uid']?>);">Print Collateral Receipt</a>
                        <?php if( $output['is_can_cancel'] ){ ?>
                            <a href="<?php echo getUrl('member_credit','cancelAuthorizeContract',array(
                                'uid' => $detail['uid']
                            ),false,ENTRY_COUNTER_SITE_URL); ?>" class="btn btn-default">
                                Cancel
                            </a>
                        <?php  } ?>
                    </div>

                </div>
            </div>
         </div>
    </div>
</div>
<?php require_once template('widget/app.config.js'); ?>
<?php include(template(":widget/item.image.viewer.js"));?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>

<script>
    function remove_img_item(e){
        $(e).closest(".image-item").remove();
    }
    function callWin_snapshot_slave() {

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
    function callWin_snapshot_asset(_e){
        if(window.external){
            try{
                var _img_path = window.external.getSnapshot("0");
                if(_img_path != "" && _img_path != null){
                    var _item = '<div class="image-item"><img class="img" src="'+getUPyunImgUrl(_img_path, '180x120')+'"><input type="hidden" class="asset-img-url" value="'+_img_path+'"><a class="a-delete" onclick="remove_img_item(this)"><i class="fa fa-close"></i></a></div>';
                    var _el=$(_e).closest(".image-list");
                    _el.prepend(_item);
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    function callWin_snapshot_contract(){
        if(window.external){
            try{
                var _img_path = window.external.getSnapshot("0");
                if(_img_path != "" && _img_path != null){
                    var _item = '<div class="image-item snapshot_div"><img class="img" src="'+getUPyunImgUrl(_img_path, '180x120')+'"><input type="hidden" class="contract-img-url" value="'+_img_path+'"><a class="a-delete" onclick="remove_img_item(this)"><i class="fa fa-close"></i></a></div>';
                    $('#div_contract_images').prepend(_item);
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    function calculateAssetsList(){
        var _assets = [];
        $("#td_asset_list").find(".chk-received").each(function(){
            if($(this).prop('checked')){
                var _member_asset_id = $(this).attr('uid');
                var _img_arr = [];
                $(this).closest('.asset-info-wrap').find(".image-list").find('.asset-img-url').each(function(){
                    _img_arr.push($(this).val());
                });
                var _item = {};
                _item.asset_mortgage_id = _member_asset_id;
                _item.is_received=1;
                _item.asset_images = _img_arr;
                _assets.push(_item);
            }
        });
        return _assets;
    }
    function updateAuthorize(){
        //处理资产
        var _assets=calculateAssetsList();
        _assets = encodeURI(JSON.stringify(_assets));
        $('#received_list').val(_assets);
        //处理合同图片
        var _contract_img_list=[];
        $("#div_contract_images").find(".image-item").find(".contract-img-url").each(function(){
            _contract_img_list.push($(this).val());
        });
        _contract_img_list=_contract_img_list.join(",");
        $("#contract_images").val(_contract_img_list);


        $('#authorizeForm').waiting();
        $('#authorizeForm').submit();
    }

    function print_collateral(contract_id) {
        var mortgage_id = '';
        $('.chk-print').each(function () {
            if ($(this).is(":checked")) {
                var _id = $(this).val();
                if (mortgage_id) {
                    mortgage_id += '_' + _id;
                } else {
                    mortgage_id += _id;
                }
            }
        });

//        window.location.href = "<?php //echo getUrl('print_form', 'printCollateral', array(), false, ENTRY_COUNTER_SITE_URL)?>//&contract_id=" + contract_id + "&mortgage_id=" + mortgage_id;
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printCollateral', array(), false, ENTRY_COUNTER_SITE_URL)?>&contract_id=" + contract_id + "&mortgage_id=" + mortgage_id);
    }
    function print_contract(_contract_id){
//        window.location.href = "<?php //echo getUrl('print_form', 'printCreditAgreement', array(), false, ENTRY_COUNTER_SITE_URL)?>//&contract_id=" + _contract_id
        if(window.external){
            window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printCreditAgreement', array(), false, ENTRY_COUNTER_SITE_URL)?>&contract_id=" + _contract_id);
        }
    }

    function print_contract_1(uid) {
        if(window.external){
            window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printAssetMortgage', array(), false, ENTRY_COUNTER_SITE_URL)?>&uid=" + uid);
        }

//        window.location.href = "<?php //echo getUrl('print_form', 'printAssetMortgage', array(), false, ENTRY_COUNTER_SITE_URL)?>//&uid=" + uid;
    }
</script>
