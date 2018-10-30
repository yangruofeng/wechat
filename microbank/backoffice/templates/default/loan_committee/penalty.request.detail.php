<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .input-h30 {
        height: 30px !important;
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
        /*background-color: #FFF;*/
        overflow: hidden;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Penalty Request</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan_committee', 'approvePenaltyRequest', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Penalty Request</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 1000px">
        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Penalty Request</h5>
            </div>
            <div class="content">
                <?php $request_detail = $output['request_detail']; ?>
                <form class="form-horizontal" style="margin-bottom: 0">
                    <input type="hidden" name="uid" value="<?php echo $request_detail['uid'] ?>">
                    <table class="table">
                        <tbody class="table-body">
                        <tr>
                            <td><label class="control-label">Contract Sn</label></td>
                            <td colspan="3">
                                <?php foreach ($request_detail['penalty_list'] as $penalty_list){
                                    echo $penalty_list['contract_sn'].'； ';
                                }?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Client Account</label></td>
                            <td><?php echo $request_detail['login_code'] ?></td>
                            <td><label class="control-label">Client Grade</label></td>
                            <td><?php echo $request_detail['grade_code'] ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Currency</label></td>
                            <td><?php echo $request_detail['currency'] ?></td>
                            <td><label class="control-label">Due Amount</label></td>
                            <td><em><?php echo ncPriceFormat($request_detail['receivable']) ?></em></td>

                        </tr>
                        <tr>
                            <td><label class="control-label">Reduce Amount</label></td>
                            <td><em><?php echo ncPriceFormat($request_detail['deducting']) ?></em></td>
                            <td><label class="control-label">Actual Amount</label></td>
                            <td><em><?php echo ncPriceFormat($request_detail['paid']); ?></em></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Apply Remark</label></td>
                            <td><?php echo $request_detail['remark']; ?></td>
                            <td><label class="control-label">Apply Time</label></td>
                            <td><?php echo timeFormat($request_detail['create_time']); ?></td>
                        </tr>
                        <?php if ($output['is_handle']) { ?>
                            <tr>
                                <td><label class="control-label">Remark</label></td>
                                <td colspan="3">
                                <textarea class="form-control" name="audit_remark"
                                          style="width: 80%;height: 100px"></textarea>

                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td><label class="control-label">Auditor</label></td>
                                <td><?php echo $request_detail['auditor_name']; ?></td>
                                <td><label class="control-label">Audit Result</label></td>
                                <td>
                                    <?php
                                    if ($request_detail['state'] >= loanPenaltyReceiptStateEnum::APPROVED) {
                                        echo 'Approved';
                                    } else if ($request_detail['state'] == loanPenaltyReceiptStateEnum::REJECTED) {
                                        echo 'Rejected';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Audit Time</label></td>
                                <td><?php echo timeFormat($request_detail['audit_time']); ?></td>
                                <td><label class="control-label">Audit Remark</label></td>
                                <td><?php echo $request_detail['audit_remark']; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="4" style="text-align: center">
                                <?php if ($output['is_handle']) { ?>
                                    <button type="button" class="btn btn-danger" onclick="checkApprove();"><i
                                            class="fa fa-check"></i><?php echo 'Approve' ?></button>
                                    <button type="button" class="btn btn-info" onclick="checkReject();"><i
                                            class="fa fa-close"></i><?php echo 'Reject' ?></button>
                                    <button type="button" class="btn btn-default" onclick="checkAbandon();"><i
                                            class="fa fa-reply"></i><?php echo 'Abandon' ?></button>
                                <?php } ?>
                                <button type="button" class="btn btn-default" onclick="javascript:history.back(-1)"><i
                                        class="fa fa-reply"></i><?php echo 'Back' ?></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function checkApprove() {
        var values = $(".form-horizontal").getValues();
        values.state = '<?php echo loanPenaltyReceiptStateEnum::APPROVED ?>';
        auditPenaltyRequest(values);
    }

    function checkReject() {
        var values = $(".form-horizontal").getValues();
        values.state = '<?php echo loanPenaltyReceiptStateEnum::REJECTED ?>';
        auditPenaltyRequest(values);
    }

    function auditPenaltyRequest(values) {
        if (!values.uid) {
            return;
        }
        yo.loadData({
            _c: 'loan_committee',
            _m: 'auditPenaltyRequest',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                   setTimeout(function () {
                       window.location.href = '<?php echo getUrl('loan_committee', 'approvePenaltyRequest', array(), false, BACK_OFFICE_SITE_URL);?>';
                   },2000)

                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function checkAbandon() {
        var values = $(".form-horizontal").getValues();
        if (!values.uid) {
            return;
        }
        yo.loadData({
            _c: 'loan_committee',
            _m: 'abandonPenaltyRequestTask',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                         window.location.href = '<?php echo getUrl('loan_committee', 'approvePenaltyRequest', array(), false, BACK_OFFICE_SITE_URL);?>';
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>