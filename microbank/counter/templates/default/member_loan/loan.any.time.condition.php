<?php if(!$data['STS']){?>
    <?php echo $data['MSG'];?>
<?php }else{?>
    <div>
        <?php
        $data=$data['DATA'];
        $product=$data['product'];
        $max_rate_amt=0;
        foreach($data['rate_list'] as $rate_item){
            if($max_rate_amt<$rate_item['loan_size_max']){
                $max_rate_amt=$rate_item['loan_size_max'];
            }
        }
        $max_rate_amt=min($product['product_credit'],$max_rate_amt);

        ?>
        <div class="modal-header">
            <label id="myModalLabel"  class="modal-title control-label">
                Product Name ：<?php echo $product['product_name'] ?> (<?php echo $product['repayment_way']?>)'
            </label>
        </div>
        <div class="modal-form clearfix" style="margin: 30px">
            <form class="form-horizontal" id="my_form" action="<?php echo getUrl('member_loan', 'addLoanContractStepOne', array(), false, ENTRY_COUNTER_SITE_URL) ?>" method="post">

                <input type="hidden" id="product_id" name="product_id" value="<?php echo $product['uid'] ?>">
                <input type="hidden" id="term_type" name="term_type" value="">
                <input type="hidden" id="member_uid" name="member_uid" value="<?php echo $data['member_id']?>">
                <input type="hidden" id="product_credit" name="product_credit" value="<?php echo $product['product_credit']?>">
                <input type="hidden" id="m_uid" name="m_uid" value="<?php echo $product['m_uid']?>">
                <input type="hidden" id="repayment_type" name="repayment_type" value="<?php echo $product['interest_type'] ?>">
                <input type="hidden" id="repayment_period" name="repayment_period" value="<?php echo $product['repayment_type'] ?>">
                <div class="col-sm-12">
                    <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Currency</label>
                    <div class="col-sm-8">
                        <input class="currency" type="hidden" name="currency" value="<?php echo $product['currency']?>"  id="loan_currency">
                        <label><?php echo $product['currency']?></label>
                    </div>
                </div>
                <div class="col-sm-12" style="margin-top: 15px">
                    <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Amount</label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control" name="amount" id="loan_amount" value="" placeholder="Please Input">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12" style="margin-top: 15px;text-align: center">
                    Max Amount(Balance):<label id="lbl_credit_currency_balance"><?php echo $max_rate_amt?></label>
                </div>
                <div class="col-sm-12" style="margin-top: 15px">
                    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Terms</label>
                    <div class="col-sm-8">
                        <select class="form-control terms" name="terms" id="loan_terms"></select>
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-12" style="margin-top: 15px;">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-7 " id="loan_page_tips" style="color: red;font-style: italic;">
                        <?php echo $data['page_tip']?>
                    </div>
                </div>
            </form>
        </div>

        <div style="text-align: center;padding-bottom: 20px">
            <a onclick="showproduct()" class="btn btn-default"><i class="fa fa-reply"></i><?php echo 'Cancel'?></a>
            <a class="btn btn-danger" onclick="modal_submit()"><i class="fa fa-check"></i><?php echo 'Next'?></a>
        </div>

        <!--客户产品利率信息-->
        <div >
            <table class="table">
                <thead style="background-color: #ddd;">
                <tr>
                    <th>Currency</th>
                    <th>Loan Amount</th>
                    <?php if($product['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                        <th>Service Fee</th>
                    <?php }else{?>
                        <th>Loan Days</th>
                        <th>Interest Rate</th>
                        <th>Operation Fee</th>
                    <?php }?>
                </tr>
                </thead>
                <tbody id="interest_rate_list_body">
                <?php foreach($data['rate_list'] as $rate){?>
                    <tr>
                        <td><?php echo $rate['currency']?></td>
                        <td><?php echo $rate['loan_size_min']." ~ ".$rate['loan_size_max']?></td>

                        <?php if($product['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <td>
                                <?php echo $rate['service_fee'].($rate['service_fee_type']==1?'':'%');?>
                            </td>
                        <?php }else{?>
                            <td><?php echo $rate['min_term_days']." ~ ".$rate['max_term_days']?></td>
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
                </tbody>
            </table>
        </div>
    </div>
    <script>
        LOAN_CATEGORY_OPTION = $.parseJSON('<?php echo my_json_encode($product);?>');
    </script>
<?php }?>
