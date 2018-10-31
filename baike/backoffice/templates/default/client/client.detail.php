<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=2" rel="stylesheet" type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.css?v=1" rel="stylesheet"/>
<style>
    .loan-exp {
        float: left;
        margin-left: 10px;
        position: relative;
        margin-top: 3px;
    }

    .loan-exp > span {
        color: #5b9fe2;
    }

    .loan-exp > span:hover {
        color: #ea544a;
    }

    .loan-exp-wrap {
        filter: alpha(Opacity=0);
        opacity: 0;
        -moz-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -o-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        -webkit-transition: top .2s ease-in-out, opacity .2s ease-in-out;
        transition: top .2s ease-in-out, opacity .2s ease-in-out;
        visibility: hidden;
        position: absolute;
        top: 24px;
        right: 10px;
        padding: 7px 10px;
        border: 1px solid #ddd;
        background-color: #f6fcff;
        color: #5b9fe2;
        font-size: 12px;
        font-family: Arial, "Hiragino Sans GB", simsun;
    }

    .loan-exp-hover .loan-exp-wrap {
        filter: alpha(enabled=false);
        opacity: 1;
        visibility: visible;
    }

    .loan-exp-wrap .pos {
        position: relative;
    }

    .loan-exp-table .t {
        color: #a5a5a5;
        font-size: 12px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a {
        color: #000;
        font-size: 18px;
        font-weight: 400;
        text-align: left;
    }

    .loan-exp-table .a .y {
        color: #ea544a;
    }

    .triangle-up {
        left: auto!important;
        right: 30px;
    }

    .loan-exp-table .t {
        height: 20px;
    }

    .loan-exp-table .a {
        font-size: 14px;
        height: 30px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('client', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $item = $output['detail'];
        $addr = $output['addr'];
        $credit_info = $output['credit_info']; ?>
        <input type="hidden" name="credit" id="credit" value="<?php echo $item['credit'] ?: 0; ?>">
        <input type="hidden" name="balance" id="balance" value="<?php echo memberClass::getCreditBalance($item['uid'])->DATA['balance'] ?: 0; ?>">
        <div class="client-detail-wrap clearfix">
            <div class="clearfix">
                <dl class="account-basic clearfix">
                    <dt class="pull-left">
                    <p class="account-head">
                        <img src="<?php echo getImageUrl($item['member_icon'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg' ?>" class="avatar-lg">
                    </p>
                    </dt>
                    <dd class="pull-left margin-large-left" style="padding: 11px 0 12px">
                        <p class="text-small">
                            <span class="show pull-left base-name marginright10">CID</span>:
                            <span class="marginleft10"><?php echo $item['obj_guid']; ?></span>
                            <input type="hidden" name="obj_guid" id="obj_guid" value="<?php echo $item['obj_guid']; ?>">
                            <input type="hidden" name="uid" id="uid" value="<?php echo $item['uid']; ?>">
                        </p>

                        <p class="text-small">
                            <span class="show pull-left base-name marginright10">Login Account</span>:
                            <span class="marginleft10"><?php echo $item['login_code']; ?></span>
                        </p>

                        <p class="text-small">
                            <span class="show pull-left base-name marginright10">Phone</span>:
                            <span class="marginleft10"><?php echo $item['phone_id']; ?></span>
                        </p>

                        <p class="text-small">
                            <span class="show pull-left base-name marginright10">Status</span>:
                            <span class="marginleft10"><?php echo $lang['client_member_state_' . $item['member_state']]; ?></span>
                        </p>

                        <p class="text-small">
                            <span class="show pull-left base-name marginright10">Member Grade</span>:<span
                                class="marginleft10"><?php echo $item['member_grade']?:''; ?></span>
                        </p>

<!--                        <p class="text-small">-->
<!--                            <span class="show pull-left base-name marginright10">Address</span>:<span class="marginleft10">--><?php //echo $addr; ?><!--</span>-->
<!--                        </p>-->
                    </dd>
                </dl>
                <dl class="credit-basic clearfix">
                    <dt class="pull-left">
                    <div class="fact-data fact-data-1">
                        <div class="epie-chart easyPieChart" data-percent="45" style="width: 120px; height: 120px;">
                            <div class="credit-lan">
                                <p class="base-name">Credit Balance</p>

                                <p class="balance"><?php echo $credit_info['balance']; ?></p>
                            </div>
                            <canvas id="myCanvas" width="130" height="130"></canvas>
                        </div>
                    </div>
                    </dt>
                    <dd class="pull-left margin-large-left">
                        <h5 class="text-small" style="margin-top: 12px;">
                            <span class="show pull-left base-name marginright10">Credit</span>:<span
                                class="marginleft10"><?php echo $credit_info['credit']; ?></span>
                        </h5>
                        <h5 class="text-small">
                            <span class="show pull-left base-name marginright10">Credit Balance</span>:<span
                                class="marginleft10"><?php echo $credit_info['balance']; ?></span>
                        </h5>
                        <h5 class="text-small">
                            <span class="show pull-left base-name marginright10">Loan Balance</span>:<span
                                class="marginleft10"><?php echo memberClass::getLoanBalance($item['uid'])->DATA ?: '0.00'; ?></span>
                        </h5>

                        <div class="custom-btn-group approval-btn-group" style="margin-top: 5px;">
                            <!--<button type="button" class="btn btn-info"
                                    onclick="javascript:window.location.href='<?php echo getUrl('loan', 'editCredit', array('obj_guid' => $item['obj_guid'], 'show_menu' => 'loan-credit'), false, BACK_OFFICE_SITE_URL) ?>'">
                                <i class="fa fa-edit"></i>Edit Credit
                            </button>-->
                            <button type="button" class="btn btn-info"
                                    onclick="javascript:window.location.href='<?php echo getUrl('client', 'creditReport', array('obj_guid' => $item['obj_guid']), false, BACK_OFFICE_SITE_URL) ?>'">
                                <i class="fa fa-vcard-o"></i>Report
                            </button>
                        </div>
                    </dd>
                </dl>
                <dl class="assets-basic clearfix">
                    <dt>
                        <div class="assets-info">
                            <div class="info-detail">
                                <?php $currency = (new currencyEnum())->Dictionary();?>
                                <?php foreach ($output['savings_balance'] as $k => $v) {?>
                                    <?php if($currency[$k]){?>
                                        <div class="item item-<?php echo strtolower($k);?>">
                                            <a href="<?php echo getUrl('client', 'clientSavingsBalanceFlow', array('uid'=>$item['uid'],'currency'=>$k), false, BACK_OFFICE_SITE_URL) ?>"><span class="p"><?php echo $k;?>: <?php echo ncPriceFormat($v);?></span></a>
                                        </div>
                                    <?php }?>
                                <?php }?>
                            </div>
                        </div>
                    </dt>
                </dl>
            </div>
            <div class="other-detail clearfix">
                <div class="pull-left">
                    <div class="verify-wrap">
                        <div>
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#information" aria-controls="information" role="tab" data-toggle="tab">Information</a>
                                </li>
                                <li role="presentation">
                                    <a href="#certificate" aria-controls="certificate" role="tab" data-toggle="tab">Certificate</a>
                                </li>
                                <li role="presentation">
                                    <a href="#supplementary" aria-controls="supplementary" role="tab" data-toggle="tab">Supplementary</a>
                                </li>
                                <li role="presentation">
                                    <a href="#occupation" aria-controls="occupation" role="tab" data-toggle="tab">Occupation</a>
                                </li>
                                <li role="presentation">
                                    <a href="#authority" aria-controls="authority" role="tab" data-toggle="tab">Authority</a>
                                </li>
                                <li role="presentation">
                                    <a href="#check_result" aria-controls="check_result" role="tab" data-toggle="tab">Check Result</a>
                                </li>
                            </ul>
                            <div class="tab-content client-verify-info verify-info">
                                <div role="tabpanel" class="tab-pane active" id="information">
                                    <table class="table">
                                        <tbody class="table-body">
                                        <tr>
                                            <td><label class="control-label">ID Number</label></td>
                                            <td><?php echo $item['id_sn']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">ID Type</label></td>
                                            <td><?php echo $item['id_type'] ? 'Abroad' : 'Domestic'; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">ID Expiry Date</label></td>
                                            <td><?php echo dateFormat($item['id_expire_time']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Date of Birth</label></td>
                                            <td><?php echo dateFormat($item['birthday']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Place Of Birth Province</label></td>
                                            <td><?php echo $addr[$item['id_address1']]['node_text']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Place Of Birth Country</label></td>
                                            <td><?php echo $addr[$item['id_address2']]['node_text']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Place Of Birth District</label></td>
                                            <td><?php echo $addr[$item['id_address3']]['node_text']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Place Of Birth Commune</label></td>
                                            <td><?php echo $addr[$item['id_address4']]['node_text']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Gender</label></td>
                                            <td><?php echo ucwords($item['gender']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Marital Status</label></td>
                                            <td><?php echo ucwords($item['civil_status']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Nationality</label></td>
                                            <td><?php echo strtoupper($item['nationality']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Is Government Employee</label></td>
                                            <td><?php echo $item['is_government'] ? 'YES' : 'NO'; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Is Staff</label></td>
                                            <td><?php echo $item['is_staff'] ? 'YES' : 'NO'; ?></td></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Residence</label></td>
                                            <td><?php echo $item['residence_place']['full_text'];?></td></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Create time</label></td>
                                            <td><?php echo timeFormat($item['create_time']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Open Source</label></td>
                                            <td><?php echo $lang['source_type_' . $item['open_source']] ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="certificate">
                                    <div class="activity-list">
                                        <?php $verify_field = $output['cert_type_lang']; $verifys = $output['verifys']; ?>
                                        <?php foreach ($verify_field as $key => $value) { ?>
                                            <div class="item">
                                                <div><?php echo $value; ?>
                                                    <span>
                                                        <?php if ($verifys[$key] == 1) { ?>
                                                            <i class="fa fa-check" onclick="showCheckDetail(<?php echo $item['uid']; ?>, <?php echo $key ?>)" aria-hidden="true" style="font-size: 18px;color:green;"></i>
                                                        <?php } else { ?>
                                                            <i class="fa fa-question" aria-hidden="true" style="font-size: 18px;color:red;"></i>
                                                        <?php } ?>
                                                            <i></i>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="supplementary">
                                    <div class="no-record">
                                        No Record
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="occupation">
                                    <?php if ($output['member_work']) { ?>
                                        <table class="table">
                                            <tbody class="table-body">
                                            <tr>
                                                <td><label class="control-label">Company Name</label></td>
                                                <td><?php echo $output['member_work']['company_name']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Company Address</label></td>
                                                <td><?php echo $output['member_work']['company_addr']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Position</label></td>
                                                <td><?php echo $output['member_work']['position']; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Is Government</label></td>
                                                <td><?php echo $output['member_work']['is_government'] ? 'YES' : 'NO'; ?></td>
                                            </tr>
                                            <tr>
                                                <td><label class="control-label">Remark of auditor</label></td>
                                                <td><?php echo $output['member_work']['verify_remark']; ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <div class="no-record">
                                            No Record
                                        </div>
                                    <?php } ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="authority">
                                    <div class="black-info">
                                        <div class="list clearfix">
                                            <?php $black = $output['black'];
                                            $count = count($black); ?>
                                            <?php if ($count > 0) { ?>
                                                <?php foreach ($black as $key => $val) {
                                                    $label = '';
                                                    $state = '';
                                                    $field = '';
                                                    switch ($val['type']) {
                                                        case blackTypeEnum::LOGIN :
                                                            $label = 'Login';
                                                            $state = $val['check'];
                                                            $type = $val['type'];
                                                            break;
                                                        case blackTypeEnum::DEPOSIT :
                                                            $label = 'Deposit';
                                                            $state = $val['check'];
                                                            $type = $val['type'];
                                                            break;
                                                        case blackTypeEnum::INSURANCE :
                                                            $label = 'Insurance';
                                                            $state = $val['check'];
                                                            $type = $val['type'];
                                                            break;
                                                        case blackTypeEnum::CREDIT_LOAN :
                                                            $label = 'Credit Loan';
                                                            $state = $val['check'];
                                                            $type = $val['type'];
                                                            break;
                                                        case blackTypeEnum::MORTGAGE_LOAN :
                                                            $label = 'Mortgage Loan';
                                                            $state = $val['check'];
                                                            $type = $val['type'];
                                                            break;
                                                        default:
                                                            $label = 'Login';
                                                            $state = $val['check'];
                                                            $type = $val['type'];
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="<?php echo $state == 1 ? 'disabled' : ''; ?>">
                                                        <i class="fa <?php echo $state == 1 ? 'fa-remove' : 'fa-check'; ?>"></i><?php echo $label; ?>
                                                        <!--<em onclick="_confirm(<?php echo $type; ?>, <?php echo $state  ? 0: 1; ?>);"><?php echo $state ? '<i class="fa fa-minus"></i>' : '<i class="fa fa-plus"></i>'; ?></em>-->
                                                     </span>
                                                <?php } ?>
                                                <?php } else { ?>
                                                <span><i class="fa fa-check"></i>登录<em
                                                        onclick="_confirm('<?php echo blackTypeEnum::LOGIN; ?>', 1);"><!--<i
                                                            class="fa fa-plus"></i></em>--></span>
                                                <span><i class="fa fa-check"></i>存款<em
                                                        onclick="_confirm('<?php echo blackTypeEnum::DEPOSIT; ?>', 1);"><!--<i
                                                            class="fa fa-plus"></i></em>--></span>
                                                <span><i class="fa fa-check"></i>保险<em
                                                        onclick="_confirm('<?php echo blackTypeEnum::INSURANCE; ?>', 1);"><!--<i
                                                            class="fa fa-plus"></i></em>--></span>
                                                <span><i class="fa fa-check"></i>信用贷<em
                                                        onclick="_confirm('<?php echo blackTypeEnum::CREDIT_LOAN; ?>', 1);"><!--<i
                                                            class="fa fa-plus"></i></em>--></span>
                                                <span><i class="fa fa-check"></i>抵押贷<em
                                                        onclick="_confirm('<?php echo blackTypeEnum::MORTGAGE_LOAN; ?>', 1);"><!--<i
                                                            class="fa fa-plus"></i></em>--></span>
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="check_result">
                                    <table class="table">
                                        <tbody class="table-body">
                                        <tr>
                                            <td><label class="control-label">Credit Officer</label></td>
                                            <td><?php echo $item['co_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Assets Valuation</label></td>
                                            <td>
                                                <div style="float: left"><em style="font-weight: 600;font-size: 18px;color: red"><?php echo ncAmountFormat($output['assets_valuation']); ?></em></div>
                                                <?php if ($output['assets_valuation_type']){ ?>
                                                    <div class="loan-exp">
                                                        <!--<span class="loan-plan-detail">Detail</span> -->
                                                        <div class="loan-exp-wrap">
                                                            <div class="pos">
                                                                <em class="triangle-up"></em>
                                                                <table class="loan-exp-table" style="width:300px;">
                                                                    <tr class="t">
                                                                        <td>Asset Type</td>
                                                                        <td>Valuation</td>
                                                                    </tr>
                                                                    <?php foreach($output['assets_valuation_type'] as $assets_valuation){?>
                                                                        <tr class="a">
                                                                            <td><?php echo $certificationTypeEnumLangLang[$assets_valuation['asset_type']]; ?></td>
                                                                            <td><?php echo ncAmountFormat($assets_valuation['assets_valuation']); ?></td>
                                                                        </tr>
                                                                    <?php }?>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Evaluate Time</label></td>
                                            <td><?php echo $output['evaluate_time']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Suggest Credit </label></td>
                                            <td><em style="font-weight: 600;font-size: 16px"><?php echo ncAmountFormat($output['credit_suggest']['suggest_credit']); ?></em></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Repayment Ability(Monthly)</label></td>
                                            <td><em style="font-weight: 600;font-size: 16px"><?php echo ncAmountFormat($output['credit_suggest']['monthly_repayment_ability']); ?></em></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Business Profitability</label></td>
                                            <td><span style="font-weight: 600;font-size: 16px"><?php echo 'None'; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Advise</label></td>
                                            <td><span><?php echo $output['credit_suggest']['remark']; ?></span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pull-right">
                    <div class="verify-wrap">
                        <div>
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#loan" aria-controls="loan" role="tab" data-toggle="tab">Loan</a>
                                </li>
                                <li role="presentation">
                                    <a href="#insurance" aria-controls="insurance" role="tab" data-toggle="tab">Insurance</a>
                                </li>
                                <li role="presentation">
                                    <a href="#savings" aria-controls="savings" role="tab" data-toggle="tab">Savings</a>
                                </li>
                            </ul>
                            <?php $contract_info = $output['contract_info'];
                            $loan_summary = $output['loan_summary'];
                            $guarantee_loan_summary = $output['guarantee_loan_summary']; ?>
                            <div class="tab-content client-verify-info">
                                <div role="tabpanel" class="tab-pane active" id="loan">
                                    <div class="contract-wrap">
                                        <div class="contract-info">
                                            <div class="item">
                                                All Enquiries
                                                <span class="t"><?php echo $contract_info['all_enquiries'] ?: '-'; ?></span>
                                            </div>
                                            <div class="item">
                                                Earliest Loan Issue Date
                                                <span class="t"><?php echo dateFormat($contract_info['earliest_loan_issue_date']) ?: '-'; ?></span>
                                            </div>
                                            <div class="item">
                                                Total Contracts <span class="t"><?php echo $loan_summary['contract_num_summary']['total_contracts']; ?></span>
                                            </div>
                                            <div class="item clearfix">
                                                <div class="d d1">
                                                    <em><?php echo $loan_summary['contract_num_summary']['normal_processing_contracts']; ?></em>Normal
                                                </div>
                                                <div class="d d2">
                                                    <em><?php echo $loan_summary['contract_num_summary']['delinquent_contracts']; ?></em>Delinquent
                                                </div>
                                                <div class="d d3">
                                                    <em><?php echo $loan_summary['contract_num_summary']['complete_contracts']; ?></em>Closed
                                                </div>
                                                <div class="d d4">
                                                    <em><?php echo $loan_summary['contract_num_summary']['rejected_contracts']; ?></em>Rejected
                                                </div>
                                                <div class="d d5">
                                                    <em><?php echo $loan_summary['contract_num_summary']['write_off_contracts']; ?></em>Write
                                                    off
                                                </div>
                                            </div>
                                            <div class="item">
                                                Total Limits <span
                                                    class="t"><?php echo $loan_summary['contract_amount_summary']['total_principal']; ?></span>
                                            </div>
                                            <div class="item">
                                                Total Liabilities <span
                                                    class="t"><?php echo $loan_summary['contract_amount_summary']['total_liabilities']; ?></span>
                                            </div>
                                            <div class="item">
                                                Total Write Off Amounts <span
                                                    class="t"><?php echo $loan_summary['contract_amount_summary']['total_write_off_amount']; ?></span>
                                            </div>
                                            <div class="item">
                                                Outstanding Write Off Balance <span
                                                    class="t"><?php echo $loan_summary['contract_amount_summary']['total_outstanding_write_off_balance']; ?></span>
                                            </div>
                                            <div class="item other">
                                                Guaranteed Contracts <span
                                                    class="t"><?php echo $guarantee_loan_summary['contract_num_summary']['total_contracts']; ?></span>
                                            </div>
                                            <div class="item other clearfix">
                                                <div class="d d1">
                                                    <em><?php echo $guarantee_loan_summary['contract_num_summary']['normal_processing_contracts']; ?></em>Normal
                                                </div>
                                                <div class="d d2">
                                                    <em><?php echo $guarantee_loan_summary['contract_num_summary']['delinquent_contracts']; ?></em>Delinquent
                                                </div>
                                                <div class="d d3">
                                                    <em><?php echo $guarantee_loan_summary['contract_num_summary']['complete_contracts']; ?></em>Closed
                                                </div>
                                                <div class="d d4">
                                                    <em><?php echo $guarantee_loan_summary['contract_num_summary']['rejected_contracts']; ?></em>Rejected
                                                </div>
                                                <div class="d d5">
                                                    <em><?php echo $guarantee_loan_summary['contract_num_summary']['write_off_contracts']; ?></em>Write
                                                    off
                                                </div>
                                            </div>
                                            <div class="item other">
                                                Total Guaranteed Limits <span
                                                    class="t"><?php echo $guarantee_loan_summary['contract_amount_summary']['total_principal']; ?></span>
                                            </div>
                                            <div class="item other">
                                                Total Guaranteed Liabilities <span
                                                    class="t"><?php echo $guarantee_loan_summary['contract_amount_summary']['total_liabilities']; ?></span>
                                            </div>
                                            <div class="item other">
                                                Total Guaranteed Write Off Amounts <span
                                                    class="t"><?php echo $guarantee_loan_summary['contract_amount_summary']['total_write_off_amount']; ?></span>
                                            </div>
                                            <div class="item other">
                                                Outstanding Guaranteed Write Off Balance <span
                                                    class="t"><?php echo $guarantee_loan_summary['contract_amount_summary']['total_outstanding_write_off_balance']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="insurance">
                                    <div class="contract-wrap">
                                        <?php $contracts = $output['insurance_contracts'];
                                        $count = count($contracts); ?>
                                        <div class="contract-list">
                                            <div class="">
                                                <div class="activity-list">
                                                    <?php if ($count > 0) { ?>
                                                        <?php foreach ($contracts as $key => $value) { ?>
                                                            <div class="item">
                                                                <div>
                                                                    <small class="pull-right text-navy"></small>
                                                                    <strong><?php echo $value['contract_sn'] ?></strong>

                                                                    <div><?php echo $value['product_name'] ?>&nbsp;&nbsp;&nbsp;<?php echo $value['currency'] ?>
                                                                        &nbsp;&nbsp;&nbsp;<?php echo $value['price'] ?></div>
                                                                    <div class="b">
                                                                        <small
                                                                            class="text-muted"><?php echo timeFormat($value['create_time']) ?></small>
                                                                        <?php $class;
                                                                        $label;
                                                                        switch ($value['state']) {
                                                                            case loanContractStateEnum::CREATE :
                                                                                $class = 'label-primary';
                                                                                $label = 'Create';
                                                                                break;
                                                                            case loanContractStateEnum::PROCESSING :
                                                                                $class = 'label-success';
                                                                                $label = 'Ongoing';
                                                                                break;
//                                          case loanContractStateEnum::FAILURE :
//                                            $class = 'label-danger';
//                                            $label = 'Failure';
//                                            break;
                                                                            case loanContractStateEnum::COMPLETE :
                                                                                $class = 'label-warning';
                                                                                $label = 'Complete';
                                                                                break;
                                                                            case loanContractStateEnum::WRITE_OFF :
                                                                                $class = 'label-default';
                                                                                $label = 'Write Off';
                                                                                break;
                                                                            default:
                                                                                $class = 'label-default';
                                                                                $label = 'Write Off';
                                                                                break;
                                                                        } ?>
                                                                        <span
                                                                            class="label <?php echo $class; ?>"><?php echo $label; ?></span>
                                                                        <a class="a-detail"
                                                                           href="<?php echo getUrl('insurance', 'contractDetail', array('uid' => 1, 'show_menu' => 'insurance-contract'), false, BACK_OFFICE_SITE_URL) ?>">Detail>></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <div class="no-record">
                                                            No Record
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="savings">
                                    <div class="no-record">
                                        coming soon
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=1"></script>
<script>
    var credit = $('#credit').val(), balance = $('#balance').val();
    var ring = parseFloat(balance) / parseFloat(credit) * 100;
    drawRing(130, 130, ring);
    function drawRing(w, h, val) {
        //先创建一个canvas画布对象，设置宽高
        var c = document.getElementById('myCanvas'), ctx = c.getContext('2d'), lineWidth = 8;
        ctx.canvas.width = w;
        ctx.canvas.height = h;
        //圆环有两部分组成，底部灰色完整的环，根据百分比变化的环
        //先绘制底部完整的环
        //起始一条路径
        ctx.beginPath();
        //设置当前线条的宽度
        ctx.lineWidth = lineWidth;
        //设置笔触的颜色
        ctx.strokeStyle = '#f1f1f1';
        //arc()方法创建弧/曲线（用于创建圆或部分圆）arc(圆心x,圆心y,半径,开始角度,结束角度)
        ctx.arc(65, 65, 57, 0, 2 * Math.PI);
        //绘制已定义的路径
        ctx.stroke();

        //绘制根据百分比变动的环
        ctx.beginPath();
        ctx.lineWidth = lineWidth;
        ctx.strokeStyle = '#E84F34';
        //设置开始处为0点钟方向（-90*Math.PI/180）
        //x为百分比值（0-100）
        ctx.arc(65, 65, 57, -90 * Math.PI / 180, (val * 3.6 - 90) * Math.PI / 180);
        ctx.stroke();
    }
    

    function showCheckDetail(member_id, cert_type) {
        if(!member_id || !cert_type){
            return;
        }

        yo.loadData({
            _c: 'client',
            _m: 'getCheckDetailUrl',
            param: {member_id: member_id, cert_type: cert_type, source_mark: 'client_detail'},
            callback: function (_o) {
                if (_o.STS) {
                    var url = _o.DATA;
                    window.location.href = url;
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>
