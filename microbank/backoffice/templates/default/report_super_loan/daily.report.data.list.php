<style>
    .total-amount{
        font-size: 1.2em;
        color:red;
        font-weight: 600;
    }
</style>
<table class="table table-bordered table-striped table-hover">
    <tr class="table-header">
        <td>No.</td>
        <td>Branch</td>
        <td>CO Name</td>
        <td>Customer Name</td>
        <!--<td>Customer Address</td>-->
        <td>Province</td>
        <td>District</td>
        <td>Commune</td>
        <td>Village</td>
        <td>Cycle</td>
        <td>Loan Account</td>
        <td>ACE Account</td>
        <td>Phone Number</td>
        <td>Credit Amount</td>
        <td>Credit Date</td>
        <td>Maturity Date</td>
        <td>Loan Amount</td>
        <td>Repayment Amount</td>
        <td>Repayment Date</td>
        <td>Loan Arrea</td>
        <td>Number of withdrawal</td>
        <td>Day Late</td>
        <td>Closed Date</td>
        <td>Remark</td>
    </tr>
    <?php
        $i=0;
        $total_credit_amount = 0;
        $total_loan_amount = 0;
        $total_repayment_amount = 0;
        $total_loan_arrea = 0;
        foreach( $data['data'] as $item){
    ?>
        <tr>
            <td>
                <?php echo ++$i;?>
            </td>
            <td>
                <?php echo $item['branch_name']?>
            </td>
            <td>
                <?php echo $item['officer_name']?>
            </td>
            <td>
                <?php echo $item['display_name']?>
            </td>
            <!--<td>
                <?php /*echo $item['client_address']*/?>
            </td>-->
            <td>
                <?php echo $item['id1_text']?>
            </td>
            <td>
                <?php echo $item['id2_text']?>
            </td>
            <td>
                <?php echo $item['id3_text']?>
            </td>
            <td>
                <?php echo $item['id4_text']?>
            </td>

            <td>
                <?php echo $item['loan_cycle']?>
            </td>
            <td>
                <?php echo $item['obj_guid']; ?>
            </td>
            <td>
                <?php echo $item['ace_account']; ?>
            </td>
            <td>
                <?php echo $item['phone_id']; ?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['category_credit'],0); ?>
            </td>
            <td>
                <?php echo formatDateToYmd($item['credit_grant_time']); ?>
            </td>
            <td>
                <?php echo $item['maturity_date']?formatDateToYmd($item['maturity_date']):''; ?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['loan_amount']);?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['repayment_amount']);?>
            </td>
            <td>
                <?php echo $item['repayment_date']?formatDateToYmd($item['repayment_date']):''; ?>
            </td>

            <td>
                <?php echo ncPriceFormat($item['loan_arrea']);?>
            </td>
            <td>
                <?php echo $item['withdraw_number']; ?>
            </td>
            <td>
                <?php echo $item['day_late']; ?>
            </td>
            <td>
                <?php echo $item['closed_date']?formatDateToYmd($item['closed_date']):''; ?>
            </td>
            <td>

            </td>
        </tr>
    <?php
        $total_credit_amount+=$item['category_credit'];
        $total_loan_amount += $item['loan_amount'];
        $total_repayment_amount += $item['repayment_amount'];
        $total_loan_arrea += $item['loan_arrea'];
        }
    ?>
    <tr class="success">
        <td colspan="2" class="text-right"><label for="">Total</label></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="total-amount"><?php echo ncPriceFormat($total_credit_amount,0); ?></td>
        <td></td>
        <td></td>
        <td class="total-amount"><?php echo ncPriceFormat($total_loan_amount); ?></td>
        <td class="total-amount"><?php echo ncPriceFormat($total_repayment_amount); ?></td>
        <td></td>
        <td class="total-amount"><?php echo ncPriceFormat($total_loan_arrea); ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>
<?php if (!$is_print) { ?>
<?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>
