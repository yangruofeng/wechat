<div>
    <?php
    $cate=$match_category;
    $ret_match=$match_result;
    ?>
    <table class="table table-bordered">
        <tr class="table-header">
            <td>Category Name</td>
            <td><kbd><?php echo $cate['alias']?></kbd></td>
            <td>Repayment Type</td>
            <td><strong><?php echo $cate['sub_product_name']?></strong></td>
            <td>Interest Package</td>
            <td><strong><?php echo $cate['interest_package_name']?></strong></td>
        </tr>

        <?php foreach($ret_match as $ccy_key=>$ret){?>
            <tr>
                <td class="text-right"><?php echo strtoupper($ccy_key)?>-Limit</td>
                <td class="text-left"><strong><?php echo ncPriceFormat($cate['credit_'.$ccy_key],0)?></strong></td>
                <td class="text-right">Credit-Terms</td>
                <td class="text-left">
                    <?php if($cate['credit_'.$ccy_key]){?>
                        <strong><?php echo $cate['credit_terms']?> Months</strong>
                    <?php }?>
                </td>
                <td>Interest State</td>
                <td style="background: <?php if(!$ret['is_matched']) echo 'red'?>">
                    <?php if($ret['is_matched']){ echo 'Matched';}else{?>
                        <?php foreach($ret['msg'] as $no_msg){?>
                            <p style="font-size: 10px"><?php echo $no_msg?></p>
                        <?php }?>
                    <?php }?>
                </td>
            </tr>
            <tr>
                <td colspan="10" style="margin: 0!important;padding: 0!important;">
                    <table class="table table-bordered" style="margin: 0!important;padding: 0!important;">
                        <?php if(count($ret['list'])){?>
                            <tr>
                                <td>Terms</td>
                                <td>Size</td>
                                <td colspan="2">Interest(NoMortgage/Soft/Hard)</td>
                                <td colspan="2">OperationFee(NoMortgage/Soft/Hard)</td>
                            </tr>
                            <?php foreach($ret['list'] as $item){
                                    if($ret['is_matched'] && !$item['is_matched']) continue; //只显示matched到的，如果没有match到或者不是onetime的，全部显示 #09ed09
                                ?>
                                <tr style="background-color: <?php if($item['is_matched']) echo ''?>">
                                    <td><?php echo $item['min_term_days']." ~ ". $item['max_term_days']?></td>
                                    <td><?php echo ncPriceFormat($item['loan_size_min'],0)." ~ ". ncPriceFormat($item['loan_size_max'],0)?></td>
                                    <td colspan="2">
                                        <?php echo $item['interest_rate']."% ~ ".$item['interest_rate_mortgage1']."% ~ ".$item['interest_rate_mortgage2']."%"?>
                                    </td>
                                    <td colspan="2">
                                        <?php echo $item['operation_fee']."% ~ ".$item['operation_fee_mortgage1']."% ~ ".$item['operation_fee_mortgage2']."%"?>
                                    </td>
                                </tr>
                            <?php }?>
                        <?php }else{ if(!$ret['is_matched']){?>
                            <tr>
                                <td colspan="10" style="background: red">NO SETTING</td>
                            </tr>
                        <?php }}?>
                    </table>
                </td>
            </tr>

        <?php }?>

    </table>
</div>