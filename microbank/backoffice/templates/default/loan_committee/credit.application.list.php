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
            <td>Time</td>
            <?php if(!$data['state']){?>
                <td><?php echo 'Branch';?></td>
            <?php }?>
            <td><?php echo 'Operator';?></td>
            <?php if ($data['state'] == 2) { ?>
                <td><?php echo 'Reject Remark';?></td>
            <?php } else { ?>
                <td><?php echo 'Function';?></td>
            <?php } ?>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="20"><?php include(template(":widget/no_record"))?></td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $credit_suggest) { ?>
                <tr>
                    <td>
                        <?php echo generateGuid($credit_suggest['member_id'],objGuidTypeEnum::CLIENT_MEMBER)?>
                    </td>
                    <td>
                        <?php echo $credit_suggest['display_name'] ? : $credit_suggest['login_code'];?>
                    </td>

                    <td>
                        <?php echo ncAmountFormat($credit_suggest['monthly_repayment_ability']);?>
                    </td>
                    <td>
                        <?php echo $credit_suggest['credit_terms'] . ' Months';?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_suggest['default_credit']);?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_suggest['max_credit']);?>
                    </td>
                    <td>
                        <?php echo $credit_suggest['vote_time'] ? timeFormat($credit_suggest['vote_time']) : timeFormat($credit_suggest['update_time']); ?>
                    </td>

                    <?php if(!$data['state']){?>
                        <td>
                            <?php echo $credit_suggest['branch_name'];?>
                        </td>
                    <?php }?>
                    <td>
                        <?php echo $credit_suggest['operator_name'];?>
                    </td>
                    <?php if ($data['state'] == 2) { ?>
                        <td><?php echo $credit_suggest['remark'];?></td>
                    <?php } else { ?>
                        <td>
                            <?php
                            if ($data['state'] > commonApproveStateEnum::APPROVING) {
                                $method = 'showCreditGrant';
                            } elseif ($data['state'] == commonApproveStateEnum::APPROVING) {
                                $method = 'voteCreditApplication';
                            } else {
                                $method = 'showRequestCredit';
                            }
                            ?>
                            <a class="btn btn-default" href="<?php echo getUrl('loan_committee', $method, array('uid' => $credit_suggest['uid']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-address-card-o"></i>Detail</a>
                        </td>
                    <?php } ?>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>
