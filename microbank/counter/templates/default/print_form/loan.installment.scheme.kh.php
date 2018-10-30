<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    html,body{
        background-color: #fff;
    }
    body {
        width: 1000px;
        margin: 20px auto;
        font-size: 15px;
    }

    .pad_50 {
        padding-left: 50px;
    }

    .pad_15 {
        padding-left: 10px;
    }

    .title {
        /*word-break: break-all;*/
        /*word-wrap: break-word;*/
        /*word-break: normal;*/
    }

    .w-50 {
        width: 7%;
    }

    .w-100 {
        width: 10.5%;
    }

    .w-120 {
        width: 15%;
    }
</style>
<?php
$member_info = $output['client_info'];
$loan_time_unit_lang = enum_langClass::getLoanTimeUnitLang();
$interest_lang = enum_langClass::getLoanRepaymentPeriodLang();
?>
<?php
$contract_info = $output['contract_info'];
$decimal=2;
if($contract_info['currency']==currencyEnum::KHR){
    $decimal=0;
}

?>

<!--太宽了点，打印两边丢失-->
<div style="padding: 0 20px;">
    <div style="text-align: center;position: relative">
        <img src="resource/img/login/c-logo.png" style="position:absolute;top:6px;left: 15px">
        <p style="font-size: 20px;font-weight: bold">គ្រឹះស្ថានមីក្រូហិរញ្ញវត្ថុ សំរឹទ្ធិស័ក លីមីតធីត</p>
        <p style="font-size: 16px;font-weight: 600"><?php echo $lang['print_samrithisak_microfinance_limited'];?></p>
        <p style="font-weight:600;margin-top: 30px"><?php echo $lang['print_payment_schedule'];?></p>
    </div>
    <div class="col-sm-12"  style="border: 1px solid black;padding-left:3px">
        <div class="col-sm-4" style="padding-left: 3px">
            <table>
                <tr>
                    <td style="width: 150px"><?php echo $lang['print_account_number'];?> :</td>
                    <td><?php echo $contract_info['contract_sn']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_client_name'];?> :</td>
                    <td><?php echo $member_info['kh_display_name']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_english_name'];?> :</td>
                    <td><?php echo $member_info['display_name']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_amount'];?> :</td>
                    <td><?php echo $contract_info['currency'] . ' ' . ncPriceFormat($contract_info['loan_amount'],$decimal)?></td>
                </tr>
                <tr>
                    <td><?php echo " អត្រាការប្រាក់  " //$lang['print_interest_rate'];?> :</td>
                    <td>
                        <?php if ($contract_info['contract_info']['interest_rate_type']) { ?>
                            <?php echo $contract_info['contract_info']['interest_rate']?>
                        <?php } else { ?>
                            <?php echo $contract_info['contract_info']['interest_rate'] . '%'?>
                        <?php } ?>
                        <?php echo '/' . $interest_lang[$contract_info['contract_info']['interest_rate_unit']]; ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-sm-3">
            <table>
                <tr>
                    <td><?php echo $lang['print_cif'];?> :</td>
                    <td><?php echo $member_info['obj_guid']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_gender'];?> :</td>
                    <td><?php echo $member_info['gender']== memberGenderEnum::FEMALE?$lang['enum_gender_female']:$lang['enum_gender_male']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_circle'];?> :</td>
                    <td><?php echo $contract_info['loan_times']?></td>
                </tr>
                <tr>
                    <td colspan="2" ><?php echo $lang['print_occupation'];?> : &nbsp;&nbsp;&nbsp;<?php echo $lang['work_type_' . $member_info['work_type']] ?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_period'];?> :</td>
                    <td><?php echo $contract_info['loan_period_value'] . $loan_time_unit_lang[$contract_info['loan_period_unit']];?></td>
                </tr>
            </table>
        </div>
        <div class="col-sm-5" >
            <table>
                <tr>
                    <td><?php echo $lang['common_phone_number'];?> :</td>
                    <td><?php echo $member_info['phone_id']?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_disburse_date'];?> :</td>
                    <td><?php echo dateFormat($contract_info['disburse_date'], '-')?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_first_pay_date'];?> :</td>
                    <td><?php echo dateFormat($contract_info['first_pay_date'], '-')?></td>
                </tr>
                <tr>
                    <td><?php echo $lang['print_final_pay_date'];?> :</td>
                    <td><?php echo dateFormat($contract_info['final_pay_date'], '-')?></td>
                </tr>
                <tr>
                    <td colspan="2" >
                    អត្រាពិន័យ: &nbsp;&nbsp;&nbsp;<?php echo $contract_info['penalty_rate_yearly'];?>%<?php echo "ក្នុងមួយឆ្នាំលើប្រាក់ត្រូវបង់"; ?>
                    </td>
                </tr>
