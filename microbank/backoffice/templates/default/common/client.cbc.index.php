<?php
$client_cbc = $output['cbc'];
?>
<div class="row" style="max-width: 1000px">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list">
                    <table class="table">
                        <tbody class="table-body">
                        <?php if (!$client_cbc) { ?>
                            <tr>
                                <td>
                                    <?php include(template(":widget/no_record")); ?>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4"><label class="control-label">General</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">All Previous Enquiries</span></td>
                                <td><?php echo $client_cbc['all_previous_enquiries']; ?></td>
                                <td><span class="pl-25">Enquiries For Previous 30 Days</span></td>
                                <td><?php echo $client_cbc['enquiries_for_previous_30_days']; ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Earliest Loan Issue Date</span></td>
                                <td><?php echo dateFormat($client_cbc['earliest_loan_issue_date']); ?></td>
                                <td><span class="pl-25">Normal Accounts</span></td>
                                <td><?php echo $client_cbc['normal_accounts']; ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Delinquent Accounts</span></td>
                                <td><?php echo $client_cbc['delinquent_accounts']; ?></td>
                                <td><span class="pl-25">Closed Accounts</span></td>
                                <td><?php echo $client_cbc['closed_accounts']; ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Reject Accounts</span></td>
                                <td><?php echo $client_cbc['reject_accounts']; ?></td>
                                <td><span class="pl-25">Write Off Accounts</span></td>
                                <td><?php echo $client_cbc['write_off_accounts']; ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Total Limits(USD)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['total_limits']); ?></td>
                                <td><span class="pl-25">Total Liabilities(USD)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['total_liabilities']); ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Total Limits(KHR)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['total_limits_khr']); ?></td>
                                <td><span class="pl-25">Total Liabilities(KHR)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['total_liabilities_khr']); ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Total Limits(THB)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['total_limits_thb']); ?></td>
                                <td><span class="pl-25">Total Liabilities(THB)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['total_liabilities_thb']); ?></td>
                            </tr>
                            <tr>
                                <td colspan="4"><label class="control-label">Guarantee</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Normal Accounts</span></td>
                                <td><?php echo $client_cbc['guaranteed_normal_accounts']; ?></td>
                                <td><span class="pl-25">Delinquent Accounts</span></td>
                                <td><?php echo $client_cbc['guaranteed_delinquent_accounts']; ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Closed Accounts</span></td>
                                <td><?php echo $client_cbc['guaranteed_closed_accounts']; ?></td>
                                <td><span class="pl-25">Reject Accounts</span></td>
                                <td><?php echo $client_cbc['guaranteed_reject_accounts']; ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Write Off Accounts</span></td>
                                <td colspan="3"><?php echo $client_cbc['guaranteed_write_off_accounts']; ?></td>
                            </tr>
                            <tr>
                                <td ><span class="pl-25">Total Limits(USD)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['guaranteed_total_limits']); ?></td>
                                <td><span class="pl-25">Total Liabilities(USD)</span></td>
                                <td colspan="3"><?php echo ncAmountFormat($client_cbc['guaranteed_total_liabilities']); ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Total Limits(KHR)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['guaranteed_total_limits_khr']); ?></td>
                                <td><span class="pl-25">Total Liabilities(KHR)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['guaranteed_total_liabilities_khr']); ?></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Total Limits(THB)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['guaranteed_total_limits_thb']); ?></td>
                                <td><span class="pl-25">Total Liabilities(THB)</span></td>
                                <td><?php echo ncAmountFormat($client_cbc['guaranteed_total_liabilities_thb']); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>