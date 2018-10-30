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
$history=$output['pending_receive'];
$asset=$output['asset'];
?>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container" style="margin-top: 10px;max-width: 1200px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>

        <div class="business-content">
<!--            <div class="col-sm-6" style="padding: 10px 5px 10px 0px">-->
<!--                <div class="basic-info container" style="margin-top: 10px">-->
<!--                    <div class="ibox-title" style="background-color: #DDD">-->
<!--                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Transfer</h5>-->
<!--                    </div>-->
<!--                    <div class="content">-->
<!--                        <form id="frm_item"  method="POST" enctype="multipart/form-data" action="--><?php //echo getUrl('mortgage', 'submitTransferToTeller', array(), false, ENTRY_COUNTER_SITE_URL);?><!--">-->
<!--                            <input type="hidden" name="asset_id" value="--><?php //echo $asset['uid']?><!--">-->
<!--                            <div class="col-sm-12 form-group">-->
<!--                                <label class="col-sm-3 control-label">Receiver</label>-->
<!--                                <div class="col-sm-8">-->
<!--                                    <select class="form-control" name="receiver_id">-->
<!--                                        <option value="0">Please Select</option>-->
<!--                                        --><?php //foreach($output['receiver_list'] as $receiver){
//                                            if($receiver['uid']==$output['token_uid']) continue;
//                                            ?>
<!--                                            <option value="--><?php //echo $receiver['uid']?><!--">--><?php //echo $receiver['user_name']?><!--(--><?php //echo $receiver['user_code']?><!--)</option>-->
<!--                                        --><?php //}?>
<!---->
<!--                                    </select>-->
<!--                                    <div class="error_msg"></div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-sm-12 form-group">-->
<!--                                <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>-->
<!--                                <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>-->
<!--                            </div>-->
<!---->
<!--                        </form>-->
<!--                        <table class="table table-bordered">-->
<!--                            <tr>-->
<!--                                <td>Time</td>-->
<!--                                <td>To Receiver</td>-->
<!--                                <td>To Branch</td>-->
<!--                                <td>State</td>-->
<!--                                <td>Function</td>-->
<!--                            </tr>-->
<!--                            --><?php //if(count($history)){?>
<!--                                --><?php //$request_state_list=(new assetRequestWithdrawStateEnum())->Dictionary();?>
<!--                                --><?php //foreach($history as $item){?>
<!--                                    <tr>-->
<!--                                        <td>--><?php //echo $item['create_time']?><!--</td>-->
<!--                                        <td>--><?php //echo $item['to_operator_name']?><!--</td>-->
<!--                                        <td>--><?php //echo $item['to_branch_name']?><!--</td>-->
<!--                                        <td>--><?php //echo $request_state_list[$item['state']]?><!--</td>-->
<!--                                        <td>-->
<!--                                            <a class="btn btn-default" href="--><?php //echo getUrl("mortgage","submitDeletePendingReceiveOfTransfer",array("request_id"=>$item['uid'],"asset_id"=>$item['member_asset_id']),false,ENTRY_COUNTER_SITE_URL)?><!--">Delete</a>-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                --><?php //}?>
<!--                            --><?php //}else{?>
<!--                                <tr>-->
<!--                                    <td colspan="5">-->
<!--                                        --><?php //require template(":widget/no_record")?>
<!--                                    </td>-->
<!--                                </tr>-->
<!--                            --><?php //}?>
<!---->
<!--                        </table>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            <div class="col-sm-12" style="padding: 10px 0px 10px 5px">
                <?php include(template("widget/item.asset.reference2"))?>
            </div>

            <div class="col-sm-12" style="margin: 20px 0px;text-align: center">
                <button type="button" class="btn btn-default" style="min-width: 80px;" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
            </div>

        </div>
    </div>
</div>







