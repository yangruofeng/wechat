<style>
    .analysis-list-item-remark{
        font-size: 0.6rem;color: #808080;font-style: italic;
    }
</style>

<div class="weui-cells__title">
    Client Request Credit
</div>
<div class="weui-cells">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            Credit
        </div>
        <div class="weui-cell__ft">
            <span><?php echo $member_request ? ncPriceFormat($member_request['credit'],0) : '' ?></span>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__bd">
            Terms
        </div>
        <div class="weui-cell__ft">
            <span><?php echo $member_request ? $member_request['terms'] . 'Months' : '' ?></span>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__bd">
            Interest-Rate
        </div>
        <div class="weui-cell__ft">
            <span><?php echo $member_request ? $member_request['interest_rate'] . '%' : '' ?></span>
        </div>
    </div>
</div>
<div class="weui-cells__title">
    System Analysis
</div>
<div class="weui-cells">
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <?php echo 'Income(Monthly)';?>
        </div>
    </div>
    <div class="weui-cell" style="padding-top: 0;padding-bottom: 0">
        <div class="weui-cell__bd">
            <div class="weui-cells" style="margin-top: 0">
                <?php foreach($member_income as $item_key=>$list){?>
                    <div class="weui-cell">
                        <div class="weui-cell__hd"><?php echo $item_key;?></div>
                        <div class="weui-cell__bd" style="margin-top: 0;padding-left: 10px">
                            <?php foreach($list as $item){?>
                                <p onclick="<?php if($item_key=='business') echo 'btn_show_business_detail(this)'?>"
                                    data-detail="<?php echo $item['analysis_detail']?>"
                                    >
                                    <?php echo $item['credit_key']?>
                                    <span><?php echo $item['credit_val'] . '*' . $item['credit_rate'] . ' = '?><em><?php echo ncPriceFormat($item['credit'],0) ?></em></span>
                                    <span style="float: right">
                                        <i class="<?php if($item_key=='business') echo 'weui-icon__right'?>"></i>
                                    </span>
                                </p>
                                <p>
                                    <span class="analysis-list-item-remark"><?php echo $item['remark']?></span>
                                </p>
                            <?php }?>
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <?php echo 'Expense(Monthly)';?>
        </div>
    </div>
    <div class="weui-cell" style="padding-top: 0;padding-bottom: 0">
        <div class="weui-cell__bd">
            <div class="weui-cells" style="margin-top: 0">
                <?php foreach($member_expense as $item_key=>$list){?>
                    <div class="weui-cell">
                        <div class="weui-cell__hd"><?php echo $item_key;?></div>
                        <div class="weui-cell__bd" style="margin-top: 0;padding-left: 10px">
                            <?php foreach($list as $item){?>
                                <p>
                                    <?php echo $item['credit_key']?>
                                    <span><?php echo $item['credit_val'] . '*' . $item['credit_rate'] . ' = '?><em><?php echo ncPriceFormat($item['credit'],0) ?></em></span>
                                </p>
                                <p>
                                    <span class="analysis-list-item-remark"><?php echo $item['remark']?></span>
                                </p>
                            <?php }?>
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>

    <div class="weui-cell">
        <div class="weui-cell__bd">
            <?php echo 'Repay Ability(Monthly)';?>
        </div>
        <div class="weui-cell__ft">
            <?php echo ncPriceFormat($analysis['ability'],0)?>
        </div>
    </div>
    <p style="text-align: right;padding-right: 10px">
        <span class="analysis-list-item-remark">=Income(<?php echo $analysis['total_income']?>)-Expense(<?php echo $analysis['total_expense']?>)</span>
    </p>
</div>
<div class="weui-cells__title">
    <?php echo 'System Suggest Credit';?>
</div>
<div class="weui-cells">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <?php echo 'Default Credit';?>
        </div>
        <div class="weui-cell__ft">
            <span><?php echo ncPriceFormat($suggest_profile['default_credit'],0)?></span>
        </div>
    </div>
    <p style="text-align: right;padding-right: 10px">
        <span class="analysis-list-item-remark"><?php echo $suggest_profile['default_credit_remark']?></span>
    </p>
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <?php echo 'Terms';?>
        </div>
        <div class="weui-cell__bd">

        </div>
        <div class="weui-cell__ft">
            <span><?php echo $suggest_profile['terms'] . 'Months'?></span>

        </div>
    </div>
    <p style="text-align: right;padding-right: 10px">
        <span class="analysis-list-item-remark"><?php echo $suggest_profile['terms_remark']?></span>
    </p>
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <?php echo 'Max Credit';?>
        </div>
        <div class="weui-cell__bd">

        </div>
        <div class="weui-cell__ft">
            <span><?php echo ncPriceFormat($suggest_profile['max_credit'],0)?></span>

        </div>
    </div>
    <div style="text-align: left;padding-left: 10px">
        <div class="analysis-list-item-remark"><?php echo $suggest_profile['max_credit_remark']?></div>
    </div>
</div>

<div class="weui-cells__title">
    <?php echo 'Increase Credit By Mortgage';?>
</div>
<div class="weui-cells">
    <?php foreach($analysis['suggest']['increase'] as $item) {?>
    <div class="weui-cell">
        <div class="weui-cell__hd">
            <?php echo $item['credit_key']; ?>
        </div>
        <div class="weui-cell__bd" style="text-align: right">
            <?php echo $item['credit_val'] . '*' . $item['credit_rate'] . ' = '?>
        </div>
        <div class="weui-cell__ft">
            <em><?php echo ncPriceFormat($item['credit'],0) ?></em>
        </div>
    </div>
    <?php }?>
    <p style="text-align: right;padding-right: 10px">
        <span class="analysis-list-item-remark"><?php echo $suggest_profile['increase_remark']?></span>
    </p>
</div>
<?php if($analysis['analysis_asset']){?>
    <div class="weui-cells__title">
        Notices Of Assets
    </div>
<?php }?>
<div class="weui-cells">
    <?php foreach($analysis['analysis_asset'] as $notice_item){?>
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <?php echo $notice_item;?>
            </div>
        </div>
    <?php }?>
</div>

<script>
    function btn_show_business_detail(_e){
        var _detail=$(_e).data('detail');
        console.log(_detail);
        yo.dynamicTpl({
            tpl:"suggest_credit/credit.business.analysis.item",
            ext:{data:{business:_detail}},
            callback:function(_tpl){
                alert(_tpl);
            }
        });
    }
</script>