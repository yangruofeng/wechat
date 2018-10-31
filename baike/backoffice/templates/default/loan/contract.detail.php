<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=4" rel="stylesheet" type="text/css"/>
<style>
    .important-info .item {
        color: #fff;
        padding: 33px 0 37px;
        font-size: 14px;
        width: 32.5%;
    }

    .important-info .item:first-child {
        padding: 16px 0 37px;
    }

    .important-info .item:last-child {
        margin-right: 0;
    }

    .important-info .important-left {
        float: left;
        width: 56%;
    }

    .important-info .important-right {
        float: right;
        width: 43%;
        background-color: #fff;
    }

    .important-info .item .time {
        font-size: 30px;
    }

    .important-info .item a {
        color: #fff;
    }

    .important-info .item a:hover {
        text-decoration: none;
    }

    .important-info .text-small {
        margin: 0;
        line-height: 24px;
    }

    .important-info .repayment-blue {
        background-color: #00aced;
        border-color: #00aced;
    }

    .important-info .repayment-red {
        background-color: #e7505a;
        border-color: #e7505a;
    }

    .important-info .repayment-blue1 {
        background-color: #9358ac;
        border-color: #9358ac;
    }

    .important-info .repayment-fb1 {
        background-color: #355290;
        border-color: #355290;
    }

    .oprt-function {
        background: none !important;
        margin-top: 4px !important;
    }

    .oprt-function button {
        float: left;
        margin-right: 8px;
        padding: 5px 12px;
    }

    .oprt-function button:first-child {
        margin-bottom: 7px;
    }

    .wrap td em {
        font-style: normal;
        font-weight: 600;
    }

    .top-fold {
        display: none;
        float: right;
        color: #6c6cff;
    }

    .top-fold:hover {
        cursor: pointer;
    }

    .account-basic {
        margin-bottom: 0;
        padding: 12px 5px 5px 15px;
    }

    .account-basic dd {
        margin-left: 10px;
    }

    .loan-modify-penalties {
        margin-top: 4px;
        float: right;
    }

    .loan-modify-penalties a {
        color: #5b9fe2;
        cursor: pointer;
        text-decoration: none;
    }

    #modifyPenaltiesModal .modal-dialog ,#repaymentModal .modal-dialog {
        margin-top: 60px !important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if($output['source'] == 'client_detail') {?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li>
                        <a href="<?php echo getUrl('client', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                    </li>
                    <li><a onclick="javascript:history.go(-1)"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Contract Detail</span></a></li>
                </ul>
            <?php } elseif ($output['source'] == 'tools_client_detail') { ?>
                <h3>Search Client</h3>
                <ul class="tab-base">
                    <li><a onclick="javascript:history.go(-1)"><span>Search</span></a></li>
                    <li><a class="current"><span>Contract Detail</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>Contract</h3>
                <ul class="tab-base">
                    <li>
                        <a href="<?php echo getUrl('loan', 'contract', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                    </li>
                    <li><a class="current"><span>Detail</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <?php $item = $output['detail'];
    $insurance = $output['insurance']; ?>
    <div class="container">
        <div class="important-info clearfix">
            <div class="important-left">
                <div class="item repayment-blue">
                    <div class="time"><em
                            style="color: #fff;font-size: 12px;display: block;"><?php echo $item['currency']; ?></em> <?php echo ncPriceFormat($item['apply_amount']); ?>
                    </div>
                    <div class="name">Principal</div>
                </div>
                <div class="item repayment-red">
                    <div class="time"><?php echo ncPriceFormat($item['left_principal']); ?></div>
                    <div class="name">Pending Repayment</div>
                </div>
                <div class="item repayment-blue1">
                    <div class="time">
                        <?php if ($insurance[$item['uid']]['price']) { ?>
                            <!--<a href="<?php echo getUrl('insurance', 'contractDetail', array('uid' => $row['insurance_contract_id'], 'show_menu' => 'insurance-contract'), false, BACK_OFFICE_SITE_URL) ?>"><?php echo $row['price']; ?></a>-->
                            <a href="<?php echo getUrl('insurance', 'contract', array('show_menu' => 'insurance-contract'), false, BACK_OFFICE_SITE_URL) ?>"><?php echo ncPriceFormat($insurance[$item['uid']]['price']); ?></a>
                        <?php } else { ?>
                            0.00
                        <?php } ?>
                    </div>
                    <div class="name">Insurance Price</div>
                </div>
            </div>
            <div class="important-right">
                <dl class="account-basic clearfix">
                    <dt class="pull-left">
                    <p class="account-head">
                        <a title="<?php echo $item['display_name']?>" href="<?php echo getUrl('client', 'clientDetail', array('uid' => $item['member_id'], 'show_menu' => 'client-client'), false, BACK_OFFICE_SITE_URL) ?>">
                            <img src="<?php echo getImageUrl($item['member_icon'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg' ?>" class="avatar-lg" alt="">
                        </a>
                    </p>
                    </dt>
                    <dd class="pull-left">
                        <p class="text-small">
                            <span class="show pull-left base-name marginright10">Contract Sn</span>:
                            <span class="marginleft10"><?php echo $item['contract_sn']; ?></span>
                        </p>

                        <p class="text-small">
                            <span class="show pull-left base-name marginright10">Contract State</span>:
    						<span class="marginleft10">
                  <?php $label='';
                  switch ($item['state']) {
                      case loanContractStateEnum::CREATE :
                          $class = 'label-primary';
                          $label = 'Create';
                          break;
                      case loanContractStateEnum::PENDING_APPROVAL :
                          $class = 'label-success';
                          $label = 'Pending Approval';
                          break;
                      case loanContractStateEnum::PENDING_DISBURSE :
                          $class = 'label-success';
                          $label = 'Pending Disburse';
                          break;
                      case loanContractStateEnum::PROCESSING :
                          $class = 'label-success';
                          $label = 'Ongoing';
                          break;
                      case loanContractStateEnum::PAUSE :
                          $class = 'label-success';
                          $label = 'Pause';
                          break;
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
                          $label = 'Create';
                          break;
                  } ?>
                  <?php echo $label; ?>
    						</span>
                        </p>
                        <?php if($output['penalties_total'] > 0 && $item['state'] > loanContractStateEnum::PENDING_APPROVAL){?>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright10">Penalties Total</span>:
                                <span class="marginleft10"><?php echo ncPriceFormat($output['penalties_total']); ?></span>
                            </p>
                        <?php } else {?>
                            <p class="text-small">
                                <span class="show pull-left base-name marginright10">Member Name/Account</span>:
                                <span class="marginleft10">
                                    <a href="<?php echo getUrl('client', 'clientDetail', array('uid' => $item['member_id'], 'show_menu' => 'client-client'), false, BACK_OFFICE_SITE_URL) ?>"><?php echo $item['display_name'] ? $item['display_name'] : $item['login_code']; ?></a>
                                </span>
                            </p>
                        <?php }?>
                        <p class="text-small oprt-function clearfix">
                            <button type="button" class="btn btn-danger">Bill Collection</button>
                            <?php if($output['penalties_total'] > 0 && $item['state'] > loanContractStateEnum::PENDING_APPROVAL){?>
                                <button title="Modify Penalties" type="button" class="btn btn-default" onclick="modifyPenalties(<?php echo $output['detail']['uid'] ?>)">Penalties</button>
                            <?php } ?>
                        </p>
                    </dd>
                </dl>
            </div>
        </div>
        <div class="base-info clearfix">
            <div class="contract-base-info">
                <div class="ibox-title">
                    <h5>Contract Info</h5>
                    <span class="top-fold">Fold <i class="fa fa-angle-double-up"></i></span>
                </div>
                <div class="content fold clearfix">
                    <div class="displayall">
                        <span>Open</span><i class="fa fa-angle-double-down"></i>
                    </div>
                    <div class="wrap">
                        <table>
                            <tr>
                                <td>Contract Sn：</td>
                                <td><em><?php echo $item['contract_sn']; ?></em></td>
                            </tr>
                            <tr>
                                <td>Apply Amount：</td>
                                <td><em><?php echo ncPriceFormat($item['apply_amount']).$item['currency']; ?></em></td>
                            </tr>

                            <tr>
                                <td>Loan Time: </td>
                                <td><em><?php echo $item['loan_period_value'].' '.$item['loan_period_unit']; ?></em></td>
                            </tr>

                            <tr>
                                <td>Loan Days: </td>
                                <td><em><?php echo $item['loan_term_day']; ?></em></td>
                            </tr>

                            <tr>
                                <td>Start Date：</td>
                                <td><em><?php echo timeFormat($item['start_date']); ?></em></td>
                            </tr>
                            <tr>
                                <td>End Date：</td>
                                <td><em><?php echo timeFormat($item['end_date']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Repayment Type: </td>
                                <td>
                                    <em>
                                        <?php
                                        $type_lang = enum_langClass::getLoanInstallmentTypeLang();
                                        if( !interestTypeClass::isPeriodicRepayment($item['repayment_type']) ){
                                            echo $type_lang[$item['repayment_type']];
                                        }else{
                                            echo $type_lang[$item['repayment_type']].'('.$item['repayment_period'].')';
                                        }
                                        ?>
                                    </em>

                                </td>
                            </tr>

                            <tr>
                                <td>installment Frequencies：</td>
                                <td><em><?php echo $item['installment_frequencies']; ?></em></td>
                            </tr>

                            <tr>
                                <td>Interest Rate：</td>
                                <td><em><?php echo $item['interest_rate'].'%'.'('.$item['interest_rate_unit'].')'; ?></em></td>
                            </tr>

                            <tr>
                                <td>Operation Fee：</td>
                                <td><em><?php echo $item['operation_fee'].'%'.'('.$item['operation_fee_unit'].')'; ?></em></td>
                            </tr>

                            <tr>
                                <td>Admin Fee：</td>
                                <td><em><?php echo $item['admin_fee'].($item['admin_fee_type']==1?'':'%'); ?></em></td>
                            </tr>

                            <tr>
                                <td>Loan Fee:</td>
                                <td><em><?php echo $item['loan_fee'].($item['loan_fee_type']==1?'':'%'); ?></em></td>
                            </tr>

                            <tr>
                                <td>Penalty Rate：</td>
                                <td><em><?php echo $item['penalty_rate'].'%'.'('.$item['penalty_divisor_days'].' days)'; ?></em></td>
                            </tr>


                            <tr>
                                <td>Is Full Interest:</td>
                                <td><em><?php echo $item['is_full_interest']==1?'Yes':'No'; ?></em></td>
                            </tr>

                            <tr>
                                <td>Prepayment Interest:</td>
                                <td><em><?php echo $item['prepayment_interest'].($item['prepayment_interest_type']==1?'':'%'); ?></em></td>
                            </tr>

                            <tr>
                                <td>Grace Days:</td>
                                <td><em><?php echo $item['grace_days']; ?></em></td>
                            </tr>

                            <tr>
                                <td>Is Balloon Payment:</td>
                                <td><em><?php echo $item['is_balloon_payment']==1?'Yes':'No'; ?></em></td>
                            </tr>

                            <tr>
                                <td>Is Advance Interest:</td>
                                <td><em><?php echo $item['is_advance_interest']==1?'Yes':'No'; ?></em></td>
                            </tr>

                            <tr>
                                <td>Is Advance Annual Fee:</td>
                                <td><em><?php echo $item['is_advance_annual_fee']==1?'Yes':'No'; ?></em></td>
                            </tr>

                            <tr>
                                <td>Is First Repayment Annual Fee:</td>
                                <td><em><?php echo $item['is_first_repayment_annual_fee']==1?'Yes':'No'; ?></em></td>
                            </tr>

                            <tr>
                                <td>Is Insured:</td>
                                <td><em><?php echo $item['is_insured']==1?'Yes':'No'; ?></em></td>
                            </tr>

                        </table>
                    </div>
                    <div class="wrap">
                        <table>
                            <tr>
                                <td>State：</td>
                                <td>
                                    <?php $label='';
                                    switch ($item['state']) {
                                        case loanContractStateEnum::CREATE :
                                            $class = 'label-primary';
                                            $label = 'Create';
                                            break;
                                        case loanContractStateEnum::PENDING_APPROVAL :
                                            $class = 'label-success';
                                            $label = 'Pending Approval';
                                            break;
                                        case loanContractStateEnum::PENDING_DISBURSE :
                                            $class = 'label-success';
                                            $label = 'Pending Disburse';
                                            break;
                                        case loanContractStateEnum::PROCESSING :
                                            $class = 'label-success';
                                            $label = 'Ongoing';
                                            break;
                                        case loanContractStateEnum::PAUSE :
                                            $class = 'label-success';
                                            $label = 'Pause';
                                            break;
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
                                    <em><?php echo $label; ?></em>
                                </td>
                            </tr>

                            <tr>
                                <td>Receivable Principal：</td>
                                <td><em><?php echo ncPriceFormat($item['receivable_principal']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Receivable Interest：</td>
                                <td><em><?php echo ncPriceFormat($item['receivable_interest']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Receivable Operation Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['receivable_operation_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Receivable Admin Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['receivable_admin_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Receivable Loan Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['receivable_loan_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Receivable Annual Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['receivable_annual_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Receivable Insurance Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['receivable_insurance_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Receivable Penalty：</td>
                                <td><em><?php echo ncPriceFormat($item['receivable_penalty']); ?></em></td>
                            </tr>


                            <tr>
                                <td>Loss Principal：</td>
                                <td><em><?php echo ncPriceFormat($item['loss_principal']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Loss Interest：</td>
                                <td><em><?php echo ncPriceFormat($item['loss_interest']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Loss Operation Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['loss_operation_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Loss Admin Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['loss_admin_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Loss Loan Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['loss_loan_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Loss Annual Fee：</td>
                                <td><em><?php echo ncPriceFormat($item['loss_annual_fee']); ?></em></td>
                            </tr>

                            <tr>
                                <td>Loss Penalty：</td>
                                <td><em><?php echo ncPriceFormat($item['loss_penalty']); ?></em></td>
                            </tr>


                            <tr>
                                <td>Loan Cycle：</td>
                                <td><em><?php echo $item['loan_cycle']; ?></em></td>
                            </tr>

                            <tr>
                                <td>Creator Name：</td>
                                <td><em><?php echo $item['creator_name']?:'System'; ?></em></td>
                            </tr>

                            <tr>
                                <td>Create Time：</td>
                                <td><em><?php echo timeFormat($item['create_time']); ?></em></td>
                            </tr>


                            <tr>
                                <td>Finish Time：</td>
                                <td><em><?php echo timeFormat($item['finish_time']); ?></em></td>
                            </tr>


                        </table>
                    </div>
                </div>
                <div class="hiddenall" style="display: none;">
                    <span>Fold</span><i class="fa fa-angle-double-up"></i>
                </div>
            </div>
            <div class="product-info" style="min-height: 180px;">
                <div class="ibox-title">
                    <h5>Product Info</h5>
                </div>
                <div class="content">
                    <table>
                        <tr>
                            <td>Name：</td>
                            <td>
                                <?php echo $item['sub_product_name']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Code：</td>
                            <td><?php echo $item['sub_product_code']; ?></td>
                        </tr>

                       <!-- <tr>
                            <td>Feature：</td>
                            <td><?php /*echo $item['product_feature'] ?: 'None'; */?></td>
                        </tr>
                        <tr>
                            <td>Description：</td>
                            <td><?php /*echo $item['product_description'] ?: 'None'; */?></td>
                        </tr>-->
                    </table>
                </div>
            </div>
        </div>
        <div class="contract-plan clearfix">
            <div class="loan-plan">
                <div class="ibox-title">
                    <h5>Loan Disbursement</h5>
                </div>
                <div class="ibox-content">
                    <div class="activity-list">
                        <?php $disbursement = $output['disbursement']; ?>
                        <?php foreach ($disbursement as $key => $value) { ?>
                            <div class="item">
                                <div
                                    class="state-img <?php if ($value['state'] == schemaStateTypeEnum::COMPLETE) { ?>loan-done<?php } elseif ($value['state'] == schemaStateTypeEnum::GOING) { ?>loan-going<?php } ?>"></div>
                                <div class="period"><?php echo $value['scheme_idx']; ?></div>
                                <div class="clearfix">
                                    <div class="loan-amount">Amount： <em><?php echo $value['amount']; ?></em></div>
                                    <div class="loan-exp">
                                        <span class="loan-plan-detail">Detail</span>
                                        <div class="loan-exp-wrap">
                                            <div class="pos">
                                                <em class="triangle-up"></em>
                                                <table class="loan-exp-table">
                                                    <tr class="t">
                                                        <td>Amount</td>
                                                        <td></td>
                                                        <td>Principal</td>
                                                        <td></td>
                                                        <td>Annual Fee</td>
                                                        <td></td>
                                                        <td>Interest</td>
                                                        <td></td>
                                                        <td>Admin Fee</td>
                                                        <td></td>
                                                        <td>Loan Fee</td>
                                                        <td></td>
                                                        <td>Operation Fee</td>
                                                        <td></td>
                                                        <td>Insurance Fee</td>
                                                    </tr>
                                                    <tr class="a">
                                                        <td class="y"><?php echo $value['amount']; ?></td>
                                                        <td>&nbsp;=&nbsp;</td>
                                                        <td><?php echo $value['principal']; ?></td>
                                                        <td>&nbsp;-&nbsp;</td>
                                                        <td><?php echo $value['deduct_annual_fee']; ?></td>
                                                        <td>&nbsp;-&nbsp;</td>
                                                        <td><?php echo $value['deduct_interest']; ?></td>
                                                        <td>&nbsp;-&nbsp;</td>
                                                        <td><?php echo $value['deduct_admin_fee']; ?></td>
                                                        <td>&nbsp;-&nbsp;</td>
                                                        <td><?php echo $value['deduct_loan_fee']; ?></td>
                                                        <td>&nbsp;-&nbsp;</td>
                                                        <td><?php echo $value['deduct_operation_fee']; ?></td>
                                                        <td>&nbsp;-&nbsp;</td>
                                                        <td><?php echo $value['deduct_insurance_fee']; ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="loan-date">
                                    Disbursement Time： <?php echo timeFormat($value['disbursable_date']); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="repayment-plan">
                <div class="ibox-title">
                    <h5>loan-exp</h5>
                    <?php if ($item['state'] >= loanContractStateEnum::PENDING_DISBURSE && $item['state'] < loanContractStateEnum::COMPLETE){?>
                    <?php }?>
                </div>
                <div class="ibox-content">
                    <div class="activity-list">
                        <?php $installment = $output['installment']; ?>
                        <?php foreach ($installment as $key => $value) { ?>
                            <div class="item">
                                <div
                                    class="state-img <?php if ($value['state'] == 100) { ?>repayment-done<?php } elseif ($value['state'] == 10) { ?>repayment-going<?php } ?>"></div>
                                <div class="period"><?php echo $value['scheme_idx']; ?></div>
                                <div class="clearfix">
                                    <div class="loan-amount">Repayment Amount： <em><?php echo $value['amount']; ?></em>
                                    </div>
                                    <div class="loan-exp">
                                        <span class="loan-plan-detail">Detail</span>
                                        <div class="loan-exp-wrap">
                                            <div class="pos">
                                                <em class="triangle-up"></em>
                                                <table class="loan-exp-table">
                                                    <tr class="t">
                                                        <td>Amount</td>
                                                        <td></td>
                                                        <td>Principal</td>
                                                        <td></td>
                                                        <td>Interest</td>
                                                        <td></td>
                                                        <td>Admin Fee</td>
                                                        <td></td>
                                                        <td>Operation Fee</td>
                                                        <?php if ($value['penalties'] > 0) { ?>
                                                            <td></td>
                                                            <td>Penalties</td>
                                                        <?php } ?>
                                                    </tr>
                                                    <tr class="a">
                                                        <td class="y"><?php echo $value['amount']; ?></td>
                                                        <td>&nbsp;=&nbsp;</td>
                                                        <td><?php echo $value['receivable_principal']; ?></td>
                                                        <td>&nbsp;+&nbsp;</td>
                                                        <td><?php echo $value['receivable_interest']; ?></td>
                                                        <td>&nbsp;+&nbsp;</td>
                                                        <td><?php echo $value['receivable_admin_fee']; ?></td>
                                                        <td>&nbsp;+&nbsp;</td>
                                                        <td><?php echo $value['receivable_operation_fee']; ?></td>
                                                        <?php if ($value['penalties'] > 0) { ?>
                                                            <td>&nbsp;+&nbsp;</td>
                                                            <td><?php echo $value['penalties'] ?></td>
                                                        <?php } ?>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="loan-date">
                                    Repayment Time: <?php echo timeFormat($value['receivable_date']); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="loan-plan repayment-history" style="margin-top: 20px">
                <div class="ibox-title">
                    <h5>Repayment History</h5>
                </div>
                <div class="ibox-content">
                    <div class="activity-list">
                        <?php $repayment_history = $output['repayment_history']; ?>
                        <?php $i = 0;foreach ($repayment_history as $key => $value) { ++$i?>
                            <div class="item">
                                <div class="period"><?php echo $i; ?></div>
                                <div class="clearfix">
                                    <div class="loan-amount">Amount： <em><?php echo $value['amount']; ?></em></div>
<!--                                    <div class="loan-exp">-->
<!--                                        <span class="loan-plan-detail">Detail</span>-->
<!--                                        <div class="loan-exp-wrap">-->
<!--                                            <div class="pos">-->
<!--                                                <em class="triangle-up"></em>-->
<!--                                                <table class="loan-exp-table">-->
<!--                                                    <tr class="t">-->
<!--                                                        <td>Amount</td>-->
<!--                                                        <td></td>-->
<!--                                                        <td>Principal</td>-->
<!--                                                        <td></td>-->
<!--                                                        <td>Annual Fee</td>-->
<!--                                                        <td></td>-->
<!--                                                        <td>Interest</td>-->
<!--                                                        <td></td>-->
<!--                                                        <td>Admin Fee</td>-->
<!--                                                        <td></td>-->
<!--                                                        <td>Operation Fee</td>-->
<!--                                                    </tr>-->
<!--                                                    <tr class="a">-->
<!--                                                        <td class="y">--><?php //echo $value['amount']; ?><!--</td>-->
<!--                                                        <td>&nbsp;=&nbsp;</td>-->
<!--                                                        <td>--><?php //echo $value['principal']; ?><!--</td>-->
<!--                                                        <td>&nbsp;-&nbsp;</td>-->
<!--                                                        <td>--><?php //echo $value['deduct_annual_fee']; ?><!--</td>-->
<!--                                                        <td>&nbsp;-&nbsp;</td>-->
<!--                                                        <td>--><?php //echo $value['deduct_interest']; ?><!--</td>-->
<!--                                                        <td>&nbsp;-&nbsp;</td>-->
<!--                                                        <td>--><?php //echo $value['deduct_admin_fee']; ?><!--</td>-->
<!--                                                        <td>&nbsp;-&nbsp;</td>-->
<!--                                                        <td>--><?php //echo $value['deduct_operation_fee']; ?><!--</td>-->
<!--                                                    </tr>-->
<!--                                                </table>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                </div>
                                <div class="loan-date">
                                    Repayment Time： <?php echo timeFormat($value['create_time']); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modifyPenaltiesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Modify Penalties'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">

                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_penalties()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="repaymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Repayment'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="repayment_form">
                        <input name="uid" value="<?php echo $item['uid']?>" type="hidden">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span
                                    class="required-options-xing"></span><?php echo 'Contract Sn' ?></label>

                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="<?php echo $item['contract_sn']?>" id="scheme_name" readonly>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span
                                    class="required-options-xing"></span><?php echo 'Expired Repayment'; ?></label>
                            <div class="col-sm-9">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr class="table-header" style="background: #EFEFEF">
                                        <td>Scheme Name</td>
                                        <td>Repayment Total</td>
                                        <td>Principal And Interest</td>
                                        <td>Penalties</td>
                                    </tr>
                                    </thead>
                                    <tbody class="table-body">
                                    <?php if(!$output['repayment_arr']){?>
                                        <tr>
                                            <td colspan="4">Null</td>
                                        </tr>
                                    <?php }else{ ?>
                                    <?php
                                    $repayment_total = 0;
                                    $principal_interest_total = 0;
                                    $penalties_total = 0;
                                    foreach ($output['repayment_arr'] as $scheme){?>
                                        <tr>
                                            <td>
                                                <?php echo $scheme['scheme_name']?>
                                            </td>
                                            <td>
                                                <?php $repayment = $scheme['amount'] + $scheme['penalties'] - $scheme['actual_payment_amount'];
                                                $repayment_total += $repayment;
                                                echo ncAmountFormat($repayment)?>
                                            </td>
                                            <td>
                                                <?php $principal_interest = ($scheme['amount'] - $scheme['actual_payment_amount']) > 0 ? $scheme['amount'] - $scheme['actual_payment_amount'] : 0;
                                                $principal_interest_total += $principal_interest;
                                                echo ncAmountFormat($principal_interest)?>
                                            </td>
                                            <td>
                                                <?php $penalties_total += $scheme['penalties'];
                                                echo ncAmountFormat($scheme['penalties'])?>
                                            </td>
                                        </tr>
                                    <?php }?>
                                    <tr style="font-weight: 700">
                                        <td>
                                            <?php echo 'Total' ?>
                                        </td>
                                        <td>
                                            <?php echo ncAmountFormat($repayment_total) ?>
                                        </td>
                                        <td>
                                            <?php echo ncAmountFormat($principal_interest_total) ?>
                                        </td>
                                        <td>
                                            <?php echo ncAmountFormat($penalties_total) ?>
                                        </td>
                                    </tr>
                                    <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Repayment Total' ?></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="repayment_total" value="" style="width: 373px;">
                                    <select class="form-control" name="currency" id="" style="width: 120px;">
                                        <?php $cy_list = (new currencyEnum())->toArray(); foreach( $cy_list as $currency ){ ?>
                                            <option value="<?php echo $currency; ?>" <?php if($currency == currencyEnum::USD ){ echo 'selected';} ?> ><?php echo $currency; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Remark' ?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="" name="remark">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="submit_repayment()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        var height = $('.product-info').height();
        $('.contract-base-info .content').height(height - 40);
        $('.displayall').click(function () {
            $('.contract-base-info .content').height('auto');
            $('.contract-base-info .content').removeClass('fold');
            $('.displayall').css('display', 'none');
            $('.hiddenall').css('display', 'block');
            $('.top-fold').css('display', 'block');
        });
        $('.hiddenall,.top-fold').click(function () {
            $('.contract-base-info .content').height(height);
            $('.contract-base-info .content').addClass('fold');
            $('.displayall').css('display', 'block');
            $('.hiddenall').css('display', 'none');
            $('.top-fold').css('display', 'none');
        });

        $('.loan-exp').hover(function () {
            $(this).addClass('loan-exp-hover');
        }, function () {
            $(this).removeClass('loan-exp-hover');
        });
    });

    function modifyPenalties(uid) {
        uid = parseInt(uid);
        if (!uid) {
            return;
        }

        yo.dynamicTpl({
            tpl: "loan/penalties.form",
            dynamic: {
                api: "loan",
                method: "modifyPenalties",
                param: {uid: uid}
            },
            callback: function (_tpl) {
                $("#modifyPenaltiesModal .modal-form").html(_tpl);
                $('#modifyPenaltiesModal').modal('show');
            }
        })
    }

    $('#modifyPenaltiesModal').delegate('#deducting_penalties','change',function(){
        var deducting_penalties = $(this).val();
        if(Number(deducting_penalties) <= penalties_total){
            $(this).closest('.form-group').find('.error_msg').html('');
            var remaining_penalties = penalties_total - deducting_penalties;
            $("#remaining_penalties").val(remaining_penalties.toFixed(2));
        } else {
            $(this).closest('.form-group').find('.error_msg').html('It can\'t be greater than penalties total!');
        }
    })

    function save_penalties() {
        var values = $('#penalty_form').getValues();
        if (!values.uid || values.deducting_penalties > penalties_total) {
            return;
        }

        yo.loadData({
            _c: "loan",
            _m: "savePenaltiesApply",
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $('#modifyPenaltiesModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function repayment_btn() {
        $('#repaymentModal').modal('show');
    }

    function submit_repayment() {
        if (!$("#repayment_form").valid()) {
            return;
        }

        var values = $("#repayment_form").getValues();

        yo.loadData({
            _c: "loan",
            _m: "submitRepayment",
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#repayment_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            repayment_total: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            repayment_total: {
                required: '<?php echo 'Required!'?>'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

</script>
