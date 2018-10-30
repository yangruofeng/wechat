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
                <div class="container" style="padding: 20px">
                    <div class="row">
                        <div class="panel panel-default" style="border: solid 1px #d9d9d9">
                            <div class="panel-heading">
                                <h5 class="panel-title">Mortgage & Collateral</h5>
                            </div>
                            <table class="table table-bordered">
                                <?php if(count($detail['assets']) > 0 || count($detail['is_assets']) > 0){ ?>
                                    <?php foreach ($detail['assets'] as $k => $v) { ?>
                                        <tr>
                                            <td rowspan="2" style="width: 40%">
                                                <dl class="dl-horizontal">
                                                    <dt>Certificate Type</dt>
                                                    <dd><?php echo $asset_enum[$v['asset_type']];?></dd>
                                                    <dt>Name</dt>
                                                    <dd><?php echo $v['asset_name'];?></dd>
                                                    <dt>Certificate ID</dt>
                                                    <dd><?php echo $v['asset_sn'];?></dd>
                                                    <dt>Soft/Hard</dt>
                                                    <dd> <?php echo $asset_enum[$v['asset_type']];?> </dd>
                                                </dl>

                                            </td>
                                            <td rowspan="2" style="width: 15%">
                                                <h4>
                                                    <?php if($v['credit']>0){?>
                                                        <span class="label label-primary">Mortgage</span>
                                                    <?php }else{?>
                                                        <span class="label label-success">Collateral</span>
                                                    <?php }?>
                                                </h4>
                                                <p class="chk-asset-item" data-asset-id="<?php echo $v['member_asset_id']?>" data-uid="<?php echo $v['uid']?>">
                                                    <input type="checkbox" class="chk-asset-received"> Received
                                                </p>

                                            </td>
                                            <td>
                                                <?php
                                                $image_list=$detail['asset_image'][$v['member_asset_id']];
                                                $viewer_width = 360;
                                                include(template(":widget/item.image.viewer.list"));
                                                ?>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="image-list asset-image-list-<?php echo $v['uid'];?> clearfix">
                                                    <div class="image-item snapshot_div"
                                                         onclick="callWin_snapshot_asset('asset-image-list-<?php echo $v['uid'];?>');"  >
                                                        <img class="asset-img" src="resource/img/member/photo.png">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php }else{?>
                                    <tr>
                                        <td colspan="10"><?php include(template(":widget/no_record"))?></td>
                                    </tr>
                                <?php }?>

                            </table>

                        </div>

                    </div>

                    <div class="row">
                        <table class="table table-bordered">
                            <tr>
                                <td style="width: 200px;background-color: #f5f5f5">* Scene Photo</td>
                                <td style="background-color: #f5f5f5">* Contract Photo</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="snapshot_div">
                                        <img id="img_slave" onclick="callWin_snapshot_slave()" src="<?php echo getImageUrl($client_info['member_scene_image'],imageThumbVersion::MAX_240)?:'resource/img/member/photo.png'?>">
                                    </div>
                                </td>
                                <td>
                                    <div class="image-list contract-image-list clearfix" id="div_contract_images">
                                        <div class="image-item snapshot_div" onclick="callWin_snapshot_contract();">
                                            <img src="resource/img/member/photo.png">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="row">
                        <div class="panel panel-default" style="border: solid 1px #d9d9d9">
                            <div class="panel-heading">
                                <h5 class="panel-title">Receivable Fee</h5>
                            </div>
                            <table class="table table-bordered">
                                <tr>
                                    <td style="border: solid 1px #d9d9d9">Credit Category</td>
                                    <td  style="border: solid 1px #d9d9d9">Is One Time</td>
                                    <td  style="border: solid 1px #d9d9d9">Credit</td>
                                    <td style="border: solid 1px #d9d9d9">Loan Fee</td>
                                    <td style="border: solid 1px #d9d9d9">Admin Fee</td>
                                    <td style="border: solid 1px #d9d9d9">Annual Fee</td>
                                    <td style="border: solid 1px #d9d9d9">Sub-Total</td>
                                </tr>
                                <?php $has_one_time_category=false; foreach($output['credit_currency'] as $ccy_item){?>
                                    <?php
                                    $cate_item=$output['credit_category'][$ccy_item['member_credit_category_id']];
                                    if($cate_item['is_one_time']){
                                        $has_one_time_category=true;//判断是否存在one-time的category，有的话显示one-click
                                    }
                                    ?>
                                    <?php if($ccy_item['credit_usd']>0){?>
                                        <tr>
                                            <td><?php echo $cate_item['alias']?></td>
                                            <td>
                                                <?php if( $cate_item['is_one_time']) { ?>
                                                    <span><i class="fa fa-check" style="color:red;"></i></span>
                                                    <a target="_blank" href="<?php echo getUrl('print_form','printPreviewInstallmentSchemeByGrantProductUid',array(
                                                            'grant_product_uid' => $ccy_item['uid'],
                                                            'currency' => currencyEnum::USD
                                                    ),false,ENTRY_COUNTER_SITE_URL); ?>" class="btn btn-info btn-xs" style="margin-left: 10px;">
                                                        Preview Contract
                                                    </a>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo ncPriceFormat($ccy_item['credit_usd'],0)?> USD</td>
                                            <td><?php echo $ccy_item['desc_loan_fee']?></td>
                                            <td><?php echo $ccy_item['desc_admin_fee']?></td>
                                            <td><?php echo $ccy_item['desc_annual_fee']?></td>
                                            <td> <?php echo $ccy_item['sub_total']?></td>
                                        </tr>
                                    <?php }?>
                                    <?php if($ccy_item['credit_khr']>0){?>
                                        <tr>
                                            <td><?php echo $cate_item['alias']?></td>
                                            <td>
                                                <?php if( $cate_item['is_one_time']) { ?>
                                                    <span><i class="fa fa-check" style="color:red;"></i></span>
                                                    <a target="_blank" href="<?php echo getUrl('print_form','printPreviewInstallmentSchemeByGrantProductUid',array(
                                                        'grant_product_uid' => $ccy_item['uid'],
                                                        'currency' => currencyEnum::KHR
                                                    ),false,ENTRY_COUNTER_SITE_URL); ?>" class="btn btn-info btn-xs" style="margin-left: 10px;">
                                                        Preview Contract
                                                    </a>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo ncPriceFormat($ccy_item['credit_khr'],0)?> KHR</td>
                                            <td><?php echo $ccy_item['desc_loan_fee_khr']?></td>
                                            <td><?php echo $ccy_item['desc_admin_fee_khr']?></td>
                                            <td><?php echo $ccy_item['desc_annual_fee_khr']?></td>
                                            <td> <?php echo ncPriceFormat($ccy_item['sub_total_khr']*4000,0)?></td>
                                        </tr>
                                    <?php }?>


                                <?php }?>
                                <tr>
                                    <td colspan="5" class="text-right">
                                        <h5>Total Fee</h5>
                                    </td>
                                    <td>
                                        <input type="hidden" id="loan_fee" value="<?php echo $output['total_fee']?>">
                                        <input type="hidden" id="total_fee_usd" value="<?php echo $output['total_fee_usd']; ?>">
                                        <input type="hidden" id="total_fee_khr" value="<?php echo $output['total_fee_khr']; ?>">

                                        <?php if($output['total_fee_usd']>0){?>
                                            <h3>USD <?php echo $output['total_fee_usd']?></h3>
                                        <?php }?>
                                        <?php if($output['total_fee_khr']>0){?>
                                            <h3>KHR <?php echo ncPriceFormat($output['total_fee_khr'],0)?></h3>
                                        <?php }?>
                                        <?php if($output['total_fee']==0){?>
                                            <h3>USD 0</h3>
                                        <?php }?>



                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="10" style="background-color: #ffff77;padding-left: 100px">
                                        #. If you choose cash method, the cash will be paid at here
                                        <br/>
                                        <?php if($output['is_only_super_loan']){?>
                                            #. Only support receive cash for <kbd>super-loan</kbd>
                                        <?php }?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">
                                        <div>
                                            <p>
                                                <input type="radio" name="fee_from" value="<?php echo repaymentWayEnum::CASH; ?>" checked onclick="changetype(this)">
                                                <label class="control-label" style="width: 150px;text-align: left"><?php echo 'Cash';?></label>
                                            </p>
                                            <p>
                                                <input type="radio" name="fee_from" value="<?php echo repaymentWayEnum::PASSBOOK; ?>"  onclick="changetype(this)">
                                                <label class="control-label"  style="width: 150px;text-align: left"><?php echo 'Balance';?></label>
                                            </p>
                                        </div>
                                    </td>
                                    <td colspan="10">
                                        <div class="col-sm-8 form-group" id="choose_currency" style="padding-right: 0px;display: block;margin-bottom: 0">
                                            <div class="col-sm-4" style="font-weight: 700;padding-right: 0px">first currency</div>
                                            <div class="col-sm-8" style="padding-left: 10px">
                                                <span>
                                                    <input type="number" name='usd_amount' value="" class="col-sm-8" style="height: 30px;border: 1px solid #ccc;" currency="<?php echo currencyEnum::USD;?>" onchange="calcCurrencyAmount(this)" >                            <span class="input-group-addon col-sm-4" style="min-width: 50px;border-left: 0;height: 30px;border-radius: 0px;"><?php echo currencyEnum::USD; ?></span>
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
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <div class="col-sm-12 form-group">
                                            <label class="col-sm-6 control-label"><span class="required-options-xing">*</span>Cashier Password</label>
                                            <div class="col-sm-6" style="padding-right: 10px">
                                                <input type="password" class="form-control" name="cashier_trading_password" id="trading_password" value="" >
                                                <div class="error_msg"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <!--display: table-cell;-->
                                    <td colspan="10"  style="/*display: none;*/">
                                        <?php if($has_one_time_category){?>
                                            <div >
                                                <input type="checkbox" id="chk_one_click" name="is_auto_disburse_one_time" value="1" onclick="chk_change_one_click(this);"> One Click To Disburse For One Time Loan
                                            </div>
                                        <?php }?>
                                    </td>
                                </tr>
                                <!--display: table-row;-->
                                <tr id="tr_one_click" style="display: none;">
                                    <td colspan="10">
                                        <?php include(template("member_credit_v2/client.credit.part.authorize.oneclick"));?>
                                    </td>

                                </tr>

                            </table>

                        </div>
                    </div>

                </div>

                <input type="hidden" name="is_have_one_time_category" value="<?php echo $has_one_time_category?1:0; ?>">

                <input type="hidden" id="<?php echo currencyEnum::USD . '_min_value'; ?>" value="<?php echo currencyMinValueEnum::USD;?>">
                <input type="hidden" id="<?php echo currencyEnum::KHR . '_min_value'; ?>" value="<?php echo 1; //currencyMinValueEnum::KHR;?>">
                <?php foreach ($output['exchange_list'] as $key => $exchange) { ?>
                    <input type="hidden" id="<?php echo $key; ?>" value="<?php echo $exchange;?>">
                <?php } ?>
                <input type="hidden" id="KHR_total" value="0">
                <input type="hidden" id="USD_total" value="0">


                <div class="col-sm-12 form-group">
                    <div class="operation">
                        <a class="btn btn-primary" id="btn_submit_form" onclick="submitAuthorize();">Submit</a>
                    </div>
                </div>
            </form>
        <?php }?>

    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>
