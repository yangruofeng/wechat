<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    em {
        font-weight: 500;
        font-size: 15px;
    }

    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .input-h30 {
        height: 30px !important;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    .pl-75 {
        padding-left: 75px;
        font-weight: 500;
    }

    .pl-125 {
        padding-left: 125px;
        font-weight: 400;
    }

    #check_list td {
        width: 25%;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px !important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        /*background-color: #FFF;*/
        overflow: hidden;
    }

    .content td {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    .nav-tabs {
        height: 34px !important;
    }

    .nav-tabs li a {
        padding: 7px 12px !important;
    }

    .tab-content label {
        margin-bottom: 0px !important;
    }

    .form-horizontal .control-label {
        text-align: left;
    }

    .co_suggest_list tr > td:first-child {
        width: 150px;
    }

    .nav-tabs li {
        min-width: 70px;
    }
</style>
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
        <div class="col-sm-12" style="padding-left: 200px">
            <?php require_once template('widget/item.member.summary.relative'); ?>
        </div>


        <?php $last_suggest = $output['last_suggest']; ?>
        <div class="col-sm-7">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Request Credit</h5>
                </div>
                <div class="content">
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
                    <form class="form-horizontal disable" method="post" action="<?php echo getUrl('web_credit', 'saveRequestCredit', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <input type="hidden" name="form_submit" value="ok">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">

                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Repay Ability(Monthly)</label></td>
                                <td>
                                    <input type="number" class="form-control input-h30" name="monthly_repayment_ability" value="<?php echo $last_suggest['monthly_repayment_ability']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Max Terms</label></td>
                                <td>
                                    <div class="input-group" style="width: 100%">
                                        <input type="number" class="form-control input-h30" name="credit_terms" value="<?php echo $last_suggest['credit_terms']; ?>">
                                        <span class="input-group-addon" style="min-width: 60px;border-left: 0">Months</span>
                                    </div>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr class="tr-category-input" data-category-index="0">
                                <td><label class="control-label">Default Credit(No Mortgaged)</label></td>
                                <td>
                                    <input type="number" class="form-control input-h30 count_credit" data-hidden="0" name="default_credit" value="<?php echo $last_suggest['default_credit']; ?>" onblur="creditAmtItemChanged(this)">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="pl-25">For Credit Category:</span>
                                </td>
                                <td>
                                    <select class="form-control select-category" data-category-index="0" name="default_credit_category_id">
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
                                            <input type="number" class="form-control input-h30 count_credit" data-hidden="0" name="increase_credit[]"
                                                   value="<?php echo $last_suggest_detail_list[$val['uid']]['credit']; ?>" onblur="creditAmtItemChanged(this)"
                                                   style="<?php if(!$last_suggest_detail_list[$val['uid']]['credit']) echo 'display:none'?>"
                                                >
                                            <input type="hidden" name="asset_id[]" value="<?php echo $val['uid'];?>">
                                        </td>
                                    </tr>
                                    <tr class="tr-credit-category-<?php echo $val['uid']?>" style="<?php if(!$last_suggest_detail_list[$val['uid']]['credit']) echo 'display:none'?>">
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
                                <td colspan="2" style="color:red;font-weight: 500">
                                    <i class="fa fa-warning"></i>Require: MaxCredit = DefaultCredit + (Increase Credit By Mortgaged)
                                </td>
                            </tr>

                            <tr>
                                <td><label class="control-label">Max Credit</label></td>
                                <td>
                                    <input type="text" class="form-control input-h30" name="max_credit" value="<?php echo $last_suggest['max_credit']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>

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
                        <?php include_once(template("web_credit/suggest.credit.operator.currency"))?>
                        <table class="table" style="margin-top: 20px">
                            <tbody>
                            <tr>
                                <td><label class="control-label">Remark</label></td>
                                <td>
                                    <textarea class="form-control" name="remark" style="width: 100%;height: 50px"><?php echo $output['last_suggest']['remark']; ?></textarea>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">

                                    <?php if( $last_suggest['is_can_not_edit'] ){ ?>
                                        <div class="alert alert-warning" style="font-size: 14px;color:red;">
                                            The request has been submit to HQ to be approved,can not edit now!
                                        </div>
                                    <?php }else{ ?>
                                        <button type="button" class="btn btn-primary" id="bm-submit"><i class="fa fa-check"></i><?php echo 'Save' ?></button>

                                        <!--                                    <a href="--><?php //echo getUrl('branch_manager', 'getRequestCreditHistory', array('operator_id' => $last_suggest['operator_id'], 'request_type' => 1, 'member_id' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?><!--" type="button" class="btn btn-primary"><i class="fa fa-list"></i>--><?php //echo 'History' ?><!--</a>-->

                                    <?php } ?>


                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </form>
                </div>
            </div>
        </div>

       <?php include(template("web_credit/suggest.credit.operator.right"))?>

        <div style="margin-top:10px;margin-bottom: 30px" class="col-sm-12">
            <?php $source_mark = ($output['is_bm'] ? 'bm_suggest' : 'op_suggest'); ?>
            <?php include(template("widget/item.client.reference")); ?>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(document).ready(function () {
        $(".select-category").on("change",function(){
            refreshCategoryCCY();
        });
        refreshCategoryCCY();
        initCategoryCCY();
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
        refreshCategoryCCY();
    }
    function checkMaxCredit(){
        //所有抵押credit+default=max_credit
        var _max_credit=parseInt($('input[name="max_credit"]').val());
//        var _default_credit=parseInt($('input[name="default_credit"]').val());
        var _total_increase=0;
        $(".count_credit").each(function(){
            if(!$(this).is(":hidden")){
                _total_increase+=parseInt($(this).val());
                //判断对应的category-id是否有设置

            }

        });
        if(_max_credit!=_total_increase){
            alert("Require: MaxCredit = DefaultCredit + (Increase Credit By Mortgaged)");
            return false;
        }
        if(!_max_credit>0){
            alert("Require: MaxCredit > 0");
            return false;
        }


        return true;
    }



    $('#bm-submit').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }
        if(!checkMaxCredit()){
            return;
        }
        if(!checkCurrencyCredit()){
            return;
        }


        $('.form-horizontal').submit();

    })

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            monthly_repayment_ability: {
                required: true
            },
            credit_terms: {
                required: true
            },
            default_credit: {
                required: true
            },
            max_credit: {
                required: true
            }
        },
        messages: {
            monthly_repayment_ability: {
                required: '<?php echo 'Required!'?>'
            },
            credit_terms: {
                required: '<?php echo 'Required!'?>'
            },
            default_credit: {
                required: '<?php echo 'Required!'?>'
            },
            max_credit: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });


</script>