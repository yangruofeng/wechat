<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<?php include(template("print_form/inc.print.header"))?>
<?php $info = $output['deposit_info']; ?>

<div class="container" style="width: 770px">
    <div class="row">
        <div class="col-sm-7" style="padding-left: 70px">
            <div>
                <ul>
                    <li>
                        <p style="line-height: 50px;text-align: center;font-size: 18px;font-weight: bold">
                        Cash Deposit Voucher
                        </p>
                    </li>
                    <li>
                        <p style="text-align: center">
                            <?php echo $lang['print_cash_deposit_voucher']?>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-5" style="padding:0px 0px 0px 40px">
            <div>
                <table class="table table-no-background">
                    <tr>
                        <td>
                            <ul>
                                <li>
                                    Voucher No.
                                </li>
                                <li>
                                    <?php echo $lang['print_voucher_no']?>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <?php echo $info['uid']?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li>
                                    Voucher Time
                                </li>
                                <li>
                                    <?php echo $lang['print_voucher_time']?>
                                </li>
                            </ul>
                        </td>
                        <td>
                            <?php echo $info['update_time']?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row" style="min-height: 300px;padding-top: 30px">
        <table class="table table-no-background table-bordered">
            <tr>
                <td class="td-caption">
                    <ul>
                        <li>
                            Client Name
                        </li>
                        <li>
                            <?php echo $lang['print_client_name']?>
                        </li>
                    </ul>
                </td>
                <td class="td-content">
                    <?php echo $info['display_name']?>
                </td>
                <td class="td-caption">
                    <ul>
                        <li>
                            Client Account
                        </li>
                        <li>
                            <?php echo $lang['print_client_account']?>
                        </li>
                    </ul>
                </td>
                <td class="td-content">
                    <?php echo $info['obj_guid']?>
                </td>
                <td class="td-caption">
                    <ul>
                        <li>
                            Branch
                        </li>
                        <li>
                            <?php echo $lang['print_branch']?>
                        </li>
                    </ul>
                </td>
                <td class="td-content">
                    <?php echo $info['branch_name']?>
                </td>
            </tr>
            <tr>
                <td class="td-caption">
                    <ul>
                        <li>
                            Currency
                        </li>
                        <li>
                            <?php echo $lang['print_currency']?>
                        </li>
                    </ul>
                </td>
                <td class="td-content"><?php echo $info['currency']?></td>
                <td class="td-caption">
                    <ul>
                        <li>Amount</li>
                        <li> <?php echo $lang['print_amount']?></li>
                    </ul>
                </td>
                <td class="td-content">
                    <?php echo ncPriceFormat($info['amount'])?>
                </td>
                <td class="td-caption">
                    <ul>
                        <li>Amount In Word</li>
                        <li> <?php echo $lang['print_amount_in_word']?></li>
                    </ul>
                </td>
                <td class="td-content">
                    <?php echo formatNumberAsSpell($info['amount'])?>
                </td>
            </tr>
        </table>
        <div style="padding: 20px">
            <p>
                Note：This is to certify that the deposit took in our bank, can't used as mortgage.
            </p>
        </div>
        <table class="table table-no-background table-bordered">
            <tr>
                <td>Issued Date</td>
                <td>Period</td>
                <td>Value Date</td>
                <td>Expiry Date</td>
                <td>Interest Rate</td>
                <td>Security From</td>
                <td>Serial Number</td>
            </tr>
            <tr>
                <td>
                    <?php echo date('Y-m-d',strtotime($info['update_time']))?>
                </td>
                <td>
                    N/A
                </td>
                <td>
                    N/A
                </td>
                <td>
                    N/A
                </td>
                <td>
                    N/A
                </td>
                <td>
                    N/A
                </td>
                <td colspan="10">
                    <?php echo $info['passbook_trading_id']?>
                </td>
            </tr>
        </table>
    </div>
    <div class="row" style="padding-right: 50px;padding-left: 50px;padding-top: 20px;padding-bottom: 20px">
        <ul class="list-inline">
            <li style="width: 150px;text-align: right">
                <ul style="margin-left: -20px">
                    <li>
                        Accountant:
                    </li>
                    <li>
                        (<?php echo $lang['print_accountant']?>)
                    </li>
                </ul>
            </li>
            <li style="width: 150px;text-align: left;padding-left: 10px">
                <?php echo $info['cashier_name']?>
            </li>
            <li  style="width: 150px;text-align: right">
                <ul style="margin-left: -20px">
                    <li>Checker:</li>
                    <li>(<?php echo $lang['print_checker']?>)</li>
                </ul>
            </li>
            <li style="width: 150px;text-align: left;padding-left: 10px">
                <?php echo $info['bm_name']?>
            </li>
        </ul>
    </div>
</div>


