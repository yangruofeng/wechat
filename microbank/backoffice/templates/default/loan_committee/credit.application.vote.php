<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
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

    em {
        font-weight: 500;
        font-size: 15px;
    }

    #check_list td {
        width: 25%;
    }

    #check_list .num {
        width: 20px;
        height: 20px;
        display: inline-block;
        border: 1px solid #FFE299;
        border-radius: 10px;
        line-height: 18px;
        text-align: center;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
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
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    /*.nav-tabs {*/
    /*height: 34px!important;*/
    /*}*/

    /*.nav-tabs li a {*/
    /*padding: 7px 12px !important;*/
    /*}*/

    .tab-content label {
        margin-bottom: 0px!important;
    }

    .form-horizontal {
        margin-bottom: 0px;
    }

    .form-horizontal .control-label {
        text-align: left;
    }


    .pl-75 {
        padding-left: 75px;
        font-weight: 500;
    }

    .pl-125 {
        padding-left: 125px;
        font-weight: 400;
    }
</style>

<style>
    .voting-list .fa {
        font-size: 18px;
        margin-left: 10px;
        color: #666666;
    }

    .voting-list .fa-check {
        color: #008000!important;
    }

    .voting-list .fa-close {
        color: red!important;
    }

    #qrModal .modal-dialog {
        margin-top: 20px!important;
    }

</style>

