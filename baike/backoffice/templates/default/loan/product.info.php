<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.config.js' ?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.all.js' ?>"></script>
<style>
    .btn-release,.btn-unshelve{
        position: absolute;
        top: 0px;
        right: 0px;
        height: 30px;
        line-height: 30px;
        padding: 0px 15px;
    }
    .base-info .size-info .content{
        overflow: auto;
        padding: 5px 0 10px;
        margin: 5px 15px;
    }
</style>
<?php
$main_product_info = $output['main_product_info'];
$product_info = $output['sub_product_info'];
$repayment_period_lang = enum_langClass::getLoanRepaymentPeriodLang();
$interest_type_lang = enum_langClass::getLoanInstallmentTypeLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'product', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Sub product</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">

        <div style="min-height: 20px;">
            <button id="btn_release_product" class="btn btn-default btn-release" style="display:<?php echo $product_info['state'] != loanProductStateEnum::ACTIVE ? 'block;' : 'none;' ?>">Active</button>
            <button id="btn_unshelve_product" class="btn btn-default btn-unshelve" style="display:<?php echo $product_info['state'] == loanProductStateEnum::ACTIVE ? 'block;' : 'none;' ?>">Inactive</button>
        </div>

        <div class="page-1">
            <div class="base-info clearfix">
                <div class="product-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Sub product Info</h5></div>
                        <div class="col-sm-4">
                        </div>
                    </div>
                    <div class="content clearfix">
                        <div class="wrap1">
                            <table>
                                <tr style="font-weight: bold">
                                    <input type="hidden" id="uid" value="<?php echo $product_info['uid'];?>">
                                    <td>Product Name：</td>
                                    <td id="product_name"><?php echo $product_info['sub_product_name'];?></td>
                                </tr>

                                <tr>
                                    <td>Repayment Type: </td>
                                    <td id="repayment_type" val="<?php echo $product_info['repayment_type']; ?>"><?php  if( isset($product_info['repayment_type']) ){ echo $repayment_period_lang[$product_info['repayment_type']]; }  ?></td>
                                </tr>

                                <tr>
                                    <td>Interest Type: </td>
                                    <td id="interest_type" val="<?php echo $product_info['interest_type']; ?>"><?php  if( isset($product_info['interest_type']) ){ echo $interest_type_lang[$product_info['interest_type']]; }  ?></td>
                                </tr>

                                <tr>
                                    <td>State：</td>
                                    <td id="state" val="<?php echo $product_info['state']?>"><?php echo $lang['enum_loan_product_state_'.$product_info['state']]?></td>
                                </tr>



                            </table>
                        </div>
                        <div class="wrap2">
                            <table>
                                <tr style="font-weight: bold">
                                    <td>Product Code：</td>
                                    <td id="product_code"><?php echo $product_info['sub_product_code']?></td>
                                </tr>
                                <tr>
                                    <td>Is full interest prepayment：</td>
                                    <td id="is_full_interest_prepayment" val="<?php echo $product_info['is_full_interest_prepayment']?>"><?php echo (isset($product_info['is_full_interest_prepayment'])?($product_info['is_full_interest_prepayment']==1?'YES':'NO'):'')?></td>
                                </tr>
                                <tr>
                                    <td>Is approved prepayment request：</td>
                                    <td id="is_approved_prepayment_request" val="<?php echo $product_info['is_approved_prepayment_request']?>"><?php echo (isset($product_info['is_approved_prepayment_request'])?($product_info['is_approved_prepayment_request']==1?'YES':'NO'):'')?></td>
                                </tr>


                            </table>
                        </div>
                    </div>
                </div>
                <div class="penalty-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Penalty</h5></div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="content">
                        <table>

                            <!--<tr>
                                <td>Penalty On：</td>
                                <td id="penalty_on" val="<?php /*echo $product_info['penalty_on']*/?>"><?php /*echo $lang['enum_' . $product_info['penalty_on']]*/?></td>
                            </tr>-->

                            <tr>
                                <td>Penalty Rate：</td>
                                <td id="penalty_rate" val="<?php echo $product_info['penalty_rate'] > 0 ? $product_info['penalty_rate'] : ""; ?>"><?php echo $product_info['penalty_rate'] > 0 ? ($product_info['penalty_rate'] . '%') : ''?></td>
                            </tr>
                            <tr>
                                <td>Divisor Days：</td>
                                <td id="penalty_divisor_days" val="<?php echo $product_info['penalty_divisor_days'] > 0 ? $product_info['penalty_divisor_days'] : ""?>"><?php echo $product_info['penalty_divisor_days'] > 0 ? ($product_info['penalty_divisor_days'].' Days'):''?></td>
                            </tr>
                            <tr>
                                <td>Grace Days:</td>
                                <td id="grace_days" val="<?php echo $product_info['grace_days'] > 0 ? $product_info['grace_days'] : ""?>"><?php echo $product_info['grace_days'] > 0 ? ($product_info['grace_days'].' Days'):''?></td>

                            </tr>
                            <tr>
                                <td>Editable：</td>
                                <td id="is_editable_penalty" val="<?php echo $product_info['is_editable_penalty']?$product_info['is_editable_penalty']:''?>"><?php if( ($product_info['penalty_rate']) > 0 ){ echo $product_info['is_editable_penalty']==1?'YES':'NO'; } ?></td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
            <div class="base-info clearfix">
                <div class="size-info">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Size Rate</h5></div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="content clearfix">

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


<script>
    $(function () {
        var height = $('.product-info .content').height();
        $('.penalty-info .content').height(height);
        $('.tab-top li').click(function () {
            var _page = $(this).attr('page');
            $('.tab-top li').removeClass('active');
            $(this).addClass('active');
            $('.page-1,.page-2,.page-3').hide();
            $('.' + _page).show();
        })
    });

    var main_product_id = '<?php echo intval($main_product_info['uid']); ?>';
    var uid = '<?php echo intval($product_info['uid'])?>';
    if (uid != 0) {
        getSizeRateList(uid);
    }


    $('#btn_release_product').click(function () {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "releaseProduct",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $(this).hide();
                    $('.btn-unshelve').show();
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });

    $('#btn_unshelve_product').click(function () {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "unShelveProduct",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $(this).hide();
                    $('.btn-release').show();
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });



    function getSizeRateList(product_id) {
        if(product_id == 0) return;
        yo.dynamicTpl({
            tpl: "loan/size_rate.list",
            dynamic: {
                api: "loan",
                method: "getSizeRateList",
                param: {product_id: product_id,type:'info'}
            },
            callback: function (_tpl) {
                $(".size-info .content").html(_tpl);
            }
        });
    }





</script>
