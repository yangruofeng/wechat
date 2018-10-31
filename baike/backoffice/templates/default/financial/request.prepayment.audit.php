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
</style>
<?php $detail = $output['detail']; ?>
<?php $prepayment_detail = $output['prepayment_detail']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Request To Prepayment</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('financial', 'requestToPrepayment', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Audit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal cerification-form" id="validForm" method="post" action="<?php echo getUrl('financial', 'auditPrepayment', array(), false, BACK_OFFICE_SITE_URL)?>">
            <table class="table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Contract Sn</label></td>
                    <td>
                        <a href="<?php echo getUrl('loan', 'contractDetail', array('uid'=>$detail['contract_id']), false, BACK_OFFICE_SITE_URL); ?>">
                            <?php echo $detail['contract_sn']?>
                        </a>

                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Type</label></td>
                    <td>

                        <?php
                            switch( $detail['prepayment_type'] ){
                                case prepaymentRequestTypeEnum::PARTLY:
                                    echo 'Fixed repay principal';
                                    break;
                                case prepaymentRequestTypeEnum::FULL_AMOUNT:
                                    echo 'Full Payment';
                                    break;
                                case prepaymentRequestTypeEnum::LEFT_PERIOD:
                                    echo 'Fixed repay period';
                                    break;
                                default:
                                    echo 'Fixed repay principal';
                            }
                        ?>
                    </td>
                </tr>

                <?php if( $detail['prepayment_type'] != prepaymentRequestTypeEnum::FULL_AMOUNT ){ ?>
                    <?php if( $detail['prepayment_type'] == 0 ){ ?>
                        <tr>
                            <td>
                                <label for="" class="control-label">Apply Principal Amount</label>
                            </td>
                            <td>
                                <?php echo ncPriceFormat($detail['principal_amount']); ?>
                            </td>
                        </tr>
                    <?php }else{ ?>
                        <tr>
                            <td>
                                <label for="" class="control-label">Apply Repay Period</label>
                            </td>
                            <td>
                                <?php echo $detail['repay_period']; ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>

                <tr>
                    <td>
                        <label for="" class="control-label">Contract Must Repayment</label>
                    </td>
                    <td>
                        <div>
                            <p>Overdue Amount: <?php echo ncPriceFormat($prepayment_detail['total_overdue_amount']); ?></p>
                            <p>Next Schema Amount: <?php echo ncPriceFormat($prepayment_detail['next_repayment_amount']); ?></p>
                        </div>

                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="" class="control-label"> Left Principal</label>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($prepayment_detail['total_left_principal']); ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="" class="control-label">Total Prepayment Principal</label>
                    </td>
                   <td>
                       <?php echo ncPriceFormat($detail['principal_amount']); ?>
                   </td>
                </tr>

                <tr>
                    <td>
                        <label for="" class="control-label">Total Prepayment Fee</label>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($detail['fee_amount']); ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="" class="control-label"> Total Need Pay Amount</label>
                    </td>
                    <td>
                        <em style="padding-left: 0;font-size: 16px;color:red;">
                            <?php echo ncPriceFormat($detail['total_apply_amount']).$detail['contract_currency']; ?>

                        </em>
                    </td>
                </tr>

                <!--<tr>
                    <td><label class="control-label">Prepayment Total</label></td>
                    <td style="position: relative">
                        <em style="padding-left: 0px"><?php /*echo $detail['currency'] . ncPriceFormat($detail['amount']) */?></em>
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
                                        <td class="y"><?php /*echo ncPriceFormat($detail['amount'])*/?></td><td>&nbsp;=&nbsp;</td>
                                        <td><?php /*echo ncPriceFormat($detail['principal_amount'])*/?></td><td>&nbsp;+&nbsp;</td>
                                        <td><?php /*echo ncPriceFormat($detail['fee_amount'])*/?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>-->


<!--                --><?php //if($detail['prepayment_type'] == 2){?>
<!--                    --><?php //$i = 0;$amount = 0;foreach ($prepayment_preview['left_schema'] as $schema) {
//                        ++$i;
//                        $amount += ($schema['amount'] + $schema['penalties'])
//                        ?>
<!--                        <tr>-->
<!--                            <td>--><?php //if ($i == 1) { ?><!--<label class="control-label">Remaining Schema</label>--><?php //}?><!--</td>-->
<!--                            <td style="position: relative">-->
<!--                                <label style="display: inline-block">--><?php //echo $schema['scheme_name']?><!--: </label>-->
<!--                                <em>--><?php //echo ncAmountFormat($schema['amount'] + $schema['penalties'])?><!--</em>-->
<!--                                <span>Repayable Date: --><?php //echo dateFormat($schema['receivable_date'])?><!--</span>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                    --><?php //} ?>
<!--                    <tr>-->
<!--                        <td></td>-->
<!--                        <td>-->
<!--                            <label style="display: inline-block;font-size: 18px;font-weight: 600">--><?php //echo 'Amount'?><!--: </label>-->
<!--                            <em>--><?php //echo ncAmountFormat($amount)?><!--</em>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                --><?php //} ?>

                <tr>
                    <td><label class="control-label">Request Remark</label></td>
                    <td><?php echo $detail['apply_remark'] ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">State</label></td>
                    <td><?php echo $lang['request_prepayment_state_' . $detail['state']] ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Apply Time</label></td>
                    <td><?php echo timeFormat($detail['apply_time']) ?></td>
                </tr>
                <?php if ($output['lock']) {?>
                <tr>
                    <td><label class="control-label">Audit Remark</label></td>
                    <td><span class="color28B779">Processing...</span></td>
                </tr>
                <?php } else { ?>
                    <tr>
                        <td><label class="control-label">Audit Remark</label></td>
                        <td>
                            <textarea class="form-control" name="remark"></textarea>
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                <?php }?>
                <?php if ($output['lock']) { ?>
                    <tr>
                        <td><label class="control-label">Auditor</label></td>
                        <td><span class="color28B779"><?php echo $detail['auditor_name']; ?></span></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td><label class="control-label"></label></td>
                    <td>
                        <?php if ($output['lock']) { ?>
                            <span class="color28B779">Processing...</span>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger" onclick="javascript:history.go(-1);">
                                    <i class="fa fa-vcard-o"></i>Back
                                </button>
                            </div>
                        <?php } else { ?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger" style="min-width:80px;"
                                        onclick="submitForm('approve')">
                                    <i class="fa fa-check"></i><?php echo 'Approve'; ?>
                                </button>
                                <button type="button" class="btn btn-info" style="min-width:80px;"
                                        onclick="submitForm('disapprove')">
                                    <i class="fa fa-remove"></i><?php echo 'Disapprove'; ?>
                                </button>
                                <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"
                                        style="min-width:80px">
                                    <i class="fa fa-reply"></i><?php echo 'Back'; ?>
                                </button>
                            </div>
                        <?php }?>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="uid" value="<?php echo $detail['uid']; ?>">
        </form>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/validform/jquery.validate.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=20"></script>
<script>
    $(function () {
        $('.audit-table em').hover(function () {
            $(this).closest('tr').find('.loan-exp-wrap').css({'opacity': 1, 'visibility': 'visible'});
        }, function () {
            $(this).closest('tr').find('.loan-exp-wrap').css({'opacity': 0, 'visibility': 'hidden'});
        })
    })

    function submitForm(type) {
        var values = $('#validForm').getValues();
        values.type = type;
        yo.loadData({
            _c: "financial",
            _m: "auditPrepayment",
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.href = '<?php echo getUrl('financial', 'requestToPrepayment', array(), false, BACK_OFFICE_SITE_URL);?>'
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }
</script>
