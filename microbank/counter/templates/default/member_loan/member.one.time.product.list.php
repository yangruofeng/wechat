<?php $member_product = $output['memberOneTimeLoanList'];?>
<?php if ($member_product) { ?>
    <?php  foreach ($member_product as $product){
            $category=$output['credit_category'][$product['member_credit_category_id']];


        /*
        $list_interest=$category['interest_rate_list'];
        $list_matched=array();
        $max_days=$product['loan_period']*30;

        if($product['credit_usd']>0){
            foreach($list_interest as $rate){
                if($rate['currency']==currencyEnum::USD && $product['credit_usd']>=$rate['loan_size_min'] && $product['credit_usd']<=$rate['loan_size_max'] && $max_days>=$rate['min_term_days'] && $max_days<=$rate['max_term_days']){
                    $list_matched[currencyEnum::USD]=$rate;
                    break;
                }
            }
        }
        if($product['credit_khr']>0){
            foreach($list_interest as $rate){
                if($rate['currency']==currencyEnum::KHR && $product['credit_khr']>=$rate['loan_size_min'] && $product['credit_khr']<=$rate['loan_size_max'] && $max_days>=$rate['min_term_days'] && $max_days<=$rate['max_term_days']){
                    $list_matched[currencyEnum::KHR]=$rate;
                    break;
                }
            }
        }
        */

        ?>
        <div style="margin-bottom: 35px;">
            <table class="table" id="productbox">
                <tr>
                    <td width="25%"><label class="control-label">Product Code</label></td>
                    <td width="30%">
                        <?php /*echo $product['product_code'] */?>
                        <p> <kbd><?php echo 'KHR: '.$category['product_code_khr']; ?></kbd></p>
                        <p><kbd><?php echo 'USD: '.$category['product_code_usd']; ?></kbd></p>
                    </td>
                    <td width="25%"><label class="control-label">Credit Category</label></td>
                    <td width="20%"><?php echo ucwords(str_replace('_',' ',$product['product_name'])) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Repayment Type</label></td>
                    <td><?php echo ucwords(str_replace('_',' ',$product['repayment_type']))?></td>
                    <td><label class="control-label">Repayment Period</label></td>
                    <td colspan="3"><?php echo $product['repayment_period']?></td>
                </tr>
                <?php if($product['contract_param']){?>
                    <?php foreach($product['contract_param'] as $contract){?>
                        <tr>
                            <td>
                                Interest
                            </td>
                            <td>
                                <kbd><?php echo $category['interest_rate_'.strtolower($contract['currency'])]?> %</kbd>
                            </td>
                            <td>Operation Fee</td>
                            <td>
                                <kbd><?php echo $category['operation_fee_'.strtolower($contract['currency'])]?> %</kbd>

                            </td>
                        </tr>
                        <tr>
                            <td>Credit Balance </td>
                            <td><label><?php echo ncPriceFormat($contract['credit_amount'])?>   <?php echo $contract['currency']?>  </label></td>
                            <td colspan="2">
                                <?php if($contract['credit_amount']>0){?>

                                    <?php if( $category['is_only_loan_by_app'] ){ ?>
                                        <kbd>Loan By Member-App</kbd>
                                    <?php }else{  ?>
                                        <button class="btn btn-primary add_loan_next"
                                            <?php if (!$output['client_info']['credit_is_active'] || $output['client_info']['member_state'] != memberStateEnum::VERIFIED || $contract['credit_amount']<=0) { ?> title="Credit State is Invalid" disabled<?php } ?>
                                                onclick="applyOneTimeLoan(<?php echo $output['client_info']['uid']?>,<?php echo $product['member_credit_category_id']?>,'<?php echo $contract["currency"]?>')">
                                            Loan <?php echo $contract['currency']?>
                                        </button>
                                    <?php } ?>


                                <?php }else{?>
                                    <code>Not Allowed Loan</code>
                                <?php }?>

                            </td>
                        </tr>


                        <?php if(1>2){?>
                            <tr>
                                <td colspan="10">
                                    <table class="table table-bordered table-no-background">
                                        <tr>
                                            <td>Currency</td>
                                            <td>Terms</td>
                                            <td>Size</td>
                                            <td>Interest & OperationFee</td>
                                        </tr>
                                        <?php $rate=$list_matched[$contract['currency']] ?>
                                        <tr>
                                            <td><?php echo $rate['currency']?></td>
                                            <td><?php echo $rate['min_term_days']." ~ ".$rate['max_term_days']?></td>
                                            <td><?php echo $rate['loan_size_min']." ~ ".$rate['loan_size_max']?></td>
                                            <td>
                                                <?php if($category['interest_type']==1) echo '<kbd>No Mortgaged</kbd> '. $rate['interest_rate']."% & ".$rate['operation_fee']."%"?>
                                                <?php if($category['interest_type']==2) echo '<kbd>Mortgaged Soft</kbd> '. $rate['interest_rate_mortgage1']."% & ".$rate['operation_fee_mortgage1']."%"?>
                                                <?php if($category['interest_type']==3) echo '<kbd>Mortgaged Hard</kbd> '. $rate['interest_rate_mortgage2']."% & ".$rate['operation_fee_mortgage2']."%"?>

                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php }else{?>

                        <?php }?>

                    <?php }?>
                <?php }else{?>
                    <tr>
                        <td colspan="10">NO CREDIT BALANCE</td>
                    </tr>
                <?php }?>

            </table>
        </div>
    <?php } ?>
<?php } else { ?>
<div>
    No Record
</div>
<?php } ?>


