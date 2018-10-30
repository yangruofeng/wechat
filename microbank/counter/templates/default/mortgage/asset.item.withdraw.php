<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>
<style>
    .text-small {
        margin-bottom: 0;
    }

    .content{
        padding-bottom: 10px;
    }

    .clearfix{
        margin-bottom: 0px;
    }
</style>
<?php
$client_info=$output['client_info'];
$asset=$output['asset'];
$request_item=$output['request_item'];

?>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container" style="margin-top: 10px;max-width: 1200px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>

        <div class="business-content">
            <div class="col-sm-6" style="padding: 10px 5px 10px 0px">
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Withdraw</h5>
                    </div>
                    <?php if($output['is_last_holder']){?>
                        <div class="content">
                            <form id="frm_item"  method="POST" enctype="multipart/form-data" action="<?php echo getUrl('mortgage', 'submitWithdrawByClient', array(), false, ENTRY_COUNTER_SITE_URL);?>">
                                <input type="hidden" name="request_id" value="<?php echo $request_item['uid']?>">
                                <input type="hidden" name="member_image" id="member_image">
                                <input type="hidden" name="contract_images" id="contract_images">
                                <input type="hidden" name="agent_id1" id="agent_id1_image">
                                <input type="hidden" name="agent_id2" id="agent_id2_images">
                                <input type="hidden" name="authorization_cert" id="authorization_cert_image">
                                <input type="hidden" name="mortgage_cert" id="mortgage_cert_images">
                            </form>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label">Request Time</label>
                                <span class="col-sm-4"><?php echo $request_item['create_time']?></span>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label">Auditor</label>
                                <span class="col-sm-4"><?php echo $request_item['auditor_name']?></span>
                            </div>

                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label">Is Self</label>
                                <span class="col-sm-4"> <input type="radio" name="is_self" value="self" checked> Self  <input type="radio" name="is_self" value="agent">Agent</span>
                            </div>

                            <div id="self">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Scene Photo</label>
                                    <div class="col-sm-4">
                                        <div class="snapshot_div" onclick="callWin_snapshot_slave();">
                                            <img id="img_slave" src="resource/img/member/photo.png">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Contract Image</label>
                                    <div class="col-sm-4">
                                        <div class="image-list contract-image-list clearfix" id="div_contract_images">
                                            <div class="image-item snapshot_div" onclick="callWin_snapshot_contract();">
                                                <img id="img_contract" src="resource/img/member/photo.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="agent" style="display: none">
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Agent Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="agent_name" value="" style="width: 180px">
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Agent ID</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="agent_name" value="" style="width: 180px">
                                    </div>
                                </div>

                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>ID Front</label>
                                    <div class="col-sm-4">
                                        <div class="image-list contract-image-list clearfix" id="div_id_1_images">
                                            <div class="image-item snapshot_div" onclick="callWin_snapshot_id_1();">
                                                <img id="img_id1" src="resource/img/member/photo.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>ID Back</label>
                                    <div class="col-sm-4">
                                        <div class="image-list contract-image-list clearfix" id="div_id_2_images">
                                            <div class="image-item snapshot_div" onclick="callWin_snapshot_id_2();">
                                                <img id="img_id2" src="resource/img/member/photo.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Authorization Cert</label>
                                    <div class="col-sm-4">
                                        <div class="image-list contract-image-list clearfix" id="div_authorization_cert_images">
                                            <div class="image-item snapshot_div" onclick="callWin_snapshot_authorization_cert();">
                                                <img id="img_authorization_cert" src="resource/img/member/photo.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Mortgage Cert</label>
                                    <div class="col-sm-4">
                                        <div class="image-list contract-image-list clearfix" id="div_mortgage_cert_images">
                                            <div class="image-item snapshot_div" onclick="callWin_snapshot_mortgage_cert();">
                                                <img id="img_mortgage_cert" src="resource/img/member/photo.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-12 form-group" style="text-align: center">
                                <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                            </div>
                        </div>
                    <?php }else{?>
                        <div class="content">
                            <h3>You are not the current holder,Client Can't withdraw this asset from you!</h3>
                        </div>
                    <?php }?>

                </div><div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Mortgage</h5>
                    </div>
                    <?php $mortgage=$output['mortgage']?>
                    <div class="content">
                        <table class="table table-no-brackground">
                            <tr>
                                <td>Contract No.</td>
                                <td><label><?php echo $mortgage['contract_no']?></label></td>
                                <td>Operator</td>
                                <td><label><?php echo $mortgage['operator_name']?></label></td>
                            </tr>
                            <tr>
                                <td colspan="10">
                                    <ul class="list-inline">
                                        <?php foreach($mortgage['image_list'] as $img){?>
                                            <li class="list-group-item">
                                                <a href="<?php echo getImageUrl($img['image_path'])?>" target="_blank">
                                                    <img src="<?php echo getImageUrl($img['image_path'], imageThumbVersion::MAX_120)?>">
                                                </a>
                                            </li>
                                        <?php }?>                                        
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-6" style="padding: 10px 0px 10px 5px">
               <?php include(template("widget/item.asset.reference"))?>
            </div>

        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script>
    function remove_img_item(e) {
        $(e).closest(".image-item").remove();
    }
    
    function btn_back_onclick(){
        window.history.back(-1);
    }
    function btn_submit_onclick() {
        //处理资产

        //处理合同图片
        var _contract_img_list=[];
        $("#div_contract_images").find(".image-item").find(".contract-img-url").each(function(){
            _contract_img_list.push($(this).val());
        });
        _contract_img_list=_contract_img_list.join(",");
        $("#contract_images").val(_contract_img_list);

        var _member_img=$("#member_image").val();
//        if(!_member_img || _member_img==''){
//            alert("Require to Snapshot of Client");
//            return;
//        }

        $('#frm_item').waiting();
        $('#frm_item').submit();
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
    function callWin_snapshot_contract(){
        if(window.external){
            try{
                var _img_path = window.external.getSnapshot("0");
                if(_img_path != "" && _img_path != null){
                    var item = '<div class="image-item snapshot_div"><img class="img" src="'+getUPyunImgUrl(_img_path, '180x120')+'"><input type="hidden" class="contract-img-url" value="'+_img_path+'"><a class="btn a-delete" onclick="remove_img_item(this)"><i class="fa fa-close"></i></a></div>';
                    $('#div_contract_images').prepend(item);
                    $('#contract_images').val(_img_path);
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }

    }



    function callWin_snapshot_id_1() {
        if(window.external){
            try{
                var _img_path = window.external.getSnapshot("0");
                if (_img_path != "" && _img_path != null) {
                    $("#img_id1").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#agent_id1_image').val(_img_path);
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }

    function callWin_snapshot_id_2() {
        if(window.external){
            try{
                var _img_path = window.external.getSnapshot("0");
                if (_img_path != "" && _img_path != null) {
                    $("#img_id2").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#agent_id2_images').val(_img_path);
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }

    function callWin_snapshot_authorization_cert() {
        if(window.external){
            try{
                var _img_path = window.external.getSnapshot("0");
                if(_img_path != "" && _img_path != null){
                    var item = '<div class="image-item snapshot_div"><img class="img" src="'+getUPyunImgUrl(_img_path, '180x120')+'"><input type="hidden" class="contract-img-url" value="'+_img_path+'"><a class="btn a-delete" onclick="remove_img_item(this)"><i class="fa fa-close"></i></a></div>';
                    $('#div_authorization_cert_images').prepend(item);
                    $('#authorization_cert_image').val(_img_path);
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }

    function callWin_snapshot_mortgage_cert() {
        if(window.external){
            try{
                var _img_path = window.external.getSnapshot("0");
                if(_img_path != "" && _img_path != null){
                    var item = '<div class="image-item snapshot_div"><img class="img" src="'+getUPyunImgUrl(_img_path, '180x120')+'"><input type="hidden" class="contract-img-url" value="'+_img_path+'"><a class="btn a-delete" onclick="remove_img_item(this)"><i class="fa fa-close"></i></a></div>';
                    $('#div_mortgage_cert_images').prepend(item);
                    $('#mortgage_cert_images').val(_img_path);
                }else{
                    alert("Failed to get image path");
                }
            }catch (ex){
                alert(ex.Message);

            }
        }
    }
    
    $("input[name='is_self']").bind('change',function (){
        $('#self').hide();
        $('#agent').hide();
       var type = $("input[name='is_self']:checked").val();
       if(type=='self'){
           $('#self').show();
           $('#agent').hide()
       }
       if(type=='agent')
       {
           $('#self').hide();
           $('#agent').show()
       }
    })
</script>






