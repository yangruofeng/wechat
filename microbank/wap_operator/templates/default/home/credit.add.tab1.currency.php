
<form class="custom-form" id="frm_credit_currency" style="display: none">
    <input type="hidden" name="request_type" id="request_type" value="<?php echo researchPositionTypeEnum::CREDIT_OFFICER;?>">
    <span style="padding-left: 20px;font-size: 0.7em">
        Set Currency Credit
    </span>
    <div class="cerification-input aui-margin-b-10">
        <div class="loan-form request-credit-form">
            <ul class="aui-list aui-form-list loan-item">
                <?php foreach($output['product_list'] as $prod){?>
                    <li class="aui-list-item list-category-item list-category-item-<?php echo $prod['uid']?>">
                        <table class="aui-table" data-category-id="<?php echo $prod['category_id'] ?>" style="width: 100%;border-spacing: 0;">
                            <tr style="background-color: #b9d0b7">
                                <td>
                                    <div class="label">
                                        <span style="font-weight: bold"><?php echo $prod['alias']?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="label">
                                        <?php echo $prod['sub_product_name']?>
                                    </div>
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
                                        <div class="label">
                                            Max Terms:<?php echo $max_rate_item['max_term_days']/30?> Months
                                        </div>
                                    </td>
                                    <td>
                                        <div class="label">
                                            <?php echo $max_rate_item['interest_rate']?>% / <?php echo $max_rate_item['interest_rate_mortgage1']?>% / <?php echo $max_rate_item['interest_rate_mortgage2']?>%
                                        </div>
                                    </td>
                                </tr>
                            <?php }else{?>
                                <tr style="background-color: #b9d0b7">
                                    <td colspan="2">
                                        <div class="label">No Setting For Interest</div>
                                    </td>
                                </tr>
                            <?php }?>

                            <tr style="background-color: #b9d0b7">
                                <td>
                                    <div class="label">
                                        Credit: </span> <label style="font-weight: bold" class="span-credit-ccy-total-<?php echo $prod['uid']?>"></label>
                                    </div>
                                </td>
                                <td class="label">
                                    <input type="hidden" class="input-credit-ccy-id" name="input_credit_ccy_id[]" value="<?php echo $prod['uid']?>">
                                    <input type="checkbox" checked onchange="chk_multi_ccy_onclick(this)"> Multi Currency
                                    <input type="hidden"  name="input_credit_ccy_total[]" class="input-credit-ccy-total input-credit-ccy-total-<?php echo $prod['uid']?>">
                                    <input type="hidden"  name="input_credit_ccy_chk[]" value="1" class="input-credit-ccy-chk input-credit-ccy-chk-<?php echo $prod['uid']?>">

                                </td>
                            </tr>
                            <tr  class="tr-currency-credit">
                                <td>
                                    <div class="label">
                                        USD
                                        <input type="text" onblur="changeUsdCredit()" name="input_credit_ccy_usd[]"  class="mui_input input-credit-ccy-usd input-credit-ccy-usd-<?php echo $prod['uid']?>" >
                                    </div>
                                </td>
                                <td>
                                    <div class="label">
                                        KHR
                                        <span  class="span-credit-ccy-khr"></span>
                                        <input type="hidden" name="input_credit_ccy_khr[]"  class="mui_input input-credit-ccy-khr  input-credit-ccy-khr-<?php echo $prod['uid']?>" >
                                    </div>
                                </td>
                            </tr>
                            <tr  class="tr-shortcut">
                                <td colspan="2" class="label">
                                    4000 KHR/1 USD
                                    <button type="button" data-rate="1" onclick="fastSetCcyCredit(this)">0%</button>
                                    <button type="button" data-rate="0.8" onclick="fastSetCcyCredit(this)">20%</button>
                                    <button type="button" data-rate="0.5"  onclick="fastSetCcyCredit(this)">50%</button>
                                    <button type="button" data-rate="0"  onclick="fastSetCcyCredit(this)">100%</button>
                                </td>
                            </tr>
                        </table>
                    </li>
                <?php }?>
            </ul>
        </div>
    </div>
    <div style="padding: 0 .8rem 1rem .8rem;">
        <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" onclick="submit()"> Submit </div>
        <div class="aui-btn aui-btn-danger aui-btn-block custom-btn  aui-margin-t-15" style="background-color: #ccd9de!important;color: #000000!important" onclick="back_credit_form_onclick()"> BACK </div>
    </div>

    <div id="blankDiv" style="width:100%;height: 350px;display:none;"></div>
</form>
<script>
    function back_credit_form_onclick(){
        $("#frm_credit_currency").hide();
        $("#frm_credit").show();
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
</script>