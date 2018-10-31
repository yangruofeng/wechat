<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Client'; ?></td>
            <td><?php echo 'Contract Sn'; ?></td>
            <td><?php echo 'Scheme'; ?></td>
            <td><?php echo 'Currency'; ?></td>
            <td><?php echo 'Receivable Amount'; ?></td>
            <td><?php echo 'Receivable Date'; ?></td>
            <td><?php echo 'Overdue Days'; ?></td>
            <td><?php echo 'Overdue Amount'; ?></td>
            <td><?php echo 'Follow-Co'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($data['data'] as $row) { ?>
            <tr>
                <td>
                    <?php echo $row['display_name']; ?>
                </td>
                <td>
                    <?php echo $row['virtual_contract_sn']; ?>
                </td>
                <td>
                    <?php echo $row['scheme_name']; ?>
                </td>
                <td>
                    <?php echo $row['currency']; ?>
                </td>
                <td class="currency">
                    <?php echo ncPriceFormat($row['amount']); ?>
                </td>
                <td>
                    <?php echo dateFormat($row['receivable_date']); ?>
                </td>
                <td>
                    <?php echo $row['overdue_days']; ?>
                </td>
                <td class="currency">
                    <?php echo ncPriceFormat($row['overdue_amount']); ?>
                </td>
                <td>
                    <?php if ($row['overdue_contract_task']) { ?>
                        <span style="font-weight: 600;"><?php echo $row['overdue_contract_task']['co_name']?></span>
                        <a href="<?php echo getUrl('branch_manager', 'getCoCheckOverdueList', array('scheme_id' => $row['uid'], 'co_id' => $row['overdue_contract_task']['co_id']), false, BACK_OFFICE_SITE_URL)?>" title="Check Follow" style="color:darkred;margin-left: 10px;font-style: italic"><?php echo $row['overdue_contract_task']['state'] == 2 ? 'Done' : 'In Progress'?></a>
                        <span style="margin-left: 10px;font-style: italic"><?php echo $row['overdue_contract_task']['create_time']?></span>
                    <?php } ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <button class="custom-btn custom-btn-secondary" onclick="allot_to_check('<?php echo $row['uid'];?>','<?php echo $row['overdue_contract_task']['co_id']?>','<?php echo $row['member_id']?>')" >
                            <span><i class="fa fa-edit"></i>Allot to check</span>
                        </button>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>