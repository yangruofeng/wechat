<form id="frm_credit">
    <div class="weui-cells__title">
        <?php foreach($suggest_profile['tip'] as $tip){?>
            <p><?php echo $tip?></p>
        <?php }?>
    </div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <label class="weui-label">Repay Ability</label>
            </div>
            <div class="weui-cell__bd">
                <input class="weui-input"  type="number" pattern="[0-9]*" cursor-spacing='20' name="monthly_repayment_ability" id="txt_monthly_repayment_ability" value="<?php echo $last_submit_suggest['monthly_repayment_ability']?:'';?>" />
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd">
                <label class="weui-label">Max Terms</label>
            </div>
            <div class="weui-cell__bd">
                <input  type="number" pattern="[0-9]*"  class="weui-input" cursor-spacing='20' name="credit_terms" id="txt_credit_terms" value="<?php echo $last_submit_suggest['credit_terms']?:$suggest_profile['default_terms'];?>" />
            </div>
        </div>
        <div class="cate-item">
            <div class="weui-cell">
                <div class="weui-cell__hd">
                    <label class="weui-label">Default Credit</label>
                </div>
                <div class="weui-cell__bd">
                    <input  type="number" pattern="[0-9]*" class="weui-input credit-amount-item count_credit" cursor-spacing='20' data-hidden="0" name="default_credit" id="txt_default_credit" value="<?php echo $last_submit_suggest['default_credit']?:'';?>"/>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd">
                    <label class="weui-label"  style="font-style: italic;color: gray;font-size: 0.7rem;">For Category</label>
                </div>
                <div class="weui-cell__bd" onclick="showPickerOfCategory(this)" data-picker-id="picker_default_id" data-cate-id="<?php echo $last_submit_suggest['default_credit_category_id']?>">
                    <label>
                        <?php echo $output['product_list'][$last_submit_suggest['default_credit_category_id']]['alias']?:'No Setting';?>
                    </label>
                    <input type="hidden" class="credit-category" id="txt_default_credit_category_id" value="<?php echo $last_submit_suggest['default_credit_category_id'];?>">
                </div>
                <div class="weui-cell__ft">
                    <i class="weui-icon__down" style="font-size:1.2rem"></i>
                </div>
            </div>

        </div>



    </div>

    <?php if(count($member_assets) > 0){ ?>
        <div class="weui-cells__title">
            Increase Credit By Mortgage Asset
        </div>
        <div class="weui-cells weui-cells_checkbox">
            <?php if ($member_assets) { $last_suggest_detail_list = $last_submit_suggest['suggest_detail_list'];?>
                <?php foreach($member_assets as $val) {
                    if($last_suggest_detail_list[$val['uid']]){
                        $asset_credit=$last_suggest_detail_list[$val['uid']]['credit'];
                    }else{
                        $asset_credit=$val['credit'];
                    }
                    ?>
                    <div class="cate-item" style="border-bottom: dotted 1px #a39191">
                        <label class="weui-cell weui-check__label" onchange="onIncreaseCreditChangeState(this)"  data-uid="<?php echo $val['uid']?>"  for="chk_asset_<?php echo $val['uid']?>">
                            <div class="weui-cell__hd">
                                <input type="checkbox" class="weui-check" id="chk_asset_<?php echo $val['uid']?>" <?php if($last_suggest_detail_list[$val['uid']]['credit']>0) echo 'checked="checked"'?>>
                                <i class="weui-icon-checked"></i>
                            </div>
                            <div class="weui-cell__bd">
                                <p><?php echo $val['asset_name']?> <span style="padding-left: 20px"><?php echo $val['asset_sn']?></span></p>
                                <p><?php echo $certification_type[$val['asset_type']]; ?></p>

                            </div>
                            <div class="weui-cell__ft">
                                <p>
                                    <?php echo ncPriceFormat($val['credit'],0)?>
                                </p>
                                <p>
                                    <?php echo $val['asset_cert_type']?>
                                </p>
                            </div>
                        </label>
                        <div class="weui-cell  tr-credit-category-<?php echo $val['uid']?>" style="<?php if(!$last_suggest_detail_list[$val['uid']]['credit']) echo 'display:none'?>">
                            <?php
                            $for_cate_id=$last_suggest_detail_list[$val['uid']]['member_credit_category_id']?:current($output['product_list'])['uid'];
                            $for_cate_text=$output['product_list'][$for_cate_id]['alias'];
                            $for_amt=$last_suggest_detail_list[$val['uid']]['credit']?:$val['credit'];
                            ?>
                            <div class="weui-cell__hd">
                                <label class="weui-label"  style="font-style: italic;color: gray;font-size: 0.7rem;">
                                    Category Credit
                                </label>
                            </div>
                            <div class="weui-cell__bd">
                                <input  type="number" pattern="[0-9]*" cursor-spacing='20' class="weui-input inline-input suggest-item-asset credit-amount-item count_credit"
                                       name="increase_credit[]"  data-asset-id="<?php echo $val['uid'];?>" data-hidden="<?php if($last_suggest_detail_list[$val['uid']]['credit']){ echo '0';}else{ echo '1';}?>"
                                       value="<?php echo $for_amt;?>"/>
                                <input type="hidden" name="asset_id[]" value="<?php echo $val['uid'];?>">
                            </div>
                            <div class="weui-cell__ft" onclick="showPickerOfCategory(this)" data-picker-id="picker_increase_<?php echo $val['uid']?>" data-cate-id="<?php echo $last_submit_suggest['default_credit_category_id']?>">
                                <label>
                                    <?php echo $for_cate_text;?>
                                </label>
                                <i class="weui-icon__down" style="font-size:1.2rem"></i>
                                <input type="hidden" class="credit-category select-category"  value="<?php echo $for_cate_id;?>">
                            </div>
                        </div>
                    </div>


                <?php }?>
            <?php } else { ?>
                <div class="weui-cells__title">
                    NO RECORD
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php
    $member_collateral=$suggest_profile['collateral'];
    ?>
    <?php if(count($member_collateral) > 0){ ?>
        <div class="weui-cells__title">
            Collateral Certificates
        </div>
        <div class="weui-cells weui-cells_checkbox">
            <?php if ($member_collateral) { $last_suggest_detail_list = $last_submit_suggest['suggest_detail_list'];?>
                <?php foreach($member_collateral as $val) {
                    if($last_suggest_detail_list[$val['uid']]){
                        $asset_credit=$last_suggest_detail_list[$val['uid']]['credit'];
                    }else{
                        $asset_credit=$val['credit'];
                    }
                    ?>
                    <div class="cate-item" style="border-bottom: dotted 1px #a39191">
                        <label class="weui-cell weui-check__label collateral-item" onchange="onCollateralChangeState(this)"  data-uid="<?php echo $val['uid']?>"  for="chk_collateral_<?php echo $val['uid']?>">
                            <div class="weui-cell__hd">
                                <input type="checkbox" class="weui-check" id="chk_collateral_<?php echo $val['uid']?>" <?php if($last_suggest_detail_list[$val['uid']]) echo 'checked="checked"'?>>
                                <i class="weui-icon-checked"></i>
                            </div>
                            <div class="weui-cell__bd">
                                <p><?php echo $val['asset_name']?> <span style="padding-left: 20px"><?php echo $val['asset_sn']?></span></p>
                            </div>
                            <div class="weui-cell__ft">
                                <p><?php echo $certification_type[$val['asset_type']]; ?></p>

                            </div>
                        </label>
                    </div>
                <?php }?>
            <?php } else { ?>
                <div class="weui-cells__title">
                    NO RECORD
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="weui-cells weui-cells_form">
       <div class="weui-cell">
           <div class="weui-cell__hd">
               <label class="weui-label">
                   Max Credit
               </label>
           </div>
           <div class="weui-cell__bd">
               <input  type="number" pattern="[0-9]*" cursor-spacing='20'  class="weui-input" name="max_credit" id="txt_max_credit" value="<?php echo $last_submit_suggest['max_credit']?:'';?>" />
           </div>
       </div>
    </div>
    <?php $credit=memberClass::getCreditBalance($_GET['id']) ?>
    <?php if($credit['balance']>0){?>
        <div class="weui-cells__title">
            Credit-Type, Old Balnce is <?php echo $credit['balance']?>
        </div>
        <div class="weui-cells weui-cells_radio">
            <label class="weui-cell weui-check__label" onchange="onCreditTypeChange(this)" for="rbn_append0">
                <div class="weui-cell__bd">
                    <p>Append (Add Credit)</p>
                </div>
                <div class="weui-cell__ft">
                    <input type="radio" class="weui-check" name="is_append" value="1" id="rbn_append0">
                    <span class="weui-icon-checked"></span>
                </div>
            </label>
            <label class="weui-cell weui-check__label" onchange="onCreditTypeChange(this)" for="rbn_append1">
                <div class="weui-cell__bd">
                    <p>Replace (Set New Credit)</p>
                </div>
                <div class="weui-cell__ft">
                    <input type="radio" name="is_append" value="0" class="weui-check" id="rbn_append1" checked="checked">
                    <span class="weui-icon-checked"></span>
                </div>
            </label>
        </div>
    <?php }?>


    <div class="page__bd_spacing" style="padding-top: 10px;padding-bottom: 30px">
        <a class="weui-btn weui-btn_primary" onclick="btn_next_onclick()" href="javascript:">
            Next
        </a>
    </div>


</form>
<div id="div_step2">

</div>
<script>

    //处理选择category的数组
    var _arr_category=[];
    var _arr_category_dict={};
    <?php foreach($output['product_list'] as $prod){?>
        _arr_category.push({label:"<?php echo $prod['alias']?>",value:"<?php echo $prod['uid']?>"});
        _arr_category_dict[<?php echo $prod['uid']?>]="<?php echo $prod['alias']?>";
    <?php }?>
    function showPickerOfCategory(_e){
        var _default_cate_id=$(_e).data("cate-id");
        var _picker_id=$(_e).data("picker-id");
        weui.picker(_arr_category,{
            defaultValue:[_default_cate_id],
            onConfirm:function(_ret){
                if(_ret.length==0) return;
                var _select_cate_id=_ret[0];
                var _select_cate_text=_arr_category_dict[_select_cate_id];
                $(_e).find("label").text(_select_cate_text);
                $(_e).data("cate-id",_select_cate_id);
                $(_e).find(".credit-category").val(_select_cate_id);
            },
            id:_picker_id
    });
    }
    function onIncreaseCreditChangeState(_e){
        var _uid=$(_e).data("uid");
        var _sts=$(_e).closest("div.cate-item").find(".weui-check").is(':checked')?1:0;
        if(_sts==1){
            $(_e).closest("div.cate-item").find(".weui-check").attr("checked","checked");
            $(_e).closest("div.cate-item").find(".credit-amount-item").data("hidden",0);
            $(".tr-credit-category-"+_uid).show();
        }else{
            $(_e).closest("div.cate-item").find(".weui-check").removeAttr("checked");
            $(_e).closest("div.cate-item").find(".credit-amount-item").data("hidden",1);
            $(".tr-credit-category-"+_uid).hide();
        }
    }
    function onCollateralChangeState(_e){
        var _uid=$(_e).data("uid");
        var _sts=$(_e).closest("div.cate-item").find(".weui-check").is(':checked')?1:0;
        if(_sts==1){
            $(_e).closest("div.cate-item").find(".weui-check").attr("checked","checked");
        }else{
            $(_e).closest("div.cate-item").find(".weui-check").removeAttr("checked");
        }
    }
    function onCreditTypeChange(_e){
        $('input[name="is_append"]').removeAttr("checked");
        $(_e).find(".weui-check").attr("checked","checked");
    }
    function btn_next_onclick(){

        var _ccy_credit={};
        $("#frm_credit").find(".credit-category").each(function(){
            var _cid=$(this).val();
            if(!_ccy_credit[_cid]){
                _ccy_credit[_cid]=0;
            }
            //获取对应的credit
            if($(this).closest("div.cate-item").find(".credit-amount-item").data("hidden")==0){
                var _amt=$(this).closest("div.cate-item").find(".credit-amount-item").val();
                _ccy_credit[_cid]=parseInt(_ccy_credit[_cid])+parseInt(_amt);
            }
        });

        var _collateral_list=[];
        $("#frm_credit").find(".collateral-item").each(function(){
            var _uid=$(this).data("uid");
            var _sts=$(this).closest("div.cate-item").find(".weui-check").is(':checked')?1:0;
            if(_sts==1){
                _collateral_list.push(_uid);
            }
        });

        var _args={};
        _args.cate_list=_ccy_credit;
        _args.repay_ability=$("#txt_monthly_repayment_ability").val();
        _args.credit_terms=$("#txt_credit_terms").val();
        _args.max_credit=$("#txt_max_credit").val();
        _args.default_credit=$("#txt_default_credit").val();
        _args.request_type = $.trim($('#request_type').val());
        _args.member_id = '<?php echo $_GET['id'];?>';
        _args.officer_id = '<?php echo cookie('member_id');?>';
        _args.token = '<?php echo cookie('token');?>';
        _args.is_append=$('input[name="is_append"]:checked').val();
        _args.default_credit_category_id = $.trim($('#txt_default_credit_category_id').val());
        _args.collateral_list=_collateral_list;


        showMask();
        yo.loadData({
            _c:"suggest_credit",
            _m:"ajaxMatchFeeAndInterest",
            param:_args,
            callback:function(_o){
                if(_o.STS){
                   // refreshCategoryCCY();
                    $("#frm_credit").hide();
                    //格式化form-creidt-currency的数据
                    var _ret=_o.DATA;
                    console.log(_ret);
                    yo.dynamicTpl({
                       tpl:"suggest_credit/credit.tab1.currency",
                        ext:{data:_ret},
                        callback:function(_tpl){
                            hideMask();
                            $("#div_step2").html(_tpl);
                            $("#div_step2").show();
                        }
                    });

                }else{
                    hideMask();
                    alert(_o.MSG);
                }
            }
        });

    }


</script>
<script>
    function back_credit_form_onclick(){
        $("#frm_credit_currency").hide();
        $("#frm_credit").show();
    }
    function changeUsdCredit(){
        $(".input-credit-ccy-usd").each(function(){
            var _ccy_total=$(this).closest(".currency-cate-item").find(".input-credit-ccy-total").val();
            var _ccy_usd=$(this).val();
            var _ccy_khr=parseInt(_ccy_total)-parseInt(_ccy_usd);
            if(_ccy_khr<0){
                _ccy_khr=0;
            }
            _ccy_khr=_ccy_khr*4000;
            $(this).closest(".currency-cate-item").find(".input-credit-ccy-khr").val(_ccy_khr);
            _ccy_khr= formatAmountNoFix(_ccy_khr);
            //console.log(_ccy_khr);
            $(this).closest(".currency-cate-item").find(".span-credit-ccy-khr").text(_ccy_khr);
        });
    }
    function fastSetCcyCredit(_e){
        var _ccy_total=$(_e).closest(".currency-cate-item").find(".input-credit-ccy-total").val();

        var _rate=$(_e).data("rate");
        var _ccy_usd=parseInt(parseFloat(_ccy_total)*parseFloat(_rate));

        $(_e).closest(".currency-cate-item").find(".input-credit-ccy-usd").val(_ccy_usd);
        changeUsdCredit();
    }
    function btn_submit_onclick(){
        var _ccy_credit={};
        $("#frm_credit").find(".credit-category").each(function(){
            var _cid=$(this).val();
            if(!_ccy_credit[_cid]){
                _ccy_credit[_cid]=0;
            }
            //获取对应的credit
            if($(this).closest("div.cate-item").find(".credit-amount-item").data("hidden")==0){
                var _amt=$(this).closest("div.cate-item").find(".credit-amount-item").val();
                _ccy_credit[_cid]=parseInt(_ccy_credit[_cid])+parseInt(_amt);
            }
        });
        var _args={};
        _args.cate_list=_ccy_credit;
        _args.repay_ability=$("#txt_monthly_repayment_ability").val();
        _args.credit_terms=$("#txt_credit_terms").val();
        _args.max_credit=$("#txt_max_credit").val();
        _args.default_credit=$("#txt_default_credit").val();
        _args.default_credit_category_id = $.trim($('#txt_default_credit_category_id').val());
        _args.member_id = '<?php echo $_GET['id'];?>';
        _args.officer_id = '<?php echo cookie('member_id');?>';
        _args.token = '<?php echo cookie('token');?>';
        _args.is_append=$('input[name="is_append"]:checked').val();

        //取资产信用
        var _arr_assets = [];
        $("#frm_credit").find(".suggest-item-asset").each(function () {
            if($(this).data("hidden")==0){
                _arr_assets.push({
                    "asset_id": $(this).data('asset-id'),
                    "credit": $(this).val() ? parseInt($(this).val()) : 0,
                    "credit_category_id":$(this).closest(".cate-item").find(".select-category").val()
                });
            }
        });
       // var _arr_assets_json = encodeURI(JSON.stringify(_arr_assets));


        var _arr_ccy = [];
        $("#frm_credit_currency").find(".currency-cate-item").each(function () {
            _arr_ccy.push({
                "credit_category_id":$(this).find(".input-credit-ccy-id").val(),
                "credit":$(this).find(".input-credit-ccy-total").val(),
                "credit_usd":$(this).find(".input-credit-ccy-usd").val(),
                "credit_khr":$(this).find(".input-credit-ccy-khr").val(),
                "interest_rate":$(this).find(".input-credit-ccy-interest").val(),
                "interest_rate_khr":$(this).find(".input-credit-ccy-interest-khr").val(),
                "operation_fee":$(this).find(".input-credit-ccy-operation-fee").val(),
                "operation_fee_khr":$(this).find(".input-credit-ccy-operation-fee-khr").val(),
                "loan_fee":$(this).find(".input-credit-ccy-loan-fee").val(),
                "loan_fee_khr":$(this).find(".input-credit-ccy-loan-fee-khr").val(),
                "loan_fee_type":$(this).find(".input-credit-ccy-loan-fee-type").val(),
                "admin_fee":$(this).find(".input-credit-ccy-admin-fee").val(),
                "admin_fee_khr":$(this).find(".input-credit-ccy-admin-fee-khr").val(),
                "admin_fee_type":$(this).find(".input-credit-ccy-admin-fee-type").val(),
                "annual_fee":$(this).find(".input-credit-ccy-annual-fee").val(),
                "annual_fee_khr":$(this).find(".input-credit-ccy-annual-fee-khr").val(),
                "annual_fee_type":$(this).find(".input-credit-ccy-annual-fee-type").val()
            });
        });

        var _collateral_list=[];
        $("#frm_credit").find(".collateral-item").each(function(){
            var _uid=$(this).data("uid");
            var _sts=$(this).closest("div.cate-item").find(".weui-check").is(':checked')?1:0;
            if(_sts==1){
                _collateral_list.push(_uid);
            }
        });
        _args.collateral_list=_collateral_list;

      //  var _arr_ccy_json = encodeURI(JSON.stringify(_arr_ccy));

        _args.asset_list=_arr_assets;
        _args.currency_list=_arr_ccy;

        showMask();
        yo.loadData({
           _c:"suggest_credit",
            _m:"ajaxSubmitSuggestCredit",
            param:_args,
            callback:function(_o){
                hideMask();
                if(_o.STS){
                    alert("Saved Successfully");
                    setTimeout(function(){
                        history.back(-1);
                    },1000);
                }else{
                    alert(_o.MSG);
                }
            }
        });
    }
    function changeServiceFeeListVisable(_e){
        var _uid=$(_e).data("uid");
        var _div_list=$(".cate-interest-list-"+_uid);
        var _sts=_div_list.data("sts");
        if(_sts==0){
            _div_list.data("sts",1);
            _div_list.show();
        }else{
            _div_list.data("sts",0);
            _div_list.hide();
        }
    }
</script>