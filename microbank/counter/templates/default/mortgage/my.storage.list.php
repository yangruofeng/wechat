<?php
$certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
$flow_type=(new assetStorageFlowType())->Dictionary();
?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Asset-SN</td>
            <td>Asset-Name</td>
            <td>Asset-Type</td>
            <td>From</td>
            <td>To</td>
            <td>Storage Type</td>
            <td>Time</td>
            <td>Client</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['asset_sn'] ?>
                    </td>
                    <td>
                        <?php echo $row['asset_name'] ?>
                    </td>
                    <td>
                        <?php echo $certificationTypeEnumLang[$row['asset_type']] ?>
                    </td>
                    <td>
                        <label>
                            <?php echo $row['form_operator_name']?:$row['member_name']?>
                        </label>
                        <em style="padding-left: 20px;font-size: 11px;font-style: italic">
                            <?php echo $row['from_branch_name']?:'CLIENT'?>
                        </em>
                    </td>
                    <td>
                        <label>
                            <?php echo $row['to_operator_name']?>
                        </label>
                        <em style="padding-left: 20px;font-size: 11px;font-style: italic">
                            <?php echo $row['to_branch_name']?>
                        </em>
                    </td>
                    <td>
                        <?php echo $flow_type[$row['flow_type']] ?>
                    </td>
                    <td>
                        <?php echo $row['create_time'] ?>
                    </td>
                    <td>
                        <label>
                            <?php echo $row['member_name']?>
                        </label>
                        <em style="padding-left: 20px;font-size: 11px;font-style: italic">
                            <?php echo $row['phone_id']?>
                        </em>
                    </td>
                    <td>
                        <a class="btn btn-default"
                           href="<?php echo getUrl('mortgage', 'showMyStorageAssetDetailPage', array('asset_id'=>$row['member_asset_id']), false, ENTRY_COUNTER_SITE_URL); ?>">
                            <i class="fa fa-id-card"></i><?php echo 'detail' ?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="20"<?php include(template(":widget/no_record"))?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
