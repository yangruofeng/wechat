
<?php include_once(template('widget/inc_header_weui'));?>
<div class="page__bd">
    <?php $list = $output['credit_category'];?>
    <?php if(!$list){?>
        <div class="weui-msg">
            <div class="weui-msg__icon-area"><i class="weui-icon-info weui-icon_msg"></i></div>
            <div class="weui-msg__text-area">
                <h2 class="weui-msg__title">
                    No Setting
                </h2>
            </div>
        </div>
    <?php }?>
    <?php foreach($list as $item){?>
        <div class="weui-form-preview">
            <div class="weui-form-preview__hd">
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label"><?php echo $item['sub_product_name']?></label>
                    <em class="weui-form-preview__value"><?php echo $item['alias']?></em>
                </div>
            </div>
            <div class="weui-form-preview__bd">
                <?php foreach($item['interest_rate_list'] as $rate){?>
                    <div class="weui-form-preview__item">
                        <label class="weui-form-preview__label">Term(Months)</label>
                            <span class="weui-form-preview__value">
                                <?php echo (ceil($rate['min_term_days']/30))." ~ ".(ceil($rate['max_term_days']/30))?>
                            </span>
                    </div>
                    <div class="weui-form-preview__item">
                        <label class="weui-form-preview__label">Size(<?php echo $rate['currency']?>)</label>
                            <span class="weui-form-preview__value">
                                <?php echo ncPriceFormat($rate['loan_size_min'])." ~ ".ncPriceFormat($rate['loan_size_max'])?>
                            </span>
                    </div>
                    <div class="weui-form-preview__item">
                        <label class="weui-form-preview__label">Interest</label>
                            <span class="weui-form-preview__value">
                                <?php echo $rate['interest_rate']." ~ ".$rate['interest_rate_mortgage1']." ~ ".$rate['interest_rate_mortgage2']?>
                            </span>
                    </div>
                    <div class="weui-form-preview__item">
                        <label class="weui-form-preview__label">Operation Fee</label>
                            <span class="weui-form-preview__value">
                                <?php echo $rate['operation_fee_rate']." ~ ".$rate['operation_fee_mortgage1']." ~ ".$rate['operation_fee_mortgage2']?>
                            </span>
                    </div>
                    <br/>

                <?php }?>
            </div>
        </div>

    <?php }?>
</div>
