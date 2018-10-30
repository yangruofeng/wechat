<?php
$list_currency_old=$output['credit_currency'];
$list_category=$output['credit_category'];
$list_currency=array();
$max_days=$detail['credit_terms']*30;
foreach($list_currency_old as $cc_item){
    $category=$list_category[$cc_item['member_credit_category_id']];
    $cc_item['category_info']=$category;
    $cc_item['interest_list']=$category['interest_rate_list'];
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
                        <p>
                            <?php echo  $item['category_info']['sub_product_name']?:'NO-SETTING'?>
                        </p>
                    </td>
                    <td>
                        <?php if($item['category_info']['is_one_time']){?>
                            <i class="fa fa-check"></i>
                        <?php }?>
                    </td>
                    <td>
                        Credit: <kbd><?php echo $item['credit_usd']?></kbd>
                        <?php if($item['category_info']['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <p>
                                Loan Fee:<kbd><?php echo $item['loan_fee']?> <?php if($item['loan_fee_type']){ echo '$';}else{echo '%';}?></kbd>
                            </p>
                            <p>
                                Admin Fee:<kbd><?php echo $item['admin_fee']?> <?php if($item['admin_fee_type']){ echo '$';}else{echo '%';}?></kbd>
                            </p>
                            <p>
                                Annual Fee:<kbd><?php echo $item['annual_fee']?> <?php if($item['annual_fee_type']){ echo '$';}else{echo '%';}?></kbd>
                            </p>
                        <?php }else{?>
                            <p>
                                Annual Fee:<kbd><?php echo $item['annual_fee']?> <?php if($item['annual_fee_type']){ echo '$';}else{echo '%';}?></kbd>
                            </p>
                        <?php }?>
                        <br/>
                        Interest: <?php echo $item['interest_rate']?> %
                        <br/>
                        Operation-Fee: <?php echo $item['operation_fee']?> %
                    </td>
                    <td>
                        <?php if($item['category_info']['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            Credit: <kbd><?php echo $item['credit_khr']?></kbd>
                            <p>
                                Loan Fee:<kbd><?php echo $item['loan_fee_khr']?> <?php if($item['loan_fee_type']){ echo 'KHR';}else{echo '%';}?></kbd>
                            </p>
                            <p>
                                Admin Fee:<kbd><?php echo $item['admin_fee_khr']?> <?php if($item['admin_fee_type']){ echo 'KHR';}else{echo '%';}?></kbd>
                            </p>
                            <p>
                                Annual Fee:<kbd><?php echo $item['annual_fee_khr']?> <?php if($item['annual_fee_type']){ echo '$';}else{echo '%';}?></kbd>
                            </p>

                            <br/>
                            Interest: <?php echo $item['interest_rate_khr']?> %
                            <br/>
                            Operation-Fee: <?php echo $item['operation_fee_khr']?> %
                        <?php }else{?>
                            Not Support
                        <?php }?>
                    </td>


                </tr>


            <?php }?>

        </table>
    <?php }else{?>
        <div class="no-record">No MATCH</div>
    <?php }?>
</div>