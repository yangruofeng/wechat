
<?php
$contract_detail_data = $output['contract_detail_data'];
$contract_info = $contract_detail_data['contract_info'];
$decimal=2;
if($contract_info['currency']==currencyEnum::KHR){
    $decimal=0;
}
$contract_installment_schema = $contract_detail_data['loan_installment_scheme'];
$member_info = $contract_detail_data['member_info'];
?>
<div class="basic-info" style="width: 900px">

    <table class="table contract-table">
        <thead>
        <tr class="table-header">
            <td colspan="4"><label class="control-label">Basic Information</label></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <tr>
            <td>Loan Amount</td>
            <td><?php echo ncPriceFormat($contract_info['apply_amount']); ?></td>
            <td>Currency</td>
            <td><?php echo $contract_info['currency']; ?></td>

        </tr>
        <tr>
            <td>Contract SN</td>
            <td><?php echo $contract_info['contract_sn']; ?></td>
            <td>Loan Terms</td>
            <td><?php echo $contract_info['loan_period_value'].$contract_info['loan_period_unit']; ?></td>

        </tr>

        <tr>
            <td>Operation Fee</td>
            <td><?php echo  $contract_info['operation_fee'].'% '.ucwords($contract_info['operation_fee_unit']); ?></td>
            <td>Interest Rate</td>
            <td><?php echo  $contract_info['interest_rate'].'% '.ucwords($contract_info['interest_rate_unit']); ?></td>
        </tr>

        <tr>
            <td>Client Name</td>
            <td><?php echo ($member_info['display_name']?:$member_info['login_code']).'('. $member_info['obj_guid'].')'; ?></td>
            <td>Client phone</td>
            <td><?php echo  $member_info['phone_id']; ?></td>
        </tr>
        <tr>
            <td>First Repay</td>
            <td><?php $first_repay = reset($contract_installment_schema);
                echo $first_repay['receivable_date']; ?></td>
            <td>Repay Day</td>
            <td><?php
                switch ($contract_info['due_date_type']){
                    case dueDateTypeEnum::FIXED_DATE:
                        echo $first_repay['receivable_date'];
                        break;
                    case dueDateTypeEnum::PER_WEEK:
                        echo 'The ' . $contract_info['due_date'] .'th of each week';
                        break;
                    case dueDateTypeEnum::PER_MONTH:
                        echo 'The ' . $contract_info['due_date'] . 'th of each month';
                        break;
                    case dueDateTypeEnum::PER_YEAR:
                        echo 'The ' . $contract_info['due_date'] . ' of each year';
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
            <td colspan="10"><label class="control-label">Total Amount</label></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Total Loan</td>
            <td>Admin Fee</td>
            <td>Loan Fee</td>
            <td>Total Interest</td>
            <td>Operation Fee</td>
            <td>Service Charges</td>
            <td>Insurance Fee</td>
            <td>Disburse Amount</td>
            <td>Total Repayment</td>
        </tr>
        <?php if ( true ) { ?>
            <tr>
                <td>
                    <?php echo ncPriceFormat($contract_info['apply_amount'],$decimal) ?>
                </td>
                <td>
                    <?php echo ncPriceFormat($contract_detail_data['total_admin_fee'],$decimal) ?>
                </td>
                <td>
                    <?php echo ncPriceFormat($contract_detail_data['total_loan_fee'],$decimal) ?>
                </td>
                <td>
                    <?php echo ncPriceFormat($contract_detail_data['total_interest'],$decimal) ?>
                </td>

                <td>
                    <?php echo ncPriceFormat($contract_detail_data['total_operation_fee'],$decimal) ?>
                </td>
                <td>
                    <?php echo ncPriceFormat($contract_detail_data['total_service_fee'],$decimal) ?>

                </td>
                <td>
                    <?php echo ncPriceFormat($contract_detail_data['total_insurance_fee'],$decimal) ?>
                </td>

                <td>
                    <kbd>
                        <?php echo ncPriceFormat($contract_detail_data['actual_receive_amount'],$decimal) ?>
                    </kbd>
                </td>
                <td>
                    <strong>
                        <?php echo ncPriceFormat($contract_detail_data['total_repayment'],$decimal) ?>
                    </strong>
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
            <td>Operation Fee</td>
            <td>Remaining Principal</td>
            <td>Payable Total</td>
        </tr>
        <?php if ($contract_installment_schema) { ?>
            <?php foreach ($contract_installment_schema as $row) { ?>
                <tr>
                    <td><?php echo $row['scheme_name']?></td>
                    <td><?php echo $row['receivable_date']?></td>
                    <td><?php echo system_toolClass::diffBetweenTwoDays($row['receivable_date'],$row['interest_date']); ?></td>
                    <td><?php echo ncPriceFormat($row['receivable_principal'],$decimal);?></td>
                    <td><?php echo ncPriceFormat($row['receivable_interest'],$decimal);?></td>
                    <td><?php echo ncPriceFormat($row['receivable_operation_fee'],$decimal);?></td>
                    <td><?php echo ncPriceFormat($row['initial_principal']-$row['receivable_principal'],$decimal);?></td>
                    <td><?php echo ncPriceFormat($row['amount'],$decimal);?></td>
                </tr>
            <?php } ?>
            <tr style="text-align: center"><td colspan="10">
                    <input type="hidden" id="contract_id" value="<?php echo $contract_info['uid']; ?>"/>
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

<script>
    function print_installment_scheme() {
        var _uid = '<?php echo $contract_info['uid']; ?>';
        var _show_scheme = 1;
        if( window.external ){
            try{
                window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printInstallmentScheme', array(), false, ENTRY_COUNTER_SITE_URL)?>&contract_id="+_uid+"&_show_scheme="+_show_scheme);
            }catch (ex ){
                alert(ex.Message);
            }
        }else{
            alert('Undefined');
        }
    }
</script>