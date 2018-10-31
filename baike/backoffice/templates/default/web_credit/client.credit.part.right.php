<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.css?v=1" rel="stylesheet"/>
<div class="col-sm-6" style="padding-bottom: 20px">
    <div class="panel panel-primary panel-item">
        <div class="panel-heading">
            <p class="panel-title">
                Credit-Category
                <a type="button" class="btn btn-default" href='<?php echo getUrl('web_credit', 'editMemberCreditCategory', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>'>
                    <i class="fa fa-edit"></i>
                </a>
            </p>
        </div>
        <table class="table table-hover table-bordered">
            <tr>
                <td class="text-right"><label>Total Credit Limit</label></td>
                <td class="text-left"><?php echo ncPriceFormat($output['credit_info']['credit']) ?></td>
                <td class="text-right"><label>Repayment Ability(USD)</label></td>
                <td class="text-left"><?php echo ncPriceFormat($output['loan_account']['repayment_ability'])?></td>
            </tr>
            <tr>
                <td class="text-right"><label>Repayment Date</label></td>
                <td class="text-left"><?php echo  $output['loan_account']['due_date']? $output['loan_account']['due_date']." Of Each Month":'Follow First Time Loan'?></td>

                <td class="text-right"><label>Credit-Terms(Month)</label></td>
                <td class="text-left"><?php echo $output['credit_info']['credit_terms']?:'No Setting'?></td>

            </tr>
            <?php
                //判断是否有semiballoon的还款方式
                $has_semiballoon=false;
                foreach($credit_category as $cate){
                    if($cate['interest_type']==interestPaymentEnum::SEMI_BALLOON_INTEREST){
                        $has_semiballoon=true;
                        break;
                    }
                }
                if($has_semiballoon){
            ?>
                    <tr>
                        <td class="text-right">
                            <label for="">Semi balloon principal repay period</label>
                        </td>
                        <td>
                            <?php if( $output['loan_account']['principal_periods']){  ?>
                                <?php echo $output['loan_account']['principal_periods']; ?> (Months)
                            <?php }else{ ?>
                                No Setting
                            <?php } ?>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php }?>

        </table>
        <table class="table table-hover table-bordered">
            <tr class="table-header">
               <td>Category</td>
                <td>Repayment</td>
                <td>Interest</td>
                <td>One Time</td>
                <td>Credit</td>
                <td>USD</td>
                <td>KHR</td>

            </tr>
            <?php if($credit_category){ ?>
                <?php foreach ($credit_category as $v) { ?>
                    <tr>
                        <td>
                            <?php echo $v['alias']?>
                        </td>
                        <td>
                            <?php echo $v['sub_product_name']?>
                        </td>
                        <td style="background: <?php if(!$v['interest_package_name']) echo 'yellow'?>">
                            <a href="<?php echo getBackOfficeUrl("web_credit","creditCategoryInterestPage",array('member_id'=>$client_info['uid'],'mcc_id'=>$v['uid']))?>">
                                <?php echo $v['interest_package_name']?:'No Setting'?> <i class="fa fa-link"></i>
                            </a>
                        </td>
                        <td>
                            <?php if($v['is_one_time']){?>
                                <i class="fa fa-check"></i>
                            <?php }?>
                        </td>
                        <td>
                            Limit:<?php echo ncPriceFormat($v['credit'],0)?>
                            <br/>
                            BAL: <?php echo ncPriceFormat($v['credit_balance'],0)?>
                        </td>
                        <td>
                            Limit:<?php echo ncPriceFormat($v['credit_usd'],0)?>
                            <br/>
                            BAL: <?php echo ncPriceFormat($v['credit_usd_balance'],0)?>
                        </td>
                        <td>
                            Limit:<?php echo ncPriceFormat($v['credit_khr'],0)?>
                            <br/>
                            BAL: <?php echo ncPriceFormat($v['credit_khr_balance'],0)?>
                        </td>

                    </tr>
                <?php  } ?>
            <?php }else{?>
                <tr>
                    <td colspan="20">
                        <?php include(template(":widget/no_record"))?>
                    </td>
                </tr>
            <?php }?>
        </table>
    </div>
    <div class="panel panel-default panel-item" style="border: solid 1px #ddd;">
        <div class="panel-heading">
            <p class="panel-title">
                Client Request
                <?php if(!is_array($client_request) || $client_request['state'] == creditRequestStateEnum::CANCEL || $client_request['state']==creditRequestStateEnum::DONE) { ?>
                    <a type="button" class="btn btn-default" href="<?php echo getUrl('web_credit', 'addMemberRequestPage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>">
                        <i class="fa fa-plus"></i>
                    </a>
                <?php } elseif($client_request['state']==creditRequestStateEnum::CREATE) { ?>
                    <a type="button" class="btn btn-default" href="<?php echo getUrl('web_credit', 'editMemberRequestPage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>">
                        <i class="fa fa-edit"></i>
                    </a>
                <?php }else{ ?>
                <?php }?>
                <code>
                    <?php echo $client_request['uid']?>
                    <?php if(is_array($client_request)) {echo "STATE:". strtoupper($lang['client_request_state_' .$client_request['state']]);}{echo '';}?>
                </code>

            </p>
        </div>
        <?php if(is_array($client_request)){?>
            <table class="table table-hover table-bordered">
                <tr>
                    <td class="text-right">Amount</td>
                    <td class="text-left"><?php echo $client_request['credit']?ncPriceFormat($client_request['credit']):0;?></td>
                    <td class="text-right">Terms</td>
                    <td class="text-left"><?php echo $client_request['terms']?:0;?> Months</td>
                </tr>
                <tr>
                    <td class="text-right">
                        Interest
                    </td>
                    <td class="text-left">
                        <?php echo $client_request['interest_rate']?>%
                    </td>
                    <td class="text-right">
                        Request-Time
                    </td>
                    <td class="text-left">
                        <?php echo $client_request['create_time']?>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Purpose</td>
                    <td class="text-left" colspan="3">
                        <?php echo $client_request['purpose'];?>
                    </td>

                </tr>
                <?php if(count($output['client_relative'])){?>
                    <tr>
                        <td colspan="4" style="text-align: center;font-weight: bold">Relative</td>
                    </tr>
                <?php }?>
                <?php foreach($output['client_relative'] as $rel){?>
                    <tr>
                        <td>
                            <a href="<?php echo getImageUrl($rel['headshot']) ?>" target="_blank" title="Head portraits">
                                <img class="img-icon"
                                     src="<?php echo getImageUrl($rel['headshot'], imageThumbVersion::SMALL_ICON) ?>">
                            </a>
                        </td>
                        <td>
                            <ul>
                                <li>
                                    <label><?php echo $rel['name']?></label>
                                </li>
                                <li>
                                    <?php echo $rel['relation_type']." / ".$rel['relation_name']?>
                                </li>
                            </ul>
                        </td>
                        <td><?php echo $rel['contact_phone']?></td>
                        <td>
                            <a class="btn btn-default" href="<?php echo getUrl('web_credit', 'addMemberCbcPage', array("member_id" => $client_info['uid'], 'client_id' => $rel['uid'], "client_type" => 1), false, BACK_OFFICE_SITE_URL);?>">CBC</a>
                        </td>
                    </tr>
                <?php }?>
            </table>
        <?php }else{?>
            <p>
                <?php include(template(":widget/no_record"))?>
            </p>
        <?php }?>


    </div>
    <div class="panel panel-default panel-item">
        <div class="panel-heading">
            <p class="panel-title">Assets & Collateral</p>
        </div>
    </div>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php
        foreach($output['assets_type'] as $a_key=>$a_value){
            $cur_list=array();
            foreach($output['assets'] as $asset_type_key=>$asset_list){
                if($asset_type_key==$a_key){
                    $cur_list=$asset_list;
                    break;
                }
            }
            ?>
            <div class="panel panel-default panel-item" id="panel_asset_head_<?php echo $a_key?>">
                <div class="panel-heading" style="background-color: #fff;padding-left: 50px;">
                    <p class="panel-title" style="font-weight: 300;border-bottom: dashed 1px #ddd">
                        * <?php echo strtoupper($a_value)?>
                        <a style="float: right;position: relative" href="<?php echo getUrl("web_credit","addAssetItem",array("asset_type"=>$a_key,"member_id"=>$client_info['uid']),false,BACK_OFFICE_SITE_URL)?>" class="btn btn-default">
                            <i class="fa fa-plus"></i>
                        </a>
                        <a role="button" data-toggle="collapse" style="display: none" data-parent="#accordion" href="#panel_asset_body_<?php echo $a_key?>" aria-expanded="true" aria-controls="panel_asset_body_<?php echo $a_key?>">
                           <i class="fa fa-angle-down"></i>
                        </a>
                    </p>
                </div>
                <div id="panel_asset_body_<?php echo $a_key?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="panel_asset_head_<?php echo $a_key?>" style="padding-left: 100px">
                    <?php if(count($cur_list)>0){?>
                        <table class="table table-hover table-bordered">
                            <tr class="table-header">
                                <td>AssetName(Id)</td>
                                <td>Valuation</td>
                                <td>Rental</td>
                                <td>Function</td>
                            </tr>
                            <?php foreach($cur_list as $asset_item){?>
                                <tr>
                                    <td>
                                        <?php if($asset_item['relative_id']>0){?>
                                            <span><i class="fa fa-legal"></i></span>
                                        <?php }?>
                                        <?php echo $asset_item['asset_name'].($asset_item['asset_sn']?'('.$asset_item['asset_sn'].')':'');?>
                                    </td>
                                    <td>
                                        <?php echo $asset_item['officer_evaluation']?>
                                    </td>
                                    <td>
                                        <?php echo $asset_item['officer_rent']?>
                                    </td>
                                    <td>
                                        <a class="btn btn-default" href="<?php echo getUrl("web_credit","assetItemDetail",array("asset_id"=>$asset_item['uid'],"member_id"=>$client_info['uid']),false,BACK_OFFICE_SITE_URL)?>">Edit</a>
                                    </td>
                                </tr>
                            <?php }?>
                        </table>
                    <?php }?>
                </div>
            </div>
        <?php }
        ?>

    </div>

    <div class="panel panel-default panel-item"  style="border: solid 1px #ddd;">
        <div class="panel-heading">
            <p class="panel-title">
                Business
            </p>
        </div>
        <?php if (!$business_income) { ?>
            <div>
                <?php include(template(":widget/no_record")) ?>
            </div>
        <?php } ?>
    </div>

    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <?php foreach($business_income as $key => $business){ ?>
            <div class="panel panel-default panel-item" id="panel_asset_head_<?php echo $a_key?>">
                <div class="panel-heading" style="background-color: #fff;padding-left: 50px;">
                    <p class="panel-title" style="font-weight: 300;border-bottom: dashed 1px #ddd">
                        * <?php echo strtoupper($business['industry_name'])?>
                        <a style="float: right;position: relative"
                           href="<?php echo getUrl("web_credit", "addMemberBusinessIncomePage", array('industry_id' => $key, 'member_id' => $client_info['uid']), false, BACK_OFFICE_SITE_URL) ?>"
                           class="btn btn-default">
                            <i class="fa fa-plus"></i>
                        </a>
                        <a role="button" data-toggle="collapse" style="display: none" data-parent="#accordion" href="#panel_business_body_<?php echo $key?>" aria-expanded="true" aria-controls="panel_business_body_<?php echo $key?>">
                            <i class="fa fa-angle-down"></i>
                        </a>
                    </p>
                </div>

                <div id="panel_business_body_<?php echo $key?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="panel_business_head_<?php echo $key?>" style="padding-left: 100px">
                    <?php if (count($business['income_business']) > 0) { ?>
                        <table class="table table-hover table-bordered">
                            <tr class="table-header">
                                <td>Business Name</td>
                                <td>Relative</td>
                                <td>Employees</td>
                                <td>Profit</td>
                                <td>Function</td>
                            </tr>
                            <?php foreach($business['income_business'] as $income_business){ ?>
                                <tr>
                                    <td>
                                        <?php if($income_business['state']>=100){?>
                                            <i class="fa fa-lock"></i>
                                        <?php }?>

                                        <?php echo $income_business['branch_code'] ?>
                                    </td>
                                    <td>
                                        <?php foreach ($income_business['owner_list'] as $owner) { ?>
                                            <span style="padding-right: 15px"><?php echo $owner['relative_name'] ?></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php echo ($income_business['employees'] > 0) ? $income_business['employees'] : ''; ?>
                                    </td>
                                    <td>
                                        <?php echo $income_business['profit'] > 0 ? ncPriceFormat($income_business['profit']) : ''; ?>
                                    </td>
                                    <td>
                                        <?php if ($income_business['is_add']) { ?>
                                            <a href="<?php echo getUrl('web_credit', 'addMemberBusinessIncomePage', array('industry_id' => $key, 'member_id' => $client_info['uid'], 'branch_code'=>$income_business['branch_code']), false, BACK_OFFICE_SITE_URL); ?>">Edit</a>
                                        <?php } else { ?>
                                            <a href="<?php echo getUrl('web_credit', 'editMemberBusinessIncomePage', array('income_id' => $income_business['uid'],'member_id' => $client_info['uid']), false, BACK_OFFICE_SITE_URL); ?>">
                                                <?php if($income_business['state']>=100){?>
                                                    Check
                                                <?php }else{?>
                                                    Edit
                                                <?php }?>

                                            </a>
                                        <?php } ?>
                                        <?php if ($output['is_bm'] && $income_business['state']<100) { ?>
                                            <a style="margin-left: 10px"
                                               onclick="deleteBusinessIncome(<?php echo $client_info['uid'] ?>, <?php echo $key ?>, '<?php echo $income_business['branch_code'] ?>');">Delete</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }?>
                        </table>
                    <?php }?>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="panel panel-default panel-item" style="border: solid 1px #ddd;">
        <div class="panel-heading">
            <p class="panel-title">
                Salary
                <button type="button" class="btn btn-default" onclick="javascript:location.href='<?php echo getUrl('web_credit', 'addMemberSalaryIncomePage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>'">
                    <i class="fa fa-plus"></i></button>
            </p>
        </div>
        <?php if($salary_list){?>
            <table class="table table-hover  table-bordered" style="border: solid 1px #ddd">
                <tr class="table-header">
                    <td>Company</td>
                    <td>Employee Name</td>
                    <td>Position</td>
                    <td>Salary</td>
                    <td>Function</td>
                </tr>
                <?php foreach ($salary_list as $v) {?>
                    <tr>
                        <td>
                            <?php if($v['state']>=100){?>
                                <span><i class="fa fa-lock"></i></span>
                            <?php }?>
                            <?php echo $v['company_name'];?>
                        </td>
                        <td>
                            <?php echo $v['relative_name']?>
                        </td>
                        <td>
                            <?php echo $v['position'];?>
                        </td>
                        <td>
                            <?php echo $v['salary']>0?ncPriceFormat($v['salary']):'';?>
                        </td>
                        <td>
                            <a href="<?php echo getUrl('web_credit', 'editMemberSalaryIncomePage', array('uid'=>$v['uid']), false, BACK_OFFICE_SITE_URL);?>">
                                <?php if($v['state']>=100){?>
                                    Check
                                <?php }else{?>
                                    Edit
                                <?php }?>

                            </a>
                        </td>
                    </tr>

                <?php }?>
            </table>

        <?php }else{?>
            <div>
                <?php include(template(":widget/no_record"))?>
            </div>
        <?php }?>


    </div>
    <div class="panel panel-default panel-item" style="border: solid 1px #ddd">
        <div class="panel-heading">
            <p class="panel-title">
                Attachment
                <a type="button" class="btn btn-default" href="<?php echo getUrl('web_credit', 'addMemberAttachmentPage', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL);?>">
                    <i class="fa fa-plus"></i>
                </a>
            </p>
        </div>
        <?php if($attachment_list){?>
            <table class="table table-hover  table-bordered" >
                <tr class="table-header">
                    <td>Title</td>
                    <td>Extend Type</td>
                    <td>Amount</td>
                    <td>Function</td>
                </tr>
                <?php
                $ext_list=(new memberAttachmentTypeEnum())->Dictionary();
                ?>
                <?php foreach ($attachment_list as $v) {?>
                    <tr>
                        <td>
                            <?php if($v['state']>=100){?>
                                <i class="fa fa-lock"></i>
                            <?php }?>
                            <?php echo $v['title'];?>
                        </td>
                        <td>
                            <?php echo $ext_list[$v['ext_type']];?>
                        </td>
                        <td>
                            <?php echo $v['ext_amount']>0?ncPriceFormat($v['ext_amount']):'';?>
                        </td>
                        <td>
                            <a href="<?php echo getUrl('web_credit', 'editMemberAttachmentPage', array('uid'=>$v['uid']), false, BACK_OFFICE_SITE_URL);?>">
                                <?php if($v['state']>=100){?>
                                    Check
                                <?php }else{?>
                                    Edit
                                <?php }?>

                            </a>
                        </td>
                    </tr>
                <?php }?>
            </table>
        <?php }else{?>
            <div>
                <?php include(template(":widget/no_record"))?>
            </div>
        <?php }?>



    </div>
    <?php if($output['is_bm']){?>
        <div class="panel panel-default panel-item" style="border: solid 1px #ddd">
            <div class="panel-heading">
                <p class="panel-title">
                    Suggest Credit
                    <a class="btn btn-default" href="<?php echo getUrl('web_credit', 'editSuggestCreditPage', array('uid' => $client_info['uid']), false, BACK_OFFICE_SITE_URL);?>">
                        <i class="fa fa-edit"></i>
                    </a>
                </p>
            </div>
            <?php if($suggest_list){?>
                <table class="table table-hover table-bordered" >
                    <tr class="table-header">
                        <td>Update Time</td>
                        <td>Repayment Ability</td>
                        <td>Max Terms</td>
                        <td>Max Credit</td>
                    </tr>
                    <?php $v=$suggest_list;//原来是准备展示多行的，只展示一行才清晰?>
                    <tr>
                        <td>
                            <?php echo $v['update_time']?:$v['request_time'];?>
                        </td>
                        <td>
                            <?php echo $v['monthly_repayment_ability'];?>
                        </td>
                        <td>
                            <?php echo $v['credit_terms']?>
                        </td>
                        <td>
                            <?php echo $v['max_credit'];?>
                        </td>
                    </tr>
                    <?php if($v['state'] == memberCreditSuggestEnum::CREATE && $client_request['state'] == creditRequestStateEnum::CREATE && !$is_voting_suggest){?>
                        <tr>
                            <td colspan="10" style="text-align: center">
                                <?php if ($output['is_bm']) { ?>
                                    <button id="btn_submit_hq" type="button" class="btn btn-info" onclick="submit_hq_onclick(<?php echo $v['uid'] ?>)"><i class="fa fa-mail-forward "></i>
                                        <?php echo 'Submit Headquarters' ?>
                                    </button>
                                    <!--先取消bm的自主授信
                                        <?php if ($v['max_credit'] > $output['approve_credit_limit']) { ?>
                                            <button id="btn_submit_hq" type="button" class="btn btn-info" onclick="submit_hq_onclick(<?php echo $v['uid'] ?>)"><i class="fa fa-mail-forward "></i>
                                                <?php echo 'Submit Headquarters' ?>
                                            </button>
                                        <?php } else { ?>
                                            <button id="btn_submit_bm" type="button" class="btn btn-info" onclick="submit_bm_onclick(<?php echo $v['uid'] ?>)"><i class="fa fa-mail-forward "></i>
                                                <?php echo 'Fast Grant' ?>
                                            </button>
                                        <?php } ?>
                                        -->
                                <?php }else{ ?>
                                    <?php if($output['credit_grant_profile']['allow_operator_submit_to_hq']){?>
                                        <button id="btn_submit_hq" type="button" class="btn btn-info" onclick="submit_hq_onclick(<?php echo $v['uid'] ?>)"><i class="fa fa-mail-forward "></i>
                                            <?php echo 'Submit Headquarters' ?>
                                        </button>
                                    <?php }?>
                                <?php }?>
                            </td>
                        </tr>
                    <?php }else{?>
                        <tr>
                            <td colspan="10">
                                <?php
                                $state_list=(new memberCreditSuggestEnum())->Dictionary();
                                echo $state_list[$v['state']];
                                ?>
                                <?php if( $v['state'] == memberCreditSuggestEnum::NO_PASS ){ ?>
                                    <kbd><?php echo $v['suggest_grant_info']['remark']; ?></kbd>
                                    <?php if( !empty($v['suggest_grant_info']['grant_attender_list'])){   ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr class="table-header">
                                                    <td>
                                                        Voter
                                                    </td>
                                                    <td>
                                                        Result
                                                    </td>
                                                    <td>
                                                        Remark
                                                    </td>
                                                </tr>

                                                <?php $common_lang = enum_langClass::getCommonApproveStateLang(); ?>
                                                <?php foreach( $v['suggest_grant_info']['grant_attender_list'] as $k_value ){ ?>
                                                    <tr>
                                                        <td><?php echo $k_value['user_name']; ?></td>
                                                        <td><?php echo $common_lang[$k_value['vote_result']]?:$k_value['vote_result']; ?></td>
                                                        <td><?php echo $k_value['vote_remark']; ?></td>
                                                    </tr>
                                                <?php ?>

                                            </table>
                                        </div>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>

                                <?php if($v['state']==memberCreditSuggestEnum::PENDING_APPROVE){?>
                                    <button type="button" class="btn btn-info" onclick="cancel_submit_hq_onclick(<?php echo $v['uid']?>)">Cancel Submit</button>
                                <?php }?>
                            </td>
                        </tr>
                    <?php }?>

                </table>

            <?php }else{?>
            <div>
                <?php include(template(":widget/no_record"))?>
            </div>
            <?php } ?>
        </div>

    <?php }?>

</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.min.js?v=1"></script>
<script>
    function deleteBusinessIncome(member_id, industry_id, branch_code) {
        yo.confirm('Confirm','are you sure to delete the research?',function (_r) {
            if(!_r) return false;
            yo.loadData({
                _c: 'web_credit',
                _m: 'deleteAllMemberBusinessIncome',
                param: {member_id: member_id, industry_id: industry_id, branch_code: branch_code},
                callback: function (_o) {
                    if (_o.STS) {
                        console.log(_o.MSG)
                        alert('Deleted success!', 1,function(){
                            window.location.href = '<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['client_info']['uid']), false, BACK_OFFICE_SITE_URL)?>';
                        });
                    } else {
                        alert(_o.MSG, 2);
                    }
                }
            });
        });
    }
</script>