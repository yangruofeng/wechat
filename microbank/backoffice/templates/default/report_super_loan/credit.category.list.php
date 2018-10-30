<table class="table table-bordered table-striped table-hover">
    <tr class="table-header">
        <td>No.</td>
        <td>Branch Name</td>
        <td>CID</td>
        <td>Client Name</td>
        <td>Phone</td>
        <td>SuperLoan Credit</td>
        <td>Credit Balance</td>
        <td>Loan Times</td>
        <td>Loan Amount</td>
        <td>Service Fee</td>
        <td>Outstanding</td>
    </tr>
    <?php foreach($data['data'] as $i=>$item){?>
        <tr>
            <td>
                <?php echo $i+1;?>
            </td>
            <td><?php echo $item['branch_name']; ?></td>
            <td>
                <?php echo $item['obj_guid']?>
            </td>
            <td>
                <?php echo $item['display_name']?>
            </td>
            <td>
                <?php echo $item['phone_id']?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['credit'],0)?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['credit_balance'],0);?>
            </td>
            <td>
                <?php echo $item['loan_times']?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['loan_amount'],0)?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['service_fee'],0)?>
            </td>
            <td>
                <?php echo ncPriceFormat($item['outstanding'],0)?>
            </td>
        </tr>
    <?php }?>
</table>
<?php if (!$is_print) { ?>
<?php include_once(template("widget/inc_content_pager")); ?>
<?php } ?>

