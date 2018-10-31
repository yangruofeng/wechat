<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<?php $client_info = $output['client_info'];?>
<?php $certification_type = enum_langClass::getCertificationTypeEnumLang(); ?>
<?php $cert_type = array(
    certificationTypeEnum::LAND => 'land_credit_rate',
    certificationTypeEnum::HOUSE => 'house_credit_rate',
    certificationTypeEnum::MOTORBIKE => 'motorbike_credit_rate',
    certificationTypeEnum::CAR => 'car_credit_rate',
    certificationTypeEnum::STORE=>'store_credit_rate'
) ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Request Credit</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid' => $client_info['uid']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Request Credit</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container">
        <div class="col-sm-12">
            <?php require_once template('widget/item.member.summary1'); ?>
        </div>
        <div class="col-sm-12" style="/*padding-left: 200px*/">
            <?php require_once template('widget/item.member.summary.relative'); ?>
        </div>


        <?php $last_suggest = $output['last_suggest']; ?>
        <div class="col-sm-7">
            <div class="panel panel-default" id="div_panel_step1">
                <div class="panel-heading">
                    <h5 class="panel-title"><i class="fa fa-id-card-o"></i>Request Credit</h5>
                </div>
                <div class="panel-body">
                    <ul style="list-style: disc!important;padding-left: 40px;background-color: yellow;">
                    <?php if($output['analysis']['suggest']['tip']){?>

                            <?php foreach($output['analysis']['suggest']['tip'] as $tip_item){?>
                                <li style="padding-top: 5px;padding-bottom: 5px;">
                                    <?php echo $tip_item?>
                                </li>
                            <?php }?>

                    <?php }?>
                        <li style="padding-top: 5px;padding-bottom: 5px;">
                            <?php echo $last_suggest['source_desc']?>
                        </li>
                    </ul>
                    <form class="form-horizontal disable" method="post" action="<?php echo getUrl('web_credit_v2', 'saveRequestCredit', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <div id="frm_credit">
                            <input type="hidden" name="form_submit" value="ok">
                            <input type="hidden" name="member_id" id="txt_member_id" value="<?php echo $client_info['uid']?>">
                            <table class="table">
                                <tbody class="table-body">
                                <tr>
                                    <td><label class="control-label">Repay Ability(Monthly)</label></td>
                                    <td>
                                        <input type="number" class="form-control input-h30" name="monthly_repayment_ability" id="txt_monthly_repayment_ability" value="<?php echo $last_suggest['monthly_repayment_ability']; ?>">
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Max Terms</label></td>
                                    <td>
                                        <div class="input-group" style="width: 100%">
                                            <input type="number" class="form-control input-h30" name="credit_terms" id="txt_credit_terms" value="<?php echo $last_suggest['credit_terms']; ?>">
                                            <span class="input-group-addon" style="min-width: 60px;border-left: 0">Months</span>
                                        </div>
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                                <tr class="tr-category-input" data-category-index="0">
                                    <td><label class="control-label">Default Credit(No Mortgaged)</label></td>
                                    <td>
                                        <input type="number" class="form-control input-h30 count_credit" data-hidden="0" id="txt_default_credit" name="default_credit" value="<?php echo $last_suggest['default_credit']; ?>">
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="pl-25">For Credit Category:</span>
                                    </td>
                                    <td>
                                        <select class="form-control select-category" data-category-index="0" id="txt_default_credit_category_id" name="default_credit_category_id">
                                            <?php foreach($output['product_list'] as $prod){?>
                                                <option value="<?php echo $prod['uid']?>" <?php if($last_suggest['default_credit_category_id']==$prod['uid']) echo 'selected'?>><?php echo $prod['alias']?></option>
                                            <?php }?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td><label class="control-label">Increase Credit By Mortgage</label></td>
                                    <td></td>
                                </tr>
                                <?php
                                $member_assets = $output['member_assets'];
                                $last_suggest_detail_list = $last_suggest['suggest_detail_list'];
                                foreach($last_suggest_detail_list as $sugg_asset_item){
                                    if($sugg_asset_item['credit']>0){
                                        $all_suggest_asset_ids[$sugg_asset_item['member_asset_id']]=$sugg_asset_item['member_asset_id']; //处理哪些需要匹配的资产，如果没有匹配到，要提醒给用户
                                    }
                                }
                                ?>
                                <?php if ($member_assets) {?>
                                    <?php foreach($member_assets as $i=>$val) {?>
                                        <?php
                                        if($last_suggest_detail_list[$val['uid']]['credit']>0){
                                            unset($all_suggest_asset_ids[$val['uid']]);//认为这个资产已经匹配到了
                                        }
                                        ?>
                                        <tr class="tr-category-input" data-category-index="<?php echo $i+1;?>">
                                            <td>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" data-uid="<?php echo $val['uid']?>" class="chk-increase" onchange="chk_increase_onchange(this)"
                                                            <?php if($last_suggest_detail_list[$val['uid']]['credit']>0) echo 'checked'?>>
                                                        <input type="hidden" class="chk-increase-value" name="chk_increase[]" value="<?php if($last_suggest_detail_list[$val['uid']]['credit']>0){ echo '1';}else{ echo '0';}?>">
                                                        <span><?php echo $val['asset_name']; ?></span>
                                                        <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$val['asset_type']]; ?>)</span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control input-h30 count_credit suggest-item-asset" data-hidden="<?php if($last_suggest_detail_list[$val['uid']]['credit']>0){ echo 0;}else{ echo 1;}?>" name="increase_credit[]"
                                                       value="<?php echo $last_suggest_detail_list[$val['uid']]['credit']; ?>""
                                                       data-asset-id="<?php echo $val['uid']?>"
                                                       style="<?php if(!$last_suggest_detail_list[$val['uid']]['credit']) echo 'display:none'?>"
                                                    >
                                                <input type="hidden" name="asset_id[]" value="<?php echo $val['uid'];?>">
                                            </td>
                                        </tr>
                                        <tr class="tr-credit-category-<?php echo $val['uid']?>" style="<?php if(!$last_suggest_detail_list[$val['uid']]['credit']) echo 'display:none'?>;border-bottom: dashed 1px darkgray">
                                            <td>
                                                <span class="pl-25">For Credit Category:</span>
                                            </td>
                                            <td>
                                                <select class="form-control select-category"  data-category-index="<?php echo $i+1;?>" name="member_credit_category_id[]">
                                                    <?php foreach($output['product_list'] as $prod){?>
                                                        <option value="<?php echo $prod['uid']?>" <?php if($last_suggest_detail_list[$val['uid']]['member_credit_category_id']==$prod['uid']) echo 'selected'?>><?php echo $prod['alias']?></option>
                                                    <?php }?>
                                                </select>
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

                                <?php if(count($all_suggest_asset_ids)){?>
                                    <!--提醒用户哪些资产没有匹配到-->
                                    <tr>
                                        <td colspan="10">
                                            <?php foreach($all_suggest_asset_ids as $not_match_id){
                                                $no_match_item=$output['analysis']['all_asset'][$not_match_id];
                                                ?>
                                                <p>
                                                    <kbd>
                                                        <span>No Evaluation OF </span>
                                                        <span><?php echo $no_match_item['asset_name']; ?></span>
                                                        <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$no_match_item['asset_type']]; ?>)</span>
                                                    </kbd>
                                                </p>

                                            <?php }?>
                                        </td>
                                    </tr>

                                <?php }?>
                                <tr>
                                    <td><label class="control-label">Collateral Certificates</label></td>
                                    <td></td>
                                </tr>
                                <?php   $member_collateral=$output['analysis']['suggest']['collateral'];  ?>
                                <?php if(!$member_collateral){?>
                                    <tr>
                                        <td><span class="pl-25"></span></td>
                                        <td>
                                            No Record
                                        </td>
                                    </tr>
                                <?php }?>
                                <?php foreach($member_collateral as $val){?>
                                    <tr class="tr-category-input" data-category-index="<?php echo $i+1;?>">
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" data-uid="<?php echo $val['uid']?>" class="chk-increase collateral-item"
                                                        <?php if($last_suggest_detail_list[$val['uid']]) echo 'checked'?>>
                                                    <span><?php echo $val['asset_name']; ?></span>
                                                    <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$val['asset_type']]; ?>)</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                <?php }?>
                                <tr>
                                    <td colspan="2" style="color:red;font-weight: 500">
                                        <i class="fa fa-warning"></i>Require: MaxCredit = DefaultCredit + (Increase Credit By Mortgaged)
                                    </td>
                                </tr>

                                <tr>
                                    <td><label class="control-label">Max Credit</label></td>
                                    <td>
                                        <input type="text" class="form-control input-h30" id="txt_max_credit" name="max_credit" value="<?php echo $last_suggest['max_credit']; ?>">
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                                <?php if( $last_suggest['is_can_not_edit'] ){ ?>
                                    <tr>
                                        <td><label>Loan Fee</label></td>
                                        <td><?php echo $last_suggest['loan_fee']." ".($last_suggest['loan_fee_type']?'$':'%')?></td>
                                    </tr>
                                    <tr>
                                        <td><label>Admin Fee</label></td>
                                        <td><?php echo $last_suggest['admin_fee']." ".($last_suggest['admin_fee_type']?'$':'%')?></td>
                                    </tr>
                                <?php }?>

                                <?php if($credit){
                                    //处理有没有历史credit的问题
                                    if($credit['balance']>0){//这是需要让用户选择是append还是replace
                                        ?>
                                        <tr>
                                            <td>
                                                Grant Type
                                            </td>
                                            <td>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="is_append" id="rbn_append0" value="0"
                                                            <?php if(!isset($last_suggest['is_append']) || !$last_suggest['is_append']) echo 'checked'?>>
                                                        Replace
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="is_append" id="rbn_append1" value="1"
                                                            <?php if(isset($last_suggest['is_append']) && $last_suggest['is_append']) echo 'checked'?>>
                                                        Append
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="color:red;font-weight: 500">
                                                <i class="fa fa-warning"></i>The Expiry Time Is <?php echo $credit['expire_time']?>if you choosed append the credit
                                            </td>
                                        </tr>
                                    <?php }}?>
                                </tbody>
                            </table>

                            <?php if(!$last_suggest['is_can_not_edit'] ){ ?>
                                <p style="padding: 20px 10px" class="text-center">
                                    <button type="button" class="btn btn-primary" onclick="btn_next_onclick();" style="width: 200px"> <?php echo 'Next' ?></button>
                                </p>
                            <?php }?>
                        </div>
                        <div id="div_credit_part2">
                            <?php if( $last_suggest['is_can_not_edit'] ){ ?>

                                <table class="table">
                                    <tr style="background-color: #808080">
                                        <td colspan="10">
                                            <span>Currency-Credit</span>
                                        </td>
                                    </tr>
                                    <?php foreach($last_suggest['suggest_product'] as $last_prod_item){
                                        $match_category=$output['product_list'][$last_prod_item['member_credit_category_id']];
                                        ?>
                                        <tr>
                                            <td><kbd><?php echo $match_category['alias']?></kbd></td>
                                            <td><?php echo $match_category['sub_product_name']?></td>
                                            <td>
                                                USD：<?php echo ncPriceFormat($last_prod_item['credit_usd'])?>
                                                <br/>
                                                KHR：<?php echo ncPriceFormat($last_prod_item['credit_khr'])?>
                                            </td>
                                            <td>
                                                USD-Interest：<?php echo ncPriceFormat($last_prod_item['interest_rate'])?>%
                                                <br/>
                                                KHR-Interest：<?php echo ncPriceFormat($last_prod_item['interest_rate_khr'])?>%
                                            </td>
                                            <td>
                                                USD-OP-Fee：<?php echo ncPriceFormat($last_prod_item['operation_fee'])?>%
                                                <br/>
                                                KHR-OP-Fee：<?php echo ncPriceFormat($last_prod_item['operation_fee_khr'])?>%
                                            </td>

                                        </tr>

                                    <?php }?>
                                </table>

                            <?php }?>
                        </div>

                        <table class="table" style="margin-top: 20px">
                            <tbody>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <?php if( $last_suggest['is_can_not_edit'] ){ ?>
                                        <div class="alert alert-warning" style="font-size: 14px;color:red;">
                                            The request has been submit to HQ to be approved,can not edit now!
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </form>
                </div>
            </div>

        </div>

       <?php include(template("web_credit_v2/suggest.credit.operator.right"))?>

        <div style="margin-top:10px;margin-bottom: 30px" class="col-sm-12">
            <?php $source_mark = ($output['is_bm'] ? 'bm_suggest' : 'op_suggest'); ?>
            <?php include(template("widget/item.client.reference")); ?>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_next_onclick(){
        var _ccy_credit={};
        $("#frm_credit").find(".select-category").each(function(){
            var _cid=$(this).val();
            if(!_ccy_credit[_cid]){
                _ccy_credit[_cid]=0;
            }
            //获取对应的credit
            var _txt_credit=$(this).closest("tr").prev("tr").find(".count_credit");

            if(_txt_credit.data("hidden")==0){
                var _amt=_txt_credit.val();
                console.log(_amt);
                _ccy_credit[_cid]=parseInt(_ccy_credit[_cid])+parseInt(_amt);
            }
        });
        var _args={};
        _args.cate_list=_ccy_credit;
        _args.repay_ability=$("#txt_monthly_repayment_ability").val();
        _args.credit_terms=$("#txt_credit_terms").val();
        _args.max_credit=$("#txt_max_credit").val();
        _args.default_credit=$("#txt_default_credit").val();
        _args.member_id = '<?php echo $client_info['uid'];?>';
        _args.is_append=$('input[name="is_append"]:checked').val();
        _args.default_credit_category_id = $.trim($('#txt_default_credit_category_id').val());

        $(document).waiting();
        yo.loadData({
            _c:"web_credit_v2",
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
                        tpl:"web_credit_v2/suggest.credit.operator.currency",
                        ext:{data:_ret},
                        callback:function(_tpl){
                            $(document).unmask();
                            $("#div_credit_part2").html(_tpl);
                            $("#div_credit_part2").show();
                        }
                    });

                }else{
                    $(document).unmask();
                    alert(_o.MSG,2);
                }
            }
        });

    }
