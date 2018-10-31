<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Cross Application</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Porfolio Summary Report</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'authorizedSystemUsers', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Authorized System Users</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'transactionListing', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Transaction</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'todayOpenAccounts', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Today Open Accounts</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'dailyTransactionListing', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Daily Transaction</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'summaryAccountByGL', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Summary Account By GL</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'GLAccountAndCustomerBalance', array(), false, BACK_OFFICE_SITE_URL)?>"><span>GL Account and Customer Balance</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'GLAccountMovement', array(), false, BACK_OFFICE_SITE_URL)?>"><span>GL Account Movement</span></a></li>
                <li><a href="<?php echo getUrl('report_cross_application', 'EndOfMonthAccountListing', array(), false, BACK_OFFICE_SITE_URL)?>"><span>End of Month Account</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <div>
                   <span class="label label-danger" style="font-size: 26px;">Sample</span>
                    <table class="table">
                        <thead>
                            <tr class="table-header">
                                <td>Description</td>
                                <td>Loan</td>
                                <td>Totals</td>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            <tr class="tr_odd">
                                <td>No. of Customers</td>
                                <td>630</td>
                                <td>630</td>
                            </tr>
                            <tr class="tr_even">
                                <td>No. of Accounts</td>
                                <td>359</td>
                                <td>359</td>
                            </tr>
                            <tr class="tr_odd">
                                <td>Amount Approved</td>
                                <td>2,375,489.00</td>
                                <td>2,375,489.00</td>
                            </tr>
                            <tr class="tr_even">
                                <td>Amount Disbursed</td>
                                <td>2,385,317.58</td>
                                <td>2,385,317.58</td>
                            </tr>
                            <tr class="tr_odd">
                                <td>Amount Undisbursed</td>
                                <td>-9,828.58</td>
                                <td>-9,828.58</td>
                            </tr>
                            <tr class="tr_even">
                                <td>Balance</td>
                                <td>2,229,212.57</td>
                                <td>N/A</td>
                            </tr>
                            <tr class="tr_odd">
                                <td>OverDue Balance</td>
                                <td>624.32</td>
                                <td>624.32</td>
                            </tr>
                            <tr class="tr_even">
                                <td>Accrued Credit Interest:</td>
                                <td>-</td>
                                <td>0.00</td>
                            </tr>
                            <tr class="tr_odd">
                                <td>Accrued Debit Interest</td>
                                <td>112.00</td>
                                <td>112.00</td>
                            </tr>
                            <tr class="tr_even">
                                <td>Overdue Int.</td>
                                <td>211.48</td>
                                <td>211.48</td>
                            </tr>
                            <tr class="tr_odd">
                                <td>Accrued Penalties</td>
                                <td>0.00</td>
                                <td>0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

