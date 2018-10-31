
<?php $payment_type_lang = enum_langClass::getLoanInstallmentTypeLang(); ?>
<div class="table-responsive">
    <table class="table table-bordered table-hover">

        <tr class="table-header">
            <td rowspan="2">BranchID</td>
            <td rowspan="2">Branch Name</td>
            <td rowspan="2">CID</td>
            <td rowspan="2" style="min-width: 150px;">Account Number</td>
            <td rowspan="2">Borrower Name</td>
            <td rowspan="2">Gender</td>
            <td rowspan="2">Coborrower Name</td>
            <td rowspan="2">Province</td>
            <td rowspan="2">District</td>
            <td rowspan="2">Commune</td>
            <td rowspan="2">Village</td>
            <td rowspan="2">Phone Number</td>
            <td rowspan="2">Product Type</td>
            <td rowspan="2" style="min-width: 100px;">Disbursed Date</td>
            <td rowspan="2" style="min-width: 100px;">Maturity Date</td>
            <td rowspan="2">Disbursed Amt</td>
            <td rowspan="2">Current Balance</td>
            <td rowspan="2">Loan Term</td>
            <td rowspan="2">Monthly IntRate</td>
            <td rowspan="2">Number Of Payment</td>
            <td rowspan="2">Cycle</td>
            <td rowspan="2">Currency</td>
            <td rowspan="2">Loan Purpose</td>
            <td rowspan="2">Co Name</td>
            <td rowspan="2" style="min-width: 100px;">Payment Type</td>
            <td rowspan="2">Day Late</td>
            <td rowspan="2">Provision Type</td>
            <td colspan="5">Payment Amount</td>
        </tr>
        <tr class="table-header">
            <td>Principal</td>
            <td>Interest</td>
            <td>Operation Fee</td>
            <td>Penalty</td>
            <td>Total</td>
        </tr>

        <?php if ($data['data']) {  foreach( $data['data'] as $row ){ ?>
            <tr>
                <td><?php echo $row['branch_id']; ?></td>
                <td><?php echo $row['branch_name']; ?></td>
                <td><?php echo $row['client_obj_guid']; ?></td>
                <td><?php echo $row['contract_sn']; ?></td>
                <td><?php echo $row['display_name']; ?></td>
                <td><?php echo $row['gender'] == memberGenderEnum::FEMALE?'F':'M'; ?></td>
                <td><?php echo $row['coborrower_name']; ?></td>
                <td><?php echo $row['id1_text']; ?></td>
                <td><?php echo $row['id2_text']; ?></td>
                <td><?php echo $row['id3_text']; ?></td>
                <td><?php echo $row['id4_text']; ?></td>
                <td><?php echo $row['phone_id']; ?></td>
                <td><?php echo $row['product_type']; ?></td>
                <td><?php echo date('Y-m-d',strtotime($row['start_date'])); ?></td>
                <td><?php echo date('Y-m-d',strtotime($row['end_date'])); ?></td>
                <td><?php echo ncPriceFormat($row['apply_amount']); ?></td>
                <td><?php echo ncPriceFormat($row['loan_balance']); ?></td>
                <td><?php echo $row['loan_period_value'].' '.ucwords($row['loan_period_unit']); ?></td>
                <td><?php echo $row['monthly_interest_rate'].'%'; ?></td>
                <td><?php echo $row['repayment_num']; ?></td>
                <td><?php echo $row['loan_actual_cycle']; ?></td>
                <td><?php echo $row['currency']; ?></td>
                <td><?php echo $row['propose']; ?></td>
                <td><?php echo $row['officer_name']; ?></td>
                <td><?php echo $payment_type_lang[$row['repayment_type']]?:$row['repayment_type']; ?></td>
                <td><?php echo $row['day_late']; ?></td>
                <td><?php echo 'Regular'; ?></td>
                <td><?php echo ncPriceFormat($row['repayment_principal']); ?></td>
                <td><?php echo ncPriceFormat($row['repayment_interest']); ?></td>
                <td><?php echo ncPriceFormat($row['repayment_operation_fee']); ?></td>
                <td><?php echo ncPriceFormat($row['repayment_penalty']); ?></td>
                <td><?php echo ncPriceFormat($row['repayment_principal']+$row['repayment_interest']+$row['repayment_operation_fee']+$row['repayment_penalty']); ?></td>
            </tr>

        <?php } } else { ?>
            <tr>
                <td colspan="50">No Record</td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php if (!$is_print) { ?>
    <?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>
