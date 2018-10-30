<style>
    .record-base-table>tbody>tr>td{
        padding: 2px;
    }
    .image-item{
        position: relative;
    }
    .image-item img{
        position: relative;
    }
    .image-item .a-delete{
        right: -10px;top: -10px;position: absolute;width: 20px;height: 20px;border-radius: 60%;background-color: red;color: #ffffff
    }
    .image-item .a-delete .fa-close{
        top:3px;position: absolute;left: 6px
    }


   #notCheckPassword{
        width: 20px;
        position: absolute;
        top: 6px;
        right: 18px;
    }

    #checkPasswordFailure{
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 18px;
    }

   #checkPasswordDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 18px;
    }

</style>

<div role="tabpanel" class="tab-pane active" id="tab_authorize">
    <div class="authorize-form clearfix">
        <?php if(!$detail['uid'] || ($detail['contract'] && !count($detail['assets']))){?>
            <h4>Nothing to Authorize,Please Check Contract-List;</h4>
        <?php }else{?>
            <form  action="<?php //echo getUrl('member_credit', 'submitClientAuthorize', array(), false, ENTRY_COUNTER_SITE_URL) ?>" method="post" id="authorizeForm">
                <input type="hidden" name="form_submit" value="ok">
                <input type="hidden" name="grant_id" id="grant_id" value="<?php echo $detail['uid'];?>">
                <input type="hidden" name="member_id" id="member_id" value="<?php echo $detail['member_id'];?>">
                <input type="hidden" name="member_image" id="member_image" value="<?php echo $client_info['member_scene_image']?>">
                <input type="hidden" name="mortgage_list" id="mortgage_list" value="">
                <input type="hidden" name="contract_images" id="contract_images" value="">
                <input type="hidden" id="exchange_rate" value="<?php echo $output['exchange_rate'];?>">
                <div class="col-sm-12 form-group">
                    <label class="col-sm-2 control-label">Scene Photo</label>
                    <div class="col-sm-4">
                        <div class="snapshot_div">
                            <img id="img_slave" src="<?php echo getImageUrl($client_info['member_scene_image'],imageThumbVersion::MAX_240)?>">
                        </div>

                    </div>
                </div>
                <?php if(count($detail['assets']) > 0 || count($detail['is_assets']) > 0){ ?>
                    <div class="col-sm-12 form-group">
                        <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Mortgage List</label>
                        <div class="col-sm-10">
                            <?php foreach ($detail['assets'] as $k => $v) { ?>
                                <div class="asset-info-wrap clearfix">
                                    <div class="col-sm-3 form-group left">
                                        <label class="col-sm-12 control-label mortgage-label">
                                            <div class="ck_wrap" style="padding: 5px">
                                                <p>
                                                    <?php echo $asset_enum[$v['asset_type']];?>:<?php echo $v['asset_name'];?>
                                                </p>
                                                <p>
                                                    <?php echo $v['asset_cert_type']?>:<?php echo $v['asset_sn'];?>
                                                </p>
                                                <?php if($v['relative_id']>0){?>
                                                    <p>From Relative: <?php echo $v['relative_name']?></p>
                                                <?php }?>
                                                <p>Credit: <span class=""><?php echo ncPriceFormat($v['credit']);?></span></p>
                                                <input type="checkbox" checked disabled onclick="//after_mortgage_changed(this)" class="chk-asset-mortgage" name="chk_mortgage" uid="<?php echo $v['member_asset_id'];?>" val="<?php echo $v['credit'];?>" />
                                                <span class="c-asset-state"><?php echo 'Mortgage';?></span>
                                            </div>
                                            <div class="mortgage-type" style="display: block;padding-left: 20px">
                                                <input type="checkbox" class="chk-asset-received">Received
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-sm-9 form-group right">
                                        <?php
                                            $image_list=$detail['asset_image'][$v['member_asset_id']];
                                            $viewer_width = 360;
                                            include(template(":widget/item.image.viewer.list"));
                                        ?>
                                        <div class="image-list asset-image-list-<?php echo $v['uid'];?> clearfix">
                                            <div class="image-item snapshot_div"
                                                 onclick="callWin_snapshot_asset('asset-image-list-<?php echo $v['uid'];?>');"  >
                                                <img class="asset-img" src="resource/img/member/photo.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-sm-6 form-group">
                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Activate Credit</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="amount" name="total_credit" value="<?php echo $detail['contract'] ? 0 : $detail['credit'];?>" readonly>
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="col-sm-6 form-group" style="height: 45px">
                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Total Fee</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" style="font-size: 15px;font-weight: bold;" class="form-control" id="loan_fee" name="loan_fee" value="0" readonly>
                            <span class="input-group-addon" style="min-width: 40px;border-left: 0;height: 30px!important;border-radius: 0">USD</span>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                </div>

                <div class="col-sm-6 form-group" style="height: 45px">
                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Loan Fee</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" class="form-control" id="loan_fee_amount" name="loan_fee_amount" value="0" readonly>
                            <span class="input-group-addon" style="min-width: 40px;border-left: 0;height: 30px!important;border-radius: 0">USD</span>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                </div>

                <div class="col-sm-6 form-group" style="height: 45px">
                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Admin Fee</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" class="form-control" id="admin_fee_amount" name="admin_fee_amount" value="0" readonly>
                            <span class="input-group-addon" style="min-width: 40px;border-left: 0;height: 30px!important;border-radius: 0">USD</span>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                </div>
<!--                <div class="col-sm-6 form-group">-->
<!--                    <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Contract State</label>-->
<!--                    <div class="col-sm-6">-->
<!--                        <input type="checkbox"  name="is_draft" value="1">Draft-->
<!--                    </div>-->
<!--                </div>-->
                <div class="col-sm-6 form-group">
                    <label class="col-sm-4 control-label" style="padding-right: 0px"><span class="required-options-xing">*</span>Receive Money From</label>
                    <div class="col-sm-8">
                        <input type="radio" name="fee_from" value="<?php echo repaymentWayEnum::CASH; ?>" checked onclick="//changetype(this)"><label class="control-label"><?php echo 'Cash';?></label>
                        <br />
                        <input type="radio" name="fee_from" value="<?php echo repaymentWayEnum::PASSBOOK; ?>"  onclick="//changetype(this)"> <label class="control-label"><?php echo 'Balance';?></label><br/>

                    </div>
                    <p id="tip" style="color: red;margin-left: 115px">If you choose cash method, <br/> the cash will be paid at here</p>
                </div>
                <div class="col-sm-6 form-group" id="choose_currency" style="padding-right: 0px;display: block;margin-bottom: 50px">
                    <div class="col-sm-4" style="font-weight: 700;padding-right: 0px">first currency</div>
                    <div class="col-sm-8" style="padding-left: 10px">
                        <span>
                            <input type="number" name='usd_amount' value="" class="col-sm-8" style="height: 30px;border: 1px solid #ccc;" currency="<?php echo currencyEnum::USD;?>" onchange="calcCurrencyAmount(this)" >
                             <span class="input-group-addon col-sm-4" style="min-width: 50px;border-left: 0;height: 30px;border-radius: 0px;"><?php echo currencyEnum::USD; ?></span>
                        </span>
                    </div>
                    <div class="col-sm-4" style="font-weight: 700;padding-left:0px;padding-right: 0px;margin-top: 10px">second currency</div>
                    <div class="col-sm-8" style="margin-top: 8px;padding-left: 10px">
                        <span>
                            <input type="number" name='khr_amount' value="" class="col-sm-8" style="height: 30px;border: 1px solid #ccc;" currency="<?php echo currencyEnum::KHR;?>" onchange="calcCurrencyAmount(this)" >
                             <span class="input-group-addon col-sm-4" style="min-width: 50px;border-left: 0;height: 30px;border-radius: 0px;"><?php echo currencyEnum::KHR; ?></span>
                        </span>
                    </div>
                </div>
                <div class="col-sm-6 form-group">
                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Contract Image</label>
                    <div class="col-sm-8">
                        <div class="image-list contract-image-list clearfix" id="div_contract_images">
                            <div class="image-item snapshot_div" onclick="callWin_snapshot_contract();" style="width: 100%">
                                <img src="resource/img/member/photo.png">
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="<?php echo currencyEnum::USD . '_min_value'; ?>" value="<?php echo currencyMinValueEnum::USD;?>">
                <input type="hidden" id="<?php echo currencyEnum::KHR . '_min_value'; ?>" value="<?php echo 1; //currencyMinValueEnum::KHR;?>">
                <?php foreach ($output['exchange_list'] as $key => $exchange) { ?>
                    <input type="hidden" id="<?php echo $key; ?>" value="<?php echo $exchange;?>">
                <?php } ?>
                <input type="hidden" id="KHR_total" value="0">
                <input type="hidden" id="USD_total" value="0">

                <div class="col-sm-6 form-group">
                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Cashier Password</label>
                    <div class="col-sm-8" style="padding-right: 10px">
                        <input type="password" class="form-control" name="cashier_trading_password" id="trading_password" value="" >
                        <div class="error_msg"></div>
                    </div>
                </div>

                <!--<div class="col-sm-6 form-group" id="div_member_password" style="display: none;">
                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Client Password</label>
                    <div class="col-sm-8" style="position: relative;padding-right: 10px">
                        <a class="form-control authorize_input btn btn-default" onclick="clientPassword()">Client Verify
                            <img id="notCheckPassword" src="resource/img/member/verify-1.png">
                            <img id="checkPasswordDone" src="resource/img/member/verify-2.png">
                            <img id="checkPasswordFailure" src="resource/img/member/verify-3.png">
                        </a>
                        <input type="hidden" name="member_trading_password" value="">
                    </div>
                </div>-->

                <div class="col-sm-12 form-group">
                    <div class="operation">
                        <a class="btn btn-primary" onclick="submitAuthorize();">Submit</a>
                    </div>
                </div>
            </form>
        <?php }?>

    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>
<script>

    function changetype(_e) {
        $("#choose_currency").hide();
        $('#div_member_password').hide();
        $('#tip').hide();
        var type = $(_e).val();
        if(type == <?php echo repaymentWayEnum::CASH ?> ){
            $("#choose_currency").show();
            $('#tip').show();
        }else if(type == <?php echo repaymentWayEnum::PASSBOOK ?> ){
            $("#choose_currency").hide();
            $('#tip').hide();
            $('#div_member_password').show();

        }
    }

    var m_max_credit = "<?php echo $detail['max_credit']?$detail['max_credit']:0;?>";
    var m_default_credit = "<?php echo $detail['default_credit']?$detail['default_credit']:0;?>";

    var m_first_contract = "<?php echo $detail['contract']?1:0;?>";
    var m_rate = '<?php echo json_encode(loanSettingClass::getLoanFeeByGrantId($detail['uid']));?>';
    m_rate = eval('(' + m_rate + ')');

    // ！！！！因为是用KHR买入USD，只能用KHR->USD的汇率反向计算USD需要多少KHR
    // 因为USD汇率大于KHR，用USD的整数来运算，避免小数位数失真
    var USD_KHR_EX_RATE = <?php echo round(1/$output['khr_usd_rate'],2); ?>;
    var KHR_USD_EX_RATE = 1/USD_KHR_EX_RATE;
    console.log(USD_KHR_EX_RATE);
    console.log(KHR_USD_EX_RATE);


    //console.log(m_rate);
    var _min_loan_fee = 0;

    $(window).ready(function () {
        calculateLoanFee();
    });
    function after_mortgage_changed(e) {
        var _type_wrap = $(e).parents('.mortgage-label').find('.mortgage-type');
        $(e).prop('checked') ? _type_wrap.show() : _type_wrap.hide();
        calculateLoanFee();
    }
    function remove_img_item(e) {
        $(e).closest(".image-item").remove();
    }
    function callWin_snapshot_asset(el) {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("0");
                if (_img_path != "" && _img_path != null) {
                    var item = '<div class="image-item"><img class="img" src="' + getUPyunImgUrl(_img_path, '180x120') + '"><input type="hidden" class="asset-img-url" value="' + _img_path + '"><a class="a-delete" onclick="remove_img_item(this)"><i class="fa fa-close"></i></a></div>';
                    $('.' + el).prepend(item);
                } else {
                    alert("Failed to get image path");
                }
            } catch (ex) {
                alert(ex.Message);

            }
        }
    }
    function callWin_snapshot_contract() {
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("0");
                if (_img_path != "" && _img_path != null) {
                    var item = '<div class="image-item snapshot_div"><img class="img" src="' + getUPyunImgUrl(_img_path, '180x120') + '"><input type="hidden" class="contract-img-url" value="' + _img_path + '"><a class="a-delete" onclick="remove_img_item(this)"><i class="fa fa-close"></i></a></div>';
                    $('#div_contract_images').prepend(item);
                } else {
                    alert("Failed to get image path");
                }
            } catch (ex) {
                alert(ex.Message);

            }
        }
    }

    function calculateLoanFee(){
        var _fee = 0;
        var _loan_fee_amount = 0;
        var _admin_fee_amount = 0;

        // 判断要计算的信用值
        var _amount = 0;
        // 先计算抵押物的信用值
        $("#authorizeForm").find(".chk-asset-mortgage").each(function () {
            if ($(this).prop('checked')) {
                _amount = FloatAdd(_amount, $(this).attr("val"));
            }
        });

        if( m_first_contract == "0" ){
            // 第一次,需要加上default的
            _amount = FloatAdd(_amount,m_default_credit);
        }

        _amount = (Number(_amount) > Number(m_max_credit)) ? m_max_credit : _amount;

        var _loan_fee_rate = m_rate.loan_fee;
        var _loan_fee_rate_type = m_rate.loan_fee_type;
        var _admin_fee_rate = m_rate.admin_fee;
        var _admin_fee_rate_type = m_rate.admin_fee_type;

        if( _loan_fee_rate_type == 1 ){
            _loan_fee_amount = _loan_fee_rate;
        }else{
            _loan_fee_amount = FloatMul(_amount,_loan_fee_rate/100);
        }

        if( _admin_fee_rate_type == 1 ){
            _admin_fee_amount = _admin_fee_rate;
        }else{
            _admin_fee_amount = FloatMul(_amount,_admin_fee_rate/100);
        }

        _fee =  FloatAdd(_loan_fee_amount,_admin_fee_amount);

        $('#loan_fee_amount').val(formatAmount(_loan_fee_amount));
        $('#admin_fee_amount').val(_admin_fee_amount);
        $("#loan_fee").val(_fee);
        $("#amount").val(_amount);
        $('select[name="currency"]').val(0);
        $("#USD_total").val(_fee);
    }

    function calculateLoanFee_old() {
        var _fee = 0;
        var _amount = 0;
        var _rate_type = m_rate.follow_sign_contract_rate_type;
        var _rate_value = m_rate.follow_sign_contract_rate_value;
        var _min_rate_value = m_rate.min_follow_sign_contract_fee;
        if (m_first_contract == "0") {
            _amount = m_default_credit;
            _rate_type = m_rate.first_sign_contract_rate_type;
            _rate_value = m_rate.first_sign_contract_rate_value;
            _min_rate_value = m_rate.min_first_sign_contract_fee;
        }
        $("#authorizeForm").find(".chk-asset-mortgage").each(function () {
            if ($(this).prop('checked')) {
                _amount = FloatAdd(_amount, $(this).attr("val"));
            }
        });

        _amount = (Number(_amount) > Number(m_max_credit)) ? m_max_credit : _amount;
        if (_rate_type == 1) {
            _fee = _rate_value;
        } else {
            _fee = FloatMul(_amount, _rate_value / 100);
        }
        _fee = _fee < _min_rate_value ? _min_rate_value : _fee;
        $('#loan_fee_amount').val(_fee);
        $('#admin_fee_amount').val(0);
        $("#loan_fee").val(_fee);
        $("#amount").val(_amount);
        $('select[name="currency"]').val(0);
        $("#USD_total").val(_fee);

    }

    var _currency_min_value_USD = Number('<?php echo currencyMinValueEnum::USD;?>');
    //var _currency_min_value_KHR = Number('<?php echo currencyMinValueEnum::KHR;?>');
    var _currency_min_value_KHR = Number('<?php echo 1;?>');  // 不要用100了，会在账户多出钱


    function selectCurrency(_e) {
        var _currency = $(_e).val();
        var _loan_fee = Number($("#loan_fee").val());
        if (_currency == 'USD') {
            $('input[name="loan_fee"]').val(_loan_fee);
            _min_loan_fee = _loan_fee;
            $('input[name="loan_fee"]').attr('disabled', false);
        } else if (_currency == 'KHR') {
            var _exchange_rate = Number($('#exchange_rate').val());
            _loan_fee = _loan_fee / _exchange_rate;

            _loan_fee = Math.ceil(_loan_fee / _currency_min_value_KHR) * _currency_min_value_KHR;
            _loan_fee = Number(_loan_fee).toFixed(fractionalDigit(_currency_min_value_KHR));
            $('input[name="loan_fee"]').val(_loan_fee);
            _min_loan_fee = _loan_fee;
            $('input[name="loan_fee"]').attr('disabled', false);
        } else {
            $('input[name="loan_fee"]').val('');
            $('input[name="loan_fee"]').attr('disabled', true);
        }
    }

    $('input[name="loan_fee"]').change(function () {
        var _currency = $('select[name="currency"]').val();
        var _loan_fee = Number($(this).val());
        if (_currency == 'USD') {
            _loan_fee = Math.ceil(_loan_fee / _currency_min_value_USD) * _currency_min_value_USD;
            _loan_fee = Number(_loan_fee).toFixed(fractionalDigit(_currency_min_value_USD));
            $('input[name="loan_fee"]').val(_loan_fee);
        } else if (_currency == 'KHR') {
            _loan_fee = Math.ceil(_loan_fee / _currency_min_value_KHR) * _currency_min_value_KHR;
            _loan_fee = Number(_loan_fee).toFixed(fractionalDigit(_currency_min_value_KHR));
            $('input[name="loan_fee"]').val(_loan_fee);
        }
    });

    function fractionalDigit(_number) {//几位小数
        if (Math.floor(_number) === _number) return 0;
        var x = String(_number).indexOf('.') + 1;
        var y = String(_number).length - x;
        return y;
    }

    function calculateAssetsList() {
        var _assets = [];
        $("#authorizeForm").find(".chk-asset-mortgage").each(function () {
            if ($(this).prop('checked')) {
                var _member_asset_id = $(this).attr('uid');
                var _mortgage_received = $(this).closest('.mortgage-label').find('.chk-asset-received').first().prop("checked");
                _mortgage_received = _mortgage_received ? 1 : 0;
                var _imgs_arr = [];
                $(this).closest('.asset-info-wrap').find(".image-list").find('.asset-img-url').each(function () {
                    _imgs_arr.push($(this).val());
                });
                var _item = {};
                _item.member_asset_id = _member_asset_id;
                _item.is_received = _mortgage_received;
                _item.asset_images = _imgs_arr;
                _assets.push(_item);
            }
        });
        return _assets;
    }

    function submitAuthorize() {
        //处理资产
        var _assets = calculateAssetsList();
        if (m_first_contract == "1") {
            if (_assets.length == 0) {
                alert('Please select mortgage');
                return false;
            }
        }

        var _currency = $('select[name="currency"]').val();
        if (_currency == 0) {
            alert('Please select currency.');
            return false;
        }

        var _loan_fee = $('input[name="loan_fee"]').val();
        if (Number(_loan_fee) < _min_loan_fee) {
            alert('The input amount less than the loan fee.');
            return false;
        }

        _assets = encodeURI(JSON.stringify(_assets));
        $('#mortgage_list').val(_assets);
        //处理合同图片
        var _contract_img_list = [];
        $("#div_contract_images").find(".image-item").find(".contract-img-url").each(function () {
            _contract_img_list.push($(this).val());
        });
        _contract_img_list = _contract_img_list.join(",");
        // 没有合同图片要限制提交,但一旦高拍仪出问题也是问题啊，先不限制了
        if( !_contract_img_list ){
            alert('Please take photo of contract!');
            return false;
        }
        $("#contract_images").val(_contract_img_list);

        /*var trading_password = $('#trading_password').val();
        if (!trading_password) {
            alert('Please input trading password');
            return false;
        }*/
        //$('#authorizeForm').waiting();
        //$('#authorizeForm').submit();
        // 使用ajax
        var params = $('#authorizeForm').getValues();
        $('.container').waiting();
        yo.loadData({
            _c: "member_credit",
            _m: "ajaxConfirmSignAuthoriseContract",
            param: params,
            callback: function (_o) {
                $('.container').unmask();
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function (){
                        window.location.reload();
                    },2000)

                } else {
                    alert(_o.MSG);
                }
            }
        });

    }

    //浮点数加法运算
    function FloatAdd(arg1, arg2) {
        var r1, r2, m;
        try {
            r1 = arg1.toString().split(".")[1].length
        } catch (e) {
            r1 = 0
        }
        try {
            r2 = arg2.toString().split(".")[1].length
        } catch (e) {
            r2 = 0
        }
        m = Math.pow(10, Math.max(r1, r2));
        return (arg1 * m + arg2 * m) / m;
    }

    //浮点数乘法运算
    function FloatMul(arg1, arg2) {
        var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
        try {
            m += s1.split(".")[1].length
        } catch (e) {
        }
        try {
            m += s2.split(".")[1].length
        } catch (e) {
        }
        return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
    }

    function calcCurrencyAmount(_e) {
        var _amount = $(_e).val();
        var _currency = $(_e).attr('currency');
        var _currency_min_value = Number($('#' + _currency + '_min_value').val());
        _amount = Math.ceil(_amount / _currency_min_value) * _currency_min_value;
        _amount = Number(_amount).toFixed(fractionalDigit(_currency_min_value));
        console.log(_amount);
        $(_e).val(_amount);

        var _total_loan_fee = Number($('#loan_fee').val());

        // 计算各币种的分配 暂时只有USD和KHR
        if( _currency == '<?php echo currencyEnum::USD; ?>' ){

            var _other_currency = '<?php echo currencyEnum::KHR; ?>';
            var _other_currency_min_value = Number($('#' + _other_currency + '_min_value').val());

            // 当前币种是USD
            if( _amount >= _total_loan_fee ){
                $('input[name="khr_amount"]').val(0);
                return true;
            }
            // 不足额
            var _left_usd_amount = _total_loan_fee-_amount;
            // 需要多少KHR
            var _usd_khr_rate = USD_KHR_EX_RATE;
            var _need_khr_amount = _left_usd_amount*_usd_khr_rate;
            // 换算成最小额度
            _need_khr_amount = Math.ceil(_need_khr_amount / _other_currency_min_value) * _other_currency_min_value;
            $('input[name="khr_amount"]').val(_need_khr_amount);
            return true;

        }else{

            // 收的是KHR
            var _other_currency = '<?php echo currencyEnum::USD; ?>';
            var _other_currency_min_value = Number($('#' + _other_currency + '_min_value').val());

            // 折算成USD
            var _ex_rate = KHR_USD_EX_RATE;
            var _ex_usd_amount = _amount/_ex_rate;
            if( _ex_usd_amount >= _total_loan_fee ){
                $('input[name="usd_amount"]').val(0);
                return true;
            }

            // 剩余多少美金
            var _left_usd_amount = _total_loan_fee-_ex_usd_amount;
            // 换算成最低单位
            _left_usd_amount = Math.ceil(_left_usd_amount / _other_currency_min_value) * _other_currency_min_value;
            $('input[name="usd_amount"]').val(_left_usd_amount);
            return true;


        }


    }


    function calcCurrencyAmount_old(_e) {
        var _amount = $(_e).val();
        var _currency = $(_e).attr('currency');
        var _currency_min_value = Number($('#' + _currency + '_min_value').val());
        _amount = Math.ceil(_amount / _currency_min_value) * _currency_min_value;
        _amount = Number(_amount).toFixed(fractionalDigit(_currency_min_value));
        $(_e).val(_amount);

        if (_currency == '<?php echo currencyEnum::USD?>') {
            var _other_currency = '<?php echo currencyEnum::KHR?>';
        } else {
            var _other_currency = '<?php echo currencyEnum::USD?>';
        }
        var _other_currency_min_value = Number($('#' + _other_currency + '_min_value').val());
        var _exchange = Number($('#' + _currency + '_' + _other_currency).val());
        var _other_exchange = Number($('#' + _other_currency + '_' + _currency).val());

        var _usd_total = Number($('#USD_total').val());
        var _khr_total = Number($('#KHR_total').val());
        if (_currency == '<?php echo currencyEnum::USD?>') {
            if (_amount > _usd_total) {
                var _usd_more = _amount - _usd_total;
                var _usd_to_khr_more = _usd_more * _exchange;
                _khr_total -= _usd_to_khr_more;
            } else {
                var _usd_lack = _usd_total - _amount;
                var _usd_to_khr_lack = _usd_lack * _other_exchange;
                _khr_total += _usd_to_khr_lack;
            }
            if (_khr_total > 0) {
                _khr_total = Math.ceil(_khr_total / _other_currency_min_value) * _other_currency_min_value;
                _khr_total = Number(_khr_total).toFixed(fractionalDigit(_other_currency_min_value));
                $('input[name="khr_amount"]').val(_khr_total);
            } else {
                $('input[name="khr_amount"]').val(0);
            }
        } else {
            if (_amount > _khr_total) {
                var _khr_more = _amount - _khr_total;
                var _khr_to_usd_more = _khr_more / _exchange;
                _usd_total -= _khr_to_usd_more;
            } else {
                var _khr_lack = _khr_total - _amount;
                var _khr_to_usd_lack = _khr_lack / _other_exchange;
                _usd_total += _khr_to_usd_lack;
            }

            if (_usd_total > 0) {
                _usd_total = Math.ceil(_usd_total / _other_currency_min_value) * _other_currency_min_value;
                _usd_total = Number(_usd_total).toFixed(fractionalDigit(_other_currency_min_value));
                $('input[name="usd_amount"]').val(_usd_total);
            } else {
                $('input[name="usd_amount"]').val(0);
            }
        }
    }

    function fractionalDigit(_number) {//几位小数
        if (Math.floor(_number) === _number) return 0;
        var x = String(_number).indexOf('.') + 1;
        var y = String(_number).length - x;
        return y;
    }

    function clientPassword() {
        var client_password = window.external.inputPasswordWithKeyInfo('');
        $("input[name='member_trading_password']").val(client_password);
        if($("input[name='member_trading_password']").val()){
            $('#notCheckPassword').hide();
            $('#checkPasswordFailure').hide();
            $('#checkPasswordDone').show();
        }else{
            $('#notCheckPassword').hide();
            $('#checkPasswordFailure').show();
        }
    }

</script>
