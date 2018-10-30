<?php
$last_suggest=$data['last_suggest']?:array();
$last_ccy=$last_suggest['suggest_product']?:array();
?>
<div id="frm_credit_currency">
    <table class="table table-bordered">
        <tr class="table-header">
            <td colspan="3" style="font-size: 15px">
                <span style="padding-left: 80px">
                    Max-Credit:  <kbd><?php echo ncPriceFormat($data['max_credit'],0)?></kbd>
                </span>
                 <span style="padding-left: 80px">
                    Credit-Terms:  <kbd><?php echo $data['credit_terms']?> Month(s)</kbd>
                </span>
                <input type="hidden" id="txt_loan_fee_type" value="<?php echo $data['fee_setting']['loan_fee_type']?>">
                <input type="hidden" id="txt_admin_fee_type" value="<?php echo $data['fee_setting']['admin_fee_type']?>">
            </td>
        </tr>
        <tr class="table-header">
            <td colspan="3">
                Set Currency & Interest
            </td>
        </tr>
        <?php foreach($data['category_list'] as $prod){?>
            <tr>
                <td colspan="3" style="padding: 20px">
                    <div class="currency-cate-item">
                        <table class="table">
                            <tr>
                                <td>
                                    <label class="weui-label" style="font-weight: bold"><?php echo $prod['alias']?></label>
                                </td>
                                <td>
                                </td>
                                <td>
                                    <label><?php echo $prod['sub_product_name']?></label>
                                    <input type="hidden" class="input-credit-ccy-id"  value="<?php echo $prod['uid']?>">
                                    <input type="hidden" class="input-credit-ccy-total" value="<?php echo $prod['credit']?>">
                                </td>
                            </tr>
                            <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                <tr>
                                    <td>
                                        Loan-Fee USD
                                        <input type="hidden" class="input-credit-ccy-loan-fee-type" value="<?php echo $prod['fee_setting']['loan_fee_type']?>">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" pattern="[0-9]*" class="form-control  input-credit-ccy-loan-fee"
                                                   value="<?php echo $last_ccy[$prod['uid']]['loan_fee']?:$prod['fee_setting']['loan_fee']?>">
                                            <span class="input-group-addon"><?php if($prod['fee_setting']['loan_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                        </div>
                                    </td>
                                    <td style="color: #808080">
                                        <span style="padding-left: 10px">Default: <?php echo $prod['fee_setting']['loan_fee']?> <?php if($prod['fee_setting']['loan_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Admin-Fee USD
                                        <input type="hidden" class="input-credit-ccy-admin-fee-type" value="<?php echo $prod['fee_setting']['admin_fee_type']?>">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" pattern="[0-9]*" class="form-control  input-credit-ccy-admin-fee" name="admin_fee"
                                                   value="<?php echo $last_ccy[$prod['uid']]['admin_fee']?:$prod['fee_setting']['admin_fee']?>">
                                            <span class="input-group-addon"><?php if($prod['fee_setting']['admin_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                        </div>
                                    </td>
                                    <td style="color: #808080">
                                        <span style="padding-left: 10px">Default: <?php echo $prod['fee_setting']['admin_fee']?> <?php if($prod['fee_setting']['admin_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                    </td>
                                </tr>
                                <?php if($prod['fee_setting']['annual_fee']>0){?>
                                    <tr>
                                        <td>
                                            Annual Fee
                                            <input type="hidden" class="input-credit-ccy-annual-fee-type" value="<?php echo $prod['fee_setting']['annual_fee_type']?>">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" pattern="[0-9]*" class="form-control  input-credit-ccy-annual-fee"
                                                       value="<?php echo $last_ccy[$prod['uid']]['annual_fee']?:$prod['fee_setting']['annual_fee']?>">
                                                <span class="input-group-addon"><?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                            </div>
                                        </td>
                                        <td style="color: #808080">
                                            <span style="padding-left: 10px">Default: <?php echo $prod['fee_setting']['annual_fee']?> <?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                        </td>
                                    </tr>
                                <?php }?>
                            <?php }else{?>
                                <tr>
                                    <td>
                                        Annual Fee
                                        <input type="hidden" class="input-credit-ccy-annual-fee-type" value="<?php echo $prod['fee_setting']['annual_fee_type']?>">
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" pattern="[0-9]*" class="form-control  input-credit-ccy-annual-fee"
                                                   value="<?php echo $last_ccy[$prod['uid']]['annual_fee']?:$prod['fee_setting']['annual_fee']?>">
                                            <span class="input-group-addon"><?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                        </div>
                                    </td>
                                    <td style="color: #808080">
                                        <span style="padding-left: 10px">Default: <?php echo $prod['fee_setting']['annual_fee']?> <?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                    </td>
                                </tr>
                            <?php }?>


                            <tr style="display: <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE) echo 'none'?>">
                                <td>
                                    Interest-USD
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" pattern="[0-9]*"  class="form-control input-credit-ccy-interest"
                                               <?php if(!$prod['is_interest_editable']) {echo 'disabled';}?>
                                               value="<?php echo $last_ccy[$prod['uid']]['interest_rate']>0?$last_ccy[$prod['uid']]['interest_rate']:$prod['default_interest_rate']?>">

                                        <span class="input-group-addon">%(Monthly)</span>
                                    </div>

                                </td>
                                <td>
                                    <span style="padding-left: 20px">Default:<?php echo $prod['default_interest_rate']?> %</span>
                                </td>
                            </tr>
                            <tr style="display: <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE) echo 'none'?>">
                                <td>
                                    Operation-Fee-USD
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" pattern="[0-9]*"  class="form-control input-credit-ccy-operation-fee"
                                            <?php if(!$prod['is_interest_editable']) {echo 'disabled';}?>
                                               value="<?php echo $last_ccy[$prod['uid']]['operation_fee']>0?$last_ccy[$prod['uid']]['operation_fee']:$prod['default_operation_fee']?>">

                                        <span class="input-group-addon">%(Monthly)</span>
                                    </div>

                                </td>
                                <td>
                                    <span style="padding-left: 20px">Default:<?php echo $prod['default_operation_fee']?> %</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>Credit-USD</strong>
                                </td>
                                <td>
                                    <input type="number" pattern="[0-9]*" onblur="changeUsdCredit()" style="width: 262px"
                                           class="form-control input-credit-ccy-usd" cursor-spacing='20'
                                                <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE) {echo 'disabled';}?>
                                           value="<?php echo ($last_ccy[$prod['uid']]['credit']==$prod['credit'])?$last_ccy[$prod['uid']]['credit_usd']:$prod['credit_usd']?>">
                                </td>
                                <td>
                                    <span>4000 KHR/1 USD</span>
                                    <input type="hidden" class="input-credit-ccy-khr"
                                           value="<?php echo ($last_ccy[$prod['uid']]['credit']==$prod['credit'])?$last_ccy[$prod['uid']]['credit_khr']:$prod['credit_khr'];?>" >
                                </td>
                            </tr>


                            <?php if($prod['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                <tr>
                                    <td>
                                        Loan-Fee KHR
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" pattern="[0-9]*" class="form-control  input-credit-ccy-loan-fee-khr"
                                                   value="<?php echo $last_ccy[$prod['uid']]['loan_fee_khr']?:$prod['fee_setting']['loan_fee']*($prod['fee_setting']['loan_fee_type']?4000:1)?>">
                                            <span class="input-group-addon"><?php if($prod['fee_setting']['loan_fee_type']){ echo 'KHR';}else{ echo '%';}?></span>
                                        </div>
                                    </td>
                                    <td style="color: #808080">
                                        <span style="padding-left: 10px">Default: <?php echo $prod['fee_setting']['loan_fee']*($prod['fee_setting']['loan_fee_type']?4000:1)?> <?php if($prod['fee_setting']['loan_fee_type']){ echo 'KHR';}else{ echo '%';}?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Admin-Fee KHR
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" pattern="[0-9]*" class="form-control  input-credit-ccy-admin-fee-khr"
                                                   value="<?php echo $last_ccy[$prod['uid']]['admin_fee_khr']?:$prod['fee_setting']['admin_fee']*($prod['fee_setting']['admin_fee_type']?4000:1)?>">
                                            <span class="input-group-addon"><?php if($prod['fee_setting']['admin_fee_type']){ echo 'KHR';}else{ echo '%';}?></span>
                                        </div>
                                    </td>
                                    <td style="color: #808080">
                                        <span style="padding-left: 10px">Default: <?php echo $prod['fee_setting']['admin_fee']*($prod['fee_setting']['admin_fee_type']?4000:1)?> <?php if($prod['fee_setting']['admin_fee_type']){ echo 'KHR';}else{ echo '%';}?></span>
                                    </td>
                                </tr>
                                <?php if($prod['fee_setting']['annual_fee']>0){?>
                                    <tr>
                                        <td>
                                            Annual-Fee KHR
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" pattern="[0-9]*" class="form-control  input-credit-ccy-annual-fee-khr"
                                                       value="<?php echo $last_ccy[$prod['uid']]['annual_fee_khr']?:$prod['fee_setting']['annual_fee']?>">
                                                <span class="input-group-addon"><?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                            </div>
                                        </td>
                                        <td style="color: #808080">
                                            <span style="padding-left: 10px">Default: <?php echo $prod['fee_setting']['annual_fee']?> <?php if($prod['fee_setting']['annual_fee_type']){ echo '$';}else{ echo '%';}?></span>
                                        </td>
                                    </tr>
                                <?php }?>

                            <?php }?>


                            <tr style="display: <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE) echo 'none'?>">
                                <td>
                                    Interest-KHR
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" pattern="[0-9]*" class="form-control input-credit-ccy-interest-khr"
                                            <?php if(!$prod['is_interest_editable']) {echo 'disabled';}?>
                                               value="<?php echo $last_ccy[$prod['uid']]['interest_rate_khr']>0?$last_ccy[$prod['uid']]['interest_rate_khr']:$prod['default_interest_rate']?>">
                                        <span class="input-group-addon">%(Monthly) </span>

                                    </div>
                                </td>
                                <td>
                                    <span style="padding-left: 20px">Default: <?php echo $prod['default_interest_rate']?> %</span>
                                </td>
                            </tr>
                            <tr style="display: <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE) echo 'none'?>">
                                <td>
                                    Operation-Fee-USD
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" pattern="[0-9]*"  class="form-control input-credit-ccy-operation-fee-khr"
                                            <?php if(!$prod['is_interest_editable']) {echo 'disabled';}?>
                                               value="<?php echo $last_ccy[$prod['uid']]['operation_fee_khr']>0?$last_ccy[$prod['uid']]['operation_fee_khr']:$prod['default_operation_fee']?>">

                                        <span class="input-group-addon">%(Monthly)</span>
                                    </div>

                                </td>
                                <td>
                                    <span style="padding-left: 20px">Default:<?php echo $prod['default_operation_fee']?> %</span>
                                </td>
                            </tr>
                            <tr style="display: <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE) echo 'none'?>">
                                <td>
                                    <strong>Credit-KHR</strong>
                                </td>
                                <td>
                                    <label style="color: #808080" class="span-credit-ccy-khr">
                                        <?php echo ncPriceFormat(($last_ccy[$prod['uid']]['credit']==$prod['credit'])?$last_ccy[$prod['uid']]['credit_khr']:$prod['credit_khr'],0)?>
                                    </label>
                                </td>
                                <td>
                                    <div>
                                        <button type="button" style="padding: 2px;margin: 0" class="weui-btn weui-btn_mini weui-btn_default" data-rate="1" onclick="fastSetCcyCredit(this)">0%</button>
                                        <button type="button" style="padding: 2px;margin: 0" class="weui-btn weui-btn_mini weui-btn_default"  data-rate="0.8" onclick="fastSetCcyCredit(this)">20%</button>
                                        <button type="button" style="padding: 2px;margin: 0" class="weui-btn weui-btn_mini weui-btn_default"  data-rate="0.5"  onclick="fastSetCcyCredit(this)">50%</button>
                                        <button type="button" style="padding: 2px;margin: 0" class="weui-btn weui-btn_mini weui-btn_default"  data-rate="0"  onclick="fastSetCcyCredit(this)">100%</button>
                                    </div>
                                </td>
                            </tr>
                            <?php if($prod['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                <tr>
                                    <td colspan="10">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td>Loan Size</td>
                                                <td>Service Fee</td>
                                            </tr>
                                            <?php foreach($prod['interest_list'] as $sp_item){
                                                 if(!$sp_item['is_show_for_client']) continue;
                                                ?>
                                                <tr>
                                                    <td><?php echo ncPriceFormat($sp_item['loan_size_min'],0)." ~ ".ncPriceFormat($sp_item['loan_size_max'],0)?></td>
                                                    <td class="text-right">
                                                         <?php echo $sp_item['service_fee']?> <?php if($sp_item['service_fee_type']){ echo '$';}else{echo '%';}?>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        </table>
                                    </td>
                                </tr>
                            <?php }?>
                        </table>
                    </div>
                </td>
            </tr>
        <?php }?>
    </table>
    <div>
        <ul class="list-group loan-item">
            <li class="list-group-item" style="padding: 0">
                <textarea class="form-control" name="remark" id="txt_remark" placeholder="Please Input Comment" style="width: 100%;height: 50px"><?php echo $output['last_suggest']['remark']; ?></textarea>
            </li>
            <li class="list-group-item">
                <p class="text-center">
                  <button type="button" class="btn btn-primary" onclick="btn_submit_onclick()" style="width: 300px"> Submit </button>
                </p>
                <p class="text-center">
                    <button type="button" class="btn btn-default" onclick="btn_back_onclick()" style="width: 300px"> Back </button>
                </p>
            </li>
        </ul>
    </div>
</div>
