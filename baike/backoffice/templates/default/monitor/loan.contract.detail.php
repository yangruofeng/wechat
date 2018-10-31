<?php
$data = $output['data'];
$contract_info = $data['contract_info'];
$product_info = $data['loan_product_info'];
$member_info = $data['member_info'];
$remain_payable_amount = $data['remain_payable_amount'];
$loan_installment_scheme = $data['loan_installment_scheme'];
$billpay_history = $data['billpay_history'];
$repayment_history = $data['repayment_history'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan Contract</h3>
            <ul class="tab-base">
                <li><a  class="current"><span>Detail</span></a></li>
                <li><a onclick="javascript:history.back(-1);">Back</a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="data-content">
            <div class="col-sm-5">
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Contract Information</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <td><label for="">Client Code</label></td><td><?php echo $member_info['login_code'];?></td>
                                </tr>
                                <tr>
                                    <td><label for="">Contract Sn</label></td><td><?php echo $contract_info['contract_sn'];?></td>
                                </tr>
                                <tr>
                                    <td><label for="">Product</label></td><td><?php echo $data['product_category_name'];?></td>
                                </tr>
                                <tr>
                                    <td><label for="">Loan Date</label></td><td><?php echo dateFormat($contract_info['start_date']);?></td>
                                </tr>
                                <tr>
                                    <td><label for="">Terms</label></td><td><?php echo $contract_info['loan_period_value'];?>&nbsp;<?php echo $contract_info['loan_period_unit'];?></td>
                                </tr>
                                <tr>
                                    <td><label for="">Due Date</label></td><td><?php echo 'The ' . $contract_info['due_date'] . 'th of each month' ?></td>
                                </tr>
                                <tr>
                                    <td><label for="">State</label></td><td><?php echo $lang['loan_contract_state_' . $contract_info['state']]?><input type="hidden" id="state" value="<?php echo $contract_info['state']?>"/></td>
                                </tr>
                                <tr>
                                    <td><label for="">Currency</label></td><td><?php echo $contract_info['currency'];?></td>
                                </tr>
                                <tr>
                                    <td><label for="">Loan Limit</label></td>
                                    <td><?php echo ncPriceFormat($contract_info['receivable_principal']);?></td>
                                </tr>
                                <tr>
                                    <td><label for="">Remains Principal</label></td><td><?php echo ncPriceFormat($remain_payable_amount['principal']);?></td>
                                </tr>
                                <tr>
                                    <td><label for="">Remains Balance</label></td><td><?php echo ncPriceFormat($remain_payable_amount['total']);?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Billpay History</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr class="table-header">
                                    <td>Bill Code</td>
                                    <td>Partner Code</td>
                                    <td>Partner Name</td>
                                    <td>Create Time</td>
                                </tr>
                                </thead>
                                <tbody class="table-body">
                                <?php if ($billpay_history) { ?>
                                    <?php foreach ($billpay_history as $row) { ?>
                                        <tr>
                                            <td><?php echo $row['bill_code']?></td>
                                            <td><?php echo $row['partner_code']?></td>
                                            <td><?php echo $row['partner_name']?></td>
                                            <td><?php echo $row['create_time']?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="4">
                                            <div>
                                                <?php include(template(":widget/no_record")); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Repayment History</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr class="table-header">
                                    <td>Periods</td>
                                    <td>Repayment Time</td>
                                    <td>Payable Principal</td>
                                    <td>Payable Interest</td>
                                    <td>Operating Charges</td>
                                    <td>Penalty</td>
                                    <td>Repayment Total</td>
                                </tr>
                                </thead>
                                <tbody class="table-body">
                                <?php if ($repayment_history) { ?>
                                    <?php foreach ($repayment_history as $row) { ?>
                                        <tr>
                                            <td><?php echo $row['scheme_name']?></td>
                                            <td><?php echo $row['done_time']?></td>
                                            <td><?php echo ncPriceFormat($row['receivable_principal'])?></td>
                                            <td><?php echo ncPriceFormat($row['receivable_interest'])?></td>
                                            <td><?php echo ncPriceFormat($row['receivable_operation_fee'] + $row['receivable_admin_fee'])?></td>
                                            <td><?php echo ncPriceFormat($row['penalty'])?></td>
                                            <td><?php echo ncPriceFormat($row['amount'] + $row['penalty'])?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="7">
                                            <div>
                                                <?php include(template(":widget/no_record")); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Installment Scheme</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr class="table-header">
                                    <td>Periods</td>
                                    <td>Repayment Time</td>
                                    <td>Payable Principal</td>
                                    <td>Payable Interest</td>
                                    <td>Operating Charges</td>
                                    <td>Penalty</td>
                                    <td>Payable Total</td>
                                    <td>State</td>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                <?php if($loan_installment_scheme){?>
                                    <?php foreach($loan_installment_scheme as $row){?>
                                        <tr>
                                            <td><?php echo $row['scheme_name']?></td>
                                            <td><?php echo $row['receivable_date']?></td>
                                            <td><?php echo ncPriceFormat($row['receivable_principal'])?></td>
                                            <td><?php echo ncPriceFormat($row['receivable_interest'])?></td>
                                            <td><?php echo ncPriceFormat($row['receivable_operation_fee'] + $row['receivable_admin_fee'])?></td>
                                            <td><?php echo ncPriceFormat($row['penalty'])?></td>
                                            <td><?php echo ncPriceFormat($row['amount'] + $row['penalty'])?></td>
                                            <td>
                                                <?php if($row['state'] == 100 && $k == $i) {
                                                    echo 'Paid off';
                                                } else if ($row['state'] == 100) {
                                                    echo 'Paid';
                                                } else if ($row['receivable_date'] < strtotime('Y-m-d 23:59:59',time())) {
                                                    echo 'Overdue';
                                                } else {
                                                    echo 'Pending Repayment';
                                                } ?>
                                            </td>
                                        </tr>
                                    <?php }?>
                                <?php }else{?>
                                    <tr>
                                        <td colspan="8">
                                            <div>
                                                <?php include(template(":widget/no_record")); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



