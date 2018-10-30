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



<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Committee</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan_committee', 'grantCreditHistory', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>History</span></a>
                </li>
                <li><a class="current"><span>Credit Grant</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $client_info = $output['client_info']; ?>
        <div class="col-sm-12">
            <?php require_once template('widget/item.member.summary1'); ?>
        </div>

        <div class="col-sm-8">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Credit Grant</h5>
                </div>
                <div class="content">
                    <?php
                        $credit_grant=$output['credit_grant'];
                        $credit_category=$output['credit_category'];
                        $member_assets=$output['member_assets'];
                        $no_need_delete_btn = true;
                        include(template("widget/inc.credit.grant.detail"));

                    ?>

                </div>
                <div class="text-center" style="margin-bottom: 10px;">
                    <?php $authorized_contract = $output['authorized_contract']; ?>
                    <?php if (!$authorized_contract) { ?>
                        <button type="button" class="btn btn-danger" onclick="deleteCreditGrant(<?php echo $credit_grant['uid']; ?>)" style="width: 30%"><i class="fa fa-close"></i>Delete</button>
                    <?php } ?>
                    <button class="btn btn-default" onclick="javascript:history.go(-1);" style="width: 30%"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </div>
            </div>

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
                        <?php foreach ($output['member_credit_grant_vote'] as $v) { ?>
                            <tr>
                                <td><?php echo $v['user_name']; ?></td>
                                <td>
                                    <i class="fa fa-<?php echo $v['vote_result'] == 0 ? 'question' : ($v['vote_result'] == 10 ? 'close' : 'check') ?>"></i>
                                </td>
                                <td><?php echo $v['update_time'] ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php $authorized_contract_list = $output['authorized_contract']; ?>
        <?php if ($authorized_contract_list) { ?>
            <div class="col-sm-6">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Authorized Contract</h5>
                    </div>
                    <div class="content">
                        <table class="table">
                            <?php foreach ($authorized_contract_list as $authorized_contract){ ?>
                                <tr>
                                    <td>
                                        <span style="font-weight: 600"><?php echo $authorized_contract['contract_no']; ?></span>
                                    </td>
                                    <td>
                                        <?php echo timeFormat($authorized_contract['create_time']); ?>
                                    </td>
                                    <td>
                                        <span>Fee:</span>
                                        <span style="font-weight: 500"><?php echo ncPriceFormat($authorized_contract['fee']); ?></span>
                                    </td>
                                    <td>
                                        <span><?php echo $authorized_contract['branch_name']; ?></span>
                                    </td>
                                    <td>
                                        <span><?php echo $authorized_contract['update_operator_name'] ? :$authorized_contract['officer_name']; ?></span>
                                    </td>
                                </tr>

                                <?php $credit = 0;if ($authorized_contract['mortgage_list']) { ?>
                                    <?php foreach ($authorized_contract['mortgage_list'] as $val) { $credit += $val['credit'];?>
                                        <tr>
                                            <td></td>
                                            <td colspan="2">
                                                <span>
                                                    <span><?php echo $val['asset_name']; ?></span>
                                                    <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$val['asset_type']]; ?>)</span>
                                                </span>
                                            </td>
                                            <td colspan="2">
                                                <span style="font-weight: 600">
                                                    <?php echo ncPriceFormat($val['credit']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td></td>
                                        <td colspan="4">
                                            No Record
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td></td>
                                    <td colspan="2">
                                        <span style="font-weight: 600">Get Credit</span>
                                    </td>
                                    <td colspan="2">
                                        <span style="font-weight: 600;font-size: 16px"><?php echo ncPriceFormat($credit) ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" style="height: 30px"></td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Loan Contract List</h5>
                    </div>

                    <div class="content">
                        <table class="table">
                            <tbody class="table-body">
                            <?php if ($output['contract_list']) { ?>
                                <?php foreach ($output['contract_list'] as $v) { ?>
                                    <tr>
                                        <td><span style="font-weight: 600"><?php echo $v['contract_sn']; ?></span></td>
                                        <td><?php echo $v['sub_product_name']; ?></td>
                                        <td><?php echo dateFormat($v['start_date']) ?></td>
                                        <td><span style="font-weight: 600"><?php echo $v['currency'] . ncPriceFormat($v['apply_amount']) ?></span></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="4">
                                        No record
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        <?php } ?>
    </div>
</div>
<script>
    function deleteCreditGrant(_uid) {
        if (!_uid) {
            return;
        }
        yo.confirm("Delete", "<?php echo $lang['common_confirm_delete']?>", function (_r) {
            if (!_r) return;
            $(".page").waiting();
            yo.loadData({
                _c: "loan_committee",
                _m: "deleteCreditGrant",
                param: {uid: _uid},
                callback: function (_o) {
                    $(".page").unmask();
                    if (_o.STS) {
                        alert('Removed Already',1,function(){
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