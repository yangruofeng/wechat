<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
    }

    .width250{
        width: 250px;
    }

    .content{
        padding: 5px
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }

</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div style="max-width: 700px">
        <div class="ibox-title">
            <h5><i class="fa fa-id-card-o"></i>Confirm Prepayment</h5>
        </div>
        <div class="content">
                <div>
                    <form class="prepayment_confirm" action="<?php echo getUrl('member_loan', 'submitPrepaymentApply', array(), false, ENTRY_COUNTER_SITE_URL)?>" method="post">
                        <input type="hidden" name="contract_id" value="<?php echo $output['contract_info']['contract_id']?>"/>
                        <input type="hidden" name="prepayment_type" value="<?php echo $output['prepayment_type']?>"/>
                        <table class="table contract-table">
                            <tbody class="table-body">
                            <tr>
                                <td class="width250"><label class="control-label">Client-Name</label></td>
                                <td><?php echo $output['client_info']['login_code']?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Contract Sn</label></td>
                                <td><?php echo $output['contract_info']['contract_sn']?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Currency</label></td>
                                <td><?php echo $output['prepayment_preview']['currency']?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Principal</label></td>
                                <td><span class="money-style"><?php echo ncPriceFormat($output['prepayment_preview']['total_paid_principal'])?></span></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Interest</label></td>
                                <td><span class="money-style"><?php echo ncPriceFormat($output['prepayment_preview']['total_paid_interest'])?></span></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Operation fee</label></td>
                                <td><span class="money-style"><?php echo ncPriceFormat($output['prepayment_preview']['total_paid_operation_fee'])?></span></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Total Amount</label></td>
                                <td><span class="money-style"><?php echo ncPriceFormat($output['prepayment_preview']['total_prepayment_amount'])?></span></td>
                            </tr>
                            <tr style="text-align: center">
                                <td colspan="2">
                                    <a class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
                                    <a class="btn btn-danger"><i class="fa fa-check"></i><?php echo 'Submit' ?></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
   </div>


</div>


<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>


<script>

    $('.btn-danger').click(function () {
        $('.prepayment_confirm').submit();
    });


</script>

