<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'CID';?></td>
            <td><?php echo 'Account';?></td>
            <td><?php echo 'Phone';?></td>
            <td><?php echo 'Credit';?></td>
            <td><?php echo 'Credit Balance';?></td>
            <td><?php echo 'Work Type';?></td>
            <td><?php echo 'Grade';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){ $credit_info = memberClass::getCreditBalance($row['uid']); ?>
            <tr>
                <td>
                    <?php echo $row['obj_guid'] ?>
                </td>
                <td>
                    <?php echo $row['login_code'] ?>
                </td>
                <td>
                    <?php echo $row['phone_id'] ?>
                </td>
                <td>
                    <?php echo $credit_info['credit']; ?>
                </td>
                <td>
                    <?php echo $credit_info['balance']; ?>
                </td>
                <td>
                    <?php echo ucwords(str_replace('_', ' ', $row['work_type'])); ?>
                </td>
                <td>
                    <?php echo $row['grade_code']; ?>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']); ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <a title="" class="custom-btn custom-btn-secondary"
                           href="<?php echo getUrl('loan_committee', 'editMemberCredit', array('member_id' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                            <span><i class="fa fa-vcard-o"></i>Edit Credit</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
