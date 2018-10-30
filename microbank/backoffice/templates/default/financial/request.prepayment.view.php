<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .audit-table > tr > td:first-child {
        width: 200px;
    }

    .audit-table textarea {
        width: 300px;
        height: 80px;
        float: left;
    }

    .custom-btn-group {
        float: inherit;
    }

    .audit-table em {
        font-size: 20px;
        font-style: normal;
        color: #ea544a;
        padding-left: 10px;
        padding-right: 10px;
    }

    .loan-exp-wrap {
        filter: alpha(Opacity=0);
        opacity: 0;
        z-index: 99;
        -moz-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -o-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -webkit-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        transition: top .2s ease-in-out, opacity .2s ease-in-out;
        visibility: hidden;
        position: absolute;
        top: 37px;
        left: 8px;
        padding: 7px 10px;
        border: 1px solid #ddd;
        background-color: #f6fcff;
        color: #5b9fe2;
        font-size: 12px;
        font-family: Arial, "Hiragino Sans GB", simsun;
    }

    .loan-exp-wrap .pos {
        position: relative;
    }

    .triangle-up {
        background-position: 0 -228px;
        height: 8px;
        width: 12px;
        display: block;
        position: absolute;
        top: -15px;
        left: 40px;
        bottom: auto;
    }

    .triangle-up {
        background-image: url(./resource/image/common-slice-s957d0c8766.png);
        background-repeat: no-repeat;
        overflow: hidden;
    }

    .loan-exp-table .t {
        color: #a5a5a5;
        font-size: 12px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a {
        color: #000;
        font-size: 18px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a .y {
        color: #ea544a;
    }

    .table-body tr td:nth-child(2n+1){
        width: 230px!important;
    }
</style>
<?php $detail = $output['detail']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Request To Prepayment</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('financial', 'requestToPrepayment', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Schema</span></a>
                </li>
                <li><a class="current"><span>View</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal cerification-form" id="validForm" method="post" action="<?php echo getUrl('loan', 'auditRepayment', array(), false, BACK_OFFICE_SITE_URL)?>">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Contract Sn</label></td>
                    <td><?php echo $detail['contract_sn']?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Type</label></td>
                    <td><?php echo 'Prepayment (' . ($lang['request_prepayment_type_' . $detail['prepayment_type']]) . ')' ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Prepayment Total</label></td>
                    <td style="position: relative">
                        <em style="padding-left: 0px"><?php echo ncAmountFormat($detail['amount']) ?></em>
                        <div class="loan-exp-wrap" style="width: 300px">
                            <div class="pos">
                                <em class="triangle-up"></em>
                                <table class="loan-exp-table">
                                    <tbody><tr class="t">
                                        <td>Amount</td><td></td>
                                        <td>Principal</td><td></td>
                                        <td>Fee</td>
                                    </tr>
                                    <tr class="a">
                                        <td class="y"><?php echo ncPriceFormat($detail['amount'])?></td><td>&nbsp;=&nbsp;</td>
                                        <td><?php echo ncPriceFormat($detail['principal_amount'])?></td><td>&nbsp;+&nbsp;</td>
                                        <td><?php echo ncPriceFormat($detail['fee_amount'])?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">State</label></td>
                    <td><?php echo $lang['request_prepayment_state_' . $detail['state']] ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Request Remark</label></td>
                    <td><?php echo $detail['apply_remark'] ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Apply Time</label></td>
                    <td><?php echo timeFormat($detail['apply_time']) ?></td>
                </tr>

                <?php if ($detail['auditor_name']) { ?>
                    <tr>
                        <td><label class="control-label">Auditor Name</label></td>
                        <td><?php echo $detail['auditor_name'] ?></td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Audit Remark</label></td>
                        <td><?php echo $detail['audit_remark'] ?></td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Audit Time</label></td>
                        <td><?php echo $detail['audit_time'] ?></td>
                    </tr>
                <?php } ?>

                <?php if ($detail['handler_name']) { ?>
                    <tr>
                        <td><label class="control-label">Handler Name</label></td>
                        <td><?php echo $detail['handler_name'] ?></td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Audit Remark</label></td>
                        <td><?php echo $detail['handle_remark'] ?></td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Audit Time</label></td>
                        <td><?php echo $detail['handle_time'] ?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td></td>
                    <td>
                        <div class="custom-btn-group approval-btn-group">
                            <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"
                                    style="min-width:80px">
                                <i class="fa fa-reply"></i><?php echo 'Back'; ?>
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
    $(function () {
        $('.audit-table em').hover(function () {
            $(this).closest('tr').find('.loan-exp-wrap').css({'opacity': 1, 'visibility': 'visible'});
        }, function () {
            $(this).closest('tr').find('.loan-exp-wrap').css({'opacity': 0, 'visibility': 'hidden'});
        })
    })
</script>