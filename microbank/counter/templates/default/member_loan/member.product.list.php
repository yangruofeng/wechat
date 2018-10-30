<?php $member_product = $output['product_list'];?>
<?php  foreach ($member_product as $product){ ?>
    <?php
    $category=$output['credit_category'][$product['uid']];
    $list_interest=$category['interest_rate_list'];

    ?>
    <div style="margin-bottom: 35px;border: 1px solid lightgrey">
        <table class="table" id="productbox">
            <tr>
                <td width="25%"><label class="control-label">Product Code</label></td>
                <td width="30%">
                    <?php /*echo $product['sub_product_code'] */?>
                    <p><kbd><?php echo 'KHR: '.$category['product_code_khr']; ?></kbd></p>
                    <p><kbd><?php echo 'USD: '.$category['product_code_usd']; ?></kbd></p>
                </td>
                <td width="25%"><label class="control-label">Credit Category</label></td>
                <td width="20%"><?php echo ucwords(str_replace('_',' ',$product['alias'])) ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Repayment Type</label></td>
                <td><?php echo $product['sub_product_name']?></td>
                <td></td>
                <td></td>
                <!--<td><label class="control-label">Min Rate</label></td>
                <td><?php /*echo $product['monthly_min_rate'] */?></td>-->
            </tr>
            <tr>
                <td><label class="control-label">Credit Limit USD</label></td>
                <td><?php echo ncPriceFormat($product['credit_usd'])?></td>
                <td><label class="control-label">Credit Balance USD</label></td>
                <td>
                    <?php echo ncPriceFormat($product['credit_usd_balance'])?>
                    <?php if($product['credit_usd_balance']>0){?>
                        <br/>
                        <?php if( $category['is_only_loan_by_app'] ){?>
                            <kbd>Loan By Member-App</kbd>
                        <?php }else{?>
                            <button class="btn btn-primary add_loan_next"
                                <?php if (!$output['client_info']['credit_is_active'] || $output['client_info']['member_state'] != memberStateEnum::VERIFIED || $product['credit_balance']<=0) { ?> title="Credit State is Invalid" disabled<?php } ?>
                                    onclick="showLoan(<?php echo $product['sub_product_id'] ?>,<?php echo $product['uid'] ?>,<?php echo $product['credit_usd_balance'] ?>,'<?php echo ucwords(str_replace('_',' ',$product['alias'])) ?>','<?php echo $product['sub_product_name'] ?>','<?php echo $product['interest_type'] ?>','<?php echo $product['repayment_type'] ?>','<?php echo currencyEnum::USD ?>')">
                                Loan USD
                            </button>
                        <?php }?>

                    <?php }?>
                </td>
            </tr>
            <tr>
                <td><label class="control-label">Credit Limit KHR</label></td>
                <td><?php echo ncPriceFormat($product['credit_khr'])?></td>
                <td><label class="control-label">Credit Balance KHR</label></td>
                <td>
                    <?php echo ncPriceFormat($product['credit_khr_balance'])?>
                    <?php if($product['credit_khr_balance']>0){?>
                        <br/>
                        <?php if($product['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <button class="btn btn-primary add_loan_next"
                                <?php if (!$output['client_info']['credit_is_active'] || $output['client_info']['member_state'] != memberStateEnum::VERIFIED || $product['credit_balance']<=0) { ?> title="Credit State is Invalid" disabled<?php } ?>
                                    onclick="showLoan(<?php echo $product['sub_product_id'] ?>,<?php echo $product['uid'] ?>,<?php echo $product['credit_khr_balance'] ?>,'<?php echo ucwords(str_replace('_',' ',$product['alias'])) ?>','<?php echo $product['sub_product_name'] ?>','<?php echo $product['interest_type'] ?>','<?php echo $product['repayment_type'] ?>','<?php echo currencyEnum::KHR ?>')">
                                Loan KHR
                            </button>
                        <?php }else{?>
                            <kbd>Loan By Member-App</kbd>
                        <?php }?>

                    <?php }?>
                </td>
            </tr>



            <?php if($list_interest){?>
                <tr>
                    <td colspan="10">
                        <table class="table table-bordered table-no-background">
                            <tr>
                                <td>Currency</td>
                                <?php if($category['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                    <td>Terms</td>
                                <?php }?>

                                <td>Size</td>
                                <?php if($category['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                    <td>Service Fee</td>
                                <?php }else{?>
                                    <td>Interest</td>
                                    <td>OperationFee</td>
                                <?php }?>

                            </tr>
                            <?php foreach($list_interest as $rate){
                                if(!$rate['is_show_for_client']) continue;
                                ?>
                                <tr>
                                    <td><?php echo $rate['currency']?></td>
                                    <?php if($category['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                        <td><?php echo $rate['min_term_days']." ~ ".$rate['max_term_days']?></td>
                                    <?php }?>
                                    <td><?php echo $rate['loan_size_min']." ~ ".$rate['loan_size_max']?></td>
                                    <?php if($category['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                        <td>
                                             <?php echo $rate['service_fee']?> <?php if($rate['service_fee_type']){echo '$';}else{echo '%';}?>
                                        </td>
                                    <?php }else{?>
                                        <td>
                                            <?php echo $rate['interest_rate']."%"?> /
                                            <?php echo $rate['interest_rate_mortgage1']."%"?> /
                                            <?php echo $rate['interest_rate_mortgage2']."%"?> /
                                        </td>
                                        <td>
                                            <?php echo $rate['operation_fee']."%"?> /
                                            <?php echo $rate['operation_fee_mortgage1']."%"?> /
                                            <?php echo $rate['operation_fee_mortgage2']."%"?> /
                                        </td>
                                    <?php }?>

                                </tr>
                            <?php }?>
                        </table>
                    </td>
                </tr>
            <?php }else{?>
                <tr>
                    <td colspan="10">NO MATCH THE INTEREST</td>
                </tr>
            <?php }?>
        </table>

    </div>
<?php } ?>

