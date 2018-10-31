<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Member CID';?></td>
            <td><?php echo 'Member Name';?></td>
            <td><?php echo 'Contract No';?></td>
            <td><?php echo 'Credit';?></td>
            <td><?php echo 'Fee';?></td>
            <td><?php echo 'Loan Fee';?></td>
            <td><?php echo 'Admin Fee';?></td>
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
                        <?php echo $credit_grant['contract_no'];?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_grant['total_credit']);?>
                    </td>
                    <td>
                        <?php echo $credit_grant['fee'];?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_grant['loan_fee_amount']);?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($credit_grant['admin_fee_amount']);?>
                    </td>
                    <td>
                        <?php echo $credit_grant['officer_name'];?>
                    </td>
                    <td>
                        <?php echo $credit_grant['create_time'];?>
                    </td>
                    <td>
                        <a class="btn btn-default" href="<?php echo getUrl('loan_committee', 'showCreditContractDetail', array('uid' => $credit_grant['uid']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-address-card-o"></i>Detail</a>
                    </td>
                </tr>
            <?php }?>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
