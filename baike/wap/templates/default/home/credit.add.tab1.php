<form class="custom-form" id="frm_credit" method="post">
    <input type="hidden" name="request_type" id="request_type" value="<?php echo researchPositionTypeEnum::CREDIT_OFFICER;?>">
    <span style="padding-left: 20px;font-size: 0.7em">
        <?php if($last_submit_suggest['is_system']){?>
            Default Data From System.Analysis
        <?php }else{?>
            Default Data From Last Suggest Time:<?php echo $last_submit_suggest['request_time']?>
        <?php }?>
    </span>
    <div class="cerification-input aui-margin-b-10">
        <div class="loan-form request-credit-form">
            <ul class="aui-list aui-form-list loan-item">
                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label label">
                            Repay Ability(Monthly)
                        </div>
                        <div class="aui-list-item-input">
                            <input type="text" class="mui_input" name="monthly_repayment_ability" id="monthly_repayment_ability" value="<?php echo $last_submit_suggest['monthly_repayment_ability']?:'';?>" />
                        </div>
                    </div>
                </li>
                <li class="aui-list-item liner2">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label label">
                            Max Terms
                        </div>
                        <div class="aui-list-item-input">
                            <input type="text" class="mui_input" name="credit_terms" id="credit_terms" value="<?php echo $last_submit_suggest['credit_terms']?:$suggest_profile['default_terms'];?>" /> <span class="u">(Months)</span>
                        </div>
                    </div>
                </li>
                <li class="aui-list-item liner2">
                    <div class="item">
                        <div class="aui-list-item-inner">
                            <div class="aui-list-item-label label" style="font-weight: bold">
                                Default Credit(No Mortgage)
                            </div>
                            <div class="aui-list-item-input">
                                <input type="text" class="mui_input credit-amount-item count_credit" data-hidden="0" name="default_credit" id="default_credit" value="<?php echo $last_submit_suggest['default_credit']?:'';?>" onblur="creditAmtItemChanged(this)" />
                            </div>
                        </div>
                        <div class="aui-list-item-inner" style="padding-left: 20px">
                            <div class="aui-list-item-label label">
                                For Credit Category
                            </div>
                            <div class="aui-list-item-input label-on">
                                <div class="mui_select_block">
                                    <select class="form-contorl input-h30 select-category" id="default_credit_category_id" name="default_credit_category_id">
                                        <?php foreach($output['product_list'] as $prod){?>
                                            <option value="<?php echo $prod['uid']?>" <?php if($last_submit_suggest['default_credit_category_id']==$prod['uid']) echo 'selected'?>><?php echo $prod['alias']?></option>
                                        <?php }?>
                                    </select>
                                    <i class="aui-iconfont aui-icon-down" style="top: 0.6rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>


                <?php if(count($member_assets) > 0){ ?>
                    <li class="aui-list-item border-b-none">
                        <div class="aui-list-item-inner">
                            <div class="aui-list-item-label label label-all" style="flex: 0 0 12rem;">
                                Increase Credit By Mortgage Asset
                            </div>
                        </div>
                    </li>
                    <li class="aui-list-item">
                        <div class="assets-list" id="assetsList">
                            <?php if ($member_assets) { $last_suggest_detail_list = $last_submit_suggest['suggest_detail_list'];?>
                                <?php foreach($member_assets as $val) {
                                        if($last_suggest_detail_list[$val['uid']]){
                                            $asset_credit=$last_suggest_detail_list[$val['uid']]['credit'];
                                        }else{
                                            $asset_credit=$val['credit'];
                                        }
                                    ?>
                                    <div class="item">
                                        <label>
                                            <input type="checkbox" data-uid="<?php echo $val['uid']?>" class="chk-increase" onchange="chk_increase_onchange(this)"
                                                <?php if($last_suggest_detail_list[$val['uid']]['credit']>0) echo 'checked'?>>
                                            <input type="hidden" class="chk-increase-value" name="chk_increase[]" value="<?php if($last_suggest_detail_list[$val['uid']]['credit']>0){ echo '1';}else{ echo '0';}?>">
                                            <span class="name" style="width: 4rem;font-weight: bold">
                                                <?php echo $val['asset_name']?>
                                                (<?php echo $certification_type[$val['asset_type']]; ?>)
                                            </span>
                                        </label>
                                        <span class="amount">
                                            <input type="text" data-hidden="0" class="mui_input inline-input suggest-item-asset credit-amount-item count_credit" onblur="creditAmtItemChanged(this)"
                                           name="increase_credit[]"  data-asset-id="<?php echo $val['uid'];?>"
                                           value="<?php echo $asset_credit;?>" style="<?php if(!$last_suggest_detail_list[$val['uid']]['credit']) echo 'display:none'?>"/>
                                             <input type="hidden" name="asset_id[]" value="<?php echo $val['uid'];?>">
                                        </span>
                                        <div class="aui-list-item-inner tr-credit-category-<?php echo $val['uid']?>" style="padding-left: 20px;<?php if(!$last_suggest_detail_list[$val['uid']]['credit']) echo 'display:none'?>">
                                            <div class="aui-list-item-label label">
                                                For Credit Category
                                            </div>
                                            <div class="aui-list-item-input label-on">
                                                <div class="mui_select_block">
                                                    <select class="form-contorl input-h30 credit_category select-category" name="member_credit_category_id">
                                                        <?php foreach($output['product_list'] as $prod){?>
                                                            <option value="<?php echo $prod['uid']?>" <?php if($last_suggest_detail_list[$val['uid']]['member_credit_category_id']==$prod['uid']) echo 'selected'?>><?php echo $prod['alias']?></option>
                                                        <?php }?>
                                                    </select>
                                                    <i class="aui-iconfont aui-icon-down" style="top: 0.6rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }?>
                            <?php } else { ?>
                                <div class="item">
                                    <span class="name">
                                        NO RECORD
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    </li>
                <?php } ?>

                <li class="aui-list-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label label" style="font-weight: bold">
                            Max Credit
                        </div>
                        <div class="aui-list-item-input">
                            <input type="text" class="mui_input" name="max_credit" id="max_credit" value="<?php echo $last_submit_suggest['max_credit']?:'';?>" />
                        </div>
                    </div>
                </li>
                <?php $credit = memberClass::getCreditBalance($_GET['id']) ?>
                <?php if($credit){
                    //处理有没有历史credit的问题
                    if($credit['balance']>0){?>
                        <li class="aui-list-item">
                            <div class="aui-list-item-inner">
                                <div class="aui-list-item-label label">
                                    Grant Type
                                </div>
                                <div class="aui-list-item-input">
                                    <div class="radio" style="width: 50%;float: left">
                                        <label>
                                            <input type="radio" name="is_append" id="rbn_append0" value="0"
                                                <?php if(!isset($last_submit_suggest['is_append']) || !$last_submit_suggest['is_append']) echo 'checked'?>>
                                            Replace
                                        </label>
                                    </div>
                                    <div class="radio" style="width: 50%;float: right">
                                        <label>
                                            <input type="radio" name="is_append" id="rbn_append1" value="1"
                                                <?php if(isset($last_submit_suggest['is_append']) && $last_submit_suggest['is_append']) echo 'checked'?>>
                                            Append
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php }}?>

                <li class="aui-list-item last-item">
                    <div class="aui-list-item-inner">
                        <div class="aui-list-item-label label label-all">
                            Remark
                        </div>
                    </div>
                </li>
                <li class="aui-list-item">
                    <div class="aui-list-item-inner paddingright075">
                        <textarea class="mui_textarea" name="remark" id="remark"><?php echo $last_submit_suggest['remark']?:'';?></textarea>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div style="padding: 0 .8rem 1rem .8rem;">
        <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" onclick="next_step_onclick()" id="submit"> NEXT </div>
    </div>
    <div id="blankDiv" style="width:100%;height: 350px;display:none;"></div>
</form>
<?php include_once(template("home/credit.add.tab1.currency"))?>
<script>
//防止键盘把当前输入框给挡住
$(function () {
    //键盘弹起时为键盘高度，未弹起时为0
    var iHeight = window.innerHeight, halfH = iHeight/2;
    $('input[type="text"],textarea').on('focus', function () {
        var iTop = $(this).offset().top;
        if(iTop < halfH)  return;
        $('#blankDiv').show();
        window.scrollTo(0,iTop-100)
    });
    $('input[type="text"],textarea').on('blur', function () {
        var iTop = $(this).offset().top;
        if(iTop < halfH)  return;
        $('#blankDiv').hide();
    });
    $(".select-category").on("change",function(){
        refreshCategoryCCY();
    })
    refreshCategoryCCY();
});
//防止键盘把当前输入框给挡住
$('input[type="text"],textarea').on('click', function () {
  var target = this;
  setTimeout(function(){
        target.scrollIntoViewIfNeeded(true);
   },100);
});

//部分安卓机型适用。
if(/Android [4-6]/.test(navigator.appVersion)) {
    window.addEventListener("resize", function() {
        if(document.activeElement.tagName=="INPUT" || document.activeElement.tagName=="TEXTAREA") {
            window.setTimeout(function() {
                document.activeElement.scrollIntoViewIfNeeded(true);
            },0);
        }
    })
}

function chk_increase_onchange(_e){
    var _uid=$(_e).data("uid");
    var _sts=$(_e).is(':checked') ? 1 : 0;
    if(_sts==1){
        $(_e).closest(".item").find(".count_credit").show();
        $(_e).closest("label").find(".chk-increase-value").val(1);
        $(_e).closest("div.item").find(".credit-amount-item").data("hidden",0);
        $(".tr-credit-category-"+_uid).show();
    }else{
        $(_e).closest(".item").find(".count_credit").hide();
        $(_e).closest("label").find(".chk-increase-value").val(0);
        $(_e).closest("div.item").find(".credit-amount-item").data("hidden",1);
        $(".tr-credit-category-"+_uid).hide();
    }
    refreshCategoryCCY();
}
function refreshCategoryCCY(){
    $(".list-category-item").hide();
    $(".list-category-item").removeClass("list-category-item-active");
    var _ccy_credit={};
    $(".select-category").each(function(){
       var _cid=$(this).val();
        $(".span-credit-ccy-total-"+_cid).text(0);
        $(".input-credit-ccy-usd-"+_cid).val(0);
        $(".input-credit-ccy-khr-"+_cid).val(0);



        if(!_ccy_credit[_cid]){
            _ccy_credit[_cid]=0;
        }
        //获取对应的credit
        if($(this).closest("div.item").find(".credit-amount-item").data("hidden")==0){
            $(".list-category-item-"+_cid.toString()).show();
            var _amt=$(this).closest("div.item").find(".credit-amount-item").val();
            _ccy_credit[_cid]=parseInt(_ccy_credit[_cid])+parseInt(_amt);
        }
    });
    for(var _i in _ccy_credit){
        $(".list-category-item-"+_i.toString()).addClass("list-category-item-active");
        $(".span-credit-ccy-total-"+_i).text(_ccy_credit[_i]);
        $(".input-credit-ccy-total-"+_i).val(_ccy_credit[_i]);
        $(".input-credit-ccy-usd-"+_i).val(_ccy_credit[_i]);
    }
    changeUsdCredit();

}
function creditAmtItemChanged(_e){
    var _new_v=$(_e).val();
    var _old_v=$(_e).attr('value');
    if(_new_v!=_old_v){
        refreshCategoryCCY();
        $(_e).attr("value",_new_v);
    }
}
    function chk_multi_ccy_onclick(_e){
        var _sts=$(_e).is(':checked') ? 1 : 0;
        if(_sts==1){
            $(_e).closest("table").find(".tr-currency-credit").show();
            $(_e).closest("table").find(".tr-shortcut").show();
            $(_e).closest("table").find(".input-credit-ccy-chk").val(1);
        }else{
            $(_e).closest("table").find(".tr-currency-credit").hide();
            $(_e).closest("table").find(".tr-shortcut").hide();
            $(_e).closest("table").find(".input-credit-ccy-chk").val(0);
        }
    }
</script>