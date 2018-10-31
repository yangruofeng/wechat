<style>
    .analysis-list-item-remark{
        font-size: 0.6rem;color: #808080;font-style: italic;
    }
</style>
<ul class="aui-list analysis-list aui-margin-b-10">
    <li class="aui-list-item info-item paddingleft10">
        <div class="aui-list-item-inner content fontweight700">
            <?php echo 'Request Credit';?>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft12rem">
        <div class="aui-list-item-inner content">
            <?php echo 'Credit';?>
            <span><?php echo $member_request ? ncAmountFormat($member_request['credit']) : '' ?></span>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft12rem">
        <div class="aui-list-item-inner content">
            <?php echo 'Terms';?>
            <span><?php echo $member_request ? $member_request['terms'] . 'Months' : '' ?></span>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft12rem">
        <div class="aui-list-item-inner content">
            <?php echo 'Interest-Rate';?>
            <span><?php echo $member_request ? $member_request['interest_rate'] . '%' : '' ?></span>
        </div>
    </li>
    <li class="aui-list-item info-item">
        <div class="aui-list-item-inner content fontweight700">
            <?php echo 'System Analysis';?>
        </div>
    </li>
    <?php if(count($suggest_profile['tip'])){?>
        <li aui-list-item info-item>
            <?php foreach($suggest_profile['tip'] as $i=>$msg){?>
                <p style="background-color: #ffff00">
                    <span><?php echo ($i+1).". ".$msg;?></span>
                </p>
            <?php }?>
        </li>
    <?php }?>
    <li class="aui-list-item info-item paddingleft12rem fontweight600">
        <div class="aui-list-item-inner content">
            <?php echo 'Income(Monthly)';?>
        </div>
    </li>
    <?php foreach($member_income as $item_key=>$list){?>
        <li class="aui-list-item info-item paddingleft17rem fontweight500">
            <div class="aui-list-item-inner content">
                <?php echo $item_key;?>
            </div>
        </li>
        <?php foreach($list as $item){?>
            <li class="aui-list-item info-item paddingleft22rem">
                <div class="aui-list-item-inner content">
                    <?php echo $item['credit_key']?>
                    <span><?php echo $item['credit_val'] . '*' . $item['credit_rate'] . ' = '?><em><?php echo $item['credit'] ?></em></span>
                </div>
            </li>
            <li class="aui-list-item info-item paddingleft22rem">
                <span class="analysis-list-item-remark"><?php echo $item['remark']?></span>
            </li>
        <?php }?>
    <?php }?>

    <li class="aui-list-item info-item paddingleft12rem fontweight600">
        <div class="aui-list-item-inner content">
            <?php echo 'Expense(Monthly)';?>
        </div>
    </li>
    <?php foreach($member_expense as $item_key=>$list){?>
        <li class="aui-list-item info-item paddingleft17rem">
            <div class="aui-list-item-inner content">
                <?php echo $item_key;?>
            </div>
        </li>
        <?php foreach($list as $item){?>
            <li class="aui-list-item info-item paddingleft22rem">
                <div class="aui-list-item-inner content">
                    <?php echo $item['credit_key']?>
                    <span><em><?php echo $item['credit'] ?></em></span>
                </div>
            </li>
            <li class="aui-list-item info-item paddingleft22rem">
                <span class="analysis-list-item-remark"><?php echo $item['remark']?></span>
            </li>
        <?php }?>
    <?php }?>

    <li class="aui-list-item info-item paddingleft12rem fontweight600">
        <div class="aui-list-item-inner content">
            <?php echo 'Repay Ability = Income(monthly)-Expense(Monthly)';?>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft12rem">
        <div class="aui-list-item-inner content">
            &nbsp;<span><?php echo ncPriceFormat($analysis['ability'])?></span>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft12rem fontweight600">
        <div class="aui-list-item-inner content">
            <?php echo 'System Suggest Credit';?>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft17rem">
        <div class="aui-list-item-inner content">
            <?php echo 'Default Credit';?>
            <span><?php echo ncPriceFormat($suggest_profile['default_credit'])?></span>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft22rem">
        <span class="analysis-list-item-remark"><?php echo $suggest_profile['default_credit_remark']?></span>
    </li>
    <li class="aui-list-item info-item paddingleft17rem">
        <div class="aui-list-item-inner content">
            <?php echo 'Terms';?>
            <span><?php echo $suggest_profile['terms'] . 'Months'?></span>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft22rem">
        <span class="analysis-list-item-remark"><?php echo $suggest_profile['terms_remark']?></span>
    </li>
    <li class="aui-list-item info-item paddingleft17rem">
        <div class="aui-list-item-inner content">
            <?php echo 'Max Credit';?>
            <span><?php echo ncPriceFormat($suggest_profile['max_credit'])?></span>
        </div>
    </li>
    <li class="aui-list-item info-item paddingleft22rem">
        <span class="analysis-list-item-remark"><?php echo $suggest_profile['max_credit_remark']?></span>
    </li>
    <li class="aui-list-item info-item paddingleft17rem fontweight500">
        <div class="aui-list-item-inner content">
            <?php echo 'Increase Credit By Mortgage';?>
            <span></span>
        </div>
    </li>
    <?php if ($analysis['suggest']['increase']) { ?>
        <?php foreach($analysis['suggest']['increase'] as $item) {?>
            <li class="aui-list-item info-item paddingleft22rem">
                <div class="aui-list-item-inner content">
                    <?php echo $item['credit_key']; ?>
                    <span>
                         <?php echo $item['credit_val'] . '*' . $item['credit_rate'] . ' = '?><em><?php echo $item['credit'] ?></em>
                        </span>
                </div>
            </li>
        <?php }?>
        <li class="aui-list-item info-item paddingleft22rem">
            <span class="analysis-list-item-remark"><?php echo $suggest_profile['increase_remark']?></span>
        </li>
    <?php } else { ?>
        <li class="aui-list-item info-item paddingleft22rem">
            <div class="aui-list-item-inner content">
                <?php echo 'No Record';?>
            </div>
        </li>
    <?php } ?>
</ul>