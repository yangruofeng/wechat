<div>
    <table class="table">
        <thead>
            <tr class="table-header t1">
                <td rowspan="2">Name of District</td>
                <td rowspan="2" class="number">District</td>
                <td rowspan="2" class="number">Commune</td>
                <td rowspan="2" class="number">Village</td>
                <td colspan="2" class="number">Loan Amount</td>
                <td rowspan="2" class="number">Principal Balance</td>
                <td rowspan="2" class="number">Pay late over 30days</td>
                <td rowspan="2" class="number">Male</td>
                <td rowspan="2" class="number">Female</td>
                <td rowspan="2" class="number">Total</td>
            </tr>
            <tr class="table-header t1">
                <td class="number">USD</td>
                <td class="number">KHR</td>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php if($data){?>
                <?php foreach ($data as $k => $v) {?>
                    <tr>
                        <td colspan="11" class="city fontweight600"><?php echo json_decode($v['text_name'], true)['kh'];?></td>
                    </tr>
                    <?php foreach ($v['children'] as $ck => $cv) {?>
                        <tr>
                            <td class="district"><?php echo json_decode($cv['text_name'], true)['kh'];?></td>
                            <td class="number"><?php echo $cv['district'];?></td>
                            <td class="number"><?php echo $cv['commune'];?></td>
                            <td class="number"><?php echo $cv['village'];?></td>
                            <td class="currency"><?php echo $cv['apply_amount']['USD']?ncPriceFormat($cv['apply_amount']['USD']):'-';?></td>
                            <td class="currency"><?php echo $cv['apply_amount']['KHR']?ncPriceFormat($cv['apply_amount']['KHR']):'-';?></td>
                            <td class="currency"><?php echo $cv['principal_balance']['USD']?ncPriceFormat($cv['principal_balance']['USD']):'-';?></td>
                            <td class="currency"><?php echo $cv['principal_balance']['KHR']?ncPriceFormat($cv['principal_balance']['KHR']):'-';?></td>
                            <td class="number"><?php echo $cv['male'];?></td>
                            <td class="number"><?php echo $cv['female'];?></td>
                            <td class="number"><?php echo $cv['total'];?></td>
                        </tr>
                    <?php }?>
                    <tr class="fontweight700">
                        <td class="city number"><?php echo json_decode($v['text_name'], true)['kh'];?> Total</td>
                        <td class="number"><?php echo $v['total']['district'];?></td>
                        <td class="number"><?php echo $v['total']['commune'];?></td>
                        <td class="number"><?php echo $v['total']['village'];?></td>
                        <td class="currency"><?php echo $v['total']['apply_amount']['USD']?ncPriceFormat($v['total']['apply_amount']['USD']):'-';?></td>
                        <td class="currency"><?php echo $v['total']['apply_amount']['KHR']?ncPriceFormat($v['total']['apply_amount']['KHR']):'-';?></td>
                        <td class="currency"><?php echo $v['total']['principal_balance']['USD']?ncPriceFormat($v['total']['principal_balance']['USD']):'-';?></td>
                        <td class="currency"><?php echo $v['total']['principal_balance']['KHR']?ncPriceFormat($v['total']['principal_balance']['KHR']):'-';?></td>
                        <td class="number"><?php echo $v['total']['male'];?></td>
                        <td class="number"><?php echo $v['total']['female'];?></td>
                        <td class="number"><?php echo $v['total']['total'];?></td>
                </tr>
                <?php }?>

                
            <?php }else{?>
                <tr><td colspan="10" class="no-record">No record.</td></tr>
            <?php }?>
        </tbody>
    </table>
</div>