</script>
<script>
    $(document).ready(function () {
        /*
        $(".select-category").on("change",function(){
            refreshCategoryCCY();
        });
        refreshCategoryCCY();
        initCategoryCCY();
        */
    });
    function showPackageInterestSetting(_e){
        var _package_id=$(_e).closest("td").find("select").val();
        window.open("<?php echo getUrl("web_credit","showPackageInterestSetting",array(),false,BACK_OFFICE_SITE_URL)?>"+"&package_id="+_package_id);
    }
    function chk_increase_onchange(_e){
        var _uid=$(_e).data("uid");
        if($(_e).prop("checked")){
            $(_e).closest("tr").find(".count_credit").show();
            $(_e).closest("label").find(".chk-increase-value").val(1);
            $(_e).closest("tr").find(".count_credit").data("hidden",0);
            $(".tr-credit-category-"+_uid).show();
        }else{
            $(_e).closest("tr").find(".count_credit").hide();
            $(_e).closest("label").find(".chk-increase-value").val(0);
            $(_e).closest("tr").find(".count_credit").data("hidden",1);
            $(".tr-credit-category-"+_uid).hide();
        }
        //refreshCategoryCCY();
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
    function btn_submit_onclick(){
        var _ccy_credit={};

        var _args={};
        _args.cate_list=_ccy_credit;
        _args.repay_ability=$("#txt_monthly_repayment_ability").val();
        _args.credit_terms=$("#txt_credit_terms").val();
        _args.max_credit=$("#txt_max_credit").val();
        _args.default_credit=$("#txt_default_credit").val();
        _args.default_credit_category_id = $.trim($('#txt_default_credit_category_id').val());
        _args.member_id = $("#txt_member_id").val();
        //_args.officer_id = '<?php echo cookie('member_id');?>';
        //_args.token = '<?php echo cookie('token');?>';
        _args.is_append=$('input[name="is_append"]:checked').val();

        //取资产信用
        var _arr_assets = [];
        $("#frm_credit").find(".suggest-item-asset").each(function () {
            if($(this).data("hidden")==0){
                _arr_assets.push({
                    "asset_id": $(this).data('asset-id'),
                    "credit": $(this).val() ? parseInt($(this).val()) : 0,
                    "credit_category_id":$(this).closest("tr").next("tr").find(".select-category").val()
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
        //  var _arr_ccy_json = encodeURI(JSON.stringify(_arr_ccy));

        _args.asset_list=_arr_assets;
        _args.currency_list=_arr_ccy;

        _args.loan_fee=$("#txt_loan_fee").val();
        _args.admin_fee=$("#txt_admin_fee").val();
        _args.loan_fee_type=$("#txt_loan_fee_type").val();
        _args.admin_fee_type=$("#txt_admin_fee_type").val();
        _args.remark=$("#txt_remark").val();

        var _collateral_list=[];
        $("#frm_credit").find(".collateral-item").each(function(){
            var _uid=$(this).data("uid");
            var _sts=$(this).is(':checked')?1:0;
            if(_sts==1){
                _collateral_list.push(_uid);
            }
        });
        _args.collateral_list=_collateral_list;


       showMask();
        yo.loadData({
            _c:"web_credit_v2",
            _m:"ajaxSubmitSuggestCredit",
            param:_args,
            callback:function(_o){
                hideMask();
                if(_o.STS){
                    alert("Saved Successfully",1,function(){
                        window.location.href='<?php echo getBackOfficeUrl('web_credit','creditClient',array('uid'=>$client_info['uid'])); ?>';
                    });
                }else{
                    alert(_o.MSG,2);
                }
            }
        });
    }
    function btn_back_onclick(){
        $("#div_credit_part2").hide();
        $("#frm_credit").show();
    }



</script>