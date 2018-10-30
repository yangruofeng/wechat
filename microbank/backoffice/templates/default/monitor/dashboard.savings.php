<?php $savings = $data['data']; ?>
<?php $currency = (new currencyEnum())->Dictionary(); ?>
<div class="col-sm-12 saving">
    <?php if ($currency['USD']) { ?>
        <div class="panel">
            <div class="top warning"><img src="resource/image/dashboard-usd.png" alt=""></div>
            <div class="bottom">
                <h2><?php echo ncPriceFormat($savings[currencyEnum::USD]); ?></h2>
                <h6>USD</h6>
            </div>
        </div>
    <?php } ?>
    <?php if ($currency['CNY']) { ?>
        <div class="panel">
            <div class="top success"><img src="resource/image/dashboard-cny.png" alt=""></i>
            </div>
            <div class="bottom">
                <h2><?php echo ncPriceFormat($savings[currencyEnum::CNY]); ?></h2>
                <h6>CNY</h6>
            </div>
        </div>
    <?php } ?>
    <?php if ($currency['KHR']) { ?>
        <div class="panel">
            <div class="top primary"><img src="resource/image/dashboard-khr.png" alt=""></i>
            </div>
            <div class="bottom">
                <h2><?php echo ncPriceFormat($savings[currencyEnum::KHR]); ?></h2>
                <h6>KHR</h6>
            </div>
        </div>
    <?php } ?>
    <?php if ($currency['VND']) { ?>
        <div class="panel">
            <div class="top danger"><img src="resource/image/dashboard-vnd.png?v=1" alt=""></i>
            </div>
            <div class="bottom">
                <h2><?php echo ncPriceFormat($savings[currencyEnum::VND]); ?></h2>
                <h6>VND</h6>
            </div>
        </div>
    <?php } ?>
    <?php if ($currency['THB']) { ?>
        <div class="panel">
            <div class="top info"><img src="resource/image/dashboard-thb.png?v=1" alt=""></i>
            </div>
            <div class="bottom">
                <h2><?php echo ncPriceFormat($savings[currencyEnum::THB]); ?></h2>
                <h6>THB</h6>
            </div>
        </div>
    <?php } ?>
</div>