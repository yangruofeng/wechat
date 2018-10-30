<?php
$client_info=$output['client_info'];
?>
<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>
<style>
    .magnifier-assembly{
        padding: 0;
    }
</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="container">
        <div class="row" style="max-width: 1300px">
            <div class="col-sm-6">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Client Info</h5>
                    </div>
                    <div class="content" id="div_client_box" data-client_id="0">
                        <div>
                            <div class="row">
                                <div class="col-sm-6 col-md-3">
                                    <div class="thumbnail">
                                        <?php
                                            $image_item=$client_info['member_image'];
                                            include(template(":widget/item.image.viewer.item"));
                                        ?>
                                        <div class="caption">
                                            <h5>Headshot</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="thumbnail">
                                        <?php
                                        $image_item=$client_info['hold_id_card'];
                                        include(template(":widget/item.image.viewer.item"));
                                        ?>
                                        <div class="caption">
                                            <h5>Hold ID-Card</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <ul class="list-group">
                                        <li class="list-group-item list-group-item-info"><?php echo $client_info['phone_id']?></li>
                                        <li class="list-group-item list-group-item-text"><?php echo $client_info['display_name']?></li>
                                        <li class="list-group-item list-group-item-text">
                                            <a class="btn btn-default btn-block" href="<?php echo getUrl('member_index','start',array(),false,ENTRY_COUNTER_SITE_URL)?>">
                                                RESET
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>
                            <div class="row">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span class="badge"><?php echo $client_info['obj_guid']?></span>
                                        CID
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?php if($client_info['has_trading_password']){ echo 'YES';}else{ echo 'NO';}?></span>
                                        Set TradingPassword
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?php if($client_info['is_logging_fingerprint']){ echo 'YES';}else{ echo 'NO';}?></span>
                                        Verified FingerPrint
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?php echo $client_info['member_state_text']?></span>
                                        State
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?php echo $client_info['login_code']?></span>
                                        Login Account
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?php echo $client_info['kh_display_name']?></span>
                                        Khmer Name
                                    </li>
                                    <?php foreach($client_info['save_balance'] as $ccy=>$ccy_balance){
                                            if(!$ccy_balance) continue;
                                        ?>
                                        <li class="list-group-item">
                                            <a href="<?php echo getUrl('member_index','showMemberFlow',array('member_id'=>$client_info['uid'],'currency'=>$ccy ),false,ENTRY_COUNTER_SITE_URL)?>" style="float: right;display: inline-block">
                                                <span style="font-weight: bold"> <?php echo ncPriceFormat($ccy_balance)?> > </span>
                                            </a>
                                            Balance <?php echo $ccy;?>
                                        </li>
                                    <?php }?>
<!--                                    <li class="list-group-item">-->
<!--                                        <span class="badge">--><?php //echo $client_info['loan_account_info']['repayment_ability']>0?ncPriceFormat($client_info['loan_account_info']['repayment_ability']):'-';?><!--</span>-->
<!--                                        Monthly Repayment ability-->
<!--                                    </li>-->
                                    <li class="list-group-item">
                                        <span class="badge">
                                            <?php echo $client_info['loan_account_info']['due_date']?$client_info['loan_account_info']['due_date'].' Of Each Month':'No Setting'?>
                                        </span>
                                        Repayment Date
                                    </li>
