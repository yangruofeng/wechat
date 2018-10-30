<div>
    <table class="table">
        <thead>
            <tr class="table-header t1">
                <td rowspan="2">Provision Classification</td>
                <td rowspan="2">Borrower's Name</td>
                <td rowspan="2" class="number">Overdue Days</td>
                <td rowspan="2" class="number">Number of loans</td>
                <td rowspan="2" class="number">Loan Balance</td>
                <td rowspan="2" class="number">Overdue Balance</td>
                <td rowspan="2" class="number">Accrued Interest</td>
                <td colspan="2" class="number">Provisions</td>
            </tr>
            <tr class="table-header t1">
                <td class="number">Rate(%)</td>
                <td class="number">Amount</td>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php if(count($data) > 0){ ?>
                <?php foreach ($data as $k => $v) { ?>
                        <tr class="fontweight600">
                            <td colspan="9"><?php echo $k == 'less' ? 'Term Less Than 365 Days' : 'Term Greater Than 365 Days';?></td>
                        </tr>
                        <?php foreach ($v as $ck => $cv) {?>
                            <tr class="fontweight700">
                                <td><?php echo $ck;?></td>
                                <td></td>
                                <td></td>
                                <td class="number"><?php echo $cv['total']['contract_id']?:'0';?></td>
                                <td class="currency"><?php echo $cv['total']['loan_balance']?ncPriceFormat($cv['total']['loan_balance']):'-';?></td>
                                <td class="currency"><?php echo $cv['total']['principal']?ncPriceFormat($cv['total']['principal']):'-';?></td>
                                <td class="currency"><?php echo $cv['total']['interest']?ncPriceFormat($cv['total']['interest']):'-';?></td>
                                <td class="currency"><?php echo $cv['total']['rate'];?></td>
                                <td class="currency"><?php echo ncPriceFormat($cv['total']['amount']);?></td>
                            </tr>
                            <?php foreach ($cv['contract'] as $key => $value) {?>
                                <tr>
                                    <td><?php echo $value['contract_sn'];?></td>
                                    <td><?php echo $value['login_code'];?></td>
                                    <td class="number"><?php echo $value['days']>0?$value['days']:'0';?></td>
                                    <td></td>
                                    <td class="currency"><?php echo $value['loan_balance']?ncPriceFormat($value['loan_balance']):'-';?></td>
                                    <td class="currency"><?php echo $value['principal']?ncPriceFormat($value['principal']):'-';?></td>
                                    <td class="currency"><?php echo $value['interest']?ncPriceFormat($value['interest']):'-';?></td>
                                    <td class="currency"><?php echo $value['rate'];?></td>
                                    <td class="currency"><?php echo ncPriceFormat($value['amount']);?></td>
                                </tr>
                            <?php }?>
                        <?php }?>
                <?php }?>
            <?php }else{?>
                <tr><td colspan="9"><div class="no-record">No Record</div></td></tr>
            <?php }?>
        </tbody>
    </table>
</div>