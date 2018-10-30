
<div id="frm_credit_currency">
    <div>
        <ul class="list-group loan-item">
            <?php foreach($output['product_list'] as $prod){?>
                <li class="list-group-item list-category-item list-category-item-<?php echo $prod['uid']?>">
                    <table class="table" data-category-id="<?php echo $prod['category_id'] ?>" style="width: 100%;border-spacing: 0;">
                        <tr style="background-color: #b9d0b7">
                            <td>
                                <span style="font-weight: bold"><?php echo $prod['alias']?></span>
                            </td>
                            <td>
                                <?php echo $prod['sub_product_name']?>
                            </td>
                        </tr>
                        <?php
                        $max_days=0;
                        $max_rate_item=null;
                        foreach($prod['interest_rate_list'] as $rate_item){
                            if($max_days<$rate_item['max_term_days']){
                                $max_rate_item=$rate_item;
                            }
                        }
                        if($max_rate_item){
                            ?>
                            <tr style="background-color: #b9d0b7">
                                <td>
                                    Max Terms:<?php echo $max_rate_item['max_term_days']/30?> Months
                                </td>
                                <td>
                                    <?php echo $max_rate_item['interest_rate']?>% / <?php echo $max_rate_item['interest_rate_mortgage1']?>% / <?php echo $max_rate_item['interest_rate_mortgage2']?>%
                                </td>
                            </tr>
                        <?php }else{?>
                            <tr style="background-color: #b9d0b7">
                                <td colspan="2">
                                    No Setting For Interest
                                </td>
                            </tr>
                        <?php }?>

                        <tr style="background-color: #b9d0b7">
                            <td>
                                Credit:  <label style="font-weight: bold" class="span-credit-ccy-total-<?php echo $prod['uid']?>"></label>
                            </td>
                            <td>
                                <input type="hidden" class="input-credit-ccy-id" name="credit_ccy_id[]" value="<?php echo $prod['uid']?>">
                                <input type="checkbox" checked onchange="chk_multi_ccy_onclick(this)"> Multi Currency
                                <input type="hidden"  name="credit_ccy_total[]" class="input-credit-ccy-total input-credit-ccy-total-<?php echo $prod['uid']?>">
                                <input type="hidden"  name="credit_ccy_chk[]" value="1" class="input-credit-ccy-chk input-credit-ccy-chk-<?php echo $prod['uid']?>">

                            </td>
                        </tr>
                        <tr  class="tr-currency-credit">
                            <td>
                                USD
                                <input type="text" onblur="changeUsdCredit()" name="credit_ccy_usd[]"  class="mui_input input-credit-ccy-usd input-credit-ccy-usd-<?php echo $prod['uid']?>" >
                            </td>
                            <td>
                                KHR
                                <span  class="span-credit-ccy-khr"></span>
                                <input type="hidden" name="credit_ccy_khr[]"  class="mui_input input-credit-ccy-khr  input-credit-ccy-khr-<?php echo $prod['uid']?>" >
                            </td>
                        </tr>
                        <tr  class="tr-shortcut">
                            <td colspan="2">
                                4000 KHR/1 USD
                                <button class="btn btn-default btn-xs" type="button" data-rate="1" onclick="fastSetCcyCredit(this)">0%</button>
                                <button class="btn btn-default btn-xs" type="button" data-rate="0.8" onclick="fastSetCcyCredit(this)">20%</button>
                                <button class="btn btn-default btn-xs" type="button" data-rate="0.5"  onclick="fastSetCcyCredit(this)">50%</button>
                                <button class="btn btn-default btn-xs" type="button" data-rate="0"  onclick="fastSetCcyCredit(this)">100%</button>
                            </td>
                        </tr>
                    </table>
                </li>
            <?php }?>
        </ul>
    </div>
</div>
<script>

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
            var _cid_idx=$(this).data("category-index");
            var _tr_input=$('.tr-category-input[data-category-index="'+_cid_idx+'"]');

            if(_tr_input.find(".count_credit").data("hidden")==0){
                $(".list-category-item-"+_cid.toString()).show();
                var _amt=_tr_input.find(".count_credit").val();
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
    function initCategoryCCY(){
        <?php foreach($last_suggest['suggest_product'] as $suggest_prod){?>
            $(".list-category-item-<?php echo $suggest_prod['member_credit_category_id'] ?>").addClass("list-category-item-active");
            $(".span-credit-ccy-total-<?php echo $suggest_prod['member_credit_category_id'] ?>").text(<?php echo $suggest_prod['credit'] ?>);
            $(".input-credit-ccy-total-<?php echo $suggest_prod['member_credit_category_id'] ?>").val(<?php echo $suggest_prod['credit'] ?>);
            $(".input-credit-ccy-usd-<?php echo $suggest_prod['member_credit_category_id'] ?>").val(<?php echo $suggest_prod['credit_usd']?>);
        <?php }?>
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

    function changeUsdCredit(){
        $(".input-credit-ccy-usd").each(function(){
            var _ccy_total=$(this).closest("table").find(".input-credit-ccy-total").val();
            var _ccy_usd=$(this).val();
            var _ccy_khr=parseInt(_ccy_total)-parseInt(_ccy_usd);
            if(_ccy_khr<0){
                _ccy_khr=0;
            }
            _ccy_khr=_ccy_khr*4000;
            $(this).closest("table").find(".input-credit-ccy-khr").val(_ccy_khr);
            _ccy_khr= formatAmountNoFix(_ccy_khr);
            $(this).closest("table").find(".span-credit-ccy-khr").text(_ccy_khr);
        });
    }
    function fastSetCcyCredit(_e){
        var _ccy_total=$(_e).closest("table").find(".input-credit-ccy-total").val();
        var _rate=$(_e).data("rate");
        var _ccy_usd=parseInt(parseFloat(_ccy_total)*parseFloat(_rate));
        $(_e).closest("table").find(".input-credit-ccy-usd").val(_ccy_usd);
        changeUsdCredit();
    }
    function checkCurrencyCredit(){
        var _max_credit = parseInt($('input[name="max_credit"]').val());
//        var _default_credit=parseInt($('input[name="default_credit"]').val());
        var _total_ccy = 0;
        var _total_ccy_usd=0;
        var _total_ccy_khr=0;

        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-usd").each(function () {
            _total_ccy_usd+=parseInt($(this).val());
        });


        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-khr").each(function () {
            _total_ccy_khr+=parseInt($(this).val());
        });

        $("#frm_credit_currency").find(".list-category-item-active").find(".input-credit-ccy-total").each(function () {
            _total_ccy+=parseInt($(this).val());
        });

        if (_max_credit != _total_ccy) {
            alert("Required: MaxCredit("+_max_credit+") = Total Credit Of Credit-Category("+_total_ccy+")");
            return false;
        }

        var _total_ccy_sum=parseInt(_total_ccy_usd)+parseInt(_total_ccy_khr)/4000;

        if(_total_ccy!=_total_ccy_sum){
            alert("Required: Total Credit = USD_Credit + KHR_Credit");
            return false;
        }

        if (!_max_credit > 0) {
            alert("MaxCredit > 0");
            return false;
        }

        return true;
    }
</script>