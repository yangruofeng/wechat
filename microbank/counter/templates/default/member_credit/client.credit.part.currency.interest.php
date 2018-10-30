<?php
$list_currency_old=$output['credit_currency'];
$list_category=$output['credit_category'];
$list_currency=array();
$max_days=$detail['credit_terms']*30;
foreach($list_currency_old as $cc_item){
    $category=$list_category[$cc_item['member_credit_category_id']];
    if($category){
        if($category['is_one_time']){
            $list_interest=$category['interest_rate_list'];
            if($cc_item['credit_usd']>0){
                foreach($list_interest as $rate){
                    if($rate['currency']==currencyEnum::USD && $cc_item['credit_usd']>=$rate['loan_size_min'] && $cc_item['credit_usd']<=$rate['loan_size_max'] && $max_days>=$rate['min_term_days'] && $max_days<=$rate['max_term_days']){
                        $cc_item['interest_list'][]=$rate;
                        break;
                    }
                }
            }
            if($cc_item['credit_khr']>0){
                foreach($list_interest as $rate){
                    if($rate['currency']==currencyEnum::KHR && $cc_item['credit_khr']>=$rate['loan_size_min'] && $cc_item['credit_khr']<=$rate['loan_size_max'] && $max_days>=$rate['min_term_days'] && $max_days<=$rate['max_term_days']){
                        $cc_item['interest_list'][]=$rate;
                        break;
                    }
                }
            }
        }else{
            $cc_item['interest_list']=$category['interest_rate_list'];
        }
        $cc_item['category_info']=$category;
    }
    $list_currency[]=$cc_item;
}
?>
<div role="tabpanel" class="tab-pane" id="tab_interest">
    <?php if(count($list_currency) > 0){?>
        <table class="table table-bordered authorized-history">
            <tr class="table-header">
                <td>Category</td>
                <td>Repayment</td>
                <td>One Time</td>
                <td>USD</td>
                <td>KHR</td>
            </tr>
            <?php foreach($list_currency as $item){?>
                <tr>
                    <td style="font-weight: bold">
                        <?php echo  $item['category_info']['alias']?:'NO-SETTING OR CLOSED'?>
                    </td>
                    <td>
                        <?php echo  $item['category_info']['sub_product_name']?:'NO-SETTING'?>
                    </td>

                    <td>
                        <?php if($item['category_info']['is_one_time']){?>
                            <i class="fa fa-check"></i>
                        <?php }?>
                    </td>
                    <td>
                        Credit: <kbd><?php echo $item['credit_usd']?></kbd>
                        <br/>
                        Interest: <?php echo $item['interest_rate']?> %
                        <br/>
                        Operation-Fee: <?php echo $item['operation_fee']?> %
                    </td>
                    <td>
                        Credit: <kbd><?php echo $item['credit_khr']?></kbd>
                        <br/>
                        Interest: <?php echo $item['interest_rate_khr']?> %
                        <br/>
                        Operation-Fee: <?php echo $item['operation_fee_khr']?> %
                    </td>
                </tr>


            <?php }?>

        </table>
    <?php }else{?>
        <div class="no-record">No MATCH</div>
    <?php }?>
</div>