<?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
<?php $cert_type = array(
    certificationTypeEnum::LAND => 'land_credit_rate',
    certificationTypeEnum::HOUSE => 'house_credit_rate',
    certificationTypeEnum::MOTORBIKE => 'motorbike_credit_rate',
    certificationTypeEnum::CAR => 'car_credit_rate',
    certificationTypeEnum::STORE=>'store_credit_rate'
)?>
<?php
$credit_grant = $output['credit_grant'];
$member_assets = $output['member_assets'];
$analysis = $output['analysis'];
$member_request = $analysis['member_request'];
$member_income = $analysis['income'];
$member_expense = $analysis['expense'];
$suggest_profile = $analysis['suggest'];
$product_list = $output['product_list'];
$client_loan_account_info = $output['client_loan_account_info'];
?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Committee</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan_committee', 'userVote', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>My Vote</span></a></li>
                <li><a class="current"><span>Vote</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <?php $client_info = $output['client_info']; $credit = memberClass::getCreditBalance($client_info['uid']);?>
            <div class="col-sm-12">
                <?php require_once template('widget/item.member.summary1'); ?>
            </div>
            <div class="col-sm-12" >
                <?php require_once template('widget/item.member.summary.relative'); ?>
            </div>

            <?php include(template("loan_committee/credit.application.vote.left")); ?>
            <div class="col-sm-6">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Vote For Qr-Code</h5>
                    </div>
                    <div class="content">
                        <table class="table">
                            <tr>
                                <td>
                                    <img id="qr-small" style="width:100px;height:100px;" src="<?php echo getUrl('loan_committee', 'getQrCode', array('url' => $output['vote_url']), false, BACK_OFFICE_SITE_URL) ?>">
                                </td>
                                <td>
                                    <div class="content voting-list">
                                        <table class="table">
                                            <thead>
                                            <tr class="table-header">
                                                <td>Committee Member</td>
                                                <td>Voting Result</td>
                                                <td>VoteTime</td>
                                                <td>Comment</td>
                                            </tr>
                                            </thead>
                                            <tbody class="table-body">
                                            <?php foreach ($output['member_credit_grant_vote'] as $v) { ?>
                                                <tr>
                                                    <td><?php echo $v['user_name']; ?></td>
                                                    <td><i class="fa fa-<?php echo $v['vote_result'] == commonApproveStateEnum::CREATE ? 'question' : ($v['vote_result'] == commonApproveStateEnum::PASS ? 'check' : 'close')?>"></i></td>
                                                    <td><?php echo $v['update_time']?></td>
                                                    <td><?php echo $v['vote_remark'] ?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document" style="width: 400px">
                        <div class="modal-content">
                            <img style="width: 400px;height: 400px;" src="<?php echo getUrl('loan_committee', 'getQrCode', array('url' => $output['vote_url']), false, BACK_OFFICE_SITE_URL) ?>">
                        </div>
                    </div>
                </div>
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Credit & Interest</h5>
                    </div>
                    <div class="content">
                        <table class="table table-hover table-bordered">
                            <tr class="table-header">
                                <td>Credit-Category</td>
                                <td>Repayment</td>
                                <td>Credit Terms</td>
                                <td>USD</td>
                                <td>KHR</td>
                                <td>Sub-Total-Credit</td>
                            </tr>
                            <?php if($credit_grant['grant_product']){?>
                                <?php foreach($credit_grant['grant_product'] as $prod_item){
                                    $category=$output['credit_category'][$prod_item['member_credit_category_id']];
                                    ?>
                                    <tr>
                                        <td><?php echo $category['alias']?></td>
                                        <td>
                                            <?php echo $category['sub_product_name']?>
                                            <br />
                                            <?php if( $category['interest_type'] == interestPaymentEnum::SEMI_BALLOON_INTEREST ){ ?>
                                                <kbd>Principal Period: <?php echo $client_loan_account_info['principal_periods']?$client_loan_account_info['principal_periods'].'M':'/'; ?></kbd>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $credit_grant['credit_terms'].'M'; ?></td>
                                        <td>
                                            <kbd><?php echo ncPriceFormat($prod_item['credit_usd'])?></kbd>
                                            <?php if($category['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                                <br/>
                                                Interest: <?php echo ncPriceFormat($prod_item['interest_rate'])?> %
                                                <br/>
                                                OP-Fee: <?php echo ncPriceFormat($prod_item['operation_fee'])?> %
                                                <br/>
                                                Loan-Fee: <?php echo ncPriceFormat($prod_item['loan_fee'])?> <?php if($prod_item['loan_fee_type']){echo '$';}else{echo '%';}?>
                                                <br/>
                                                Admin-Fee: <?php echo ncPriceFormat($prod_item['admin_fee'])?> <?php if($prod_item['admin_fee_type']){echo '$';}else{echo '%';}?>

                                            <?php }else{?>
                                                <br/>
                                                ServiceFee: N/A
                                                <br/>
                                                Annual-Fee: <?php echo ncPriceFormat($prod_item['annual_fee'])?> <?php if($prod_item['annual_fee_type']){echo '$';}else{echo '%';}?>
                                            <?php }?>


                                        </td>
                                        <td>
                                            <?php if($category['special_key']!=specialLoanCateKeyEnum::FIX_REPAYMENT_DATE){?>
                                                <kbd><?php echo ncPriceFormat($prod_item['credit_khr'])?></kbd>
                                                <br/>
                                                Interest: <?php echo ncPriceFormat($prod_item['interest_rate_khr'])?> %
                                                <br/>
                                                OP-Fee: <?php echo ncPriceFormat($prod_item['operation_fee_khr'])?> %
                                                <br/>
                                                Loan-Fee: <?php echo ncPriceFormat($prod_item['loan_fee_khr'])?> <?php if($prod_item['loan_fee_type']){echo 'khr';}else{echo '%';}?>
                                                <br/>
                                                Admin-Fee: <?php echo ncPriceFormat($prod_item['admin_fee_khr'])?> <?php if($prod_item['admin_fee_type']){echo 'khr';}else{echo '%';}?>

                                            <?php }else{?>

                                            <?php }?>

                                        </td>
                                        <td>
                                            <?php echo ncPriceFormat($prod_item['credit'])?>
                                        </td>
                                    </tr>



                                <?php }?>
                            <?php }else{?>
                                <tr>
                                    <td colspan="10">
                                        <?php include(template(":widget/no_record"))?>
                                    </td>
                                </tr>
                            <?php }?>
                        </table>

                    </div>
                </div>
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Vote Now</h5>
                    </div>
                    <div class="content">
                        <form class="form-horizontal" method="post" action="<?php echo getUrl('loan_committee', 'submitVoteCreditResult', array(), false, BACK_OFFICE_SITE_URL)?>">
                            <input type="hidden" name="form_submit" value="ok">
                            <input type="hidden" name="vote_state" value="<?php echo commonApproveStateEnum::PASS; ?>">
                            <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                            <input type="hidden" name="grant_id" value="<?php echo $credit_grant['uid']; ?>">
                            <table class="table" style="margin-top: 20px">
                                <tbody>
                                <tr>
                                    <td colspan="10">
                                        <textarea class="form-control" name="vote_remark" style="width: 100%;height: 100px" placeholder="Please Input Comment"></textarea>
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                                <?php if($credit_grant['is_append']){?>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <label style="color: red">Request To Append To Last Grant </label>
                                            <span> Expiry time is <?php echo $credit['expire_time']?:'?'; ?></span>
                                        </td>
                                    </tr>

                                <?php }?>

                                <tr>
                                    <td colspan="2" style="text-align: center">
                                        <button type="button" class="btn btn-primary" id="grant_submit" style="width: 30%;margin-right: 5px"><i class="fa fa-check"></i> <?php echo 'Agree' ?></button>
                                        <button type="button" class="btn btn-default" id="grant_reject" style="width: 30%;margin-right: 5px"><i class="fa fa-close"></i> <?php echo 'Reject' ?></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div style="margin-top:10px;margin-bottom: 30px" class="col-sm-12">
                <?php
                $source_mark = 'grant_committee';
                $grant_id=$credit_grant['uid'];
                ?>
                <?php include(template("widget/item.client.reference")); ?>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>

    var suggest_id = '<?php echo $credit_grant['uid']?>';
    $(document).ready(function () {
        setInterval(getVoteResult, 5000);
        $('#qr-small').click(function () {
            $('#qrModal').modal('show');
        })
    });

    $('#grant_submit').click(function () {
        if (!$(".form-horizontal").valid()) {
            return;
        }

        $('.form-horizontal').submit();
    })

    $('#grant_reject').click(function () {
        var _remark = $('textarea[name="vote_remark"]').val();
        if (!$.trim(_remark)) {
            alert('Please input the remark.');
            return;
        }
        $('input[name="vote_state"]').val(<?php echo commonApproveStateEnum::REJECT; ?>);
        $('.form-horizontal').submit();
    });

    function getVoteResult() {
        yo.dynamicTpl({
            tpl: "loan_committee/vote.result",
            dynamic: {
                api: "loan_committee",
                method: "getVoteResult",
                param: {suggest_id: suggest_id}
            },
            callback: function (_tpl) {
                $(".voting-list tbody").html(_tpl);
            }
        });
    }

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            vote_remark: {
                required: true
            }
        },
        messages: {
            vote_remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>