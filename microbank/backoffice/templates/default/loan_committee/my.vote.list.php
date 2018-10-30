<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td>CID</td>
            <td><?php echo 'Member Name';?></td>
            <td><?php echo 'Monthly Repayment Ability';?></td>
            <td><?php echo 'Invalid Terms';?></td>
            <td><?php echo 'Default Credit';?></td>
            <td><?php echo 'Max Credit';?></td>
            <?php if ($data['state'] == 10) { ?>
                <td><?php echo 'Deadline'; ?></td>
            <?php } ?>
            <?php if ($data['state'] == 100) { ?>
                <td><?php echo 'Vote Result'; ?></td>
                <td><?php echo 'Vote Time'; ?></td>
            <?php } ?>
            <?php if ($data['state'] == 0) { ?>
                <td><?php echo 'Expire Reason'; ?></td>
            <?php } ?>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="20"><?php include(template(":widget/no_record"))?></td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td><?php echo $row['obj_guid']?></td>
                    <td>
                        <?php echo $row['display_name'] ? : $row['login_code'];?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($row['monthly_repayment_ability']);?>
                    </td>
                    <td>
                        <?php echo $row['credit_terms'] . ' Months';?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($row['default_credit']);?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($row['max_credit']);?>
                    </td>
                    <?php if($data['state'] == 10){?>
                        <td>
                            <?php echo $row['vote_expire_time']; ?>
                        </td>
                    <?php }?>
                    <?php if($data['state'] == 100){?>
                        <td>
                            <?php echo $row['my_vote_result'] == 100 ? 'Agree' : 'Disagree';?>
                        </td>
                        <td>
                            <?php echo timeFormat($row['my_update_time']);?>
                        </td>
                    <?php }?>
                    <?php if ($data['state'] == 0) { ?>
                        <td>
                            <?php if ($row['state'] == 20 || $row['state'] == 110) { ?>
                                The credit grant has rejected.
                            <?php } else { ?>
                                The vote has expired
                            <?php }?>
                        </td>
                    <?php } ?>
                    <td>
                        <?php
                        if ($data['state'] == 10) {
                            $method = 'voteCreditApplication';
                        } else {
                            $method = 'showCreditGrant';
                        }
                        ?>
                        <a class="btn btn-default" href="<?php echo getUrl('loan_committee', $method, array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-address-card-o"></i>Detail</a>
                    </td>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>
