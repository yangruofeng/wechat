<?php $credit_category=$output['credit_category']?>
<div class="col-sm-6">
    <div class="basic-info">
        <div class="ibox-title">
            <h5><i class="fa fa-id-card-o"></i>Credit Grant</h5>
        </div>
        <div class="content table-responsive">
            <form class="form-horizontal table-responsive" method="post" action="<?php echo getUrl('loan_committee', 'commitCreditApplication', array(), false, BACK_OFFICE_SITE_URL)?>">
                <input type="hidden" name="form_submit" value="ok">
                <input type="hidden" name="type" value="1">
                <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                <input type="hidden" name="suggest_id" value="<?php echo $bm_suggest['uid']; ?>">
                <table class="table table-hover table-bordered">
                    <tr class="table-header">
                        <td>Category</td>
                        <td>Repayment</td>
                        <td>One Time</td>
                        <td>Credit</td>
                        <td>Credit Balance</td>
                    </tr>
                    <?php if($credit_category){ ?>
                        <?php foreach ($credit_category as $v) { ?>
                            <tr>
                                <td>
                                    <?php echo $v['alias']?>
                                </td>
                                <td>
                                    <?php echo $v['sub_product_name']?>
                                </td>
                                <td>
                                    <?php if($v['is_one_time']){?>
                                        <i class="fa fa-check"></i>
                                    <?php }?>
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($v['credit'])?>
                                </td>
                                <td><?php echo ncPriceFormat($v['credit_balance'])?></td>
                            </tr>
                        <?php  } ?>
                    <?php }else{?>
                        <tr>
                            <td colspan="20">
                                <?php include(template(":widget/no_record"))?>
                            </td>
                        </tr>
                    <?php }?>
                </table>
                <table class="table table-hover table-bordered">
                    <tr class="warning">
                        <td colspan="10">
                            <label>
                                BRANCH : <?php echo $output['branch_name']?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Operator/Branch Manager</label></td>
                        <td><?php echo $output['operator_name']."(".$output['operator_code'].")"?></td>
                    </tr>
                    <tr>
                        <td><label>Request Time</label></td>
                        <td><?php echo $bm_suggest['request_time']?></td>
                    </tr>

                    <tr>
                        <td><label class="control-label">Monthly Repayment Ability</label></td>
                        <td>
                            <?php echo intval($bm_suggest['monthly_repayment_ability']); ?>
                        </td>
                    </tr>

                    <tr>
                        <td><label class="control-label">Default Credit</label></td>
                        <td>
                            <?php echo $bm_suggest['default_credit']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="pl-25">For Credit Category:</span>
                        </td>
                        <td>
                            <?php echo $output['credit_category'][$bm_suggest['default_credit_category_id']]['alias'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Increase Credit By</label></td>
                        <td></td>
                    </tr>

                    <?php if ($bm_suggest['suggest_detail_list']) { ?>
                        <?php foreach($bm_suggest['suggest_detail_list'] as $asset_id=>$val) {
                                if(!$val['credit']) continue;
                            ?>
                            <tr>
                                <td>
                                    <span class="pl-25">
                                        <span><?php echo $output['member_assets'][$asset_id]['asset_name']; ?></span>
                                        <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$output['member_assets'][$asset_id]['asset_type']]; ?>)</span>
                                    </span>
                                    <a style="margin-left: 10px;font-style: italic;" target="_blank" href="<?php echo getBackOfficeUrl('loan_committee','showAssetMap',array('asset_id'=>$asset_id)); ?>" >Google Map</a>
                                </td>
                                <td>
                                    <?php echo $val['credit']; ?>
                                </td>
                            </tr>
                            <tr class="tr-credit-category-<?php echo $val['uid']?>" style="<?php if(!$val['credit']) echo 'display:none'?>">
                                <td>
                                    <span  style="padding-left: 50px">For Credit Category:</span>
                                </td>
                                <td>
                                    <?php echo $output['credit_category'][$val['member_credit_category_id']]['alias'] ?>
                                </td>
                            </tr>

                        <?php }?>
                    <?php } else { ?>
                        <tr>
                            <td><span class="pl-25"></span></td>
                            <td>
                                No Record
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td><label class="control-label">Collateral Certificate</label></td>
                        <td></td>
                    </tr>
                    <?php if ($suggest_profile['collateral']) { ?>
                        <?php foreach($suggest_profile['collateral'] as $asset_id=>$item) {
                            ?>
                            <tr>
                                <td>
                                    <span class="pl-25">
                                        <span><?php echo $suggest_profile['collateral'][$asset_id]['asset_name']; ?></span>
                                        <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$suggest_profile['collateral'][$asset_id]['asset_type']]; ?>)</span>
                                    </span>
                                </td>
                                <td>
                                    <?php if($bm_suggest['suggest_detail_list'][$asset_id]){ ?>
                                        <i class="fa fa-check"></i>
                                    <?php }?>
                                </td>
                            </tr>

                        <?php }?>
                    <?php } else { ?>
                        <tr>
                            <td><span class="pl-25"></span></td>
                            <td>
                                No Record
                            </td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <td><label class="control-label">Max Credit</label></td>
                        <td>
                            <label>
                                <?php echo $bm_suggest['max_credit']; ?>
                            </label>

                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Invalid Terms</label></td>
                        <td>
                            <?php echo $bm_suggest['credit_terms']; ?> Months
                        </td>
                    </tr>



                    <tr class="warning">
                        <td colspan="10">
                            COMMENT : <?php echo $bm_suggest['remark'];?>
                        </td>
                    </tr>


                </table>

                <table class="table table-bordered">
                    <tr style="background-color: #808080">
                        <td colspan="10">
                            <span>Request Currency-Credit</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Category</td>
                        <td>Credit Terms</td>
                        <td>Currency</td>
                        <td>Credit</td>
                        <td>Interest</td>
                        <td>OperationFee</td>
                        <td>LoanFee</td>
                        <td>AdminFee</td>
                        <td>AnnualFee</td>
                    </tr>
                    <?php foreach($bm_suggest['suggest_product'] as $co_prod_item){
                        $match_category=$credit_category[$co_prod_item['member_credit_category_id']];
                        ?>
                        <tr>
                            <td >
                                <kbd><?php echo $match_category['alias']?></kbd>
                            </td>

                            <td rowspan="2"><?php echo $bm_suggest['credit_terms'].'M'; ?></td>

                            <td>USD</td>
                            <td>
                                <?php echo ncPriceFormat($co_prod_item['credit_usd'])?>
                            </td>
                            <?php if($match_category['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                <td colspan="4">
                                    <code>Auto Match Service Fee</code>
                                </td>

                            <?php }else{?>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['interest_rate'])?>%
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['operation_fee'])?>%
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['loan_fee'])?> <?php if($co_prod_item['loan_fee_type']){ echo '$';}else{echo '%';}?>
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['admin_fee'])?> <?php if($co_prod_item['admin_fee_type']){ echo '$';}else{echo '%';}?>
                                </td>
                            <?php }?>
                            <td>
                                <?php echo ncPriceFormat($co_prod_item['annual_fee'])?> <?php if($co_prod_item['annual_fee_type']){ echo '$';}else{echo '%';}?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $match_category['sub_product_name']?>
                                <br />
                                <?php if( $match_category['interest_type'] == interestPaymentEnum::SEMI_BALLOON_INTEREST ){ ?>
                                    <kbd >Principal Period: <?php echo $client_loan_account_info['principal_periods']?$client_loan_account_info['principal_periods'].'M':'/'; ?></kbd>
                                <?php } ?>
                            </td>
                            <?php if($match_category['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                <td colspan="10">
                                    <code>Not Support KHR</code>
                                </td>
                            <?php }else{?>
                                <td>KHR</td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['credit_khr'])?>
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['interest_rate_khr'])?>%
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['operation_fee_khr'])?>%
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['loan_fee_khr'])?> <?php if($co_prod_item['loan_fee_type']){ echo 'KHR';}else{echo '%';}?>
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['admin_fee_khr'])?> <?php if($co_prod_item['admin_fee_type']){ echo 'KHR';}else{echo '%';}?>
                                </td>
                                <td>
                                    <?php echo ncPriceFormat($co_prod_item['annual_fee_khr'])?> <?php if($co_prod_item['annual_fee_type']){ echo '$';}else{echo '%';}?>
                                </td>
                            <?php }?>

                        </tr>

                    <?php }?>
                </table>



                <table class="table" style="margin-top: 20px">
                    <tbody>
                    <tr>
                        <td colspan="10">
                            <textarea class="form-control" name="remark" style="width: 100%;height: 100px" placeholder="Please Input Comment"></textarea>
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                    <tr>
                        <td><label class="control-label">Approve By</label></td>
                        <td>
                            <?php foreach ($output['committee_member'] as $val) { ?>
                                <label class="checkbox-inline col-sm-6" style="margin-left: 0px">
                                    <input type="checkbox" name="committee_member[]" value="<?php echo $val['user_id'] ?>"><?php echo $val['user_name'] ?> (<?php echo $val['user_code']?>)
                                </label>
                            <?php } ?>
                            <div class="error_msg"></div>
                        </td>
                    </tr>
                    <?php if($bm_suggest['is_append']){?>
                        <tr>
                            <td colspan="2" class="text-right">
                                <input type="hidden" name="is_append" value="1">
                                <label style="color: red">Request To Append To Last Grant </label>
                                <span> Expiry time is <?php echo $credit['expire_time']?:'?'; ?></span>
                            </td>
                        </tr>

                    <?php }?>

                    <tr>
                        <td colspan="2" style="text-align: center">
                            <button type="button" class="btn btn-primary" id="grant_submit" style="width: 30%;margin-right: 5px"><i class="fa fa-check"></i> <?php echo 'Submit To Vote' ?></button>
                           <!--已经取消一个人就拒绝BM提交信息的模式，即使拒绝，也需要投票否决-->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<script>

</script>