<script>


    function chk_change_one_click(ele){
        var _el = $(ele);
        var _is_checked = _el[0].checked;
        var _tr_ele = $('#tr_one_click');
        if( _is_checked ){
            _tr_ele.show();
            $('#btn_submit_form').text('Next');
        }else{
            _tr_ele.hide();
            $('#btn_submit_form').text('Submit');
        }
    }



    function callWin_snapshot_slave() {

        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    $("#img_slave").attr("src", getUPyunImgUrl(_img_path, "180x120"));
                    $('#member_image').val(_img_path);
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }
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
           // $('#div_member_password').show();

        }
    }

    var MEMBER_ID = '<?php echo $detail['member_id']; ?>';

    var m_max_credit = "<?php echo $detail['max_credit']?$detail['max_credit']:0;?>";
    var m_default_credit = "<?php echo $detail['default_credit']?$detail['default_credit']:0;?>";

    var m_first_contract = "<?php echo $detail['contract']?1:0;?>";
    //var m_rate = '<?php echo json_encode(loanSettingClass::getLoanFeeByGrantId($detail['uid']));?>';
    //m_rate = eval('(' + m_rate + ')');
    //console.log(m_rate);

    // ！！！！因为是用KHR买入USD，只能用KHR->USD的汇率反向计算USD需要多少KHR
    // 因为USD汇率大于KHR，用USD的整数来运算，避免小数位数失真
    var USD_KHR_EX_RATE = parseFloat('<?php echo $output['usd_khr_rate']; ?>');
    var KHR_USD_EX_RATE = parseFloat('<?php echo $output['khr_usd_rate']; ?>');

    console.log(USD_KHR_EX_RATE);
    console.log(KHR_USD_EX_RATE);



    var _min_loan_fee = 0;

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

    var _currency_min_value_USD = Number('<?php echo currencyMinValueEnum::USD;?>');
    var _currency_min_value_KHR = Number('<?php echo 1;?>');  // 不要用100了，会在账户多出钱

    function fractionalDigit(_number) {//几位小数
        if (Math.floor(_number) === _number) return 0;
        var x = String(_number).indexOf('.') + 1;
        var y = String(_number).length - x;
        return y;
    }

    function calculateAssetsList() {
        var _assets = [];
        $("#authorizeForm").find(".chk-asset-item").each(function () {
            var _member_asset_id = $(this).data("asset-id");
            var _uid=$(this).data("uid");

            var _mortgage_received = $(this).find('.chk-asset-received').first().prop("checked");
            _mortgage_received = _mortgage_received ? 1 : 0;
            var _div_img=$(".asset-image-list-"+_uid);

            var _imgs_arr = [];
            _div_img.find('.asset-img-url').each(function () {
                _imgs_arr.push($(this).val());
            });
            var _item = {};
            _item.member_asset_id = _member_asset_id;
            _item.is_received = _mortgage_received;
            _item.asset_images = _imgs_arr;
            _assets.push(_item);
        });
        return _assets;
    }
    
    function submitAuthorize() {

        try{

            //处理资产
            var _assets = calculateAssetsList();

            var _currency = $('select[name="currency"]').val();
            if (_currency == 0) {
                alert('Please select currency.');
                return false;
            }

            //var _loan_fee = $('input[name="loan_fee"]').val();

            // 客户的图片，强制要求
            var _client_image = $('#member_image').val();
            if( !_client_image ){
                alert('Please take photo of client.');
                return false;
            }

            //处理合同图片
            var _contract_img_list = [];
            $("#div_contract_images").find(".image-item").find(".contract-img-url").each(function () {
                _contract_img_list.push($(this).val());
            });
            // 没有合同图片要限制提交,但一旦高拍仪出问题也是问题啊，先不限制了
            if( _contract_img_list.length==0 ){
                alert('Please take photo for <kbd>Contract Photo</kbd>');
                return false;
            }
            _contract_img_list = _contract_img_list.join(",");
            $("#contract_images").val(_contract_img_list);

            var _is_have_one_time = $('input[name="is_have_one_time_category"]').val();
            if( _is_have_one_time == 1  ){

                if(  $('#chk_one_click')[0] != undefined ){
                    var _is_one_click_for_one_time = $('#chk_one_click')[0].checked;
                    if( _is_one_click_for_one_time ){
                        var _client_trading_password_ele = $('input[name="member_trading_password"]');
                        if( _client_trading_password_ele && !_client_trading_password_ele.val() ){
                            alert('Please input client trading password.');
                            return false;
                        }

                        /*var _confirm_client_trading_password_ele = $('input[name="member_confirm_trading_password"]');
                        if( _confirm_client_trading_password_ele ){
                            if( _confirm_client_trading_password_ele.val() != _client_trading_password_ele.val() ){
                                alert('Client two password not the same.');
                                return false;
                            }
                        }*/
                    }
                }
            }



            // 使用ajax
            var params = $('#authorizeForm').getValues();
            params.mortgage_list=_assets;
            showMask();
            yo.loadData({
                _c: "member_credit",
                _m: "ajaxConfirmSignAuthoriseContract",
                ajax:{
                    timeout: 100000
                },
                param: params,
                callback: function (_o) {
                    hideMask();
                    if (_o.STS) {

                        if(_o.DATA['is_auto_disburse_one_time']){
                            console.log(_o.DATA['uid']);
                            autoLoanForOneTimeProduct(_o.DATA['uid']);
                        }else{
                            alert(_o.MSG,1,function(){
                                window.location.reload();
                            });
                        }

                    } else {
                        alert(_o.MSG);
                    }
                }
            });

        }catch (ex){
            alert(ex.Message);
        }


    }


    function autoLoanForOneTimeProduct(auth_contract_id)
    {
        if( !auth_contract_id ){
            alert('Unknown credit contract id.');
            return false;
        }

        showMask();
        yo.dynamicTpl({
            tpl: "member_credit_v2/credit.one.time.click.loan.preview",
            control:'counter_base',
            dynamic: {
                api: "member_credit",
                method: "grantCreditOneTimeLoanPreview",
                param: {credit_contract_id:auth_contract_id,member_id:MEMBER_ID}
            },
            callback: function (_tpl) {
                hideMask();
                $('#tab_authorize').html(_tpl);
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
        var _amount = Number($(_e).val());
        var _currency = $(_e).attr('currency');
        var _currency_min_value = Number($('#' + _currency + '_min_value').val());

        //_amount = Math.ceil(_amount / _currency_min_value) * _currency_min_value;
        //_amount = Number(_amount).toFixed(fractionalDigit(_currency_min_value));

        // 就四舍五入到需要的位数就行了，不然要换算取整总是有进一和退一的精度问题
        _amount = Number(_amount.toFixed(fractionalDigit(_currency_min_value)));
        //console.log(_amount);
        $(_e).val(_amount);

        var _total_loan_fee = Number($('#loan_fee').val());
        // usd和khr要分开算
        var _total_fee_usd = Number($('#total_fee_usd').val());
        var _total_fee_khr = Number($('#total_fee_khr').val());
        if( !_total_fee_usd ){
            _total_fee_usd = 0;
        }
        if( !_total_fee_khr ){
            _total_fee_khr = 0;
        }
        console.log(_total_fee_usd);
        console.log(_total_fee_khr);


        // 计算各币种的分配 暂时只有USD和KHR
        if( _currency == '<?php echo currencyEnum::USD; ?>' ){

            var _other_currency = '<?php echo currencyEnum::KHR; ?>';
            var _other_currency_min_value = Number($('#' + _other_currency + '_min_value').val());

            var _left_need_usd = 0;
            var _left_need_khr = 0;


            // 先支付本币种USD
            if( _amount < _total_fee_usd ){
                _left_need_usd = _total_fee_usd-_amount;
                _left_need_khr = _total_fee_khr;
            }else{
                // 有超出，就买KHR
                var _left_amount = _amount-_total_fee_usd;
                var _ex_khr_amount = USD_KHR_EX_RATE*_left_amount;
                if( _ex_khr_amount < _total_fee_khr){
                    _left_need_khr = _total_fee_khr-_ex_khr_amount;
                }else{
                    _left_need_khr = 0;
                }
                _left_need_usd = 0;
            }

            if( _left_need_usd<=0 && _left_need_khr<=0 ){
                $('input[name="khr_amount"]').val(0);
                return true;
            }

            // 剩余的usd和khr用 KHR支付
            var _need_khr_amount = 0;
            // 本身有剩余的KHR
            _need_khr_amount += _left_need_khr;
            // 需要购买的USD
            if( _left_need_usd > 0 ){
                _need_khr_amount += _left_need_usd/KHR_USD_EX_RATE;
            }

            // 修正下整数小数位的进一问题
            _need_khr_amount = Number(_need_khr_amount.toFixed(3));
            // 换算成最小额度
            _need_khr_amount = Math.ceil(_need_khr_amount / _other_currency_min_value) * _other_currency_min_value;
            $('input[name="khr_amount"]').val(_need_khr_amount);
            return true;

        }else{

            // 收的是KHR
            var _other_currency = '<?php echo currencyEnum::USD; ?>';
            var _other_currency_min_value = Number($('#' + _other_currency + '_min_value').val());

            var _left_need_usd = 0;
            var _left_need_khr = 0;


            // 先支付本币种KHR
            if( _amount < _total_fee_khr ){
                _left_need_usd = _total_fee_usd;
                _left_need_khr = _total_fee_khr-_amount;
            }else{
                // 有超出，就买USD
                var _left_amount = _amount-_total_fee_khr;
                var _ex_usd_amount = KHR_USD_EX_RATE*_left_amount;
                if( _ex_usd_amount < _total_fee_usd){
                    _left_need_usd = _total_fee_usd-_ex_usd_amount;
                }else{
                    _left_need_usd = 0;
                }
                _left_need_khr = 0;
            }

            if( _left_need_usd<=0 && _left_need_khr<=0 ){
                $('input[name="usd_amount"]').val(0);
                return true;
            }

            // 剩余的usd和khr用 usd支付
            var _need_usd_amount = 0;
            // 本身有剩余的USD
            _need_usd_amount += _left_need_usd;
            // 需要购买的KHR
            if( _left_need_khr > 0 ){
                _need_usd_amount += _left_need_khr/USD_KHR_EX_RATE;
            }

            // 修正下整数小数位的进一问题
            _need_usd_amount = Number(_need_usd_amount.toFixed(3));
            // 换算成最小额度
            _need_usd_amount = Math.ceil(_need_usd_amount / _other_currency_min_value) * _other_currency_min_value;
            $('input[name="usd_amount"]').val(_need_usd_amount);
            return true;


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
