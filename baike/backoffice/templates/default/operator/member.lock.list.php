<?php
$loanApplyStateLang = enum_langClass::getMemberStateLang();
?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>CID</td>
            <td>Account</td>
            <td>Client Name</td>
            <td>Contact Phone</td>
            <td>State</td>
            <td>Lock Time</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['obj_guid'] ?>
                </td>
                <td>
                    <?php echo $row['login_code'] ?>
                </td>
                <td>
                    <?php echo $row['display_name'] ?>
                </td>
                <td>
                    <?php echo $row['phone_id'] ?>
                </td>
                <td>
                    <?php echo $loanApplyStateLang[$row['member_state']] ?>
                </td>
                <td>
                    <?php echo $row['update_time'] ?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>







