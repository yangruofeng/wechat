<style>
    .btn {
        border-radius: 0;
    }
    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

    .ibox-title {
        padding-top: 12px!important;
        min-height: 40px;
    }
</style>
<?php
$client_info=$output['client_info'];
$asset=$output['asset'];
$default_item=my_json_decode($asset['research_text']);
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('web_credit', 'assetItemDetail', array('asset_id'=>$asset['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Asset Detail</span></a></li>
                <li><a  class="current"><span>Evaluate</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 1200px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-condition">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Asset Info</h5>
                </div>
                <div class="content">
                    <table class="table table-bordered">
                        <tr>
                            <td>Asset Name:</td>
                            <td><?php echo $asset['asset_name']?></td>
                            <td>Asset No.</td>
                            <td><?php echo $asset['asset_sn']?></td>
                            <td>Type</td>
                            <td><?php echo $output['asset_type']?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
        <div class="business-content">
            <div class="col-sm-7">
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Evaluate</h5>
                    </div>
                    <div class="content">
                        <form id="frm_item"  method="POST" enctype="multipart/form-data" action="<?php echo getUrl('web_credit', 'submitAssetSurvey', array(), false, BACK_OFFICE_SITE_URL);?>">
                            <input type="hidden" name="asset_id" value="<?php echo $asset['uid']?>">
                            <?php
                                $survey_info=$asset['survey_info'];
                                $items=my_json_decode($survey_info['survey_json']);
                                $items_type=my_json_decode($survey_info['survey_json_type']);
                                $items_kh=my_json_decode($survey_info['survey_json_kh']);
                                if(count($items)){
                            ?>
                                    <?php foreach($items as $item_k=>$item_name){?>
                                        <div class="col-sm-12 form-group">
                                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>
                                                <?php echo $item_name;?>
                                            </label>
                                            <div class="col-sm-8">
                                                <?php if($items_type[$item_k]==assetSurveyType::DESCRIPTION){?>
                                                    <input type="text" class="form-control" name="item_<?php echo $item_k?>" value="<?php echo $default_item[$item_k]?>">
                                                <?php }else{?>
                                                    <input type="checkbox" name="item_<?php echo $item_k?>" <?php if($default_item[$item_k]) echo 'checked'?> value="1">
                                                <?php }?>
                                            </div>
                                        </div>
                                    <?php }?>
                                    <div class="col-sm-12 form-group">
                                        <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                        <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                                    </div>
                                <?php }else{?>
                                    <div>
                                        <h3>No Setting for Survey, Please Set Survey Target At Consol.</h3>
                                    </div>
                                    <div class="col-sm-12 form-group">
                                        <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                    </div>
                                <?php }?>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }
    function btn_submit_onclick() {
        if (!$("#frm_item").valid()) {
            return;
        }
        $("#frm_item").waiting();
        $("#frm_item").submit();

    }
    $('#frm_item').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            valuation: {
                required: true
            }
        },
        messages: {
            valuation: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
</script>






