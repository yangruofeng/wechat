<style>
    #co_request .table tbody tr td {
        padding: 6px 3px;
    }

    #co_request .asset_for_category td {
        padding-top: 0px!important;
    }

    #co_request .asset_for_category td .pl-25 {
        font-weight: 400;
        font-style: italic;
    }
</style>
<div class="col-sm-5">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"  class="active">
            <a href="#co_request" aria-controls="co_request" role="tab" data-toggle="tab">Co Request</a>
        </li>
        <li role="presentation">
            <a href="#system_analysis" aria-controls="system_analysis" role="tab" data-toggle="tab" style="border-left: 0">System Analysis</a>
        </li>
        <li role="presentation">
            <a href="#coefficient_analysis" aria-controls="coefficient_analysis" role="tab" data-toggle="tab" style="border-left: 0">Coefficient Analysis</a>
        </li>
    </ul>
    <div class="tab-content">
        <?php
        $analysis = $output['analysis'];
        $member_request = $analysis['member_request'];
        $member_income = $analysis['income'];
        $member_expense = $analysis['expense'];
        $suggest_profile = $analysis['suggest'];
        $product_list = $output['product_list']; ?>

        <div role="tabpanel" class="tab-pane" id="system_analysis">
            <?php require_once template("widget/item.credit.system_analysis")?>
        </div>

        <?php $co_list = $output['co_list'];?>
        <?php $co_suggest_list = $output['co_suggest_list'];?>
        <div role="tabpanel" class="tab-pane active" id="co_request">
            <table class="table co_suggest_list">
                <tbody class="table-body">
                <tr>
                    <td>Research Item</td>
                    <?php foreach($co_list as $co){?>
                        <td>
                            <?php echo $co['officer_name']?>
                        </td>
                    <?php }?>
                </tr>
                </tbody>
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Request-Time</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td>
                            <em><?php echo $co_suggest_list[$k]['request_time']?></em>
                            <?php if($co_suggest_list[$k]['request_time'] && $output['last_grant']['grant_time']>$co_suggest_list[$k]['request_time']){?>
                                 <i class="fa fa-close"> Expired</i>
                            <?php }?>
                        </td>
                    <?php }?>
                </tr>
                <tr>
                    <td><label class="control-label">Repay Ability(Monthly)</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td>
                            <em><?php echo $co_suggest_list[$k] ? ncAmountFormat($co_suggest_list[$k]['monthly_repayment_ability']) : ''?></em>
                        </td>
                    <?php }?>
                </tr>
                <tr>
                    <td><label class="control-label">Max Terms</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td>
                            <?php echo $co_suggest_list[$k] ? $co_suggest_list[$k]['credit_terms'] . 'Months' : ''?>
                        </td>
                    <?php }?>
                </tr>
                <tr>
                    <td><label class="control-label">Default Credit</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td>
                            <em><?php echo $co_suggest_list[$k] ? ncAmountFormat($co_suggest_list[$k]['default_credit']) : ''?></em>
                        </td>
                    <?php }?>
                </tr>
                <tr>
                    <td><label class="control-label">For Credit Category</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td>
                            <?php echo $co_suggest_list[$k] ? $product_list[$co_suggest_list[$k]['default_credit_category_id']]['alias'] : ''?>
                        </td>
                    <?php }?>
                </tr>
                <?php
                    $mortgage_type=member_assetsClass::getMortgageType();
                    $collateral_type=member_assetsClass::getCollateralType();
                ?>
                <tr>
                    <td><label class="control-label">Increase Credit By Mortgage</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td></td>
                    <?php }?>
                </tr>
                <?php if($analysis['all_asset']) {?>
                    <?php foreach($analysis['all_asset'] as $assets){
                            if(!in_array($assets['asset_type'],$mortgage_type)) continue;
                        ?>
                        <tr>
                            <td>
                                <span class="pl-25">
                                    <span><?php echo $assets['asset_name']; ?></span>
                                    <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$assets['asset_type']]; ?>)</span>
                                </span>
                            </td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <em><?php echo $co_suggest_list[$k]['suggest_detail_list'] ? ncAmountFormat($co_suggest_list[$k]['suggest_detail_list'][$assets['uid']]['credit']) : ''?></em>
                                </td>
                            <?php }?>
                        </tr>
                        <tr class="asset_for_category">
                            <td>
                                <span class="pl-25">
                                    For Credit Category
                                </span>
                            </td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <?php echo $co_suggest_list[$k]['suggest_detail_list'] ? $product_list[$co_suggest_list[$k]['suggest_detail_list'][$assets['uid']]['member_credit_category_id']]['alias'] : ''?>
                                </td>
                            <?php }?>
                        </tr>
                    <?php }?>
                <?php } else { ?>
                    <tr>
                        <td></td>
                        <td colspan="10">
                            No Record
                        </td>
                    </tr>
                <?php }?>
                <tr>
                    <td><label class="control-label">Collateral Certificate</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td></td>
                    <?php }?>
                </tr>
                <?php if($analysis['all_asset']) {?>
                    <?php foreach($analysis['all_asset'] as $assets){
                        if(!in_array($assets['asset_type'],$collateral_type)) continue;
                        ?>
                        <tr>
                            <td>
                                <span class="pl-25">
                                    <span><?php echo $assets['asset_name']; ?></span>
                                    <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$assets['asset_type']]; ?>)</span>
                                </span>
                            </td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <?php if($co_suggest_list[$k]['suggest_detail_list'][$assets['uid']]){?>
                                        <i class="fa fa-check"></i>
                                    <?php }?>
                                </td>
                            <?php }?>
                        </tr>
                    <?php }?>
                <?php } else { ?>
                    <tr>
                        <td></td>
                        <td colspan="10">
                            No Record
                        </td>
                    </tr>
                <?php }?>


                <tr>
                    <td><label class="control-label">Max Credit</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td>
                            <em><?php echo $co_suggest_list[$k] ? ncAmountFormat($co_suggest_list[$k]['max_credit']) : ''?></em>
                        </td>
                    <?php }?>
                </tr>

                <tr>
                    <td><label class="control-label">Remark</label></td>
                    <?php foreach($co_list as $k => $co){?>
                        <td>
                            <?php echo $co_suggest_list[$k] ? $co_suggest_list[$k]['remark'] : ''?>
                        </td>
                    <?php }?>
                </tr>
                </tbody>
            </table>
            <?php foreach($co_list as $k => $co){
                    if($co_suggest_list[$k]['suggest_product']){
                ?>
                     <table class="table table-bordered table-hover">
                         <tr style="background-color: #808080">
                             <td colspan="10">
                                 <span>Suggest Currency-Credit BY 【<?php echo $co['officer_name'] ?>】</span>
                             </td>
                         </tr>
                         <tr>
                             <td>Category</td>
                             <td>Currency</td>
                             <td>Credit</td>
                             <td>Interest</td>
                             <td>OperationFee</td>
                             <td>LoanFee</td>
                             <td>AdminFee</td>
                             <td>AnnualFee</td>
                         </tr>
                         <?php foreach($co_suggest_list[$k]['suggest_product'] as $co_prod_item){
                             $match_category=$product_list[$co_prod_item['member_credit_category_id']];
                             ?>
                             <tr>
                                 <td>
                                     <kbd><?php echo $match_category['alias']?></kbd>
                                 </td>

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
                                        <?php echo ncPriceFormat($co_prod_item['annual_fee_khr'])?> <?php if($co_prod_item['annual_fee_type']){ echo 'KHR';}else{echo '%';}?>
                                    </td>
                                <?php }?>

                             </tr>


                         <?php }?>
                     </table>

            <?php }}?>
        </div>
        <div role="tabpanel" class="tab-pane" id="coefficient_analysis">
            <?php require_once template("widget/item.credit.coefficient_analysis")?>
        </div>
    </div>
</div>