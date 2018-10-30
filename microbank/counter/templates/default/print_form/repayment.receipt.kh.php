<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    body{
        width: 1100px;
        margin: 20px auto;
        font-size: 15px;
    }

    .down_line{
        position: absolute;
        height: 20px;
        top:90px
        display: block;
        border-bottom: 1px dashed black;
        padding:5px;
        line-height: 5px;
    }
    td{
        border: 1px solid black;
        padding: 5px;
    }
</style>
<?php $client_info = $output['client_info'];?>
<?php $biz_info = $output['biz_info'];?>

<div>
    <div style="position: relative">
        <img src="resource/img/login/c-logo.png" style="position:absolute;top:6px;left: 15px">
        <p style="margin-left: 110px"><span style="font-size: 19px;font-weight: 500;letter-spacing:10px;">គ្រឹះស្ថានមីក្រូហិរញ្ញវត្ថុ សំរឹទ្ធិស័ក លីមីតធីត</span><span style="margin-left: 35px"><?php echo $lang['print_date']; ?>:</span><span class="down_line" style="width: 230px;left:768px;padding-left: 3px"><?php echo dateFormat($biz_info['update_time'], '-') ?></span></p>
        <p><span style="margin-left: 130px;font-weight: 400;font-size: 17px;"><?php echo $lang['print_samrithisak_microfinance_limited']; ?></span><span style="margin-left: 360px"><?php echo $lang['print_exchange_rate']; ?>:</span><span class="down_line" style="width: 185px;left:815px"> 1 USD = <?php echo $output['exchange_rate']?> KHR</span></p>
        <p style="font-size: 18px;font-weight:600;margin-top: 45px;margin-left: 180px;"><?php echo $lang['print_cash_receipt_voucher']; ?></p>
    </div>
    <div class="col-sm-12" style="padding:10px">
        <div class="col-sm-6" style="padding:0px">
            <table class="col-sm-11">
                <tr>
                    <td class="col-sm-4" style="padding: 5px"><?php echo $lang['print_ac_number']; ?></td>
                    <td class="col-sm-8" style="padding: 5px"><?php echo implode('/',$biz_info['contract_sn_list']);?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_client_name']; ?></td>
                    <td><?php echo $client_info['login_code']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_principal']; ?></td>
                    <td><?php echo $biz_info['total_principal']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_interest']; ?></td>
                    <td><?php echo $biz_info['total_interest']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_operation_fees']; ?></td>
                    <td><?php echo $biz_info['total_operation_fee']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_penalty']; ?></td>
                    <td><?php echo $biz_info['total_penalty']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_other_interest']; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_fee_on_loan']; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_admin_loan']; ?></td>
                    <td><?php echo $biz_info['total_admin_fee']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_cbc_fee']; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_total_amount']; ?></td>
                    <td><?php echo $biz_info['total_amount']?></td>
                </tr>
                <tr style="height: 80px;">
                    <td colspan="2" valign="top"><?php echo $lang['print_amount_in_words']; ?>:<br/>
                        <?php
                        $words=formatNumberAsSpell($biz_info['total_amount']);
                        echo $words;
                        ?>
                    </td>
                </tr>

            </table>
        </div>

        <div class="col-sm-6" style="padding:0px;margin-top: -60px">
            <table class="col-sm-10">
                <tr>
                    <td colspan="2" style="text-align: center"><?php echo $lang['print_currency']; ?><input type="checkbox" style="margin-left:20px"/> KHR  <input type="checkbox" style="margin-left:15px"/> USD </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;font-weight: 600"><?php echo $lang['print_denomination']; ?></td>
                </tr>
                <tr style="height:120px">
                    <td class="col-sm-6" style="padding: 3px">
                        <p style="text-align: center">KHR</p>
                        <p>100,000<span>*................=.......................</span></p>
                        <p>50,000<span style="margin-left: 8px">*................=.......................</span></p>
                        <p>20,000<span style="margin-left: 8px">*................=.......................</span></p>
                        <p>10,000<span style="margin-left: 8px">*................=.......................</span></p>
                        <p>5,000<span style="margin-left: 16px">*................=.......................</span></p>
                        <p>2,000<span style="margin-left: 16px">*................=.......................</span></p>
                        <p>1,000<span style="margin-left: 16px">*................=.......................</span></p>
                        <p>500<span style="margin-left: 28px">*................=.......................</span></p>
                        <p>100<span style="margin-left: 28px">*................=.......................</span></p>
                        <p>50<span style="margin-left: 36px">*................=.......................</span></p>
                    </td>
                    <td style="border-bottom: none">
                        <p style="margin-top: -90px;text-align: center">USD</p>
                        <p>100<span>*..................=.............................</span></p>
                        <p>50<span style="margin-left: 8px">*..................=.............................</span></p>
                        <p>20<span style="margin-left: 8px">*..................=.............................</span></p>
                        <p>10<span style="margin-left: 8px">*..................=.............................</span></p>
                        <p>5<span style="margin-left: 16px">*..................=.............................</span></p>
                        <p>2<span style="margin-left: 16px">*..................=.............................</span></p>
                        <p>1<span style="margin-left: 16px">*..................=.............................</span></p>
                    </td>
                </tr>
                <tr style="height: 80px;">
                    <td valign="top"><p style="padding-top: 12px"><?php echo $lang['print_total']; ?>= .............................................</p></td>
                    <td style="border-top: none;padding: 0px"><p style="margin-top: -135px;border-top: 1px solid black;padding-left: 3px;padding-top: 20px"><?php echo $lang['print_total']; ?>= ..............................................</p></td>
                </tr>
            </table>
        </div>
    </div>

</div>