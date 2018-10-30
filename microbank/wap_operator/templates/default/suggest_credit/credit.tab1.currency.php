<?php
$last_suggest=$data['last_suggest']?:array();
$last_ccy=$last_suggest['suggest_product']?:array();
?>
<form class="custom-form" id="frm_credit_currency">
    <div class="weui-cells__title">
         <span style="padding-left: 10px"> Credit:  <kbd><?php echo ncPriceFormat($data['max_credit'],0)?></kbd></span>
        <span style="padding-left: 30px"> Terms:  <kbd><?php echo $data['credit_terms']?> Months</kbd></span>
    </div>
    <div class="weui-cells weui-cells_form">
        <?php foreach($data['category_list'] as $prod){?>
            <div class="weui-panel weui-panel_access currency-cate-item">
                <div class="weui-panel__hd" style="padding: 0">
                    <div class="weui-cell">
                        <div class="weui-cell__hd">
                            <label class="weui-label" style="font-weight: bold"><?php echo $prod['alias']?></label>
                        </div>
                        <div class="weui-cell__bd"></div>
                        <div class="weui-cell__ft"  style="font-size: 0.8rem">
                            <?php echo $prod['sub_product_name']?>
                            <input type="hidden" class="input-credit-ccy-id"  value="<?php echo $prod['uid']?>">
                            <input type="hidden" class="input-credit-ccy-total" value="<?php echo $prod['credit']?>">
                        </div>
                    </div>
                </div>
                <div class="weui-panel__bd">

                    <div class="weui-cells weui-cells_form" style="margin: 0">
                        <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">Loan-Fee USD</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" class="weui-input input-credit-ccy-loan-fee" cursor-spacing='20'
                                           value="<?php echo $last_ccy[$prod['uid']]['loan_fee']?:$prod['fee_setting']['loan_fee']?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <input type="hidden" class="weui-input input-credit-ccy-loan-fee-type" value="<?php echo $prod['fee_setting']['loan_fee_type']?>">
                                    <span><?php if($prod['fee_setting']['loan_fee_type']){ echo '$';}else{ echo '%';}?>,</span>
                                    <span style="padding-left: 10px" id="span_loan_fee">Default: <?php echo $prod['fee_setting']['loan_fee']?> <?php if($prod['fee_setting']['loan_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                </div>
                            </div>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">Admin-Fee USD</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" class="weui-input input-credit-ccy-admin-fee" cursor-spacing='20'
                                           value="<?php echo $last_ccy[$prod['uid']]['admin_fee']?:$prod['fee_setting']['admin_fee']?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <input type="hidden" class="weui-input input-credit-ccy-admin-fee-type" value="<?php echo $prod['fee_setting']['admin_fee_type']?>">
                                    <span><?php if($prod['fee_setting']['admin_fee_type']){ echo '$';}else{ echo '%';}?>,</span>
                                    <span style="padding-left: 10px" id="span_admin_fee">Default: <?php echo $prod['fee_setting']['admin_fee']?> <?php if($prod['fee_setting']['admin_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                </div>
                            </div>
                            <?php if($prod['fee_setting']['annual_fee']>0){?>
                                <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                    <div class="weui-cell__hd">
                                        <label class="weui-label">Annual Fee</label>
                                    </div>
                                    <div class="weui-cell__bd">
                                        <input type="number" pattern="[0-9]*" class="weui-input input-credit-ccy-annual-fee" cursor-spacing='20'
                                               value="<?php echo $last_ccy[$prod['uid']]['annual_fee']?:$prod['fee_setting']['annual_fee']?>">
                                    </div>
                                    <div class="weui-cell__ft" style="font-size: 0.8rem">
                                        <input type="hidden" class="weui-input input-credit-ccy-annual-fee-type" value="<?php echo $prod['fee_setting']['annual_fee_type']?>">
                                        <span><?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?>,</span>
                                        <span style="padding-left: 10px" id="span_admin_fee">Default: <?php echo $prod['fee_setting']['annual_fee']?> <?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                    </div>
                                </div>
                            <?php }?>
                        <?php }else{?>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">Annual Fee</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" class="weui-input input-credit-ccy-annual-fee" cursor-spacing='20'
                                           value="<?php echo $last_ccy[$prod['uid']]['annual_fee']?:$prod['fee_setting']['annual_fee']?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <input type="hidden" class="weui-input input-credit-ccy-annual-fee-type" value="<?php echo $prod['fee_setting']['annual_fee_type']?>">
                                    <span><?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?>,</span>
                                    <span style="padding-left: 10px" id="span_admin_fee">Default: <?php echo $prod['fee_setting']['annual_fee']?> <?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                </div>
                            </div>
                        <?php }?>

                        <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">Interest-USD</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" cursor-spacing='20' class="weui-input input-credit-ccy-interest"
                                        <?php if(!$prod['is_interest_editable']){ echo 'disabled="disabled" style="background-color:#e5dede"';}?>
                                           value="<?php echo $last_ccy[$prod['uid']]['interest_rate']>0?$last_ccy[$prod['uid']]['interest_rate']:$prod['default_interest_rate']?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <span>%(Monthly), </span>
                                    <span style="padding-left: 20px">Default:<?php echo $prod['default_interest_rate']?> %</span>
                                </div>
                            </div>
                        <?php }?>

                        <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">OP-Fee-USD</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" cursor-spacing='20' class="weui-input input-credit-ccy-operation-fee"
                                        <?php if(!$prod['is_interest_editable']){ echo 'disabled="disabled" style="background-color:#e5dede"';}?>
                                           value="<?php echo $last_ccy[$prod['uid']]['operation_fee']>0?$last_ccy[$prod['uid']]['operation_fee']:$prod['default_operation_fee']?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <span>%(Monthly), </span>
                                    <span style="padding-left: 20px">Default:<?php echo $prod['default_operation_fee']?> %</span>
                                </div>
                            </div>
                        <?php }?>


                        <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                            <div class="weui-cell__hd">
                                <label class="weui-label" style="font-weight: bold">Credit-USD</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input type="number" pattern="[0-9]*" onblur="changeUsdCredit()"
                                       class="weui-input input-credit-ccy-usd" cursor-spacing='20'
                                       <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){ echo 'disabled="disabled"';}?>"
                                       style="font-weight: bold;<?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){ echo 'background-color:#e5dede"';}?>"
                                       value="<?php echo ($last_ccy[$prod['uid']]['credit']==$prod['credit'])?$last_ccy[$prod['uid']]['credit_usd']:$prod['credit_usd']?>">
                            </div>
                            <div class="weui-cell__ft"  style="font-size: 0.8rem">
                                <span>4000 KHR/1 USD</span>
                                <input type="hidden" class="input-credit-ccy-khr"
                                       value="<?php echo ($last_ccy[$prod['uid']]['credit']==$prod['credit'])?$last_ccy[$prod['uid']]['credit_khr']:$prod['credit_khr'];?>" >
                            </div>
                        </div>


                        <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: solid 1px #000!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">Loan-Fee KHR</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" class="weui-input input-credit-ccy-loan-fee-khr" cursor-spacing='20'
                                           value="<?php echo $last_ccy[$prod['uid']]['loan_fee_khr']?:$prod['fee_setting']['loan_fee']*($prod['fee_setting']['loan_fee_type']?4000:1)?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <span><?php if($prod['fee_setting']['loan_fee_type']){ echo 'KHR';}else{ echo '%';}?>,</span>
                                    <span style="padding-left: 10px" id="span_loan_fee">Default: <?php echo $prod['fee_setting']['loan_fee']*($prod['fee_setting']['loan_fee_type']?4000:1)?> <?php if($prod['fee_setting']['loan_fee_type']){ echo 'KHR';}else{ echo '%';}?></span>
                                </div>
                            </div>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">Admin-Fee KHR</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" class="weui-input input-credit-ccy-admin-fee-khr" cursor-spacing='20'
                                           value="<?php echo $last_ccy[$prod['uid']]['admin_fee_khr']?:$prod['fee_setting']['admin_fee']*($prod['fee_setting']['admin_fee_type']?4000:1)?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <span><?php if($prod['fee_setting']['admin_fee_type']){ echo 'KHR';}else{ echo '%';}?>,</span>
                                    <span style="padding-left: 10px" id="span_admin_fee">Default: <?php echo $prod['fee_setting']['admin_fee']*($prod['fee_setting']['admin_fee_type']?4000:1)?> <?php if($prod['fee_setting']['admin_fee_type']){ echo 'KHR';}else{ echo '%';}?></span>
                                </div>
                            </div>
                            <?php if($prod['fee_setting']['annual_fee']>0){?>
                                <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                    <div class="weui-cell__hd">
                                        <label class="weui-label">Annual-Fee KHR</label>
                                    </div>
                                    <div class="weui-cell__bd">
                                        <input type="number" pattern="[0-9]*" class="weui-input input-credit-ccy-annual-fee-khr" cursor-spacing='20'
                                               value="<?php echo $last_ccy[$prod['uid']]['annual_fee_khr']?:$prod['fee_setting']['annual_fee']*($prod['fee_setting']['annual_fee_type']?4000:1)?>">
                                    </div>
                                    <div class="weui-cell__ft" style="font-size: 0.8rem">
                                        <span><?php if($prod['fee_setting']['annual_fee_type']){ echo 'KHR';}else{ echo '%';}?>,</span>
                                        <span style="padding-left: 10px" id="span_annual_fee_khr">Default: <?php echo $prod['fee_setting']['admin_fee']*($prod['fee_setting']['annual_fee_type']?4000:1)?> <?php if($prod['fee_setting']['annual_fee_type']){ echo 'KHR';}else{ echo '%';}?></span>
                                    </div>
                                </div>
                            <?php }?>

                        <?php }?>
                        <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">Interest-KHR</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" class="weui-input input-credit-ccy-interest-khr" cursor-spacing='20'
                                        <?php if(!$prod['is_interest_editable']){ echo 'disabled="disabled" style="background-color:#e5dede"';}?>
                                           value="<?php echo $last_ccy[$prod['uid']]['interest_rate_khr']>0?$last_ccy[$prod['uid']]['interest_rate_khr']:$prod['default_interest_rate']?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <span>%(Monthly), </span>
                                    <span style="padding-left: 20px">Default:<?php echo $prod['default_interest_rate']?> %</span>
                                </div>
                            </div>
                        <?php }?>
                        <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label">OP-Fee-KHR</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <input type="number" pattern="[0-9]*" cursor-spacing='20' class="weui-input input-credit-ccy-operation-fee-khr"
                                        <?php if(!$prod['is_interest_editable']){ echo 'disabled="disabled" style="background-color:#e5dede"';}?>
                                           value="<?php echo $last_ccy[$prod['uid']]['operation_fee_khr']>0?$last_ccy[$prod['uid']]['operation_fee_khr']:$prod['default_operation_fee']?>">
                                </div>
                                <div class="weui-cell__ft" style="font-size: 0.8rem">
                                    <span>%(Monthly), </span>
                                    <span style="padding-left: 20px">Default:<?php echo $prod['default_operation_fee']?> %</span>
                                </div>
                            </div>
                        <?php }?>
                        <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <div class="weui-cell" style="padding: 3px 15px;border-top: none!important;">
                                <div class="weui-cell__hd">
                                    <label class="weui-label" style="font-weight: bold">Credit-KHR</label>
                                </div>
                                <div class="weui-cell__bd">
                                    <label style="color: #808080;font-weight: bold" class="span-credit-ccy-khr">
                                        <?php echo ncPriceFormat(($last_ccy[$prod['uid']]['credit']==$prod['credit'])?$last_ccy[$prod['uid']]['credit_khr']:$prod['credit_khr'],0)?>
                                    </label>
                                </div>
                                <div class="weui-cell__ft"  style="font-size: 0.8rem">
                                    <button type="button" style="padding: 2px;margin: 0" class="weui-btn weui-btn_mini weui-btn_default" data-rate="1" onclick="fastSetCcyCredit(this)">0%</button>
                                    <button type="button" style="padding: 2px;margin: 0" class="weui-btn weui-btn_mini weui-btn_default"  data-rate="0.8" onclick="fastSetCcyCredit(this)">20%</button>
                                    <button type="button" style="padding: 2px;margin: 0" class="weui-btn weui-btn_mini weui-btn_default"  data-rate="0.5"  onclick="fastSetCcyCredit(this)">50%</button>
                                    <button type="button" style="padding: 2px;margin: 0" class="weui-btn weui-btn_mini weui-btn_default"  data-rate="0"  onclick="fastSetCcyCredit(this)">100%</button>
                                </div>
                            </div>
                        <?php }?>
                        <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <div class="weui-cell" onclick="changeServiceFeeListVisable(this)"  data-uid="<?php echo $prod['uid']?>">
                                <div class="weui-cell__hd">Loan Size</div>
                                <div class="weui-cell__bd"></div>
                                <div class="weui-cell__ft">
                                    <span style="font-size: 15px">Service Fee <i class="weui-icon__down"></i></span>
                                </div>
                            </div>
                            <div class="weui-cell cate-interest-list-<?php echo $prod['uid']?>" style="display: none;margin-top: 0" data-sts="0">
                                <div class="weui-cell__bd">
                                    <div class="weui-cells">
                                        <?php foreach($prod['interest_list'] as $sp_item){
                                            if(!$sp_item['is_show_for_client']) continue;
                                            ?>
                                            <div class="weui-cell">
                                                <div class="weui-cell__hd">
                                                    <?php echo ncPriceFormat($sp_item['loan_size_min'],0)." ~ ".ncPriceFormat($sp_item['loan_size_max'],0)?>
                                                </div>
                                                <div class="weui-cell__bd">

                                                </div>
                                                <div class="weui-cell__ft">
                                                    <span style="font-size: 15px"><?php echo $sp_item['service_fee']." ".($sp_item['service_fee_type']?'$':'%')?></span>
                                                </div>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>


                        <?php }?>



                    </div>


                </div>

            </div>
        <?php }?>
    </div>
    <div class="page__bd_spacing" style="padding-top: 20px">
        <button type="button" class="weui-btn weui-btn_primary" onclick="btn_submit_onclick()">
            Submit
        </button>
        <button type="button" class="weui-btn weui-btn_default" onclick="back_credit_form_onclick()">
            BACK
        </button>
    </div>

</form>

<script>

</script>
