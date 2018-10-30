<?php $currency_list = $data['currency_list'];?>
<?php $currency_total = $data['currency_total'];?>
<div>
    <form class="form-horizontal" id="penalty-form-one">
        <table class="table">
            <tbody class="table-body">
            <tr>
                <td><label class="control-label">Detail</label></td>
                <td></td>
            </tr>
            <?php foreach ($currency_list as $key => $currency) { ?>
                <tr>
                    <td><span class="pl-25"><?php echo $currency; ?></span></td>
                    <td><span class="money-style"><?php echo ncPriceFormat($currency_total[$key]); ?></span></td>
                </tr>
            <?php } ?>
            <tr>
                <td><label class="control-label">Penalty</label></td>
                <td></td>
            </tr>
            <tr>
                <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>"/>
                <td><span class="pl-25"><?php echo 'Currency' ?></span></td>
                <td>
                    <select class="form-control" name="currency" onclick="selectCurrency(this)">
                        <option value="0">Please Currency</option>
                        <?php foreach ($currency_list as $key => $currency) { ?>
                            <option value="<?php echo $key ?>"><?php echo $currency ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><span class="pl-25"><?php echo 'Payable Total' ?></span></td>
                <td>
                    <input type="text" class="convert_total form-control" id="0_total" value="" readonly>
                    <?php foreach ($data['one_currency_total'] as $key => $total) { ?>
                        <input type="text" class="convert_total form-control" id="<?php echo $key?>_total" value="<?php echo ncPriceFormat($total)?>" readonly style="display: none">
                        <input type="hidden" id="<?php echo $key?>_total_hidden" value="<?php echo $total;?>">
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td><span class="pl-25"><?php echo 'Reduce Amount' ?></span></td>
                <td>
                    <input type="number" class="form-control" name="deducting" value="" onchange="reduceChange()">
                </td>
            </tr>
            <tr>
                <td><span class="pl-25"><?php echo 'Actual Amount' ?></span></td>
                <td>
                    <input type="text" class="form-control" id="actual_amount" value="" readonly>
                </td>
            </tr>
            <tr>
                <td><span class="pl-25"><?php echo 'Remark' ?></span></td>
                <td>
                    <textarea class="form-control" name="remark"/>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;padding-top: 15px">
                    <a type="button" class="btn btn-default" onclick="showPenaltyDetail()"><i class="fa fa-angle-double-left"></i>Back</a>
                    <a type="button" class="btn btn-primary" onclick="submit_apply_two()"><i class="fa fa-angle-double-right"></i>Next</a>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>