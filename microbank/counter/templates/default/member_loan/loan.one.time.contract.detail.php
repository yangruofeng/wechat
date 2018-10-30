<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .container {
        width: 800px !important;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .collection-div {
        margin-bottom: 70px;
    }

    .authorize_input {
        margin-top: -8px!important;
        margin-bottom: 10px;
        position: relative;
    }

    .table{
        background-color: white!important;
    }

    .margin40{
        margin-top: 40px;
    }
</style>

<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="collection-div">
        <div class="basic-info" style="width: 900px">
            <table class="table contract-table">
                <thead>
                <tr class="table-header">
                    <td colspan="4"><label class="control-label">Basic Information</label></td>
                </tr>
                </thead>
                <tbody class="table-body">
                <tr>
                    <td>Contract SN</td>
                    <td><?php echo $output['contract_info']['contract_sn']; ?></td>
                    <td>Interest Rate:</td>
                    <td><?php echo  $output['contract_info']['interest_rate'].'% '.ucwords($output['contract_info']['interest_rate_unit']); ?></td>
                </tr>
                <tr>
                    <td>Client Code</td>
                    <td><?php echo $output['member_info']['login_code']; ?></td>
                    <td>Client phone</td>
                    <td><?php echo  $output['member_info']['phone_id']; ?></td>
                </tr>
                <tr>
                    <td>First Repay</td>
                    <td><?php echo $output['first_repay']['receivable_date']; ?></td>
                    <td>Repay Day</td>
                    <td><?php
                        switch ($output['due_date_type']){
                            case dueDateTypeEnum::FIXED_DATE:
                                echo $output['first_repay']['receivable_date'];
                                break;
                            case dueDateTypeEnum::PER_WEEK:
                                echo 'The ' . $output['due_date'] .'th of each week';
                                break;
                            case dueDateTypeEnum::PER_MONTH:
                                echo 'The ' . $output['due_date'] . 'th of each month';
                                break;
                            case dueDateTypeEnum::PER_YEAR:
                                echo 'The ' . $output['due_date'] . ' of each year';
                                break;
                            case dueDateTypeEnum::PER_DAY:
                                echo 'every day';
                                break;
                        }
                        ?></td>
                </tr>
                </tbody>
            </table>
            <table class="table">
                <thead>
                <tr class="table-header">
                    <td colspan="8"><label class="control-label">Total Amount</label></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Total Loan</td>
                    <td>Total Interest</td>
                    <td>Admin Fee</td>
                    <td>Loan Fee</td>
                    <td>Operation Fee</td>
                    <td>Insurance Fee</td>
                    <td>Actual Amount</td>
                    <td>Total Repayment</td>
                </tr>
                <?php if ($output['total_repay']) { ?>
                <tr>
                    <td>
                        <?php echo ncPriceFormat($output['total_repay']['total_loan']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($output['total_repay']['total_interest']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($output['total_repay']['total_admin_fee']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($output['total_repay']['total_loan_fee']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($output['total_repay']['total_operation_fee']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($output['total_repay']['total_insurance_fee']) ?>
                    </td>

                    <td>
                        <?php echo ncPriceFormat($output['total_repay']['actual_receive_amount']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($output['total_repay']['total_repayment']) ?>
                    </td>
                </tr>
                <?php } else { ?>
                    <tr>
                        <td colspan="8">No Record</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <table class="table">
                <thead>
                <tr class="table-header">
                    <td colspan="8"><label class="control-label">Installment Scheme</label></td>
                </tr>
                </thead>
                <tbody class="table-body">
                <tr>
                    <td>Periods</td>
                    <td>Repayment Time</td>
                    <td>Days</td>
                    <td>Payable Principal</td>
                    <td>Payable Interest</td>
                    <td>Operating Charges</td>
                    <td>Remaining Principal</td>
                    <td>Payable Total</td>
                </tr>
                <?php if ($output['loan_installment_scheme']) { ?>
                    <?php foreach ($output['loan_installment_scheme'] as $row) { ?>
                        <tr>
                            <td><?php echo $row['scheme_name']?></td>
                            <td><?php echo $row['receivable_date']?></td>
                            <td><?php echo system_toolClass::diffBetweenTwoDays($row['receivable_date'],$row['interest_date']); ?></td>
                            <td><?php echo ncPriceFormat($row['receivable_principal']);?></td>
                            <td><?php echo ncPriceFormat($row['receivable_interest']);?></td>
                            <td><?php echo ncPriceFormat($row['receivable_operation_fee']);?></td>
                            <td><?php echo ncPriceFormat($row['initial_principal']-$row['receivable_principal']);?></td>
                            <td><?php echo ncPriceFormat($row['amount']);?></td>
                        </tr>
                    <?php } ?>
                        <tr style="text-align: center"><td colspan="10">
                            <input type="hidden" id="contract_id" value="<?php echo $output['contract_id'] ?>"/>
                            <button type="button" class="btn btn-default" style="width: 200px" onclick="print_installment_scheme()"><i class="fa fa-print"></i>  Print Scheme</button>
                        </td></tr>
                <?php } else { ?>
                    <tr>
                        <td colspan="8">No Record</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="form-group button" style="width:1000px;text-align: center">
            <button type="button" class="btn btn-default" style="min-width: 80px;margin-top: 30px;margin-left: -100px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
        </div>
    </div>
</div>
<script>
    function print_installment_scheme() {
        var _uid = $('#contract_id').val();
        var _show_scheme = 1;
//        window.location.href = "<?php //echo getUrl('print_form', 'printInstallmentScheme', array(), false, ENTRY_COUNTER_SITE_URL)?>//&contract_id="+_uid+"&_show_scheme="+_show_scheme
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printInstallmentScheme', array(), false, ENTRY_COUNTER_SITE_URL)?>&contract_id="+_uid+"&_show_scheme="+_show_scheme);
    }
</script>