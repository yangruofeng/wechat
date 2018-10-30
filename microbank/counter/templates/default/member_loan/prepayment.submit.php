<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .width250 {
        width: 250px;
    }

    .content {
        padding: 5px
    }

    .info {
        margin-top: 5px;
        margin-left: 10px;
        font-size: 12px;
    }

    .btn {
        min-width: 80px;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }
</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div style="max-width: 700px">
        <div class="ibox-title">
            <h5><i class="fa fa-id-card-o"></i>Receive Prepayment</h5>
        </div>
        <div class="content">
            <div>
                <form class="receive_prepayment" action="<?php echo getUrl('member_loan', 'receivePrepayment', array(), false, ENTRY_COUNTER_SITE_URL)?>" method="post">
                    <input type="hidden" name="biz_id" value="<?php echo $output['prepayment_info']['data']['biz_id']?>"/>
                    <input type="hidden" name="contract_id" value="<?php echo $output['prepayment_info']['contract_id']?>"/>
                    <table class="table contract-table">
                        <tbody class="table-body">
                        <tr>
                            <td class="width250" colspan="2"><label class="control-label">Detail</label></td>
                        </tr>
                        <tr>
                            <td class="width250"><label class="control-label info">Client-Name</label></td>
                            <td><?php echo $output['prepayment_info']['member_code']?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label info">Contract Sn</label></td>
                            <td><?php echo $output['prepayment_info']['contract_sn']?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label info">Currency</label></td>
                            <td><?php echo $output['prepayment_info']['currency']?></td>
                            <input type="hidden" name="default_currency" value="<?php echo $output['prepayment_info']['currency']?>"/>
                        </tr>
                        <tr>
                            <td><label class="control-label info">Principal</label></td>
                            <td><label class="control-label"><?php echo ncPriceFormat($output['prepayment_info']['apply_info']['payable_principal']) ?></label></td>
                        </tr>
                        <tr>
                            <td><label class="control-label info">Interest</label></td>
                            <td><label class="control-label"><?php echo ncPriceFormat($output['prepayment_info']['apply_info']['payable_interest']) ?></label></td>
                        </tr>
                        <tr>
                            <td><label class="control-label info">Operation fee</label></td>
                            <td><label class="control-label"><?php echo ncPriceFormat($output['prepayment_info']['apply_info']['payable_operation_fee']) ?></label></td>
                        </tr>
                        <tr>
                            <td><label class="control-label info">Penalty</label></td>
                            <td><label class="control-label"><?php echo ncPriceFormat($output['prepayment_info']['apply_info']['payable_penalty']) ?></label></td>
                        </tr>
                        <tr>
                            <td><label class="control-label info">Total Amount</label></td>
                            <input type="hidden" name="default_amount" value="<?php echo$output['prepayment_info']['apply_info']['total_payable_amount'] ?>"/>
                            <td><span class="money-style"><?php echo ncPriceFormat($output['prepayment_info']['apply_info']['total_payable_amount']) ?></span></td>
                            <input type="hidden" id="<?php echo $output['prepayment_info']['currency']. '_total'; ?>" value="<?php echo $output['prepayment_info']['apply_info']['total_payable_amount'] ? : 0;?>">
                            <?php if($output['prepayment_info']['currency'] == currencyEnum::USD){ ?>
                                <input type="hidden" id="KHR_total" value="0">
                            <?php }else{?>
                                <input type="hidden" id="USD_total" value="0">
                            <?php }?>
                        </tr>

                        <tr>
                            <td colspan="2"><label class="control-label">Repayment</label></td>
                        </tr>
                        <tr>
                            <td><label class="control-label info">Receive Money From</label></td>
                            <td><select class="form-control" name="repayment_way" onchange="changetype(this)">
                                    <option value="<?php echo repaymentWayEnum::PASSBOOK ?>">Balance</option>
                                    <option value="<?php echo repaymentWayEnum::CASH ?>">Cash</option>
                                </select>
                            </td>
                        </tr>
                        <tr id="choose_usd" style="display: none">
                            <td class="col-sm-4"><label class="control-label info"><?php echo $lang['number_1'].' Currency' ?></label></td>
                            <td class="col-sm-8">
                                <span>
                                    <input type="number" name='usd_amount' value="" class="col-sm-8" style="height: 30px;border: 1px solid #ccc;width: 362px" currency="<?php echo currencyEnum::USD;?>" onchange="calcCurrencyAmount(this)" >
                                     <span class="input-group-addon col-sm-4" style="min-width: 80px;border-left: 0;height: 30px;border-radius: 0px;"><?php echo currencyEnum::USD; ?></span>
                                </span>
                            </td>
                        </tr>
                        <tr id="choose_khr" style="display: none">
                            <td class="col-sm-4"><label class="control-label info"><?php echo $lang['number_2'].' Currency' ?></label></td>
                            <td class="col-sm-8">
                                <span>
                                    <input type="number" name='khr_amount' value="" class="col-sm-8" style="height: 30px;border: 1px solid #ccc;width: 362px" currency="<?php echo currencyEnum::KHR;?>" onchange="calcCurrencyAmount(this)">
                                     <span class="input-group-addon col-sm-4" style="min-width: 80px;border-left: 0;height: 30px;border-radius: 0px;"><?php echo currencyEnum::KHR; ?></span>
                                </span>
                            </td>
                        </tr>

                        <input type="hidden" id="<?php echo currencyEnum::USD . '_min_value'; ?>" value="<?php echo currencyMinValueEnum::USD;?>">
                        <input type="hidden" id="<?php echo currencyEnum::KHR . '_min_value'; ?>" value="<?php echo currencyMinValueEnum::KHR;?>">
                        <?php foreach ($output['prepayment_info']['exchange_list'] as $key => $exchange) { ?>
                            <input type="hidden" id="<?php echo $key; ?>" value="<?php echo $exchange;?>">
                        <?php } ?>

                        <tr style="text-align: center">
                            <td colspan="2">
                                <a class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
                                <a class="btn btn-danger" id="next_btn"><i class="fa fa-check"></i><?php echo 'Next' ?></a>
                            </td>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function changetype(_e) {
        $("#choose_usd").hide();
        $("#choose_khr").hide();
        var type = $(_e).val();
        if(type == <?php echo repaymentWayEnum::CASH ?> ){
            $("#choose_usd").show();
            $("#choose_khr").show();
        }
    }

    $('#next_btn').click(function () {
        var values = $('.receive_prepayment').getValues();
//        if (!values.usd_amount && !values.khr_amount) {
//            alert('Please input prepayment amount.');
//            return;
//        }
        $('.receive_prepayment').submit();
    });

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
                var _usd_to_khr_lack = _usd_lack * _other_exchange;
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
                var _khr_to_usd_more = _khr_more / _exchange;
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

