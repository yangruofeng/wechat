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
$history=$output['request_list'];
$current_request=$output['current_request'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan_committee', 'approveWithdrawMortgageRequest', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a  class="current"><span>Request Withdraw</span></a></li>
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
            <div class="col-sm-6">
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Request History</h5>
                    </div>
                    <div class="content">
                        <form id="frm_item"  method="POST" enctype="multipart/form-data" action="<?php echo getUrl('web_credit', 'submitAssetRequestWithdraw', array(), false, BACK_OFFICE_SITE_URL);?>">
                            <input type="hidden" name="member_asset_id" value="<?php echo $asset['uid']?>">
                            <input type="hidden" name="request_id" value="<?php echo $default_item['uid']?>">
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label">Request Time</label>
                                <div class="col-sm-8">
                                    <?php echo $current_request['create_time']?>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label">Creator</label>
                                <div class="col-sm-8">
                                    <?php echo $current_request['creator_name']?>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label">Remark</label>
                                <div class="col-sm-8">
                                    <?php echo $current_request['remark']?>
                                </div>
                            </div>
                            <?php if($current_request['state']==assetRequestWithdrawStateEnum::PENDING_APPROVE){?>
                                <div class="col-sm-12 form-group">
                                    <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                    <a  href="<?php echo getUrl("loan_committee","submitApproveRequestWithdrawMortgage",array("uid"=>$current_request['uid'],"state"=>assetRequestWithdrawStateEnum::PENDING_WITHDRAW),false,BACK_OFFICE_SITE_URL)?>" class="btn btn-primary"><i class="fa fa-check"></i>Approve</a>
                                    <a  href="<?php echo getUrl("loan_committee","submitApproveRequestWithdrawMortgage",array("uid"=>$current_request['uid'],"state"=>assetRequestWithdrawStateEnum::REJECT),false,BACK_OFFICE_SITE_URL)?>" class="btn btn-default"><i class="fa fa-check"></i>Reject</a>

                                </div>
                            <?php }else{?>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-3 control-label">Audit Time</label>
                                    <div class="col-sm-8">
                                        <?php echo $current_request['audit_time']?>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-3 control-label">Auditor</label>
                                    <div class="col-sm-8">
                                        <?php echo $current_request['auditor_name']?>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-3 control-label">State</label>
                                    <div class="col-sm-8">
                                        <?php
                                        $state_list=(new assetRequestWithdrawStateEnum())->Dictionary();
                                        echo $state_list[$current_request['state']]?>
                                    </div>
                                </div>

                            <?php }?>

                        </form>
                    </div>
                </div>
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Request History</h5>
                    </div>
                    <div class="content">
                        <table class="table table-bordered">
                            <tr>
                                <td>Time</td>
                                <td>Creator</td>
                                <td>Remark</td>
                                <td>State</td>
                            </tr>
                            <?php if(count($history)){?>
                                <?php $request_state_list=(new assetRequestWithdrawStateEnum())->Dictionary();?>
                                <?php foreach($history as $item){?>
                                    <tr>
                                        <td><?php echo $item['create_time']?></td>
                                        <td><?php echo $item['creator_name']?></td>
                                        <td><?php echo $item['remark']?></td>
                                        <td><?php echo $request_state_list[$item['state']]?></td>
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
            <div class="col-sm-6">
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Storage FLow</h5>
                    </div>
                    <?php $storage_list=$output['storage_list'];$flow_type=(new assetStorageFlowType())->Dictionary();?>
                    <div class="content">
                        <?php if(count($storage_list)){?>
                            <table class="table table-bordered">
                                <tr>
                                    <td>Time</td>
                                    <td>From</td>
                                    <td>To(Holder)</td>
                                    <td>Type</td>
                                </tr>
                                <?php foreach($storage_list as $item){?>
                                    <tr>
                                        <td><?php echo $item['create_time']?></td>
                                        <td><label><?php echo $item['from_operator_name']?:$client_info['login_code']?></label><em style="font-size: 0.7rem;color: #808080;padding-left: 10px"><?php echo $item['from_branch_name']?:'client'?></em></td>
                                        <td><label><?php echo $item['to_operator_name']?></label><em style="font-size: 0.7rem;color: #808080;padding-left: 10px"><?php echo $item['to_branch_name']?></em></td>
                                        <td><?php echo $flow_type[$item['flow_type']]?></td>
                                    </tr>
                                <?php }?>
                            </table>
                        <?php }else{?>
                            <?php require template(":widget/no_record")?>
                        <?php }?>
                    </div>
                </div>
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Loan Contract</h5>
                    </div>
                    <?php $loan_list=$output['loan_list'];?>
                    <div class="content">
                        <?php if(count($loan_list)){?>
                            <table class="table table-bordered">
                                <tr>
                                    <td>Contract-SN</td>
                                    <td>Start-Date</td>
                                    <td>Product</td>
                                    <td>Principal</td>
                                    <td>Outstanding</td>
                                </tr>
                                <?php foreach($loan_list as $item){?>
                                    <tr>
                                        <td><?php echo $item['contract_sn']?></td>
                                        <td><?php echo $item['start_date']?></td>
                                        <td><?php echo $item['sub_product_name']?></td>
                                        <td><?php echo $item['principal_out']?></td>
                                        <td><?php echo $item['principal_outstanding']?></td>
                                    </tr>
                                <?php }?>
                                <tr>
                                    <td colspan="10"><label>Total Outstanding Principal:<?php echo $output['principal_outstanding']?></label></td>
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

</script>






