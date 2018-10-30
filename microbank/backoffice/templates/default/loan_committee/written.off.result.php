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
        /*background-color: #FFF;*/
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .voting-list .fa {
        font-size: 18px;
        margin-left: 10px;
        color: #666666;
    }

    .voting-list .fa-check {
        color: #008000!important;
    }

    .voting-list .fa-close {
        color: red!important;
    }

    #qrModal .modal-dialog {
        margin-top: 20px!important;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }
</style>
<?php
$client_info = $output['client_info'];
$credit = memberClass::getCreditBalance($client_info['uid']);
$written_off = $output['written_off'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Committee</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan_committee', 'approveWrittenOffRequest', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Vote</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="basic-info" style="width:auto; margin-left: 15px; margin-right: 15px;">
            <div class="ibox-title" style="position: relative">
                <h5><i class="fa fa-id-card-o"></i>Client Info</h5>
                <a href="<?php echo getUrl('branch_manager', 'showClientInfoDetail', array('uid' => $client_info['uid'], 'off_id' => $written_off['uid'], 'source' => 'written_off'), false, BACK_OFFICE_SITE_URL)?>" style="position:absolute;right: 20px;font-weight: 500;">Detail</a>
            </div>
            <div class="content">
                <table class="table">
                    <tbody class="table-body">
                    <tr>
                        <td><label class="control-label">Icon</label></td>
                        <td><label class="control-label">Login Account</label></td>
                        <td><label class="control-label">Name</label></td>
                        <td><label class="control-label">Member Credit</label></td>
                        <td><label class="control-label">Credit Balance</label></td>
                        <td><label class="control-label">Loan Balance</label></td>
                        <td><label class="control-label">Phone</label></td>
                        <td><label class="control-label">Status</label></td>
                    </tr>
                    <tr>
                        <td>
                            <a target="_blank" href="<?php echo getImageUrl($client_info['member_icon']); ?>">
                                <img src="<?php echo getImageUrl($client_info['member_icon'], imageThumbVersion::SMALL_ICON); ?>" style="max-width: 50px;max-height: 50px">
                            </a>
                        </td>
                        <td><?php echo $client_info['login_code']; ?></td>
                        <td><?php echo $client_info['display_name']; ?></td>
                        <td><?php echo ncAmountFormat($credit['credit']); ?></td>
                        <td><?php echo ncAmountFormat($credit['balance']); ?></td>
                        <td><?php echo ncAmountFormat(memberClass::getLoanBalance($credit_info['uid'])->DATA); ?></td>
                        <td><?php echo $client_info['phone_id']; ?></td>
                        <td><?php echo $lang['client_member_state_' . $client_info['member_state']]; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Written Off</h5>
                </div>
                <div class="content">
                    <table class="table">
                        <tr>
                            <td><label class="control-label">Contact-Sn</label></td>
                            <td><?php echo $written_off['contract_sn'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Product Name</label></td>
                            <td><?php echo $written_off['sub_product_name'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Client-Name</label></td>
                            <td><?php echo $written_off['display_name'] ? : $written_off['login_code']?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Loan Date</label></td>
                            <td><?php echo dateFormat($written_off['start_date']) ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Currency</label></td>
                            <td><?php echo $written_off['currency'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Loan Amount</label></td>
                            <td><?php echo ncPriceFormat($written_off['apply_amount']) ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Contract State</label></td>
                            <td><?php echo $lang['loan_contract_state_' . $written_off['contract_state']] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">loss Principal</label></td>
                            <td class="money-style"><?php echo ncPriceFormat($written_off['loss_principal']) ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Loss Interest</label></td>
                            <td class="money-style"><?php echo ncPriceFormat($written_off['loss_interest']) ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Loss Operation Fee</label></td>
                            <td class="money-style"><?php echo ncPriceFormat($written_off['loss_operation_fee']) ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Total Loss</label></td>
                            <td class="money-style"><?php echo ncPriceFormat($written_off['loss_amount'])?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Remark</label></td>
                            <td>
                                <span><?php echo $written_off['operator_remark']?></span>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Vote Result</label></td>
                            <td>
                                <span><?php echo $written_off['vote_result'] == commonApproveStateEnum::PASS ? 'Pass' : 'Reject'?></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Vote</h5>
                </div>

                <div class="content voting-list">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td>Committee Member</td>
                            <td>Voting Result</td>
                            <td>VoteTime</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                            <?php foreach ($output['vote_committee_member'] as $v) { ?>
                                <tr>
                                    <td><?php echo $v['user_name']; ?></td>
                                    <td><i class="fa fa-<?php echo $v['vote_result'] == 0 ? 'question' : ($v['vote_result'] == 10 ? 'close' : 'check')?>"></i></td>
                                    <td><?php echo $v['update_time']?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>