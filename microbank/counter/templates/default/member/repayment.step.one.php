<div>
    <?php if (!$data['sts']) { ?>
        <span class="error-msg"><?php echo $data['msg']?></span>
    <?php } else { ?>
    <form class="form-horizontal" id="repayment_form_one">
        <input type="hidden" name="biz_id" value="<?php echo $data['data']['biz_id']?>">
        <table class="table">
            <tbody class="table-body">
            <tr>
                <td><label class="control-label">Detail</label></td>
                <td></td>
            </tr>
            <?php foreach ($data['currency_list'] as $key => $currency) { $total_amount = $data['data']['total_amount'][$key]?>
                <tr>
                    <td><span class="pl-25"><?php echo $currency; ?></span></td>
                    <td><span class="money-style"><?php echo ncPriceFormat($total_amount); ?></span></td>
                    <input type="hidden" id="<?php echo $currency . '_total'; ?>" value="<?php echo $total_amount ? : 0;?>">
                </tr>
            <?php } ?>
            <tr>
                <td><label class="control-label">Repayment</label></td>
                <td></td>
            </tr>

            <tr>
                <td class="col-sm-4"><span class="pl-25"><?php echo $lang['number_1'].' Currency' ?></span></td>
                <td class="col-sm-8">
                    <div class="input-group" style="max-width: 300px">
                        <input class="form-control currency_amount" type="number" name="usd_amount" value="" currency="<?php echo currencyEnum::USD;?>" onchange="calcCurrencyAmount(this)">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0;height: 30px;border-radius: 0px"><?php echo currencyEnum::USD;?></span>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="col-sm-4"><span class="pl-25"><?php echo $lang['number_2'].' Currency' ?></span></td>
                <td class="col-sm-8">
                    <div class="input-group" style="max-width: 300px">
                        <input class="form-control currency_amount" type="number" name="khr_amount" currency="<?php echo currencyEnum::KHR;?>" value="" onchange="calcCurrencyAmount(this)">
                        <span class="input-group-addon" style="min-width: 80px;border-left: 0;height: 30px;border-radius: 0px"><?php echo currencyEnum::KHR;?></span>
                    </div>
                </td>
            </tr>

            <input type="hidden" id="<?php echo currencyEnum::USD . '_min_value'; ?>" value="<?php echo currencyMinValueEnum::USD;?>">
            <input type="hidden" id="<?php echo currencyEnum::KHR . '_min_value'; ?>" value="<?php echo currencyMinValueEnum::KHR;?>">
            <?php foreach ($data['exchange_list'] as $key => $exchange) { ?>
                <input type="hidden" id="<?php echo $key; ?>" value="<?php echo $exchange;?>">
            <?php } ?>

            <tr>
                <td colspan="2" style="text-align: center;padding-top: 15px">
                    <a type="button" class="btn btn-default" onclick="showPlanList()"><i class="fa fa-angle-double-left"></i>Back</a>
                    <a type="button" class="btn btn-primary" onclick="repayment_step2()"><i class="fa fa-angle-double-right"></i>Next</a>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
    <?php } ?>
</div>
<script>
    function calcCurrencyAmount(_e) {
        var _amount = $(_e).val();
        var _currency = $(_e).attr('currency');
        var _currency_min_value = Number($('#' + _currency + '_min_value').val());
        _amount = Math.ceil(_amount / _currency_min_value) * _currency_min_value;
        _amount = Number(_amount).toFixed(fractionalDigit(_currency_min_value));
        $(_e).val(_amount);

        if (_currency == '<?php echo currencyEnum::USD?>') {
            var _other_currency = '<?php echo currencyEnum::KHR?>';
        } else {
            var _other_currency = '<?php echo currencyEnum::USD?>';
        }
        var _other_currency_min_value = Number($('#' + _other_currency + '_min_value').val());
        var _exchange = Number($('#' + _currency + '_' + _other_currency).val());
        var _other_exchange = Number($('#' + _other_currency + '_' + _currency).val());

        var _usd_total = Number($('#USD_total').val());
        var _khr_total = Number($('#KHR_total').val());
        if (_currency == '<?php echo currencyEnum::USD?>') {
            if (_amount > _usd_total) {
                var _usd_more = _amount - _usd_total;
                var _usd_to_khr_more = _usd_more * _exchange;
                _khr_total -= _usd_to_khr_more;
            } else {
                var _usd_lack = _usd_total - _amount;
                var _usd_to_khr_lack = _usd_lack / _other_exchange;
                _khr_total += _usd_to_khr_lack;
            }
            if (_khr_total > 0) {
                _khr_total = Math.ceil(_khr_total / _other_currency_min_value) * _other_currency_min_value;
                _khr_total = Number(_khr_total).toFixed(fractionalDigit(_other_currency_min_value));
                $('input[name="khr_amount"]').val(_khr_total);
            } else {
                $('input[name="khr_amount"]').val(0);
            }
        } else {
            if (_amount > _khr_total) {
                var _khr_more = _amount - _khr_total;
                var _khr_to_usd_more = _khr_more * _exchange;
                _usd_total -= _khr_to_usd_more;
            } else {
                var _khr_lack = _khr_total - _amount;
                var _khr_to_usd_lack = _khr_lack / _other_exchange;
                _usd_total += _khr_to_usd_lack;
            }

            if (_usd_total > 0) {
                _usd_total = Math.ceil(_usd_total / _other_currency_min_value) * _other_currency_min_value;
                _usd_total = Number(_usd_total).toFixed(fractionalDigit(_other_currency_min_value));
                $('input[name="usd_amount"]').val(_usd_total);
            } else {
                $('input[name="usd_amount"]').val(0);
            }
        }
    }

    function fractionalDigit(_number) {//几位小数
        if (Math.floor(_number) === _number) return 0;
        var x = String(_number).indexOf('.') + 1;
        var y = String(_number).length - x;
        return y;
    }
</script>