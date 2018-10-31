<style>
    .avatar-icon {
        width: 50px;
        height: 50px;
    }
</style>
<?php
 $state_list=(new loanConsultStateEnum())->Dictionary();
?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Applicant Name'; ?></td>
            <td><?php echo 'Contact Phone'; ?></td>
            <td><?php echo 'Apply Amount'; ?></td>
            <td><?php echo 'Currency'?></td>
            <td><?php echo 'Terms'; ?></td>
            <td><?php echo 'Purpose';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Allot Branch';?></td>
            <td><?php echo 'Allot CO'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) {?>
                <tr>
                    <td>
                      <?php echo $row['applicant_name']?>
                    </td>
                    <td>
                        <?php echo $row['contact_phone']?>
                    </td>
                    <td>
                        <?php echo $row['apply_amount'] ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td><?php echo $row['loan_time']." ".$row['loan_time_unit']?></td>
                    <td>
                        <?php echo $row['loan_purpose']?>
                    </td>
                    <td>
                        <?php echo $row['create_time']?>
                    </td>
                    <td>
                        <?php echo $state_list[$row['state']];?>
                    </td>
                    <td>
                        <?php echo $row['branch_name']?>
                    </td>
                    <td>
                        <?php echo $row['co_name']?>
                    </td>
                    <td>
                        <div class="custom-btn-group">
                            <a class="custom-btn custom-btn-secondary"
                                href="<?php echo getUrl('operator', 'showConsultPage', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-vcard-o"></i><?php echo 'Detail';?></span>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="20">
                    <?php require_once template(":widget/no_record")?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
