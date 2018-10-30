<?php
$certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
?>
<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td>Asset Id</td>
            <td>Client Name</td>
            <td>Client Phone</td>
            <td>Asset Type</td>
            <td>Asset Name</td>
            <td>Update Time</td>
            <td>State</td>
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
                        <?php echo $row['display_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id'] ?>
                    </td>
                    <td>
                        <?php echo $certificationTypeEnumLang[$row['asset_type']] ?>
                    </td>
                    <td>
                        <?php echo $row['asset_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['update_time'] ?>
                    </td>
                    <td>
                        <?php echo $row['asset_state'] ?>
                    </td>
                    <td>
                        <a class="btn btn-default"  onclick="showModal(<?php echo $row['uid'] ?>)"><?php echo 'Receive'?></a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="8">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
