<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    em {
        font-weight: 500;
        font-size: 15px;
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
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

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

</style>
<?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
<?php $cert_type = array(
    certificationTypeEnum::LAND => 'land_credit_rate',
    certificationTypeEnum::HOUSE => 'house_credit_rate',
    certificationTypeEnum::MOTORBIKE => 'motorbike_credit_rate',
    certificationTypeEnum::CAR => 'car_credit_rate',
)?>
<?php $credit_suggest = $output['credit_suggest']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Committee</h3>
            <ul class="tab-base">
                <li><a onclick="javascript:history.back(-1)"><span>Back</span></a></li>
                <li><a class="current"><span>Credit Grant</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $client_info = $output['client_info'];?>
        <?php $member_id = $client_info['uid'];?>
        <div class="col-sm-12">
            <?php require_once template('widget/item.member.summary1'); ?>
        </div>

        <div class="col-sm-12" >
            <?php require_once template('widget/item.member.summary.relative'); ?>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Credit Grant</h5>
                </div>
                <div class="content">
                    <?php
                    $credit_grant=$output['credit_grant'];
                    $credit_category=$output['credit_category'];
                    $member_assets=$output['member_assets'];
                    include(template("widget/inc.credit.grant.detail"));

                    ?>
                </div>

            </div>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Vote</h5>
                </div>

                <div class="content voting-list">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td>Committee Member</td>
                            <td>Voting Result</td>
                            <td>Remark</td>
                            <td>VoteTime</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                            <?php foreach ($output['member_credit_grant_vote'] as $v) { ?>
                                <tr>
                                    <td><?php echo $v['user_name']; ?></td>
                                    <td><i class="fa fa-<?php echo $v['vote_result'] == 0 ? 'question' : ($v['vote_result'] == commonApproveStateEnum::PASS ? 'check' : 'close')?>"></i></td>
                                    <td><?php echo $v['vote_remark']?></td>
                                    <td><?php echo $v['update_time']?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($credit_suggest['state'] == 100) { ?>
        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Authorized Contract</h5>
                </div>
                <div class="content">
                    <table class="table">
                        <?php if ($credit_suggest['authorized_contract']) { $authorized_contract = $credit_suggest['authorized_contract']?>
                            <tr>
                                <td><label class="control-label">Authorized Branch</label></td>
                                <td>
                                    <?php echo $authorized_contract['branch_name']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Total Credit</label></td>
                                <td>
                                    <?php echo ncPriceFormat($authorized_contract['total_credit']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Loan Fee</label></td>
                                <td>
                                    <?php echo ncPriceFormat($authorized_contract['loan_fee_amount']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Admin Fee</label></td>
                                <td>
                                    <?php echo ncPriceFormat($authorized_contract['admin_fee_amount']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Operator</label></td>
                                <td>
                                    <?php echo $authorized_contract['update_operator_name'] ?: $authorized_contract['officer_name']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Time</label></td>
                                <td>
                                    <?php echo timeFormat($authorized_contract['update_time']); ?>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td>Unauthorized</td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php }?>

        <div style="margin-top:10px;margin-bottom: 30px" class="col-sm-12">
            <?php
            $source_mark = 'grant_committee';
            $grant_id=$credit_grant['uid']?:0;
            ?>
            <?php include(template("widget/item.client.reference")); ?>
        </div>
    </div>
</div>
<script>
    function completeVote(uid) {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan_committee",
            _m: "completeVoteCreditApplication",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        var data = _o.DATA;
                        if (data.is_fast_grant) {
                            var url = '<?php echo getUrl('loan_committee', 'fastGrantCredit', array(), false, BACK_OFFICE_SITE_URL) ?>';
                        } else {
                            var url = '<?php echo getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL) ?>';
                        }
                        window.location.href = url;
                    });

                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }
</script>
