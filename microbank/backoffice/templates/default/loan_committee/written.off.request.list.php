<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn'; ?></td>
            <td><?php echo 'Client Account'; ?></td>
            <td><?php echo 'Close Type'; ?></td>
            <td><?php echo 'Currency'; ?></td>
            <td><?php echo 'Loss Amount'; ?></td>
            <?php if ($data['state'] >= writeOffStateEnum::APPROVING) { ?>
                <td><?php echo 'Auditor'; ?></td>
            <?php } ?>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="7">No Record</td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['contract_sn']; ?>
                    </td>
                    <td>
                        <?php echo $row['login_code']; ?>
                    </td>
                    <td>
                        <?php echo $row['close_type'] == contractWriteOffTypeEnum::SYSTEM ? 'System' : 'Abnormal'; ?>
                    </td>
                    <td>
                        <?php echo $row['currency']; ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['loss_amount']); ?>
                    </td>
                    <?php if ($data['state'] >= writeOffStateEnum::APPROVING) { ?>
                        <td><?php echo $row['operator_name']; ?></td>
                    <?php } ?>
                    <td>
                        <?php $method = $data['state'] == writeOffStateEnum::APPROVING ? 'voteWrittenOff' : ($data['state'] == writeOffStateEnum::CREATE ? 'handleWrittenOff' : 'showWrittenOff')?>
                        <a class="btn btn-default" href="<?php echo getUrl('loan_committee', $method, array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-address-card-o"></i>Detail</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
