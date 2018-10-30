<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Credit Officer'; ?></td>
            <td><?php echo 'Overdue Client'; ?></td>
            <td><?php echo 'Overdue Contract'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($data['data'] as $row) { ?>
            <tr>
                <td>
                    <?php echo $row['officer_name']; ?>
                </td>
                <td>
                    <?php echo $row['member_count']; ?>
                </td>
                <td>
                    <?php echo $row['loan_count']; ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('branch_manager', 'overdueContractForCo', array('co_id' => $row['officer_id']), false, BACK_OFFICE_SITE_URL)?>">
                            <span><i class="fa fa-address-card-o"></i>Detail</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>