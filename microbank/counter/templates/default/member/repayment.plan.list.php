<div>
    <form class="form-horizontal" id="repayment-scheme">
    <table class="table">
        <thead>
        <tr style="background-color: #DEDEDE">
            <td></td>
            <td>Repayment Time</td>
            <td>Contract Sn</td>
            <td>Scheme</td>
            <td>Currency</td>
            <td>Principal</td>
            <td>Interest</td>
            <td>Operating Charges</td>
            <td>Penalty</td>
            <td>Total</td>
        </tr>
        </thead>
        <tbody>
        <?php if ($data['data']['schema_list']) { ?>
            <?php foreach ($data['data']['schema_list'] as $row) { ?>
                <tr>
                    <td><input type="checkbox" name="scheme_id" class="checkbox-amount" onclick="scheme_select()" currency="<?php echo $row['currency']; ?>" total="<?php echo $row['total_payable_amount']; ?>" value="<?php echo $row['uid']?>" <?php echo $row['is_checked'] ? 'checked' : ''?>></td>
                    <td>
                        <?php echo dateFormat($row['receivable_date']) ?>
                    </td>
                    <td>
                        <?php echo $row['contract_sn'] ?>
                    </td>
                    <td>
                        <?php echo $row['scheme_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['receivable_principal']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['receivable_interest']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['receivable_operation_fee']) ?>
                    </td>
                    <td style="color:lightsteelblue">
                        <?php echo ncPriceFormat($row['penalty']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['total_payable_amount']) ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="10">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </form>

    <table class="table" style="margin-top: 20px">
        <thead>
        <tr style="background-color: #DEDEDE">
            <td><label class="control-label">Product Name</label></td>
            <td><?php echo $data['data']['sub_product_name']?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach ($data['currency_list'] as $key => $currency) { ?>
            <tr>
                <td><label class="control-label">Total(<?php echo $currency;?>)</label></td>
                <td><span class="currency_total money-style" currency="<?php echo $key?>"><?php echo ncPriceFormat($data['next_repayment_arr'][$key]) ?></span></td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="2" style="text-align: center;padding-top: 15px">
                <button type="button" class="btn btn-default" onclick="showProductList()"><i class="fa fa-angle-double-left"></i>Back</button>
                <button type="button" class="btn btn-primary" onclick="repayment_step1(<?php echo $data['member_id']?>)" title="<?php echo $data['is_verify'] ? '' : 'Unverified Member'?>" <?php echo $data['is_verify'] ? '' : 'disabled'?>><i class="fa fa-angle-double-right"></i>Next</button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