<!--                <tr>-->
<!--                    <td>--><?php //echo " អត្រាការប្រាក់  " //$lang['print_interest_rate'];?><!-- :</td>-->
<!--                    <td>-->
<!--                        --><?php //if ($contract_info['contract_info']['interest_rate_type']) { ?>
<!--                            --><?php //echo $contract_info['contract_info']['interest_rate']?>
<!--                        --><?php //} else { ?>
<!--                            --><?php //echo $contract_info['contract_info']['interest_rate'] . '%'?>
<!--                        --><?php //} ?>
<!--                        --><?php //echo '/' . $interest_lang[$contract_info['contract_info']['interest_rate_unit']]; ?>
<!--                    </td>-->
<!--                </tr>-->
            </table>
        </div>
        <div class="col-sm-12" style="padding-left: 3px">
            <table style="margin-bottom: 10px">
                <tr>
                    <td valign="top" style="width: 150px"><?php echo $lang['print_address'];?> :</td>
                    <td><?php echo $output['member_full_address']?></td>
                </tr>
            </table>
        </div>

    </div>
    <div class="col-sm-12" style="border: 1px solid black;padding-left:3px;margin-top: 20px">
        <table>
            <thead>
            <tr>
                <td class="pad_15 title w-50"><?php echo $lang['print_no'];?></td>
                <td class="pad_15 title w-120"><?php echo $lang['print_payment_date'];?></td>
               <!-- <td class="pad_15 title w-120"><?php /*echo $lang['print_interest_calculate_date'];*/?></td>
                -->
                <td class="pad_15 title w-100"><?php echo $lang['print_days_of_use'];?></td>
                <td class="pad_15 title w-100"><?php echo $lang['print_begin_principal'];?></td>
                <td class="pad_15 title w-100"><?php echo $lang['print_interest_rate_for_paid'];?></td>
                <td class="pad_15 title w-100"><?php echo $lang['print_operation_fees'];?></td>
                <td class="pad_15 title w-100"><?php echo $lang['print_principal_for_paid'];?></td>
                <td class="pad_15 title w-100"><?php echo $lang['print_total_payment'];?></td>
                <td class="pad_15 title w-100"><?php echo $lang['print_principal_owed'];?></td>
            </tr>
            </thead>
            <tbody>
            <?php $i=0; foreach ($contract_info['loan_installment_scheme'] as $row) { $i++; ?>
                <tr>
                    <td class="pad_15 title w-50">
                        <?php echo $i ?>
                    </td>
                    <td class="pad_15 title w-120">
                        <?php echo dateFormat($row['receivable_date'], '-') ?>
                    </td>
                   <!-- <td class="pad_15 title w-120">
                        <?php /*echo dateFormat($row['interest_date'], '-') */?>
                    </td>-->
                    <td class="pad_15 title w-100">
                        <?php echo $row['days_of_use'] ?>
                    </td>
                    <td class="pad_15 title w-100">
                        <?php echo ncPriceFormat($row['begin_principal'],$decimal) ?>
                    </td>
                    <td class="pad_15 title w-100">
                        <?php echo ncPriceFormat($row['receivable_interest'],$decimal) ?>
                    </td>
                    <td class="pad_15 title w-100">
                        <?php echo ncPriceFormat($row['receivable_operation_fee'],$decimal) ?>
                    </td>
                    <td class="pad_15 title w-100">
                        <?php echo ncPriceFormat($row['receivable_principal'],$decimal) ?>
                    </td>
                    <td class="pad_15 title w-100">
                        <?php echo ncPriceFormat($row['amount'],$decimal); ?>
                    </td>
                    <td class="pad_15 title w-100">
                        <?php echo ncPriceFormat($row['principal_owed'],$decimal) ?>
                    </td>
                </tr>
            <?php }?>
            <tr>
                <td colspan="20" style="height: 20px"></td>
            </tr>
            <tr style="border-top: 1px solid #CCC;height: 30px">
                <td colspan="2"></td>
                <td class="pad_15">
                    <?php echo $contract_info['days_of_use_total'] ?>
                </td>
                <td class="pad_15">
                    <span style="font-weight: 600"><?php echo $lang['print_total'];?></span>
                </td>
                <td class="pad_15">
                    <?php echo ncPriceFormat($contract_info['contract_info']['receivable_interest'],$decimal) ?>
                </td>
                <td class="pad_15">
                    <?php echo ncPriceFormat($contract_info['contract_info']['receivable_operation_fee'],$decimal) ?>
                </td>
                <td class="pad_15">
                    <?php echo ncPriceFormat($contract_info['loan_amount'],$decimal) ?>
                </td>
                <td class="pad_15">
                    <?php echo ncPriceFormat($contract_info['payment_total'],$decimal) ?>
                </td>
                <td>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-sm-12" style="margin-top: 30px;padding-left: 0px">
        <p><?php echo $lang['print_installment_tip_1'];?></p>
        <p class="pad_50"><?php echo $lang['print_installment_tip_2'];?></p>
        <p class="pad_50"><?php echo $lang['print_installment_tip_3'];?></p>
        <p class="pad_50"><?php echo $lang['print_installment_tip_4'];?></p>
        <p><?php echo $lang['print_installment_tip_5'];?></p>
        <p>
            <span><?php echo $lang['print_installment_tip_6'];?></span>
            <span style="float: right"><?php echo $lang['print_address_1'];?>, <?php echo Now()?></span>
        </p>
    </div>
    <div class="col-sm-12" style="margin-bottom: 100px;padding-left: 0px;margin-top: 20px">
        <table class="col-sm-12">
            <tr>
                <td class="col-sm-4" style="padding-left: 0px">
                    <?php echo $lang['print_credit_office'];?>
                </td>
                <td class="col-sm-4">
                    <?php echo $lang['print_teller_signature'];?>
                </td>
                <td class="col-sm-4">
                    <?php echo $lang['print_client_thumb_print'];?>
                </td>
            </tr>
            <tr>
                <td class="col-sm-4" style="padding-left: 0px">
                    <?php echo $output['co']['officer_name']; ?>
                </td>
                <td class="col-sm-4">
                    <?php echo $output['cashier']['user_name']?>
                </td>
                <td class="col-sm-4">
                    <?php echo $member_info['display_name']?>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <?php echo $lang['print_phone'];?>: <?php echo $output['co']['mobile_phone']?>
                </td>
            </tr>
        </table>
    </div>
</div>