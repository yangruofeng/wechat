
    <div>
        <table class="table table-bordered">
            <thead>
            <tr class="table-header">
                <td>Client ID</td>
                <td>Client Code</td>
                <td>Client Phone</td>
                <td>Function</td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php if ($data['data']) { ?>
                <?php foreach ($data['data'] as $row) { ?>
                    <tr>
                        <td>
                            <?php echo $row['uid'] ?>
                        </td>
                        <td>
                            <?php echo $row['login_code'] ?>
                        </td>
                        <td>
                            <?php echo $row['phone_id'] ?>
                        </td>
                        <td>
                            <a class="btn btn-default" onclick="showModal(<?php echo $row['uid']?>,'<?php echo $row['login_code']?>')"><?php echo 'Receive'?></a>
                        </td>
                    </tr>
                <?php }?>
            <?php } else { ?>
                <tr>
                    <td colspan="8">No Record</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php include_once(template("widget/inc_content_pager"));?>