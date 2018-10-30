<table class="table table-hover table-bordered">
    <tr class="warning">
        <td colspan="10">
            <label>
                Request By : <?php echo $credit_grant['suggest']['operator_name']?>
            </label>
        </td>
    </tr>
    <tr>
        <td><label>Creator</label></td>
        <td><?php echo $credit_grant['operator_name']?></td>
    </tr>
    <tr>
        <td><label>Request Time</label></td>
        <td><?php echo $credit_grant['grant_time']?></td>
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
            <?php echo $credit_category[$credit_grant['default_credit_category_id']]['alias'] ?>
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
                                        <span><?php echo $member_assets[$asset_id]['asset_name']; ?></span>
                                        <span style="font-size: 12px;font-weight: 400">(<?php echo $member_assets[$asset_id]['asset_type']; ?>)</span>
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
                    <?php echo $credit_category[$val['member_credit_category_id']]['alias'] ?>
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
<div class="container">
    <p style="padding: 5px;background-color: #fcf8e3">
        <span>Request Currency-Credit</span>
    </p>
    <table class="table table-hover table-bordered">
        <tr class="table-header">
            <td>Credit-Category</td>
            <td>USD</td>
            <td>KHR</td>
            <td>Sub-Total-Credit</td>
        </tr>
        <?php if($credit_grant['grant_product']){?>
            <?php foreach($credit_grant['grant_product'] as $prod_item){
                $category=$output['credit_category'][$prod_item['member_credit_category_id']];
                ?>
                <tr>
                    <td>
                        <kbd><?php echo $category['alias']?></kbd>
                        <br/>
                        <?php echo $category['sub_product_name']?>

                    </td>
                    <td>
                        <kbd><?php echo ncPriceFormat($prod_item['credit_usd'])?></kbd>
                        <?php if($category['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <br/>
                            Annual-Fee: <?php echo $prod_item['annual_fee']?> <?php if($prod_item['annual_fee_type']){ echo '$';} else {echo '%';}?>
                        <?php }else{?>
                            <br/>
                            Loan-Fee: <?php echo $prod_item['loan_fee']?> <?php if($prod_item['loan_fee_type']){ echo '$';} else {echo '%';}?>
                            <br/>
                            Admin-Fee:<?php echo $prod_item['admin_fee']?> <?php if($prod_item['admin_fee_type']){ echo '$';} else {echo '%';}?>
                        <?php }?>

                        <?php if($category['special_key']==specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                            <br/>
                            Service Fee: N/A
                        <?php }else{?>
                            <br/>
                            Interest: <?php echo ncPriceFormat($prod_item['interest_rate'])?> %
                            <br/>
                            Operation-Fee: <?php echo ncPriceFormat($prod_item['operation_fee'])?> %

                        <?php }?>


                    </td>
                    <td>
                <?php if($category['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                    <kbd><?php echo ncPriceFormat($prod_item['credit_khr'])?></kbd>
                    <br/>
                    Loan-Fee: <?php echo $prod_item['loan_fee_khr']?> <?php if($prod_item['loan_fee_type']){ echo 'KHR';} else {echo '%';}?>
                    <br/>
                    Admin-Fee:<?php echo $prod_item['admin_fee_khr']?> <?php if($prod_item['admin_fee_type']){ echo 'KHR';} else {echo '%';}?>
                    <br/>
                    Interest: <?php echo ncPriceFormat($prod_item['interest_rate_khr'])?> %
                    <br/>
                    Operation-Fee: <?php echo ncPriceFormat($prod_item['operation_fee_khr'])?> %

                <?php }else{?>
                    Not Support
                <?php }?>

                    </td>
                    <td>
                        <?php echo ncPriceFormat($prod_item['credit'])?>
                    </td>
                </tr>



            <?php }?>
        <?php }else{?>
            <tr>
                <td colspan="10">
                    <?php include(template(":widget/no_record"))?>
                </td>
            </tr>
        <?php }?>
    </table>

</div>
<table class="table">
    <tr>
        <td colspan="10" class="text-center">
            <?php $allowed_delete =creditFlowClass::checkGrantDeletePermission($credit_grant['uid']); ?>
            <?php if ($allowed_delete) { ?>
                <?php if( !$no_need_delete_btn ){ ?>
                    <button type="button" class="btn btn-danger" onclick="deleteCreditGrant(<?php echo $credit_grant['uid']; ?>)" style="width: 30%"><i class="fa fa-close"></i> Delete </button>
                <?php } ?>

                <p>Not Signed Agreement yet,Allowed to delete,Set the state as pending approve again after deleted</p>
            <?php }else{ ?>
                <p>Already Signed Agreement,Not Allowed to delete</p>
            <?php }?>
        </td>
    </tr>

</table>
<script>
    function deleteCreditGrant(_uid) {
        if (!_uid) {
            return;
        }
        yo.confirm("Delete", "<?php echo $lang['common_confirm_delete']?>", function (_r) {
            if (!_r) return;
            $(".page").waiting();
            yo.loadData({
                _c: "loan_committee",
                _m: "deleteCreditGrant",
                param: {uid: _uid},
                callback: function (_o) {
                    $(".page").unmask();
                    if (_o.STS) {
                        alert('Removed Already',1,function(){
                            window.location.href = '<?php echo getUrl('loan_committee', 'grantCreditHistory', array(), false, BACK_OFFICE_SITE_URL) ?>';
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }
</script>