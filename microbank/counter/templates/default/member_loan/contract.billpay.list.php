<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Bill Code</td>
            <td>Partner Code</td>
            <td>Partner Name</td>
            <td>Create Time</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td><?php echo $row['bill_code']?></td>
                    <td><?php echo $row['partner_code']?></td>
                    <td><?php echo $row['partner_name']?></td>
                    <td><?php echo $row['create_time']?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>