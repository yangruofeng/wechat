<div class="page" style="width: 1000px">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Interest List</h3>
            <ul class="tab-base">
                <li><a onclick="javascript:history.go(-1)"><span>BACK</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php
        $loan_account=$output['loan_account'];
        $cate=$output['credit_category'];
        $rate_list=$cate['interest_rate_list'];
        $ret_match=loan_categoryClass::matchInterestForCategory($rate_list,$cate);
        ?>


        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-tablet"></i> Credit Category</h4>
            </div>
            <table class="table table-bordered">
                <tr>
                    <td>Category Name</td>
                    <td><kbd><?php echo $cate['alias']?></kbd></td>
                    <td>Repayment Type</td>
                    <td><strong><?php echo $cate['sub_product_name']?></strong></td>
                    <td>Interest Package</td>
                    <td><strong><?php echo $cate['interest_package_name']?></strong></td>
                </tr>
                <tr>
                    <td>Credit-Limit</td>
                    <td><strong><?php echo ncPriceFormat($cate['credit'],0)?></strong></td>
                    <td>Credit-Balance</td>
                    <td><strong><?php echo ncPriceFormat($cate['credit_balance'],0)?></strong></td>
                    <td>Credit-Terms</td>
                    <td><strong><?php echo $cate['credit_terms']?> Months</strong></td>
                </tr>

            </table>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-list"></i> Interest List</h4>
            </div>
            <?php foreach($ret_match as $ccy_key=>$ret){?>
                <table class="table table-bordered">
                    <tr class="table-header">
                        <td class="text-right"><?php echo strtoupper($ccy_key)?>-Limit</td>
                        <td class="text-left"><strong><?php echo ncPriceFormat($cate['credit_'.$ccy_key],0)?></strong></td>
                        <td class="text-right"><?php echo strtoupper($ccy_key)?>-Balance</td>
                        <td class="text-left"><strong><?php echo ncPriceFormat($cate['credit_'.$ccy_key.'_balance'],0)?></strong></td>
                        <td>Interest State</td>
                        <td style="background: <?php if(!$ret['is_matched']) echo 'red'?>">
                            <?php if($ret['is_matched']){ echo 'Matched';}else{?>
                                <?php foreach($ret['msg'] as $no_msg){?>
                                    <p style="font-size: 10px"><?php echo $no_msg?></p>
                                <?php }?>
                            <?php }?>
                        </td>
                    </tr>
                    <?php if(count($ret['list'])){?>
                        <tr class="table-header">
                            <td>Terms</td>
                            <td>Size</td>
                            <td colspan="2">Interest</td>
                            <td colspan="2">OperationFee</td>
                        </tr>
                        <?php foreach($ret['list'] as $item){?>
                            <tr style="background-color: <?php if($item['is_matched']) echo 'yellow'?>">
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
                    <?php }else{?>
                        <tr>
                            <td colspan="10" style="background: red">NO SETTING</td>
                        </tr>
                    <?php }?>
                </table>
            <?php }?>

        </div>


    </div>
</div>
