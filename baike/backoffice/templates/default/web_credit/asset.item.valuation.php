<style>
    .btn {
        border-radius: 0;
        padding: 5px 10px;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

    .ibox-title {
        padding-top: 12px!important;
        min-height: 40px;
    }
    .fw-600 {
        font-weight: 600;
    }
</style>
<?php
$client_info=$output['client_info'];
$asset=$output['asset'];
$history=$output['history'];
$co_avg=$output['co_avg'];
$default_item=$output['default_item'];
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
                            <td>Asset Name</td>
                            <td class="fw-600"><?php echo $asset['asset_name']?></td>
                            <td>Asset No.</td>
                            <td class="fw-600"><?php echo $asset['asset_sn']?></td>
                            <td>Type</td>
                            <td class="fw-600"><?php echo $output['asset_type']?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
        <div class="business-content">
            <div class="col-sm-7" style="padding-left: 0px">
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Evaluate</h5>
                    </div>
                    <div class="content">
                        <form id="frm_item"  method="POST" enctype="multipart/form-data" action="<?php echo getUrl('web_credit', 'submitAssetEvaluate', array(), false, BACK_OFFICE_SITE_URL);?>">
                            <input type="hidden" name="id" value="<?php echo $asset['uid']?>">
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Evaluation</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" name="valuation" value="<?php echo $default_item['evaluation']?>">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label">Remark</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="remark" value="<?php echo $default_item['remark']?>">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group" style="text-align: center">
                                <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                            </div>

                        </form>
                        <table class="table table-bordered">
                            <tr>
                                <td>Time</td>
                                <td>Evaluation</td>
                                <td>Remark</td>
                            </tr>
                            <?php if(count($history)){?>
                                <?php foreach($history as $item){?>
                                    <tr>
                                        <td><?php echo $item['evaluate_time']?></td>
                                        <td><?php echo $item['evaluation']?></td>
                                        <td><?php echo $item['remark']?></td>
                                    </tr>
                                <?php }?>
                            <?php }else{?>
                                <tr>
                                    <td colspan="5">
                                        <?php require template(":widget/no_record")?>
                                    </td>
                                </tr>
                            <?php }?>

                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-5"  style="padding-right: 0px">
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>CO-Average</h5>
                    </div>
                    <div class="content">
                        <?php if(count($co_avg)){?>
                            <table class="table">
                                <tr>
                                    <td>Item</td>
                                    <?php foreach($co_avg as $item){?>
                                        <td>
                                            <?php echo $item['operator_name']?>
                                        </td>
                                    <?php }?>
                                </tr>
                                <tr>
                                    <td>Evaluation</td>
                                    <?php foreach($co_avg as $item){?>
                                        <td>
                                            <?php echo $item['evaluation']?>
                                        </td>
                                    <?php }?>
                                </tr>
                                <tr>
                                    <td>Time</td>
                                    <?php foreach($co_avg as $item){?>
                                        <td>
                                            <?php echo $item['evaluate_time']?>
                                        </td>
                                    <?php }?>
                                </tr>
                                <tr>
                                    <td>Remark</td>
                                    <?php foreach($co_avg as $item){?>
                                        <td>
                                            <?php echo $item['remark']?>
                                        </td>
                                    <?php }?>
                                </tr>
                            </table>
                        <?php }else{?>
                            <?php require template(":widget/no_record")?>
                        <?php }?>

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






