<?php
$certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
$flow_type=(new assetStorageFlowType())->Dictionary();
?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Asset-SN';?></td>
            <td><?php echo 'Asset-Name';?></td>
            <td><?php echo 'Asset-Type';?></td>
            <td><?php echo 'Evaluation';?></td>
            <td><?php echo 'Storage';?></td>
            <td><?php echo 'Remark';?></td>
            <td><?php echo 'Time';?></td>

        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="7">
                    <?php include(template(":widget/no_record")); ?>
                </td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['member_assets_id'];?>
                    </td>
                    <td>
                        <?php echo $row['asset_name'];?>
                    </td>
                    <td>
                        <?php echo $certificationTypeEnumLang[$row['asset_type']] ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['evaluation']);?>
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
                        <?php echo $row['remark'];?>
                    </td>
                    <td>
                        <?php echo timeFormat($row['create_time']);?>
                    </td>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>