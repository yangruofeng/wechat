<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/css/loan.css?v=1">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-slide.css">
<div class="wrap index-wrap">
  <?php include_once(template('widget/inc_nav_header'));?>
  <ul class="home-nav aui-margin-b-10">
    <li>
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/home-pay.png" />
      <p>Pay</p>
    </li>
    <li>
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/home-collect.png" />
      <p>Collect</p>
    </li>
    <li>
      <img src="<?php echo WAP_SITE_URL;?>/resource/image/home-transfer.png" />
      <p>Transfer</p>
    </li>
  </ul>
  <div class="calculator-wrap aui-margin-b-10">
    <a href="<?php echo getUrl('loan', 'calculator', array(), false, WAP_SITE_URL)?>" class="calculator-btn"><?php echo $lang['label_calculator'];?></a>
    <p><?php echo $lang['label_loan_calculator'];?> | <?php echo $lang['label_interest_calculator'];?></p>
  </div>
  <div class="credit-loan aui-margin-b-10">
    <ul class="home-model-list">
      <li class="home-model-item home-model-title">
        <div class="title">
          <?php echo $lang['label_credit_loan'];?>
          <!--<a href="<?php echo getUrl('loan', 'creditLevelList', array(), false, WAP_SITE_URL)?>" class="credit-detail"><?php echo $lang['label_detail'];?> >></a>-->
        </div>
      </li>
      <li class="home-model-item home-model-content">
        <div class="credit-wrap clearfix">
          <div class="credit-item item-first">
            <div class="item-detail">
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/credit-bg.png" class="credit-bg" />
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/arrow-right.png" class="arrow-right" />
              <div class="content">
                <div class="top">
                  <img src="<?php echo WAP_SITE_URL;?>/resource/image/credit-mobile.png" class="credit-type" />
                  <p class="type"><?php echo $lang['act_register'];?></p>
                </div>
                <div class="bottom">
                  <div class="t">
                    <?php echo $lang['label_by_mobile_phone'];?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="credit-item">
            <div class="item-detail">
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/credit-bg.png" class="credit-bg" />
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/arrow-right.png" class="arrow-right" />
              <div class="content">
                <div class="top">
                  <img src="<?php echo WAP_SITE_URL;?>/resource/image/credit-grant.png" class="credit-type" />
                  <p class="type"><?php echo $lang['label_upload'];?></p>
                </div>
                <div class="bottom">
                  <div class="t">
                    <?php echo $lang['label_automaticly_grant_credit'];?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="credit-item item-last">
            <div class="item-detail">
              <img src="<?php echo WAP_SITE_URL;?>/resource/image/credit-bg.png" class="credit-bg" />
              <div class="content">
                <div class="top">
                  <img src="<?php echo WAP_SITE_URL;?>/resource/image/credit-money.png" class="credit-type" />
                  <p class="type"><?php echo $lang['label_get_money'];?></p>
                </div>
                <div class="bottom">
                  <div class="t">
                    <?php echo $lang['label_in_5_minutes'];?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="credit-level">
          <a href="<?php echo getUrl('loan', 'creditLevelList', array(), false, WAP_SITE_URL)?>"><img src="<?php echo WAP_SITE_URL;?>/resource/image/home-1.png" /></a>
        </div>
      </li>
    </ul>
  </div>
  <div class="mortgage-loan">
    <ul class="home-model-list">
      <li class="home-model-item home-model-title">
        <div class="title">
          <?php echo $lang['label_mortgage_loan'];?>
        </div>
      </li>
      <li class="home-model-item home-model-content">
        <div class="mortgage-wrap">
          <div class="loan-amount">
            <p class="number">1000+</p>
            <p class="label"><?php echo $lang['label_loan_amount'];?></p>
          </div>
          <div class="mortgage-type clearfix">
            <div class="item it-car">
              <div class="t">
                <?php echo $lang['label_car_mortgage'];?>
              </div>
            </div>
            <div class="item it-house">
              <div class="t">
                <?php echo $lang['label_house_mortgage'];?>
              </div>
            </div>
            <div class="item it-land">
              <div class="t">
                <?php echo $lang['label_land_mortgage'];?>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
    <div class="apply-online"><a href="<?php echo getUrl('loan', 'apply', array(), false, WAP_SITE_URL)?>"><?php echo $lang['act_apply_online'];?> >></a></div>
  </div>
</div>
<?php include_once(template('widget/inc_footer'));?>
<script src="<?php echo WAP_SITE_URL;?>/resource/script/aui/aui-slide.js"></script>
<script type="text/javascript">
var slide3 = new auiSlide({
  container:document.getElementById("aui-slide3"),
  "height": 150,
  "speed": 500,
  "autoPlay": 3000, //自动播放
  "loop": true,
  "pageShow": false
});
</script>
