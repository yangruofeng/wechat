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
        /*background-color: #FFF;*/
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
)?>
<?php $credit_suggest = $output['credit_suggest']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Committee</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Vote</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="clearfix">
            <div class="col-sm-6">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Client Info</h5>
                    </div>
                    <?php $client_info = $output['client_info']; ?>
                    <div class="content">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Account</label></td>
                                <td><?php echo $client_info['login_code'] . '(' . $lang['client_member_state_' . $client_info['member_state']] . ')'; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Phone</label></td>
                                <td><?php echo $client_info['phone_id']; ?></td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Status</label></td>
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
                        <h5><i class="fa fa-id-card-o"></i>Vote For Qr-Code</h5>
                        <a href="#" onclick="resetTimer(<?php echo $credit_suggest['uid']?>)" style="float: right;text-decoration: none"><i class="fa fa-hourglass-start"></i>Reset</a>
                    </div>
                    <div class="content">
                        <table class="table">
                            <tr>
                                <td>
                                    <img id="qr-small" style="width:100px;height:100px;" src="<?php echo getUrl('loan_committee', 'getQrCode', array('url' => $output['vote_url']), false, BACK_OFFICE_SITE_URL) ?>">
                                </td>
                                <td>
                                    <div>
                                        Please complete the vote in five minutes, otherwise it will be invalid.
                                    </div>
                                    <div style="font-size: 40px;font-weight: 600">
                                        <span style="margin-right: 10px;font-size: 18px;font-weight: 400">Countdown: </span>
                                        <span class="hour">--</span>
                                        <span>: </span>
                                        <span class="minute">--</span>
                                        <span>: </span>
                                        <span class="second">--</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Credit Grant</h5>
                </div>
                <div class="content">
                    <table class="table">
                        <?php if ($credit_suggest['credit_request']) { ?>
                            <tr>
                                <td><label class="control-label">Client Request Credit</label></td>
                                <td>
                                    <em><?php echo ncAmountFormat($credit_suggest['credit_request']['credit']); ?></em>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Client Request terms</label></td>
                                <td>
                                    <?php echo $credit_suggest['credit_request']['terms'] . 'Months'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Client Request Interest</label></td>
                                <td>
                                    <?php echo $credit_suggest['credit_request']['interest_rate'] . '%'; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td><label class="control-label">Monthly Repayment Ability</label></td>
                            <td>
                                <em><?php echo ncAmountFormat($credit_suggest['monthly_repayment_ability']); ?></em>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Default Credit</label></td>
                            <td>
                                <em><?php echo ncAmountFormat($credit_suggest['default_credit']); ?></em>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Increase Credit By</label></td>
                            <td></td>
                        </tr>

                        <?php if ($credit_suggest['suggest_detail_list']) { ?>
                            <?php foreach($credit_suggest['suggest_detail_list'] as $val) {?>
                                <tr>
                                    <td>
                                        <span class="pl-25">
                                            <span><?php echo $val['asset_name']; ?></span>
                                            <span style="font-size: 12px;font-weight: 400">(<?php echo $certification_type[$val['asset_type']]; ?>)</span>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo ncAmountFormat($val['credit']) ?>
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
                                <em><?php echo ncAmountFormat($credit_suggest['max_credit']); ?></em>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Invalid Terms</label></td>
                            <td>
                                <?php echo $credit_suggest['credit_terms'] . 'Months'?>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Remark</label></td>
                            <td>
                                <?php echo $credit_suggest['remark']; ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <!--
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
                        <?php foreach ($output['product_list'] as $key => $product) { $suggest_rate = $credit_suggest['grant_rate'][$key]?>
                            <tr>
                                <td><?php echo $product['product_name']?></td>
                                <td><?php echo isset($suggest_rate['rate_no_mortgage']) ? ($suggest_rate['rate_no_mortgage'] . '%') : ''?></td>
                                <td><?php echo isset($suggest_rate['rate_mortgage1']) ? ($suggest_rate['rate_mortgage1'] . '%') : ''?></td>
                                <td><?php echo isset($suggest_rate['rate_mortgage2']) ? ($suggest_rate['rate_mortgage2'] . '%') : ''?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    -->
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
                            <td>VoteTime</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                            <?php foreach ($output['member_credit_grant_vote'] as $v) { ?>
                                <tr>
                                    <td><?php echo $v['user_name']; ?></td>
                                    <td><i class="fa fa-<?php echo $v['vote_result'] == 0 ? 'question' : ($v['vote_state'] == 10 ? 'close' : 'check')?>"></i></td>
                                    <td><?php echo $v['update_time']?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: center">
                                    <button type="button" class="btn btn-info" onclick="completeVote(<?php echo $credit_suggest['uid']?>)" style="width: 30%"><?php echo 'Complete Vote' ?></button>
                                    <a type="button" class="btn btn-default" href="<?php echo getUrl('loan_committee', 'reeditCreditApplication', array('uid' => $credit_suggest['uid']), false, BACK_OFFICE_SITE_URL) ?>" style="width: 30%"><?php echo 'Re-Edit' ?></a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 400px">
        <div class="modal-content">
            <img style="width: 400px;height: 400px;" src="<?php echo getUrl('loan_committee', 'getQrCode', array('url' => $output['vote_url']), false, BACK_OFFICE_SITE_URL) ?>">
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    var _second = <?php echo $output['countdown'];?>;
    _second = Number(_second);
    var suggest_id = '<?php echo $credit_suggest['uid']?>';
    var timer1 = null;
    $(function () {
        setInterval(getVoteResult, 3000);
        timer1 = window.setInterval(voteCountdown, 1000);

        $('#qr-small').click(function () {
            $('#qrModal').modal('show');
        })
    })

    function voteCountdown() {
        _second -= 1;
        if (_second < 0) {
            window.clearInterval(timer1);
            var hour = 0;
            var minute = 0;
            var second = 0;
        } else {
            var hour = parseInt(_second / 3600);
            var minute = parseInt((_second - 3600 * hour) / 60);
            var second = _second % 60;
        }
        if (hour < 10) {
            hour = '0' + hour;
        }
        if (minute < 10) {
            minute = '0' + minute;
        }
        if (second < 10) {
            second = '0' + second;
        }
        $('.hour').text(hour);
        $('.minute').text(minute);
        $('.second').text(second);
    }

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
                    alert(_o.MSG);
                    var data = _o.DATA;
                    if (data.is_fast_grant) {
                        var url = '<?php echo getUrl('loan_committee', 'fastGrantCredit', array(), false, BACK_OFFICE_SITE_URL) ?>';
                    } else {
                        var url = '<?php echo getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL) ?>';
                    }
                    setTimeout(function () {
                        window.location.href = url;
                    }, 2000);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function resetTimer(uid) {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan_committee",
            _m: "resetVoteTimer",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.href = '<?php echo getUrl('loan_committee', 'voteCreditApplication', array('uid' => $credit_suggest['uid']), false, BACK_OFFICE_SITE_URL) ?>';
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>