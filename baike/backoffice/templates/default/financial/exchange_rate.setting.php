<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<style>
    .first_currency, .second_currency {
        width: 60px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Company Info</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('financial', 'exchangeRate', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a class="current"><span>Setting</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Select  Currency</label>
                <div class="col-sm-4">
                    <select name="first_currency" class="form-control">
                        <option value="0"><?php echo $lang['common_select']?></option>
                        <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                            <option value="<?php echo $key;?>" style="display: <?php echo $output['currency']['second_currency'] == $key ? 'none' : 'block'; ?>" <?php echo $output['currency']['first_currency'] == $key ? 'selected' : ''; ?>><?php echo $currency;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-1">
                </div>
                <div class="col-sm-4">
                    <select name="second_currency" class="form-control">
                        <option value="0"><?php echo $lang['common_select']?></option>
                        <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                            <option value="<?php echo $key;?>" style="display: <?php echo $output['currency']['first_currency'] == $key ? 'none' : 'block'; ?>" <?php echo $output['currency']['second_currency'] == $key ? 'selected' : ''; ?>><?php echo $currency;?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Exchange Rate</label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="buy_rate_unit" value="" readonly>
                        <span class="input-group-addon first_currency"><?php echo $output['currency']['first_currency'];?></span>
                    </div>
                    <div class="error_msg"></div>
                </div>
                <div class="col-sm-1" style="text-align: center">
                    <i class="fa fa-arrow-right" style="margin-top: 10px"></i>
                </div>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="number" class="form-control" name="buy_rate" value="" readonly>
                        <span class="input-group-addon second_currency"><?php echo $output['currency']['second_currency'];?></span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label"></label>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="number" class="form-control" name="sell_rate_unit" value="" readonly>
                        <span class="input-group-addon second_currency"><?php echo $output['currency']['second_currency'];?></span>
                    </div>
                    <div class="error_msg"></div>
                </div>
                <div class="col-sm-1" style="text-align: center">
                    <i class="fa fa-arrow-right" style="margin-top: 10px"></i>
                </div>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="sell_rate" value="" readonly>
                        <span class="input-group-addon first_currency"><?php echo $output['currency']['first_currency'];?></span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9" style="text-align: center">
                    <button id="btn_save_rate" type="button" class="btn btn-danger save-info" style="margin-left: 0;min-width: 80px">
                        <i class="fa fa-check"></i>
                        <?php echo 'Save' ?>
                    </button>
                    <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"  style="margin-left: 10px;min-width: 80px">
                        <i class="fa fa-reply"></i>
                        <?php echo 'Back' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(function () {
        var uid = '<?php echo intval($_GET['uid']);?>';
        if (uid) {
            getCurrencyRate();
        }

        $('select[name="first_currency"]').change(function () {
            var first_currency = $(this).val();
            $('.first_currency').html(first_currency);
            $('select[name="second_currency"] option').show();
            $('select[name="second_currency"] option[value="' + first_currency + '"]').hide();
            getCurrencyRate();
        });

        $('select[name="second_currency"]').change(function () {
            var second_currency = $(this).val();
            $('.second_currency').html(second_currency);
            $('select[name="first_currency"] option').show();
            $('select[name="first_currency"] option[value="' + second_currency + '"]').hide();
            getCurrencyRate();
        })
    });

    function getCurrencyRate() {
        $('[name="buy_rate"]').val('').attr('readonly', true);
        $('[name="buy_rate_unit"]').val('').attr('readonly', true);
        $('[name="sell_rate"]').val('').attr('readonly', true);
        $('[name="sell_rate_unit"]').val('').attr('readonly', true);
        var first_currency = $('select[name="first_currency"]').val();
        var second_currency = $('select[name="second_currency"]').val();
        if (first_currency != 0 && second_currency != 0) {
            yo.loadData({
                _c: 'financial',
                _m: 'getRate',
                param: {first_currency: first_currency, second_currency: second_currency},
                callback: function (_o) {
                    if (_o.STS) {
                        $('[name="buy_rate"]').attr('readonly', false);
                        $('[name="buy_rate_unit"]').attr('readonly', false);
                        $('[name="sell_rate"]').attr('readonly', false);
                        $('[name="sell_rate_unit"]').attr('readonly', false);
                        var data = _o.DATA;
                        if( data != null ){
                            $('[name="buy_rate"]').val(data.buy_rate);
                            $('[name="buy_rate_unit"]').val(data.buy_rate_unit);
                            $('[name="sell_rate"]').val(data.sell_rate);
                            $('[name="sell_rate_unit"]').val(data.sell_rate_unit);
                        }

                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        }
    }

    $('#btn_save_rate').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    });

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.col-sm-4').find('.error_msg'));
        },
        rules: {
            buy_rate_unit: {
                required: true,
                moreThanZero: true
            },
            buy_rate: {
                required: true,
                moreThanZero: true
            },
            sell_rate_unit: {
                required: true,
                moreThanZero: true
            },
            sell_rate: {
                required: true,
                moreThanZero: true
            }
        },
        messages: {
            buy_rate_unit: {
                required:  '<?php echo 'Required'?>',
                moreThanZero: 'It can\'t be less than 0'
            },
            buy_rate: {
                required: '<?php echo 'Required'?>',
                moreThanZero: 'It can\'t be less than 0'
            },
            sell_rate_unit: {
                required:  '<?php echo 'Required'?>',
                moreThanZero: 'It can\'t be less than 0'
            },
            sell_rate: {
                required: '<?php echo 'Required'?>',
                moreThanZero: 'It can\'t be less than 0'
            }
        }
    });

    jQuery.validator.addMethod("moreThanZero", function (value, element) {
        value = Number(value);
        if (value > 0) {
            return true;
        } else {
            return false;
        }
    });
</script>
