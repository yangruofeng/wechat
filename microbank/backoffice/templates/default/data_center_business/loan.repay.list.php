<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn';?></td>
            <td><?php echo 'Scheme';?></td>
            <td><?php echo 'Repay Type';?></td>
            <td><?php echo 'Client Name';?></td>
            <td><?php echo 'Currency'; ?></td>
            <td><?php echo 'Amount';?></td>
            <td><?php echo 'Principal';?></td>
            <td><?php echo 'Interest';?></td>
            <td><?php echo 'Operation Fee';?></td>
            <td><?php echo 'Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <a href="<?php echo getUrl('loan', 'contractDetail', array('uid' => $row['contract_id'], 'show_menu' => 'loan-contract'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['virtual_contract_sn'] ?></a><br/>
                </td>
                <td>
                    <?php echo $row['scheme_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['scheme_id'] > 0 ? 'Repayment' : 'Prepayment' ?><br/>
                </td>
                <td>
                    <?php echo $row['display_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['currency'] ?><br/>
                </td>
                <td>
                    <?php echo ncPriceFormat($row['amount']) ?><br/>
                </td>
                <td>
                    <?php echo ncPriceFormat($row['principal_amount']) ?><br/>
                </td>
                <td>
                    <?php echo ncPriceFormat($row['interest_amount']) ?><br/>
                </td>
                <td>
                    <?php echo ncPriceFormat($row['operation_fee_amount']) ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?><br/>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

