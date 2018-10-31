<style>
    .product-name{
        font-size: 1.3em;
        font-weight: 600;
        color:#000;
    }

</style>

<?php
$client_info = $output['client_info'];
$credit_suggest = $output['credit_suggest'];
$credit_product = $output['credit_product'];
?>

<div class="container js_container">


    <div class="page list js_show">
        <div class="page__hd">
            <h3 class="page_title" style="text-align: center;background-color: #ddd;padding: 10px 0;">Vote For Credit</h3>
        </div>
        <div class="page__bd">

            <div class="weui-panel weui-panel_access">
                <div class="weui-panel__bd">
                    <div  class="weui-media-box weui-media-box_appmsg">
                        <div class="weui-media-box__hd">
                            <img src="<?php echo getImageUrl($client_info['member_image'],imageThumbVersion::MAX_120); ?>" alt="" class="weui-media-box__thumb">
                        </div>
                        <div class="weui-media-box__bd">
                            <h4 class="weui-media-box__title"><?php echo $client_info['login_code'] . '(' . $client_info['obj_guid'] . ')';?></h4>
                            <p class="weui-media-box__desc">
                                <?php echo $client_info['display_name'];?>
                            </p>
                            <p class="weui-media-box__desc">
                                <?php echo $client_info['phone_id']?>
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="weui-cells">
                <div class="weui-cell">
                    <div class="weui-cell__bd weui-cell_primary">
                        <p>Default Credit</p>
                    </div>
                    <div class="weui-cell__ft">
                        <span><?php echo ncPriceFormat($credit_suggest['default_credit']);?></span>
                    </div>
                </div>

                <div class="weui-cell">
                    <div class="weui-cell__bd weui-cell_primary">
                        <p>Max Credit</p>
                    </div>
                    <div class="weui-cell__ft">
                        <span><?php echo ncPriceFormat($credit_suggest['max_credit']);?></span>
                    </div>
                </div>

                <div class="weui-cell">
                    <div class="weui-cell__bd weui-cell_primary">
                        <span>Invalid Terms</span>
                    </div>
                    <div class="weui-cell__ft">
                        <span><?php echo $credit_suggest['credit_terms'] . ' Months';?></span>
                    </div>
                </div>

                <div class="weui-cell">
                    <div class="weui-cell__bd weui-cell_primary">
                        <p>Remark</p>
                    </div>
                    <div class="weui-cell__ft">
                        <span><?php echo $credit_suggest['remark'];?></span>
                    </div>
                </div>

            </div>

            <?php if( !empty($credit_product) ){ foreach( $credit_product as $product ){ ?>
                <div class="weui-panel">
                    <div class="weui-panel__hd">
                        <label for="" class="product-name"><?php echo $product['credit_category_info']['alias']; ?></label>
                    </div>
                    <div class="weui-panel__bd">
                        <div class="weui-media-box weui-media-box_small-appmsg">
                            <div class="weui-cells">
                                <div class="weui-cell">
                                    <div class="weui-cell__bd weui-cell_primary">
                                        <p>Is one time</p>
                                    </div>
                                    <div class="weui-cell__ft">
                                        <?php if( $product['credit_category_info']['is_one_time'] ){ ?>
                                            <i class="weui-icon-success-no-circle"></i>
                                        <?php }else{ ?>
                                            No
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="weui-cell">
                                    <div class="weui-cell__bd weui-cell_primary">
                                        <p>Interest Package</p>
                                    </div>
                                    <div class="weui-cell__ft">
                                        <?php echo $product['credit_category_info']['interest_package_name'];?>
                                    </div>
                                </div>
                                <div class="weui-cell">
                                    <div class="weui-cell__bd weui-cell_primary">
                                        <p>Repayment Type</p>
                                    </div>
                                    <div class="weui-cell__ft">
                                        <?php echo $product['credit_category_info']['sub_product_name'];?>
                                    </div>
                                </div>

                                <?php if( $product['credit_usd'] > 0 ){ ?>
                                    <div class="">
                                        <div class="weui-panel">
                                            <div class="weui-panel__hd">
                                                <i><?php echo 'USD-credit'; ?></i>

                                            </div>
                                            <div class="weui-panel__bd">
                                                <div class="weui-media-box weui-media-box_small_appmsg">
                                                    <div class="weui-cells">
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Credit</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo ncPriceFormat($product['credit_usd']);?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Interest</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['interest_rate'].'%';?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Operation Fee</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['operation_fee'].'%';?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Loan Fee</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['loan_fee'].($product['loan_fee_type']?'':'%');?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Admin Fee</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['admin_fee'].($product['admin_fee_type']?'':'%');?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Annual Fee</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['annual_fee'].($product['annual_fee_type']?'':'%');?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if( $product['credit_khr'] > 0 ){ ?>
                                    <div class="">
                                        <div class="weui-panel">
                                            <div class="weui-panel__hd">
                                                <i><?php echo 'KHR-credit'; ?></i>

                                            </div>
                                            <div class="weui-panel__bd">
                                                <div class="weui-media-box weui-media-box_small_appmsg">
                                                    <div class="weui-cells">
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Credit</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo ncPriceFormat($product['credit_khr']);?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Interest</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['interest_rate_khr'].'%';?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Operation Fee</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['operation_fee_khr'].'%';?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Loan Fee</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['loan_fee_khr'].($product['loan_fee_type']?'':'%');?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Admin Fee</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['admin_fee_khr'].($product['admin_fee_type']?'':'%');?></label>
                                                            </div>
                                                        </div>
                                                        <div class="weui-cell">
                                                            <div class="weui-cell__bd weui-cell_primary">
                                                                <p>Annual Fee</p>
                                                            </div>
                                                            <div class="weui-cell__ft">
                                                                <label><?php echo $product['annual_fee_khr'].($product['annual_fee_type']?'':'%');?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>



                            </div>
                        </div>
                    </div>
                </div>
            <?php } } ?>

            <div>
                <form action="<?php echo getUrl('credit_vote','voteSubmit',array(),false,WAP_OPERATOR_SITE_URL); ?>" method="post" id="frm_vote_credit">

                    <input type="hidden" name="grant_id" value="<?php echo $credit_suggest['uid'] ?>">

                    <!--<div class="weui-cells__title">
                        Vote Result
                    </div>
                    <div class="weui-cells weui-cells_radio">
                        <label class="weui-cell" for="r11">
                            <div class="weui-cell__bd">
                                <p>Agree</p>
                            </div>
                            <div class="weui-cell__ft">
                                <input type="radio" class="weui-check" name="vote_state" value="<?php /*echo commonApproveStateEnum::PASS; */?>" id="r11" checked="checked">
                                <span class="weui-icon-checked"></span>
                            </div>
                        </label>
                        <label class="weui-cell" for="r12">
                            <div class="weui-cell__bd">
                                <p>Reject</p>
                            </div>
                            <div class="weui-cell__ft">
                                <input type="radio" class="weui-check" name="vote_state" value="<?php /*commonApproveStateEnum::REJECT; */?>" id="r12" >
                                <span class="weui-icon-checked"></span>
                            </div>
                        </label>
                    </div>-->

                    <div class="weui-cells weui-cells_form">

                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label for="" class="weui-label">Vote Result</label>
                            </div>
                            <div class="weui-cell__bd">
                                <label><input  type="radio"  name="vote_state" value="<?php echo commonApproveStateEnum::PASS; ?>" checked >Agree</label>
                                <label style="margin-left: 10px"><input  type="radio"  name="vote_state" value="<?php echo commonApproveStateEnum::REJECT; ?>">Reject</label>
                            </div>
                        </div>

                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label for="" class="weui-label">Login Account</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input type="text" class="weui-input" name="account" placeholder="account">
                            </div>
                        </div>

                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label for="" class="weui-label">Password</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input type="password" class="weui-input" name="password" placeholder="password">
                            </div>
                        </div>

                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label for="" class="weui-label">Remark</label>
                            </div>
                            <div class="weui-cell__bd">
                                <textarea class="weui-textarea" name="vote_remark" id="" rows="3" placeholder="input remark"></textarea>
                                <!--<div class="weui-textarea-counter">
                                    <span>0</span>/200
                                </div>-->
                            </div>
                        </div>
                    </div>

                    <div class="weui-btn-area">
                        <a href="javascript:" class="weui-btn weui-btn_primary" id="form_submit_btn">
                            Submit
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>


</div>


<script>
    $('#form_submit_btn').click(function () {
        if($('input[name="vote_state"]:checked').length == 0){
            alert('Please select vote result!');
            return;
        }

        if(!$('input[name="account"]').val()){
            alert('Please input login account!');
            return;
        }

        if(!$('input[name="password"]').val()){
            alert('Please input password!');
            return;
        }

        $('#frm_vote_credit').submit();
    })
</script>