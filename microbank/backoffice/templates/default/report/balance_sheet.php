<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/report.css?v=5" rel="stylesheet" type="text/css"/>
<style>
    .lv0 {
        font-weight: bold;
    }

    .lv1 label {
        padding-left: 10px;
        font-weight: normal;
    }

    .lv2 label {
        padding-left: 20px;
        font-weight: normal;
    }

    .lv3 label {
        padding-left: 30px;
        font-weight: normal;
    }

    .amount {
        text-align: right;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Balance Sheet</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <table style="display: none;">
            <?php
            function render($arr, $lv = 0) {
                foreach ($arr as $k => $item) {
                ?>
                <tr class="lv<?php echo $lv ?>">
                    <td>
                        <label>
                            <?php if($item['redirect']){?>
                                <?php 
                                    switch ($item['redirect']) {
                                        case balanceSheetColumnRedirectTypeEnum::CASH_ON_HAND_CO :
                                            $herf = getUrl('report', 'showCashOnHandCoListPage', array(), false, BACK_OFFICE_SITE_URL);
                                            break;
                                        case balanceSheetColumnRedirectTypeEnum::CASH_ON_HAND_TELLER :
                                            $herf = getUrl('report', 'showCashOnHandTellerListPage', array(), false, BACK_OFFICE_SITE_URL);
                                            break;
                                        case balanceSheetColumnRedirectTypeEnum::CASH_ON_HAND_OTHER :
                                            $herf = getUrl('report', 'showCashOnHandOtherListPage', array(), false, BACK_OFFICE_SITE_URL);
                                            break;
                                        case balanceSheetColumnRedirectTypeEnum::CASH_IN_VAULT_HEADQUARTERS :
                                            $herf = getUrl('report', 'showCashInVaultHeadquarterListPage', array(), false, BACK_OFFICE_SITE_URL);
                                            break;
                                        case balanceSheetColumnRedirectTypeEnum::CASH_IN_VAULT_BRANCHES :
                                            $herf = getUrl('report', 'showCashInVaultBranchListPage', array(), false, BACK_OFFICE_SITE_URL);
                                            break;
                                        case balanceSheetColumnRedirectTypeEnum::RECEIVABLE_SHORT_TERM_PRINCIPAL :
                                            $herf = getUrl('report', 'showReceivableShortPrincipalListPage', array(), false, BACK_OFFICE_SITE_URL);
                                            break;
                                        case balanceSheetColumnRedirectTypeEnum::RECEIVABLE_LONG_TERM_PRINCIPAL :
                                            $herf = getUrl('report', 'showReceivableLongPrincipalListPage', array(), false, BACK_OFFICE_SITE_URL);
                                            break;
                                        case balanceSheetColumnRedirectTypeEnum::LIABILITY_SAVINGS :
                                            $herf = getUrl('report', 'showLiabilitySavingsListPage', array(), false, BACK_OFFICE_SITE_URL);
                                            break;
                                        default:
                                            $herf = '';
                                            break;
                                    }       
                                ?>
                                <?php if(!$herf){echo $k; }else{  ?>
                                    <a href="<?php echo $herf;  ?>"><?php echo $k;  ?></a>
                                <?php }?>
                            <?php }elseif($item['type'] == 'bank'){ ?>
                                <a href="<?php echo getUrl('report', 'showBankFlowPage', array('uid'=>$item['uid'],'pid'=>$item['pid']), false, BACK_OFFICE_SITE_URL)?>"><?php echo $k;  ?></a>
                            <?php }else{ ?>
                                <?php echo $k;  ?>
                            <?php }?>
                        </label>
                    </td>
                    <td class="amount c-usd"><em><?php echo ncPriceFormat($item['amount']['USD']) ?></em></td>
                    <td class="amount c-khr"><em><?php echo ncPriceFormat($item['amount']['KHR']) ?></em></td>
                </tr>
            <?php
                    if ($item['children']) render($item['children'], $lv+1);
                }
            }
            ?>
        </table>
        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-money"></i>Assets</h5>
                </div>
                <div class="content">
                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="c-usd">USD</th>
                            <th class="c-khr">KHR</th>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php render($output['data']['assets']['children']) ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-money"></i>Liabilities &amp; Equities</h5>
                </div>
                <div class="content">
                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="c-usd">USD</th>
                            <th class="c-khr">KHR</th>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php render($output['data']['liabilities_and_equities']['children']) ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>