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
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div style="max-width: 700px">

    <div class="ibox-title">
        <h5><i class="fa fa-id-card-o"></i>Prepayment Apply</h5>
    </div>
    <div class="content">
        <div>
            <form class="prepayment_form" action="<?php echo getUrl('member', 'confirmPrepaymentApply', array(), false, ENTRY_COUNTER_SITE_URL)?>" method="post">
                <input type="hidden" name="contract_id" value="<?php echo $output['contract_info']['contract_id']?>"/>
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
                        <td><?php echo $output['contract_info']['currency']?></td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Need repayment</label></td>
                        <td><span class="money-style"><?php echo $output['prepayment_info']['total_need_pay']['total']?></span></td>
                    </tr>
                    <tr class="detail">
                        <td style="padding-left: 20px">Principal</td>
                        <td><label class="control-label"><?php echo $output['prepayment_info']['total_need_pay']['principal']?></label></td>
                    </tr>
                    <tr class="detail">
                        <td style="padding-left: 20px">Interest</td>
                        <td><label class="control-label"><?php echo $output['prepayment_info']['total_need_pay']['interest']?></label></td>
                    </tr>
                    <tr class="detail">
                        <td style="padding-left: 20px">operation fee</td>
                        <td><label class="control-label"><?php echo $output['prepayment_info']['total_need_pay']['operation_fee']?></label></td>
                    </tr>
                    <tr class="detail">
                        <td style="padding-left: 20px">Penalty</td>
                        <td><label class="control-label"><?php echo $output['prepayment_info']['total_need_pay']['penalty']?></label></td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Left periods</label></td>
                        <td><?php echo $output['prepayment_info']['total_left_periods']?></td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Left Principal</label></td>
                        <td><span class="money-style"><?php echo $output['prepayment_info']['total_left_principal']?></span></td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Pay Methods</label></td>
                        <td>
                            <select style="width: 80%;height: 30px" name="prepayment_type">
                                <option value="1"><?php echo 'Full Amount';?></option>
<!--                                --><?php //$request_type=(new prepaymentRequestTypeEnum())->Dictionary();
//                                foreach($request_type as $k=>$v){?>
<!--                                    <option value="--><?php //echo $k;?><!--">--><?php //echo $lang['prepayment_request_type_'.$k];?><!--</option>-->
<!--                                --><?php //}?>
                            </select>
                        </td>
                    </tr>
                    <tr style="text-align: center">
                        <td colspan="2">
                            <a class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
                            <a class="btn btn-danger"><i class="fa fa-check"></i><?php echo 'Next' ?></a>
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
        $('.prepayment_form').submit();
    });


</script>

