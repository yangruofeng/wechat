<div style="padding-top: 15px">
     <div class="col-sm-5 mincontent">
         <?php if(!$data['sts']){  ?>
             <div class="col-sm-12 form-group">
                 <label class="col-sm-5 control-label"><?php echo 'Hint' ?></label>
                 <div class="col-sm-7">
                     <label class="col-sm-12 control-label"><?php echo  $data['msg'] ?></label>
                 </div>
             </div>
         <?php } ?>
         <div class="col-sm-12 form-group">
             <label class="control-label" style="font-size: 16px"><?php echo 'Due Amount' ?></label>
         </div>
        <div class="col-sm-12 form-group">
            <label class="col-sm-5 control-label"><?php echo 'Currency' ?></label>
            <div class="col-sm-7">
                <span class="money-style"><?php echo $data['data']['currency'] ?></span>
            </div>
        </div>
        <div class="col-sm-12 form-group">
            <label class="col-sm-5 control-label"><?php echo 'Amount' ?></label>
            <div class="col-sm-7">
                <span class="money-style"><?php echo ncPriceFormat($data['data']['paid']);?></span>
                <input type="hidden" id="<?php echo $data['data']['currency'] . '_total'; ?>" value="<?php echo $data['data']['paid'] ? : 0;?>">
                <?php if($data['data']['currency'] == currencyEnum::USD){ ?>
                    <input type="hidden" id="KHR_total" value="0">
                <?php }else{?>
                    <input type="hidden" id="USD_total" value="0">
                <?php }?>
            </div>
        </div>
     </div>
     <div class="col-sm-7 mincontent">
         <div class="col-sm-12 form-group">
             <label class="control-label" style="font-size: 16px"><?php echo 'Actual Amount' ?></label>
         </div>
            <form id='penalty_two'>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-4 control-label"><?php echo 'Receive Money From' ?></label>
                    <div class="col-sm-8">
                        <label class="control-label"><input type="radio" name="payment_way" value="<?php echo repaymentWayEnum::PASSBOOK ?>" checked> <?php echo 'Balance'?></label><br/>
                        <label class="control-label"><input type="radio" name="payment_way" value="<?php echo repaymentWayEnum::CASH ?>"><?php echo 'Cash'?></label>
                    </div>
                </div>
                <table class="col-sm-12 form-group">
                    <tr class="col-sm-12">
                        <td class="col-sm-4"><label><?php echo $lang['number_1'].' Currency' ?></label></td>
                        <td class="col-sm-8">
                            <div class="input-group" style="max-width: 300px">
                                <input type="number" name='usd_amount' value="" class="form-control currency_amount" currency="<?php echo currencyEnum::USD;?>" onchange="calcCurrencyAmount(this)">
                                <span class="input-group-addon" style="min-width: 60px;border-left: 0;height: 30px;border-radius: 0px;"><?php echo currencyEnum::USD; ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr class="col-sm-12" style="margin-top: 20px">
                        <td class="col-sm-4"><label><?php echo $lang['number_2'].' Currency' ?></label></td>
                        <td class="col-sm-8">
                            <div class="input-group" style="max-width: 300px">
                                <input type="number" name='khr_amount' value="" class="form-control currency_amount" currency="<?php echo currencyEnum::KHR;?>" onchange="calcCurrencyAmount(this)">
                                <span class="input-group-addon" style="min-width: 60px;border-left: 0;height: 30px;border-radius: 0px;"><?php echo currencyEnum::KHR; ?></span>
                            </div>
                        </td>
                    </tr>
                </table>

                <input type="hidden" id="<?php echo currencyEnum::USD . '_min_value'; ?>" value="<?php echo currencyMinValueEnum::USD;?>">
                <input type="hidden" id="<?php echo currencyEnum::KHR . '_min_value'; ?>" value="<?php echo currencyMinValueEnum::KHR;?>">
                <?php foreach ($data['exchange_list'] as $key => $exchange) { ?>
                    <input type="hidden" id="<?php echo $key; ?>" value="<?php echo $exchange;?>">
                <?php } ?>
                <div class="col-sm-12 form-group">
                    <label class="col-sm-4 control-label"><?php echo 'Remark' ?></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control"  name="remark" value="">
                    </div>
                </div>
                <input type="hidden" name="biz_id" value="<?php echo $data['biz_id']?>">
                <div class="col-sm-12 form-group" style="text-align: center;margin-bottom: 20px">
                    <a type="button" class="btn btn-primary btn-block" onclick="penalty_apply_two()">NEXT</a>
                </div>
            </form>
        </div>
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