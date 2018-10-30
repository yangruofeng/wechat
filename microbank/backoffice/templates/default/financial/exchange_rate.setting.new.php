<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<style>
    .first_currency, .second_currency {
        width: 60px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Exchange</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Setting</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 600px">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Sell-Price</label>
                <div class="col-sm-2" style="text-align: right;padding-top: 7px">
                    <span>1 USD = </span>
                </div>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="number" class="form-control" name="sell_price" value="<?php echo $output['exchange_rate']['buy_rate']?>">
                        <span class="input-group-addon" style="min-width: 60px"><?php echo currencyEnum::KHR;?></span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Buy-Price</label>
                <div class="col-sm-2" style="text-align: right;padding-top: 7px">
                    <span>1 USD = </span>
                </div>
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="buy_price" value="<?php echo $output['exchange_rate']['sell_rate_unit']?>">
                        <span class="input-group-addon first_currency" style="min-width: 60px"><?php echo currencyEnum::KHR;?></span>
                    </div>
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-9" style="text-align: center">
                    <button type="button" class="btn btn-danger save-info" style="margin-left: 0;min-width: 80px">
                        <i class="fa fa-check"></i>
                        <?php echo 'Save' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>

    $('.save-info').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.col-sm-4').find('.error_msg'));
        },
        rules: {
            sell_price: {
                required: true,
                moreThanZero: true
            },
            buy_price: {
                required: true,
                moreThanZero: true
            }
        },
        messages: {
            sell_price: {
                required:  '<?php echo 'Required'?>',
                moreThanZero: 'It can\'t be less than 0'
            },
            buy_price: {
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
