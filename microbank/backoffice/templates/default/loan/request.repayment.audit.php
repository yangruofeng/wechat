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
<?php
$detail = $output['detail'];
$repayment_type_lang = $output['repayment_type_lang'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Repayment</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan', 'requestToRepayment', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Handle</span></a></li>
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
                    <td><?php echo$detail['type'] == "schema" ? "Schema" : "Prepayment" ?></td>
                </tr>
                <?php if($detail['type'] == requestRepaymentTypeEnum::SCHEME){?>
                    <?php $i = 0;$amount = 0;foreach ($output['expired_schema'] as $schema) {
                        ++$i;
                        $left_amount = $schema['amount']-$schema['actual_payment_amount'];
                        $amount += ($left_amount + $schema['penalties']);
                        ?>
                        <tr>
                            <td><?php if ($i == 1) { ?><label class="control-label">Expired Detail</label><?php }?></td>
                            <td style="position: relative">
                                <label style="display: inline-block"><?php echo $schema['scheme_name']?>: </label>
                                <em><?php echo  ncPriceFormat($left_amount + $schema['penalties']).$detail['currency']; ?></em>
                                <span>Repayable Date: <?php echo dateFormat($schema['receivable_date'])?></span>
                                <div class="loan-exp-wrap">
                                    <div class="pos">
                                        <em class="triangle-up"></em>
                                        <table class="loan-exp-table">
                                            <tbody><tr class="t">
                                                <td>Amount</td><td></td>
                                                <td>Principal And Interest</td><td></td>
<!--                                                <td>Interest</td><td></td>-->
<!--                                                <td>Admin Fee</td><td></td>-->
<!--                                                <td>Operation Fee</td><td></td>-->
                                                <td>Penalties</td>
                                            </tr>
                                            <tr class="a">
                                                <td class="y"><?php echo ncPriceFormat($left_amount + $schema['penalties'])?></td><td>&nbsp;=&nbsp;</td>
                                                <td><?php echo ncPriceFormat($left_amount)?></td><td>&nbsp;+&nbsp;</td>
<!--                                                <td>--><?php //echo ncPriceFormat($schema['receivable_interest'])?><!--</td><td>&nbsp;+&nbsp;</td>-->
<!--                                                <td>--><?php //echo ncPriceFormat($schema['receivable_admin_fee'])?><!--</td><td>&nbsp;+&nbsp;</td>-->
<!--                                                <td>--><?php //echo ncPriceFormat($schema['receivable_operation_fee'])?><!--</td><td>&nbsp;+&nbsp;</td>-->
                                                <td><?php echo ncPriceFormat($schema['penalties'])?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if($output['expired_schema']){?>
                        <tr>
                            <td></td>
                            <td>
                                <label style="display: inline-block;font-size: 18px;font-weight: 600"><?php echo 'Amount'?>: </label>
                                <em><?php echo   ncPriceFormat($amount).$detail['currency']; ?></em>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php $i = 0;$amount = 0;foreach ($output['unexpired_schema'] as $schema) {
                        ++$i;
                        $left_amount = $schema['amount']-$schema['actual_payment_amount'];
                        $amount += ($left_amount + $schema['penalties']);
                        ?>
                        <tr>
                            <td><?php if ($i == 1) { ?><label class="control-label">Unexpired Detail</label><?php }?></td>
                            <td style="position: relative">
                                <label style="display: inline-block"><?php echo $schema['scheme_name']?>: </label>
                                <em><?php echo  ncPriceFormat($left_amount + $schema['penalties']).$detail['currency']; ?></em>
                                <span>Repayable Date: <?php echo dateFormat($schema['receivable_date'])?></span>
                                <div class="loan-exp-wrap">
                                    <div class="pos">
                                        <em class="triangle-up"></em>
                                        <table class="loan-exp-table">
                                            <tbody><tr class="t">
                                                <td>Amount</td><td></td>
                                                <td>Principal And Interest</td><td></td>
<!--                                                <td>Interest</td><td></td>-->
<!--                                                <td>Admin Fee</td><td></td>-->
<!--                                                <td>Operation Fee</td><td></td>-->
                                                <td>Penalties</td>
                                            </tr>
                                            <tr class="a">
                                                <td class="y"><?php echo ncPriceFormat($left_amount + $schema['penalties'])?></td><td>&nbsp;=&nbsp;</td>
                                                <td><?php echo ncPriceFormat($left_amount)?></td><td>&nbsp;+&nbsp;</td>
<!--                                                <td>--><?php //echo ncPriceFormat($schema['receivable_interest'])?><!--</td><td>&nbsp;+&nbsp;</td>-->
<!--                                                <td>--><?php //echo ncPriceFormat($schema['receivable_admin_fee'])?><!--</td><td>&nbsp;+&nbsp;</td>-->
<!--                                                <td>--><?php //echo ncPriceFormat($schema['receivable_operation_fee'])?><!--</td><td>&nbsp;+&nbsp;</td>-->
                                                <td><?php echo ncPriceFormat($schema['penalties'])?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if($output['unexpired_schema']){?>
                        <tr>
                            <td></td>
                            <td>
                                <label style="display: inline-block;font-size: 18px;font-weight: 600"><?php echo 'Amount'?>: </label>
                                <em><?php echo ncPriceFormat($amount).$detail['currency'] ; ?></em>
                            </td>
                        </tr>
                    <?php }?>
                <?php } else { ?>
                    <tr>
                        <td><label class="control-label">Repayable Amount</label></td>
                        <td>
                            <em style="padding-left: 0"><?php echo  ncPriceFormat($output['repayment']['left_total']['total_repayment']).$detail['currency']; ?></em>
                            <div class="loan-exp-wrap">
                                <div class="pos">
                                    <em class="triangle-up"></em>
                                    <table class="loan-exp-table">
                                        <tbody><tr class="t">
                                            <td>Amount</td><td></td>
                                            <td>Principal</td><td></td>
                                            <td>Interest</td><td></td>
<!--                                            <td>Admin Fee</td><td></td>-->
<!--                                            <td>Operation Fee</td><td></td>-->
                                            <td>Penalties</td>
                                        </tr>
                                        <tr class="a">
                                            <td class="y"><?php echo ncPriceFormat($output['repayment']['left_total']['total_repayment'])?></td><td>&nbsp;=&nbsp;</td>
                                            <td><?php echo ncPriceFormat($output['repayment']['left_total']['total_principal'])?></td><td>&nbsp;+&nbsp;</td>
                                            <td><?php echo ncPriceFormat($output['repayment']['left_total']['total_interest'])?></td><td>&nbsp;+&nbsp;</td>
<!--                                            <td>--><?php //echo ncPriceFormat($schema['receivable_admin_fee'])?><!--</td><td>&nbsp;+&nbsp;</td>-->
<!--                                            <td>--><?php //echo ncPriceFormat($schema['receivable_operation_fee'])?><!--</td><td>&nbsp;+&nbsp;</td>-->
                                            <td><?php echo ncPriceFormat($output['repayment']['left_total']['total_penalties'])?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td><label class="control-label">Actual Repayment</label></td>
                    <td><em style="padding-left: 0;margin-left: 0;"><?php echo ncPriceFormat($detail['amount']).$detail['currency']; ?></em></td>
                </tr>

                <tr>
                    <td><label class="control-label">State</label></td>
                    <td><?php echo $lang['request_repayment_state_' . $detail['state']] ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Payer Name</label></td>
                    <td>
                        <?php if ($detail['payer_name']) { ?>
                            <?php echo $detail['payer_name'] ?>
                        <?php } else { ?>
                            <input type="text" class="form-control" name="payer_name">
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Payer Type</label></td>
                    <td>
                        <?php echo $repayment_type_lang[$detail['payer_type']]?:'Unknown'; ?>
                        <?php /*if ($detail['payer_type']) { */?><!--
                            <?php /*echo $detail['payer_type'] */?>
                        <?php /*} else { */?>
                            <input type="text" class="form-control" name="payer_type">
                        --><?php /*} */?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Payer Account</label></td>
                    <td>
                        <?php if ($detail['payer_account']) { ?>
                            <?php echo $detail['payer_account'] ?>
                        <?php }else{?>
                            <input type="text" class="form-control" name="payer_account">
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Payer Phone</label></td>
                    <td>
                        <?php if ($detail['payer_phone']) { ?>
                            <?php echo $detail['payer_phone'] ?>
                        <?php }else{?>
                            <input type="text" class="form-control" name="payer_phone">
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Bank Name</label></td>
                    <td>
                        <?php if ($detail['bank_name']) { ?>
                            <?php echo $detail['bank_name'] ?>
                        <?php }else{?>
                            <input type="text" class="form-control" name="bank_name">
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Bank Account Name</label></td>
                    <td>
                        <?php if ($detail['bank_account_name']) { ?>
                            <?php echo $detail['bank_account_name'] ?>
                        <?php }else{?>
                            <input type="text" class="form-control" name="bank_account_name">
                        <?php } ?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Bank Account No</label></td>
                    <td>
                        <?php if ($detail['bank_account_no']) { ?>
                            <?php echo $detail['bank_account_no'] ?>
                        <?php }else{?>
                            <input type="text" class="form-control" name="bank_account_no">
                        <?php } ?>
                    </td>
                </tr>

                <?php if ($detail['request_img']) { ?>
                    <tr>
                        <td><label class="control-label">Trading Record</label></td>
                        <td>
                            <a target="_blank" href="<?php echo getImageUrl($detail['request_img']) ?>">
                                <img style="max-height: 200px;max-width: 100%" src="<?php echo getImageUrl($detail['request_img']) ?>">
                            </a>
                        </td>
                    </tr>
                <?php } ?>

                <?php if ($detail['request_remark']) { ?>
                    <tr>
                        <td><label class="control-label">Request Remark</label></td>
                        <td><?php echo $detail['request_remark'] ?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td><label class="control-label">Create Time</label></td>
                    <td><?php echo timeFormat($detail['create_time']) ?></td>
                </tr>

                <?php if ($output['lock']) {?>
                <tr>
                    <td><label class="control-label">Handle Remark</label></td>
                    <td><span class="color28B779">Processing...</span></td>
                </tr>
                <?php } elseif ($detail['repayment_way'] == 0) { ?>
                <tr>
                    <td><label class="control-label">Handle Remark</label></td>
                    <td>
                        <textarea class="form-control" name="remark"></textarea>
                        <div class="error_msg"></div>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Received Date</label></td>
                    <td>
                        <input type="text" class="form-control datepicker" name="received_date" value="">
                        <div class="error_msg"></div>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($output['lock']) { ?>
                    <tr>
                        <td><label class="control-label">Handler</label></td>
                        <td><span class="color28B779"><?php echo $detail['handler_name']; ?></span></td>
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
                        <?php } elseif($detail['repayment_way'] == 0) { ?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-danger" style="min-width:80px;"
                                        onclick="submitForm('offline_success')">
                                    <i class="fa fa-check"></i><?php echo 'Success'; ?>
                                </button>
                                <button type="button" class="btn btn-info" style="min-width:80px;"
                                        onclick="submitForm('offline_failure')">
                                    <i class="fa fa-remove"></i><?php echo 'Failure'; ?>
                                </button>
                                <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"
                                        style="min-width:80px">
                                    <i class="fa fa-reply"></i><?php echo 'Back'; ?>
                                </button>
                            </div>
                        <?php } else { ?>
                            <div class="custom-btn-group approval-btn-group">
                                <button type="button" class="btn btn-info" style="min-width:80px;"
                                        onclick="submitForm('cut_payment')">
                                    <i class="fa fa-check"></i><?php echo 'Cut Payment'; ?>
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
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css">
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script>
    $(function () {
        $('.audit-table em').hover(function () {
            $(this).closest('tr').find('.loan-exp-wrap').css({'opacity': 1, 'visibility': 'visible'});
        }, function () {
            $(this).closest('tr').find('.loan-exp-wrap').css({'opacity': 0, 'visibility': 'hidden'});
        })

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

    })

    function submitForm(type) {
        if (!$("#validForm").valid()) {
            return;
        }
        var values = $('#validForm').getValues();
        values.type = type;
        yo.loadData({
            _c: "loan",
            _m: "auditRepayment",
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.href = '<?php echo getUrl('loan', 'requestToRepayment', array('type' => 'schema'), false, BACK_OFFICE_SITE_URL);?>'
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#validForm').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            remark: {
                required: true
            },
            received_date: {
                required: true
            }

        },
        messages: {
            remark: {
                required: '<?php echo 'Required!'?>'
            },
            received_date: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>
