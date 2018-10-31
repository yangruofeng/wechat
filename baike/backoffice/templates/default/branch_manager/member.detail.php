<style>
    .btn {
        height: 30px;
        min-width: 80px;
        padding: 5px 12px;
        border-radius: 0px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
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
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .activity-list .item {
        margin-top: 0;
        padding: 10px 20px 10px 15px;;
    }

    .activity-list .item div > span:first-child {
        font-weight: 500;
    }

    .activity-list .item span.check-state {
        float: right;
        font-size: 12px;
        margin-left: 5px;
    }

    .activity-list .item span.check-time {
        float: right;
        margin-right: 10px;
    }

    .activity-list .item span.check-state .fa-check {
        font-size: 18px;
        color: green;
    }

    .activity-list .item span.check-state .fa-question {
        font-size: 18px;
        color: red;
        padding-right: 5px;
    }

    #myModal .modal-dialog, #cbcModal .modal-dialog {
        margin-top: 10px!important;
    }

    #cbcModal .modal-dialog input{
        height: 30px;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    .pl-25 {
        padding-left: 25px;
    }

</style>
<?php $client_info = $output['client_info']?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Client Information</h5>
                    <a href="<?php echo getUrl('branch_manager', 'showClientInfoDetail', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL)?>" style="position:absolute;right: 10px;font-weight: 500;">Detail</a>
                </div>
                <div class="content">
                    <table class="table">
                        <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Member Icon</label></td>
                                <td>
                                    <a target="_blank" href="<?php echo getImageUrl($client_info['member_icon']); ?>">
                                        <img src="<?php echo getImageUrl($client_info['member_icon'], imageThumbVersion::SMALL_ICON); ?>" style="max-width: 50px;max-height: 50px">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">CID</label></td>
                                <td><?php echo $client_info['obj_guid']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Login Account</label></td>
                                <td><?php echo $client_info['login_code']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Name</label></td>
                                <td><?php echo $client_info['display_name']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Phone</label></td>
                                <td><?php echo $client_info['phone_id']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Status</label></td>
                                <td><?php echo $lang['client_member_state_' . $client_info['member_state']]; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php $credit_process = $output['credit_process']; ?>
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Credit Process</h5>
                </div>
                <div class="content">
                    <div class="activity-list">
                        <div class="item">
                            <div>
                                <span>Register</span>
                                <span class="check-state">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </span>
                                <span class="check-time"><?php echo timeFormat($credit_process['register']['time'])?></span>
                            </div>
                        </div>
                        <div class="item">
                            <div>
                                <span>Personal Information</span>
                                <span class="check-state">
                                    <a title="Detail" href="<?php echo $credit_process['personal_info']['is_check'] ? getUrl('branch_manager', 'showPersonalInformation', array('uid'=>$output['client_info']['uid']), false, BACK_OFFICE_SITE_URL) : '#' ?>">
                                        <i class="fa fa-<?php echo $credit_process['personal_info']['is_check'] ? 'check' : 'question'; ?>" aria-hidden="true"></i>
                                    </a>
                                </span>
                                <span class="check-time"><?php echo timeFormat($credit_process['personal_info']['time'])?></span>
                            </div>
                        </div>
                        <div class="item">
                            <div>
                                <span>Assets Information</span>
                                <span class="check-state">
                                    <a title="Detail" href="<?php echo getUrl('branch_manager', 'showAssetsInformation', array('uid'=>$output['client_info']['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <i class="fa fa-<?php echo $credit_process['chk_member_assets']['is_check'] ? 'check' : 'question'; ?>" aria-hidden="true"></i>
                                    </a>
                                </span>
                                <span class="check-time"><?php echo timeFormat($credit_process['chk_member_assets']['time'])?></span>
                            </div>
                        </div>
                        <div class="item" style="color: #112aef;font-weight: bold">
                            <div>
                                <span>Income Research</span>
                                <span class="check-state">
                                    <a title="Detail"
                                       href="<?php echo getUrl('branch_manager', 'showIncomeResearch', array('uid' => $output['client_info']['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <i class="fa fa-<?php echo $credit_process['income_research']['is_check'] ? 'check' : 'question'; ?>" aria-hidden="true"></i>
                                    </a>
                                </span>
                                <span class="check-time"><?php echo timeFormat($credit_process['income_research']['time'])?></span>
                            </div>
                        </div>

                        <div class="item" style="color: #112aef;font-weight: bold">
                            <div>
                                <span>Assets Evaluate</span>
                                <span class="check-state">
                                    <a title="Detail" href="<?php echo getUrl('branch_manager', 'showAssetsEvaluate', array('uid'=>$output['client_info']['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <i class="fa fa-<?php echo $credit_process['chk_member_assets_evaluate']['is_check'] ? 'check' : 'question'; ?>" aria-hidden="true"></i>
                                    </a>
                                </span>
                                <span class="check-time"><?php echo timeFormat($credit_process['chk_member_assets_evaluate']['time'])?></span>
                            </div>
                        </div>
                        <div class="item" style="color: #112aef;font-weight: bold">
                            <div>
                                <span>Request Credit</span>
                                <span class="check-state">
                                    <a title="Detail" href="<?php echo getUrl('branch_manager', 'showRequestCredit', array('uid'=>$output['client_info']['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <i class="fa fa-<?php echo $credit_process['chk_credit_suggest']['is_check'] ? 'check' : 'question'; ?>" aria-hidden="true"></i>
                                    </a>
                                </span>
                                <span class="check-time"><?php echo timeFormat($credit_process['chk_credit_suggest']['time'])?></span>
                            </div>
                        </div>
                        <div class="item">
                            <div>
                                <span>Grant Credit</span>
                                <span class="check-state">
                                    <em>
                                        <?php echo ncAmountFormat($credit_process['member_credit_grant']['credit']); ?>
                                    </em>
