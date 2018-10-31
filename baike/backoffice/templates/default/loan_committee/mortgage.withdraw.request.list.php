<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'RequestTime'; ?></td>
            <td><?php echo 'AssetName'; ?></td>
            <td><?php echo 'AssetSn'; ?></td>
            <td><?php echo 'MemberName'; ?></td>
            <td><?php echo 'Creator'; ?></td>
            <td>Remark</td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="7">
                    <?php include(template(":widget/no_record"))?>
                </td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['create_time']; ?>
                    </td>
                    <td>
                        <?php echo $row['asset_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['asset_sn']; ?>
                    </td>
                    <td>
                        <?php echo $row['member_display_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['creator_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['remark']?>
                    </td>
                    <td>
                        <a class="btn btn-default"
                           href="<?php echo getUrl('loan_committee', "approveWithdrawMortgageRequestDetail", array('request_id' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <i class="fa fa-address-card-o"></i>
                            Detail
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
