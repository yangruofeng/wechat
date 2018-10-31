<div class="weui-cells" style="padding: 0">
    <?php $survey_list=(my_json_decode(urldecode($data['business'])));?>
    <?php foreach($survey_list as $branch_code=>$survey_item){?>
        <div class="weui-cells__title">
            <?php
            echo $branch_code;
            $item_profit=0;
            ?>
        </div>
        <div class="weui-cells">
            <?php foreach($survey_item as $item){?>
                <div class="weui-cell" style="padding: 2px">
                    <div class="weui-cell__hd">
                        <?php echo $item['survey_name']?>
                    </div>
                    <div class="weui-cell__bd"></div>
                    <div class="weui-cell__ft" style="font-size: 20px;font-weight: bold">
                        <?php if($item['survey_type']==surveyType::INCOME){
                            echo '+';$item_profit+=$item['result'];
                        }else{
                            echo '-';$item_profit-=$item['result'];
                        }?>
                        <?php echo ncPriceFormat($item['result'],0)?>
                    </div>
                </div>
            <?php }?>
            <div class="weui-cell" style="padding: 2px">
                <div class="weui-cell__hd">Profit</div>
                <div class="weui-cell__bd"></div>
                <div class="weui-cell__ft"  style="font-size: 20px;font-weight: bold;color: blue"><?php echo ncPriceFormat($item_profit,0);?></div>
            </div>
        </div>
    <?php }?>
</div>