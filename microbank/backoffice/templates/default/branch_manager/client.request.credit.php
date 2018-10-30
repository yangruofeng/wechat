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

    .explain {
        padding-left: 10px;
        font-style: italic;
        color: #b3b3b3;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
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
<?php $certification_type = enum_langClass::getCertificationTypeEnumLang(); ?>
<?php $cert_type = array(
    certificationTypeEnum::LAND => 'land_credit_rate',
    certificationTypeEnum::HOUSE => 'house_credit_rate',
    certificationTypeEnum::MOTORBIKE => 'motorbike_credit_rate',
    certificationTypeEnum::CAR => 'car_credit_rate',
) ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <?php if ($output['source']) { ?>
                    <li><a href="<?php echo getUrl('branch_manager', 'requestCredit', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <?php } else { ?>
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                    <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Credit Process</span></a></li>
                <?php } ?>
                <li><a class="current"><span>Request Credit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 1300px">
        <?php $client_info = $output['client_info']; $credit = memberClass::getCreditBalance($client_info['uid']);?>
        <div class="col-sm-12">
            <div class="basic-info">
                <div class="ibox-title" style="position: relative">
                    <h5><i class="fa fa-id-card-o"></i>Client Info</h5>
                    <a href="<?php echo getUrl('branch_manager', 'showClientInfoDetail', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL)?>" style="position:absolute;right: 20px;font-weight: 500;">Detail</a>
                </div>
                <div class="content">
                    <table class="table">
                        <tbody class="table-body">
                        <tr>
                            <td><label class="control-label">Icon</label></td>
                            <td><label class="control-label">Login Account</label></td>
                            <td><label class="control-label">Name</label></td>
                            <td><label class="control-label">Member Credit</label></td>
                            <td><label class="control-label">Credit Balance</label></td>
                            <td><label class="control-label">Loan Balance</label></td>
                            <td><label class="control-label">Phone</label></td>
                            <td><label class="control-label">Status</label></td>
                        </tr>
                        <tr>
                            <td>
                                <a target="_blank" href="<?php echo getImageUrl($client_info['member_icon']); ?>">
                                    <img src="<?php echo getImageUrl($client_info['member_icon'], imageThumbVersion::SMALL_ICON); ?>" style="max-width: 50px;max-height: 50px">
                                </a>
                            </td>
                            <td><?php echo $client_info['login_code']; ?></td>
                            <td><?php echo $client_info['display_name']; ?></td>
                            <td><?php echo ncAmountFormat($credit['credit']); ?></td>
                            <td><?php echo ncAmountFormat($credit['balance']); ?></td>
                            <td><?php echo ncAmountFormat(memberClass::getLoanBalance($credit_info['uid'])->DATA); ?></td>
                            <td><?php echo $client_info['phone_id']; ?></td>
                            <td><?php echo $lang['client_member_state_' . $client_info['member_state']]; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Request Credit</h5>
                </div>
                <div class="content">
                    <form class="form-horizontal" method="post" action="<?php echo getUrl('branch_manager', 'saveBmRequestCredit', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <input type="hidden" name="form_submit" value="ok">
                        <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Evaluation of assets</label></td>
                                <td>
                                    <?php if ($output['source']) { ?>
                                        <em><?php echo $output['total_assets_evaluation']; ?></em>
                                    <?php } else { ?>
                                        <input type="number" class="form-control input-h30" readonly value="<?php echo $output['total_assets_evaluation']; ?>">
                                        <div class="error_msg"></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Evaluation of income(Monthly)</label></td>
                                <td>
                                    <?php if ($output['source']) { ?>
                                        <em><?php echo $output['total_income']; ?></em>
                                    <?php } else { ?>
                                        <input type="number" class="form-control input-h30" readonly value="<?php echo $output['total_income']; ?>">
                                        <div class="error_msg"></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Client Request Credit</label></td>
                                <td>
                                    <?php if ($output['source']) { ?>
                                        <em><?php echo $output['last_suggest']['client_request_credit']; ?></em>
                                    <?php } else { ?>
                                        <input type="number" class="form-control input-h30" name="client_request_credit" value="<?php echo $output['last_suggest']['client_request_credit']; ?>">
                                        <div class="error_msg"></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Monthly Repayment Ability</label></td>
                                <td>
                                    <?php if ($output['source']) { ?>
                                        <em><?php echo $output['last_suggest']['monthly_repayment_ability']; ?></em>
                                    <?php } else { ?>
                                        <input type="number" class="form-control input-h30" name="monthly_repayment_ability" value="<?php echo $output['last_suggest']['monthly_repayment_ability']; ?>">
                                        <div class="error_msg"></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Invalid Terms</label></td>
                                <td>
                                    <?php if ($output['source']) { ?>
                                        <?php echo $output['last_suggest']['credit_terms'] . 'Months'?>
                                    <?php } else { ?>
                                        <div class="input-group" style="width: 100%">
                                            <input type="number" class="form-control input-h30" name="invalid_terms" value="<?php echo $output['last_suggest']['credit_terms']; ?>">
                                            <span class="input-group-addon" style="min-width: 60px;border-left: 0">Months</span>
                                        </div>
                                        <div class="error_msg"></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Default Credit</label></td>
                                <td>
                                    <?php if ($output['source']) { ?>
                                        <em><?php echo $output['last_suggest']['default_credit']; ?></em>
                                    <?php } else { ?>
                                        <input type="number" class="form-control input-h30 count_credit" name="default_credit" value="<?php echo $output['last_suggest']['default_credit']; ?>">
                                        <div class="tip">
                                            <?php
                                            $data = $output['data'];
                                            $income_list = $data['income_list'];
                                            $industry_list = $data['industry_list'];
                                            $income_1 = $income_list['income_rental_land'] + $income_list['income_rental_housing'] + $income_list['income_salary'] + $income_list['income_others'];
                                            $income_1 = $income_1 ?: 0;
                                            $credit_rate_1 = $data['credit_system_rate']['default_credit_rate'];
                                            $credit_1 = intval($income_1 * $credit_rate_1);

                                            $total_sug = $credit_1;
                                            foreach ($industry_list as $ids_item) {
                                                $credit_ids = intval($ids_item['profit'] * $ids_item['credit_rate'] / 100);
                                                $total_sug += $credit_ids;
                                                echo "<p style='margin-bottom:5px;font-size: 12px;color: #b3b3b3'>" . $ids_item['industry_name'] . " : " . $ids_item['profit'] . "*" . $ids_item['credit_rate'] . "%=" . $credit_ids . "</p>";
                                            }
                                            echo "<p style='margin-bottom:5px;font-size: 12px;color: #b3b3b3'>" . "Others : " . $income_1 . "*" . ($credit_rate_1 * 100) . "%=" . $credit_1 . "</p>";
                                            if ($total_sug > $credit_1) {
                                                echo "<p style='margin-bottom:5px;font-size: 12px;color: #b3b3b3'>Total Suggest : " . $total_sug . "</p>";
                                            }
                                            ?>
                                        </div>
                                        <div class="error_msg"></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Increase Credit By</label></td>
                                <td></td>
                            </tr>

                            <?php if ($output['member_assets']) { ?>
                                <?php foreach($output['member_assets'] as $val) {?>
                                    <tr>
                                        <td>
                                            <span class="pl-25">
                                                <span><?php echo $val['asset_name']; ?></span>
                                                <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$val['asset_type']]; ?>)</span>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($output['source']) { ?>
                                                <em><?php echo $output['last_suggest']['suggest_detail_list'][$val['uid']]['credit']; ?></em>
                                                <span class="explain">(<?php echo ncPriceFormat($val['valuation'])?>*<?php echo $output['rate_set'][$cert_type[$val['asset_type']]] * 100 ?>%)</span>
                                            <?php } else { ?>
                                                <input type="number" class="form-control input-h30 count_credit" name="increase_credit[]" value="<?php echo $output['last_suggest']['suggest_detail_list'][$val['uid']]['credit']; ?>">
                                                <input type="hidden" name="asset_id[]" value="<?php echo $val['uid']; ?>">
                                                <span class="explain">(<?php echo ncPriceFormat($val['valuation'])?> * <?php echo $output['rate_set'][$cert_type[$val['asset_type']]] * 100 ?>% = <?php echo ncPriceFormat($val['valuation'] * $output['rate_set'][$cert_type[$val['asset_type']]])?>)</span>
                                            <?php } ?>
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
                            <tr>
                                <td><label class="control-label">Max Credit</label></td>
                                <td>
                                    <?php if ($output['source']) { ?>
                                        <em><?php echo $output['last_suggest']['max_credit']; ?></em>
                                    <?php } else { ?>
                                        <input type="text" class="form-control input-h30" name="max_credit" value="<?php echo $output['last_suggest']['max_credit']; ?>" readonly>
                                        <div class="error_msg"></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table" style="margin-top: 20px">
                            <thead>
                            <tr class="table-header">
                                <td>Product</td>
                                <td>NoMortgage</td>
                                <td>MortgageSoft</td>
                                <td>MortgageHard</td>
                            </tr>
                            </thead>
                            <tbody class="table-body">
                            <?php $bm_suggest_rate = $output['last_suggest']['suggest_rate']; ?>
                            <?php if ($output['source']) { ?>
                            <?php } else { ?>
                            <?php if ($output['product_list']) { ?>
                            <?php foreach ($output['product_list'] as $key => $product) {?>
                                <input type="hidden"  name="product_id[]" value="<?php echo $key?>">
                                <input type="hidden"  name="product_name[]" value="<?php echo $product['sub_product_name']?>">
                                <tr>
                                    <td><?php echo $product['sub_product_name']?></td>
                                    <td>
                                        <input type="number" class="form-control input-h30" name="rate_no_mortgage[]" value="<?php echo isset($bm_suggest_rate) ? $bm_suggest_rate[$key]['rate_no_mortgage'] : $product['max_rate_mortgage']?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control input-h30" name="rate_mortgage1[]" value="<?php echo isset($bm_suggest_rate) ? $bm_suggest_rate[$key]['rate_mortgage1'] : $product['max_rate_mortgage']?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control input-h30" name="rate_mortgage2[]" value="<?php echo isset($bm_suggest_rate) ? $bm_suggest_rate[$key]['rate_mortgage2'] : $product['max_rate_mortgage']?>">
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="4">Null</td></tr>
                            <?php } ?>
                            <?php } ?>
                            </tbody>
                        </table>

                        <table class="table" style="margin-top: 20px">
                            </tbody>
                            <tr>
                                <td><label class="control-label">Remark</label></td>
                                <td>
                                    <?php if ($output['source']) { ?>
                                       <span>
                                           <?php echo $output['last_suggest']['remark']; ?>
                                       </span>
                                    <?php } else { ?>
                                        <textarea class="form-control" name="remark" style="width: 100%;height: 50px"><?php echo $output['last_suggest']['remark']; ?></textarea>
                                        <div class="error_msg"></div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <?php $last_suggest = $output['last_suggest'] ; if (!$output['source']) { ?>
                                        <button type="button" class="btn btn-danger" id="bm-submit"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                                        <?php if ($last_suggest && $last_suggest['state'] == 0) { ?>
                                            <?php if ($last_suggest['max_credit'] > $output['approve_credit_limit']) { ?>
                                                <button id="btn_submit_hq" type="button" class="btn btn-info" onclick="submit_hq(<?php echo $last_suggest['uid'] ?>)"><i class="fa fa-mail-forward "></i><?php echo 'Submit Headquarters' ?></button>
                                            <?php } else { ?>
                                                <button id="btn_submit_bm" type="button" class="btn btn-info" onclick="submit_bm(<?php echo $last_suggest['uid'] ?>)"><i class="fa fa-mail-forward "></i><?php echo 'Fast Grant' ?></button>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <a href="<?php echo getUrl('branch_manager', 'getRequestCreditHistory', array('operator_id' => $last_suggest['operator_id'], 'request_type' => 1, 'member_id' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>" type="button" class="btn btn-primary"><i class="fa fa-list"></i><?php echo 'History' ?></a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>

        <?php $co_list = $output['co_list'];?>
        <?php $co_suggest_list = $output['co_suggest_list'];?>
        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Co Request Credit</h5>
                </div>
                <div class="content">
                    <table class="table co_suggest_list">
                        <tbody class="table-body">
                        <tr>
                            <td>Research Item</td>
                            <?php foreach($co_list as $co){?>
                                <td>
                                    <?php echo $co['officer_name']?>
                                </td>
                            <?php }?>
                        </tr>
                        </tbody>
                        <tbody class="table-body">
                        <tr>
                            <td><label class="control-label">Client Request Credit</label></td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <em><?php echo $co_suggest_list[$k] ? ncAmountFormat($co_suggest_list[$k]['client_request_credit']) : ''?></em>
                                </td>
                            <?php }?>
                        </tr>
                        <tr>
                            <td><label class="control-label">Monthly Repayment Ability</label></td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <em><?php echo $co_suggest_list[$k] ? ncAmountFormat($co_suggest_list[$k]['monthly_repayment_ability']) : ''?></em>
                                </td>
                            <?php }?>
                        </tr>
                        <tr>
                            <td><label class="control-label">Invalid Terms</label></td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <?php echo $co_suggest_list[$k] ? $co_suggest_list[$k]['credit_terms'] . 'Months' : ''?>
                                </td>
                            <?php }?>
                        </tr>
                        <tr>
                            <td><label class="control-label">Default Credit</label></td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <em><?php echo $co_suggest_list[$k] ? ncAmountFormat($co_suggest_list[$k]['default_credit']) : ''?></em>
                                </td>
                            <?php }?>
                        </tr>
                        <tr>
                            <td><label class="control-label">Increase Credit By</label></td>
                            <td></td>
                        </tr>

                        <?php if($output['member_assets']) {?>
                            <?php foreach($output['member_assets'] as $assets){?>
                                <tr>
                                    <td>
                                        <span class="pl-25">
                                            <span><?php echo $assets['asset_name']; ?></span>
                                            <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$assets['asset_type']]; ?>)</span>
                                        </span>
                                    </td>
                                    <?php foreach($co_list as $k => $co){?>
                                        <td>
                                            <em><?php echo $co_suggest_list[$k] ? ncAmountFormat($co_suggest_list[$k]['suggest_detail_list'][$assets['uid']]['credit']) : ''?></em>
                                        </td>
                                    <?php }?>
                                </tr>
                            <?php }?>
                        <?php } else { ?>
                            <tr>
                                <td></td>
                                <td colspan="10">
                                    No Record
                                </td>
                            </tr>
                        <?php }?>
                        <tr>
                            <td><label class="control-label">Max Credit</label></td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <em><?php echo $co_suggest_list[$k] ? ncAmountFormat($co_suggest_list[$k]['max_credit']) : ''?></em>
                                </td>
                            <?php }?>
                        </tr>
                        <tr>
                            <td><label class="control-label">Remark</label></td>
                            <?php foreach($co_list as $k => $co){?>
                                <td>
                                    <?php echo $co_suggest_list[$k] ? $co_suggest_list[$k]['remark'] : ''?>
                                </td>
                            <?php }?>
                        </tr>
                        </tbody>
                    </table>

                    <ul class="nav nav-tabs" role="tablist" style="margin-top: 20px">
                        <?php $i = 0;foreach ($co_list as $k => $co) { ++$i; ?>
                            <li role="presentation" class="<?php echo $i == 1 ? 'active' : ''?>">
                                <a href="#<?php echo $k?>" aria-controls="<?php echo $k?>" role="tab" data-toggle="tab" style="<?php echo $i == 1 ? 'border-left: 0' : ''?>"><?php echo $co['officer_name']; ?></a>
                            </li>
                        <?php }?>
                    </ul>
                    <div class="tab-content">
                        <?php $i = 0;foreach ($co_list as $k => $co) { ++$i; ?>
                            <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : ''?>" id="<?php echo $k?>">
                                <table class="table">
                                    <thead>
                                    <tr class="table-header" style="background-color: #efefef">
                                        <td>Product</td>
                                        <td>NoMortgage</td>
                                        <td>MortgageSoft</td>
                                        <td>MortgageHard</td>
                                    </tr>
                                    </thead>
                                    <tbody class="table-body">
                                    <?php if($output['product_list']){?>
                                    <?php foreach ($output['product_list'] as $key => $product) { $suggest_rate = $co_suggest_list[$k]['suggest_rate'][$key]?>
                                        <tr>
                                            <td><?php echo $product['sub_product_name']?></td>
                                            <td><?php echo isset($suggest_rate['rate_no_mortgage']) ? ($suggest_rate['rate_no_mortgage'] . '%') : ''?></td>
                                            <td><?php echo isset($suggest_rate['rate_mortgage1']) ? ($suggest_rate['rate_mortgage1'] . '%') : ''?></td>
                                            <td><?php echo isset($suggest_rate['rate_mortgage2']) ? ($suggest_rate['rate_mortgage2'] . '%') : ''?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="4">Null</td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>

                                <?php if($k){?>
                                    <table class="table" style="margin-top: 20px">
                                        <tbody>
                                        <tr>
                                            <td colspan="2" style="text-align: center">
                                                <a type="button" class="btn btn-primary" href="<?php echo getUrl('branch_manager', 'getRequestCreditHistory', array('operator_id' => $k, 'request_type' => 0, 'member_id' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-list"></i><?php echo 'History' ?></a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                <?php }?>
                            </div>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:10px;margin-bottom: 30px" class="col-sm-12">
            <?php include(template("branch_manager/client.reference.info")); ?>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(document).ready(function () {

        $('.count_credit').change(function () {
            var total = 0;
            var _fast_credit="<?php echo $output['approve_credit_limit']?:0?>";
            $('.count_credit').each(function () {
                var val = $(this).val();
                val = Number(val);
                val = val.toFixed(2);
                total += Number(val);
            });
            /*
            if(parseInt(total)>_fast_credit){
                $("#btn_sumbit_hq").show();
                $("#btn_submit_bm").hide();
            }else{
                $("#btn_sumbit_hq").hide();
                $("#btn_submit_bm").show();
            }*/
            $('input[name="max_credit"]').val(Math.round(total));
        })
    });

    function submit_hq(uid) {
        if(!uid){
            return;
        }

        yo.loadData({
            _c: "branch_manager",
            _m: "submitRequestCreditToHq",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }
    function submit_bm(uid){
        if(!uid){
            return;
        }

        yo.loadData({
            _c: "branch_manager",
            _m: "submitRequestCreditToFastGrant",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    $('#bm-submit').click(function () {
        if (!$(".form-horizontal").valid()) {
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
            invalid_terms: {
                required: true
            },
            default_credit: {
                required: true
            },
            max_credit: {
                required: true
            },
            interest_without_mortgage: {
                required: true
            },
            interest_with_mortgage: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            monthly_repayment_ability: {
                required: '<?php echo 'Required!'?>'
            },
            invalid_terms: {
                required: '<?php echo 'Required!'?>'
            },
            default_credit: {
                required: '<?php echo 'Required!'?>'
            },
            max_credit: {
                required: '<?php echo 'Required!'?>'
            },
            interest_without_mortgage: {
                required: '<?php echo 'Required!'?>'
            },
            interest_with_mortgage: {
                required: '<?php echo 'Required!'?>'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>