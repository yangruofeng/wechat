<style>
  .verify-table .locking {
    color: red;
    font-style: normal;
  }
  .verify-table .locking i {
    margin-right: 3px;
  }

</style>
<div>
    <table class="table verify-table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn';?></td>
            <td><?php echo 'Client Name';?></td>
            <td><?php echo 'Loss Amount';?></td>
            <td><?php echo 'Close Remark';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Creator';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Auditor';?></td>
            <?php if ($data['type'] == 'processed') { ?>
                <td><?php echo 'Audit Time';?></td>
            <?php } ?>
            <?php if ($data['type'] != 'processed') { ?>
                <td><?php echo 'Function';?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo $row['contract_sn']; ?>
                </td>
                <td>
                    <?php echo $row['display_name']; ?>
                </td>
                <td>
                    <?php echo ncAmountFormat($row['loss_amount']);?>
                </td>
                <td>
                    <?php echo $row['close_remark'];?>
                </td>
                <td>
                    <?php if ($row['state'] == writeOffStateEnum::APPROVING) {
                        if ($data['cur_uid'] == $row['auditor_id']) {
                            echo '<span class="locking"><i class="fa fa-gavel"></i>' . $lang['write_off_state_' . $row['state']] . '</span>';
                        } else {
                            echo '<span class="locking">' . $lang['write_off_state_' . $row['state']] . '</span>';
                        }
                    } else {
                        echo $lang['write_off_state_' . $row['state']];
                    }
                    ?>
                </td>
                <td>
                    <?php echo $row['creator_name']?>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time'])?>
                </td>
                <td>
                    <?php echo $row['auditor_name']?>
                </td>
                <?php if ($data['type'] == 'processed') { ?>
                    <td><?php echo timeFormat($row['update_time'])?></td>
                <?php } ?>
                <?php if ($data['type'] != 'processed') { ?>
                  <td>
                    <div class="custom-btn-group">
                        <a title="" class="custom-btn custom-btn-secondary" href="<?php echo getUrl('loan', 'auditWriteOff', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                            <span><i class="fa fa-check-circle-o"></i>Audit</span>
                        </a>
                    </div>
                  </td>
                <?php } ?>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>
