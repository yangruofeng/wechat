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
            <td><?php echo 'Operator';?></td>
            <td><?php echo 'Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="9">No Record</td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $credit_grant) { ?>
                <tr>
                    <td>
                        <?php echo $credit_grant['obj_guid'];?>
                    </td>
                    <td>
                        <?php echo $credit_grant['display_name'] ? : $credit_grant['login_code'];?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_grant['monthly_repayment_ability']);?>
                    </td>
                    <td>
                        <?php echo $credit_grant['credit_terms'] . ' Months';?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_grant['default_credit']);?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_grant['max_credit']);?>
                    </td>
                    <td>
                        <?php echo $credit_grant['operator_name'];?>
                    </td>
                    <td>
                        <?php echo $credit_grant['grant_time'];?>
                    </td>
                    <td>
                        <a class="btn btn-default" href="<?php echo getUrl('loan_committee', 'showGrantCreditDetail', array('uid' => $credit_grant['uid']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-address-card-o"></i>Detail</a>
                    </td>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
