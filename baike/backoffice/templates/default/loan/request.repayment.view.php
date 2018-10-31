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
$payment_type_lang = $output['payment_type_lang'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Repayment</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('loan', 'requestToRepayment', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Schema</span></a>
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
                    <td>
                        <?php
                            if( $detail['type'] == requestRepaymentTypeEnum::SCHEME ){
                                echo 'Schema repayment';
                            }elseif( $detail['type'] == requestRepaymentTypeEnum::BALANCE ){
                                echo 'Prepayment';
                            }else{
                                echo 'Unknown';
                            }
                        ?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Actual Repayment</label></td>
                    <td><?php echo ncPriceFormat($detail['amount']) ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Currency</label></td>
                    <td><?php echo $detail['currency']; ?></td>
                </tr>

                <tr>
                    <td><label class="control-label">State</label></td>
                    <td><?php echo $lang['request_repayment_state_' . $detail['state']] ?></td>
                </tr>

                <?php if ($detail['payer_name']) { ?>
                <tr>
                    <td><label class="control-label">Payer Name</label></td>
                    <td>
                        <?php echo $detail['payer_name'] ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($detail['payer_type']) { ?>
                <tr>
                    <td><label class="control-label">Payer Type</label></td>
                    <td>
                        <?php echo $payment_type_lang[$detail['payer_type']]?:'Unknown'; ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($detail['payer_account']) { ?>
                <tr>
                    <td><label class="control-label">Payer Account</label></td>
                    <td>
                        <?php echo $detail['payer_account'] ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($detail['payer_phone']) { ?>
                <tr>
                    <td><label class="control-label">Payer Phone</label></td>
                    <td>
                        <?php echo $detail['payer_phone'] ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($detail['bank_name']) { ?>
                <tr>
                    <td><label class="control-label">Bank Name</label></td>
                    <td>
                        <?php echo $detail['bank_name'] ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($detail['bank_account_name']) { ?>
                <tr>
                    <td><label class="control-label">Bank Account Name</label></td>
                    <td>
                        <?php echo $detail['bank_account_name'] ?>
                    </td>
                </tr>
                <?php } ?>
                <?php if ($detail['bank_account_no']) { ?>
                <tr>
                    <td><label class="control-label">Bank Account No</label></td>
                    <td>
                        <?php echo $detail['bank_account_no'] ?>
                    </td>
                </tr>
                <?php } ?>
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

                <?php if ($detail['auditor_name']) { ?>
                    <tr>
                        <td><label class="control-label">Auditor Name</label></td>
                        <td><?php echo $detail['auditor_name'] ?></td>
                    </tr>
                <?php } ?>

                <?php if ($detail['audit_remark']) { ?>
                    <tr>
                        <td><label class="control-label">Audit Remark</label></td>
                        <td><?php echo $detail['audit_remark'] ?></td>
                    </tr>
                <?php } ?>

                <?php if ($detail['audit_time']) { ?>
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
                <?php } ?>

                <?php if ($detail['handle_remark']) { ?>
                    <tr>
                        <td><label class="control-label">Handle Remark</label></td>
                        <td><?php echo $detail['handle_remark'] ?></td>
                    </tr>
                <?php } ?>

                <?php if ($detail['handle_time']) { ?>
                    <tr>
                        <td><label class="control-label">Handle Time</label></td>
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
