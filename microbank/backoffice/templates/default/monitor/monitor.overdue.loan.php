<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Monitor</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Overdue Loan</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <table class="table table-striped table-bordered table-hover">
                    <tr class="table-header">
                        <td>CID</td>
                        <td>Client Name</td>
                        <td>Contract No.</td>
                        <td>Product</td>
                        <td>Loan Amount</td>
                        <td>Currency</td>
                        <td>Over Period</td>
                        <td>Receivable date</td>
                        <td>Principal Balance</td>
                        <td>Receivable</td>
                        <td>Received</td>
                        <td>Branch Name</td>
                    </tr>
                    <?php if(!count($output['loan_list'])){?>
                        <tr>
                            <td colspan="20">
                                <?php include(template(":widget/no_record"))?>
                            </td>
                        </tr>
                    <?php }?>
                    <?php foreach($output['loan_list'] as $item){?>
                        <tr>
                            <td><?php echo $item['obj_guid']?></td>
                            <td><?php echo $item['display_name']?></td>
                            <td><?php echo $item['contract_sn']?></td>
                            <td><?php echo $item['credit_category']?></td>
                            <td><?php echo ncPriceFormat($item['apply_amount'])?></td>
                            <td><?php echo $item['currency']?></td>
                            <td><?php echo $item['scheme_name']?></td>
                            <td><?php echo $item['receivable_date']?></td>
                            <td><?php echo ncAmountFormat($item['initial_principal']-$item['paid_principal'])?></td>
                            <td><?php echo $item['amount']?></td>
                            <td><?php echo $item['actual_payment_amount']?></td>
                            <td><?php echo $item['branch_name']?></td>

                        </tr>
                    <?php }?>
                </table>

            </div>
        </div>
    </div>
</div>