<!--                                    <a title="Detail" href="--><?php //echo getUrl('branch_manager', 'showGrantCredit', array('uid'=>$output['client_info']['uid']), false, BACK_OFFICE_SITE_URL) ?><!--">-->
<!--                                        <i class="fa fa-question" aria-hidden="true"></i>-->
<!--                                    </a>-->
                                </span>
                                <span class="check-time"><?php echo timeFormat($credit_process['member_credit_grant']['update_time'])?></span>
                            </div>
                        </div>
                        <div class="item">
                            <div>
                                <span>Authorize Credit</span>
                                <span class="check-state">
                                    <a title="Detail" href="<?php echo getUrl('branch_manager', 'showAuthorizeCredit', array('uid'=>$output['client_info']['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <i class="fa fa-question" aria-hidden="true"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Credit Officer</h5>
                    <a href="#" onclick="showCoModal()" style="position:absolute;right: 10px;font-weight: 500;text-decoration: none">Edit</a>
                </div>
                <div class="content">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td style="padding: 8px"><?php echo 'Name'; ?></td>
                            <td style="padding: 8px"><?php echo 'Contact Phone'; ?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if ($output['co_list']) { ?>
                            <?php foreach ($output['co_list'] as $row) {?>
                                <tr>
                                    <td>
                                        <?php echo $row['user_name'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['mobile_phone'] ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="2">Null</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php $client_cbc = $output['client_cbc']; ?>
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>CBC</h5>
                    <a href="#" onclick="showCbcModal()" style="position:absolute;right: 10px;font-weight: 500;text-decoration: none">Edit</a>
                </div>
                <div class="content">
                    <table class="table audit-table">
                        <tbody class="table-body">
                            <?php if(!$client_cbc){?>
                                <tr>
                                    <td colspan="2">No Record</td>
                                </tr>
                            <?php } else {?>
                                <tr>
                                    <td colspan="2">
                                        <label class="control-label">General</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">All Previous Enquiries</span></td>
                                    <td><?php echo $client_cbc['all_previous_enquiries']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Enquiries For Previous 30 Days</span></td>
                                    <td><?php echo $client_cbc['enquiries_for_previous_30_days']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Earliest Loan Issue Date</span></td>
                                    <td><?php echo dateFormat($client_cbc['earliest_loan_issue_date']); ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Normal Accounts</span></td>
                                    <td><?php echo $client_cbc['normal_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Delinquent Accounts</span></td>
                                    <td><?php echo $client_cbc['delinquent_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Closed Accounts</span></td>
                                    <td><?php echo $client_cbc['closed_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Reject Accounts</span></td>
                                    <td><?php echo $client_cbc['reject_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Write Off Accounts</span></td>
                                    <td><?php echo $client_cbc['write_off_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Limits</span></td>
                                    <td><em><?php echo ncAmountFormat($client_cbc['total_limits']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities</span></td>
                                    <td><em><?php echo ncAmountFormat($client_cbc['total_liabilities']); ?></em></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <label class="control-label">Guarantee</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Normal Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_normal_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Delinquent Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_delinquent_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Closed Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_closed_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Reject Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_reject_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Write Off Accounts</span></td>
                                    <td><?php echo $client_cbc['guaranteed_write_off_accounts']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Limits</span></td>
                                    <td><em><?php echo ncAmountFormat($client_cbc['guaranteed_total_limits']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Total Liabilities</span></td>
                                    <td><em><?php echo ncAmountFormat($client_cbc['guaranteed_total_liabilities']); ?></em></td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Credit Officer</h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="my_form">
                        <input type="hidden" name="uid" value="<?php echo $output['client_info']['uid']; ?>">
                        <div class="col-sm-12">
                            <label  class="col-sm-2 control-label"><span class="required-options-xing">*</span>List</label>
                            <div class="col-sm-10">
                                <?php $user_co = array_column($output['co_list'], 'uid');foreach ($output['branch_co_list'] as $co) { ?>
                                    <div class="col-sm-4">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="co_id" value="<?php echo $co['uid']; ?>" <?php echo in_array($co['uid'],$user_co) ? 'checked' : ''?>><?php echo $co['user_name']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="modal_submit()"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="cbcModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit CBC</h4>
            </div>
            <div class="modal-body">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="cbc_form">
                        <input type="hidden" name="uid" value="<?php echo $output['client_info']['uid']; ?>">
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-6 control-label" style="text-align: left;font-size: 15px">General</label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>All Previous Enquiries</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="all_previous_enquiries" value="<?php echo intval($client_cbc['all_previous_enquiries']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Enquiries For Previous 30 Days</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="enquiries_for_previous_30_days" value="<?php echo intval($client_cbc['enquiries_for_previous_30_days']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Earliest Loan Issue Date</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="earliest_loan_issue_date" value="<?php echo $client_cbc['earliest_loan_issue_date']; ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Normal Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="normal_accounts" value="<?php echo intval($client_cbc['normal_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Delinquent Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="delinquent_accounts" value="<?php echo intval($client_cbc['delinquent_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Closed Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="closed_accounts" value="<?php echo intval($client_cbc['closed_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Reject Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="reject_accounts" value="<?php echo intval($client_cbc['reject_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Write Off Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="write_off_accounts" value="<?php echo intval($client_cbc['write_off_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Total Limits</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="total_limits" value="<?php echo $client_cbc['total_limits']?: 0.00; ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Total Liabilities</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="total_liabilities" value="<?php echo $client_cbc['total_liabilities']?:0.00; ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-6 control-label" style="text-align: left;font-size: 15px">Guarantee</label>
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Normal Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guaranteed_normal_accounts" value="<?php echo intval($client_cbc['guaranteed_normal_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Delinquent Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guaranteed_delinquent_accounts" value="<?php echo intval($client_cbc['guaranteed_delinquent_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Closed Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guaranteed_closed_accounts" value="<?php echo intval($client_cbc['guaranteed_closed_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Reject Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guaranteed_reject_accounts" value="<?php echo intval($client_cbc['guaranteed_reject_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Write Off Accounts</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guaranteed_write_off_accounts" value="<?php echo intval($client_cbc['guaranteed_write_off_accounts']); ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Total Limits</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guaranteed_total_limits" value="<?php echo $client_cbc['guaranteed_total_limits']?:0.00; ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Total Liabilities</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guaranteed_total_liabilities" value="<?php echo $client_cbc['guaranteed_total_liabilities']?:0.00; ?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="cbc_submit()"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function() {
        $('#cbcModal input[name="earliest_loan_issue_date"]').datepicker({
            format: 'yyyy-mm-dd'
        });
    })

    function showCoModal() {
        $('#myModal').modal('show');
    }

    function modal_submit() {
        var values = getFormJson($('#my_form'));
        yo.loadData({
            _c: 'branch_manager',
            _m: 'editMemberCo',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    function showCbcModal() {
        $('#cbcModal').modal('show');
    }

    function cbc_submit() {
        if (!$("#cbc_form").valid()) {
            return;
        }

        var values = getFormJson($('#cbc_form'));
        yo.loadData({
            _c: 'branch_manager',
            _m: 'editMemberCbc',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    $('#cbc_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            all_previous_enquiries: {
                required: true
            },
            Enquiries_for_previous_30_days: {
                required: true
            },
//            earliest_loan_issue_date: {
//                required: true
//            },
            normal_accounts: {
                required: true
            },
            delinquent_accounts: {
                required: true
            },
            closed_accounts: {
                required: true
            },
            reject_accounts: {
                required: true
            },
            write_off_accounts: {
                required: true
            },
            total_limits: {
                required: true
            },
            total_liabilities: {
                required: true
            },
            guaranteed_normal_accounts: {
                required: true
            },
            guaranteed_delinquent_accounts: {
                required: true
            },
            guaranteed_closed_accounts: {
                required: true
            },
            guaranteed_reject_accounts: {
                required: true
            },
            guaranteed_write_off_accounts: {
                required: true
            },
            guaranteed_total_limits: {
                required: true
            },
            guaranteed_total_liabilities: {
                required: true
            }
        },
        messages: {
            all_previous_enquiries: {
                required: '<?php echo 'Required'?>'
            },
            Enquiries_for_previous_30_days: {
                required: '<?php echo 'Required'?>'
            },
//            earliest_loan_issue_date: {
//                required: '<?php //echo 'Required'?>//'
//            },
            normal_accounts: {
                required: '<?php echo 'Required'?>'
            },
            delinquent_accounts: {
                required: '<?php echo 'Required'?>'
            },
            closed_accounts: {
                required: '<?php echo 'Required'?>'
            },
            reject_accounts: {
                required: '<?php echo 'Required'?>'
            },
            write_off_accounts: {
                required: '<?php echo 'Required'?>'
            },
            total_limits: {
                required: '<?php echo 'Required'?>'
            },
            total_liabilities: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_normal_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_delinquent_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_closed_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_reject_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_write_off_accounts: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_total_limits: {
                required: '<?php echo 'Required'?>'
            },
            guaranteed_total_liabilities: {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>