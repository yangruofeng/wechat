<?php $credit_category=$output['credit_category']?>
<div class="col-sm-6">
    <div class="basic-info">
        <div class="ibox-title">
            <h5><i class="fa fa-id-card-o"></i>Credit Grant</h5>
        </div>
        <div class="content">
            <table class="table table-hover table-bordered">
                <tr class="table-header">
                    <td>Category</td>
                    <td>Repayment</td>
                    <td>Interest</td>
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
                                <?php echo $v['interest_package_name']?:'Default'?>
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
                    <td><?php echo $credit_grant['request_time']?></td>
                </tr>

                <tr>
                    <td><label class="control-label">Monthly Repayment Ability</label></td>
                    <td>
                        <?php echo intval($credit_grant['monthly_repayment_ability']); ?>
                    </td>
                </tr>

                <tr>
                    <td><label class="control-label">Default Credit</label></td>
                    <td>
                        <?php echo $credit_grant['default_credit']; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="pl-25">For Credit Category:</span>
                    </td>
                    <td>
                        <?php echo $output['credit_category'][$credit_grant['default_credit_category_id']]['alias'] ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Increase Credit By</label></td>
                    <td></td>
                </tr>

                <?php if ($credit_grant['suggest_detail_list']) { ?>
                    <?php foreach($credit_grant['suggest_detail_list'] as $asset_id=>$val) {
                        if(!$val['credit']) continue;
                        ?>
                        <tr>
                            <td>
                                    <span class="pl-25">
                                        <span><?php echo $output['member_assets'][$asset_id]['asset_name']; ?></span>
                                        <span style="font-size: 12px;font-weight: 400">(<?php echo $output['member_assets'][$asset_id]['asset_type']; ?>)</span>
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
                                <?php if($credit_grant['suggest_detail_list'][$asset_id]){ ?>
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
                            <?php echo $credit_grant['max_credit']; ?>
                        </label>

                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Invalid Terms</label></td>
                    <td>
                        <?php echo $credit_grant['credit_terms']; ?> Months
                    </td>
                </tr>

                <tr class="warning">
                    <td colspan="10">
                        COMMENT : <?php echo $credit_grant['remark'];?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php include_once(template("loan_committee/inc.credit.ref.sys"))?>
</div>
<script>

</script>