<!--                                    <li class="list-group-item">-->
<!--                                        <span class="badge">-->
<!--                                            --><?php //echo $client_info['loan_account_info']['principal_periods']?:'No Setting'?>
<!--                                        </span>-->
<!--                                        Repay Principal Periods Of Semi-Balloon-->
<!--                                    </li>-->
                                    <li class="list-group-item">
                                        <span class="badge"><?php echo ncPriceFormat($client_info['credit'])?></span>
                                        Credit Limit
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?php echo ncPriceFormat($client_info['credit_balance'])?></span>
                                        Credit Balance
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?php echo $client_info['credit_detail']['expire_time']?></span>
                                        Expire Time
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?php if($client_info['credit_is_active']){ echo 'Enable';}else{ echo 'Disable';}?></span>
                                        Credit State
                                    </li>
                                    <li class="list-group-item table-responsive">
                                        <span>Credit List</span>
                                        <table class="table table-bordered  table-no-background">
                                            <tr class="table-header">
                                                <td>Credit-Category</td>
                                                <td>Repayment</td>
                                                <td>Interest</td>
                                                <td>Credit-USD</td>
                                                <td>USD-Balance</td>
                                                <td>Credit-KHR</td>
                                                <td>KHR-Balance</td>

                                            </tr>
                                            <?php
                                             $max_days=($client_info['credit_terms']?:0)*30;
                                            ?>
                                            <?php foreach($client_info['credit_category'] as $ccitem){?>
                                                <tr>
                                                    <td style="font-weight: bold"><?php echo $ccitem['alias']?></td>
                                                    <td><?php echo $ccitem['sub_product_name']?></td>
                                                    <td><?php echo $ccitem['interest_package_name']?></td>
                                                    <td><?php echo ncPriceFormat($ccitem['credit_usd'])?></td>
                                                    <td><?php echo ncPriceFormat($ccitem['credit_usd_balance'])?></td>
                                                    <td><?php echo ncPriceFormat($ccitem['credit_khr'])?></td>
                                                    <td><?php echo ncPriceFormat($ccitem['credit_khr_balance'])?></td>
                                                </tr>
                                            <?php }?>
                                        </table>
                                    </li>
                                    <li class="list-group-item">
                                        <span>Officer List</span>
                                        <table class="table table-no-background table-bordered">
                                            <tr class="table-header">
                                                <td>Code</td>
                                                <td>Name</td>
                                                <td>Position</td>
                                                <td>Phone</td>
                                            </tr>
                                            <?php if($client_info['officer_list']){?>
                                                <?php foreach($client_info['officer_list'] as $o){?>
                                                    <tr>
                                                        <td><?php echo $o['user_code']?></td>
                                                        <td><?php echo $o['officer_name']?></td>
                                                        <td><?php echo $o['user_position']?></td>
                                                        <td><?php echo $o['mobile_phone']?></td>
                                                    </tr>
                                                <?php }?>
                                            <?php }else{?>
                                                <tr>
                                                   <td colspan="10"><?php include(template(":widget/no_record"))?></td>
                                                </tr>
                                            <?php }?>
                                        </table>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-sm-6">

                <?php if( !empty($output['loan_overdue_schema'])){  ?>
                    <div class="basic-info">
                        <div class="ibox-title">
                            <h5><i class="fa fa-id-card-o"></i>Overdue Loan Schema</h5>
                        </div>
                        <div class="content">

                            <table class="table table-bordered">
                                <tr class="table-header">
                                    <td >Contract No.</td>
                                    <td >Schema Idx</td>
                                    <td >Penalty Date</td>
                                    <td >Penalty Amount</td>
                                </tr>
                                <?php $penalty_multi = array();  ?>
                                <?php  foreach($output['loan_overdue_schema'] as $item){  ?>
                                    <tr>
                                        <td>
                                            <a class="btn-link" href="<?php echo getUrl('member_loan','contractIndex',array('contract_id'=>$item['contract_id']),false,ENTRY_COUNTER_SITE_URL);?>">
                                                <?php echo $item['contract_sn'];?>
                                            </a>
                                        </td>
                                        <td><?php echo $item['scheme_name'];?></td>
                                        <td><?php echo date('Y-m-d',strtotime($item['penalty_start_date']));?></td>
                                        <td><?php echo ncPriceFormat($item['penalty_amount']).$item['currency'];?></td>

                                    </tr>
                                <?php $penalty_multi[$item['currency']] += $item['penalty_amount']; } ?>
                                <tr>
                                    <td colspan="10" >
                                        <div style="text-align: center">
                                            <label for="">Total:</label>
                                            <?php foreach( $penalty_multi as $c=>$a ){ ?>
                                                <span style="margin-left: 10px;"><?php echo $c.': '; ?><span style="color: red;font-weight: 600;"><?php echo ncPriceFormat($a); ?></span></span>
                                            <?php } ?>

                                        </div>
                                    </td>
                                </tr>
                            </table>

                        </div>
                    </div>
                <?php } ?>


                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Loan Contract</h5>
                    </div>
                    <div class="content">
                        <?php
                            $loan_contract_list=$output['loan_contract_list'];
                            $contract_state_list=(new loanContractStateEnum())->Dictionary();
                        ?>
                        <?php if(count($loan_contract_list)>0){?>
                            <table class="table table-bordered">
                                <tr class="table-header">
                                    <td width="30%">Contract No.</td>
                                    <td width="20%">Loan Time</td>
                                    <td width="25%">Product</td>
                                    <td width="25%">Principal</td>
                                    <td width="30%">State</td>
                                </tr>
                                <?php foreach($loan_contract_list as $item){?>
                                    <tr>
                                        <td>
                                            <a class="btn-link" href="<?php echo getUrl('member_loan','contractIndex',array('contract_id'=>$item['uid']),false,ENTRY_COUNTER_SITE_URL)?>">
                                                <?php echo $item['contract_sn']?>
                                            </a>
                                        </td>
                                        <td><?php echo $item['create_time']?></td>
                                        <td><?php echo $item['alias']?></td>
                                        <td><?php echo ncPriceFormat($item['receivable_principal'])?></td>
                                        <td><?php echo $contract_state_list[$item['state']]?></td>
                                    </tr>
                                <?php }?>
                            </table>
                        <?php }else{ ?>
                            <?php include(template(":widget/no_record"))?>
                        <?php }?>

                    </div>
                </div>
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-image"></i> Scene Photo</h5>
                    </div>
                    <div class="content">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="thumbnail">
                                    <?php
                                    $image_item=$client_info['last_scene_image'];
                                    include(template(":widget/item.image.viewer.item"));
                                    ?>
                                    <div class="caption">
                                        <h5>Last Scene</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <p>
                                    <span>Scene Time:</span>
                                    <span style="font-weight: bold;" class="span-time"><?php echo $client_info['last_scene_time']?:'N/A'?></span>
                                </p>
                                <?php if($client_info['last_scene_time'] && (time()-strtotime($client_info['last_scene_time']))>60*60*24*30){?>
                                    <p>
                                        Please Take New Photo,The last time has passed 30 days
                                    </p>
                                <?php }?>
                                <p>
                                    <button class="btn btn-default btn-block" onclick="btn_take_scene_photo_onclick(this)">Take Photo</button>
                                </p>
                                <p>
                                    <a class="btn btn-default btn-block"
                                       href="<?php echo getCounterUrl('member_index','memberScenePhotoHistoryPage',array('member_id'=>$client_info['uid']))?>">
                                        History</a>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Profile</h5>
                    </div>
                    <div class="content">
                        <?php if($client_info['member_state']==memberStateEnum::TEMP_LOCKING){?>
                            <a class="btn btn-block btn-default"
                               href="<?php echo getUrl('member_index','unlockMember',array('member_id'=>$client_info['uid']),false,ENTRY_COUNTER_SITE_URL)?>">
                                Unlock
                            </a>
                        <?php }?>
                        <a class="btn btn-block btn-default"
                           href="<?php echo getUrl('member_index','changeTradePwd',array('member_id'=>$client_info['uid']),false,ENTRY_COUNTER_SITE_URL)?>">
                            Change Trading-Password
                        </a>
                        <a class="btn btn-block btn-default"
                           href="<?php echo getUrl('member_index','changePhoneNum',array('member_id'=>$client_info['uid']),false,ENTRY_COUNTER_SITE_URL)?>">
                            Change Phone-Number
                        </a>
                        <a class="btn btn-block btn-default"
                           href="<?php echo getUrl('member_index','registerFingerprint',array('member_id'=>$client_info['uid']),false,ENTRY_COUNTER_SITE_URL)?>">
                            Register  Fingerprint
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function btn_take_scene_photo_onclick(_e){
        var _img_item=$(_e).closest(".row").find(".img-asset-item");
        var _span_time=$(_e).closest(".row").find(".span-time");
        var _img_viewer=$(_e).closest(".row").find(".docs-pictures");
        if (window.external) {
            try {
                var _img_path = window.external.getSnapshot("1");
                if (_img_path != "" && _img_path != null) {
                    showMask();
                    yo.loadData({
                        _c:"member_index",
                        _m:"ajaxSubmitMemberScenePhoto",
                        param:{member_id:'<?php echo $client_info["uid"]?>',image_path:_img_path},
                        callback:function(_o){
                            hideMask();
                            if(_o.STS){
                                _img_item.attr('data-original',_o.DATA.big_image);
                                _img_item.attr("src",_o.DATA.small_image+"?"+Math.random());
                                _img_viewer.viewer({url: 'data-original'});
                                alert("Saved Successfully",1,function(){
                                    _span_time.text(_o.DATA.last_time);
                                })
                            }else{
                                alert(_o.MSG,2);
                            }
                        }
                    });
                } else {
                    //alert("Failed to get image path",2);
                }
            } catch (ex) {
                alert(ex.Message,2);

            }
        }
    }
</script>
