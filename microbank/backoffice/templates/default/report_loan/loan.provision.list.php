<div>
    <table class="table">
        <thead>
            <tr class="table-header t1">
                <td rowspan="2">Provision Classification</td>
                <td rowspan="2">Borrower's Name</td>
                <td rowspan="2" class="number">Overdue Days</td>
                <td rowspan="2" class="number">Number of loans</td>
                <td rowspan="2" class="currency-title">Loan Balance</td>
                <td rowspan="2" class="currency-title">Overdue Balance</td>
                <td rowspan="2" class="currency-title">Accrued Interest</td>
                <td colspan="2" class="number">Provisions</td>
            </tr>
            <tr class="table-header t1">
                <td class="currency-title">Rate(%)</td>
                <td class="currency-title">Amount</td>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php if(count($data) > 0){ ?>
                <?php foreach ($data as $k => $v) { ?>
                    <?php if($k != 'total'){?>
                        <tr class="fontweight700">
                            <td colspan="9"><?php echo $k == 'less' ? 'Term Less Than 365 Days' : 'Term Greater Than 365 Days';?></td>
                        </tr>
                        <?php foreach ($v as $ck => $cv) {?>
                            <?php if($ck == 'total'){?>
                                <tr class="fontweight700" style="border-top: 1px solid black;background-color: white">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="number"><?php echo $cv['contract_id']?:'0';?></td>
                                    <td class="currency"><?php echo $cv['loan_balance']?ncPriceFormat($cv['loan_balance']):'-';?></td>
                                    <td class="currency"><?php echo $cv['principal']?ncPriceFormat($cv['principal']):'-';?></td>
                                    <td class="currency"><?php echo $cv['interest']?ncPriceFormat($cv['interest']):'-';?></td>
                                    <td class="currency"><?php echo $cv['rate'];?></td>
                                    <td class="currency"><?php echo ncPriceFormat($cv['amount']);?></td>
                                </tr>
                            <?php }else{ if($cv){?>
                                <tr>
                                    <td><?php echo $ck;?></td>
                                    <td></td>
                                    <td></td>
                                    <td class="number"><?php echo $cv['contract_id']?:'0';?></td>
                                    <td class="currency"><?php echo $cv['loan_balance']?ncPriceFormat($cv['loan_balance']):'-';?></td>
                                    <td class="currency"><?php echo $cv['principal']?ncPriceFormat($cv['principal']):'-';?></td>
                                    <td class="currency"><?php echo $cv['interest']?ncPriceFormat($cv['interest']):'-';?></td>
                                    <td class="currency"><?php echo $cv['rate'];?></td>
                                    <td class="currency"><?php echo ncPriceFormat($cv['amount']);?></td>
                                </tr>
                            <?php }}?>
                        <?php }?>
                    <?php }else{?>
                        <tr class="fontweight700" style="font-size: 18px;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="number"><?php echo $v['contract_id']?:'0';?></td>
                            <td class="currency"><?php echo $v['loan_balance']?ncPriceFormat($v['loan_balance']):'-';?></td>
                            <td class="currency"><?php echo $v['principal']?ncPriceFormat($v['principal']):'-';?></td>
                            <td class="currency"><?php echo $v['interest']?ncPriceFormat($v['interest']):'-';?></td>
                            <td class="currency"><?php echo $v['rate'];?></td>
                            <td class="currency"><?php echo ncPriceFormat($v['amount']);?></td>
                        </tr>
                    <?php }?>
                <?php }?>
            <?php }else{?>
                <tr><td colspan="9"><div class="no-record">No Record</div></td></tr>
            <?php }?>
        </tbody>
    </table>
</div>