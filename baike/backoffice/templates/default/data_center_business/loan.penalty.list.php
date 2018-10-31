<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn';?></td>
            <td><?php echo 'Client Name';?></td>
            <td><?php echo 'Contact Number';?></td>
            <td><?php echo 'Currency'; ?></td>
            <td><?php echo 'Overdue Amount';?></td>
            <td><?php echo 'Overdue Days';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <a href="<?php echo getUrl('loan', 'contractDetail', array('uid' => $row['contract_id'], 'show_menu' => 'loan-contract'), false, BACK_OFFICE_SITE_URL)?>"><?php echo $row['contract_sn'] ?></a><br/>
                </td>
                <td>
                    <?php echo $row['display_name'] ?><br/>
                </td>
                <td>
                    <?php echo $row['phone_id'] ?><br/>
                </td>
                <td>
                    <?php echo $row['currency'] ?><br/>
                </td>
                <td>
                    <?php echo ncPriceFormat($row['penalty_amount']) ?><br/>
                </td>
                <td>
                    <?php
                    $date1 = strtotime(date('Y-m-d', strtotime($row['receivable_date'])));
                    $date2 = strtotime(date('Y-m-d'));
                    $date = $date2 - $date1;
                    echo $date / 86400 . 'Days';
                    ?><br/>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

