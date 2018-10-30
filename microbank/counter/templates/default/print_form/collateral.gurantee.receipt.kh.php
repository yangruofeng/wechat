<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    body {
        width: 1000px;
        margin: 20px auto;
        font-size: 15px;
    }

    .member-info span, .guarantee-list span {
        display: inline-block;
    }

    .down_line {
        height: 19px;
        line-height: 9px;
        border-bottom: 1px dashed black;
        padding: 5px;
    }

    td {
        border: 1px solid black;
        padding: 15px;
    }
</style>
<?php $basic_info = $output['basic_info'];?>
<?php
$assets_info = $output['assets_info'];
$other_owner_list = $output['other_owner_list'];
$other_owner = is_array($other_owner_list)?reset($other_owner_list):array();
?>
<div>
    <div>
        <img src="resource/img/counter-icon/print_logo.png" >

    </div>
    <div style="text-align: center;position: relative">

        <p style="font-size: 18px;font-weight:600;margin-top: 25px"><?php echo $lang['print_collateral_guarantee_receipt']; ?></p>
    </div>
    <div class="col-sm-12 member-info"  style="padding:5px;margin-top: 15px;position: relative">
        <div>
            <span><?php echo $lang['print_branch_name']; ?></span>
            <span class="down_line" style="width: 180px;"><?php echo $basic_info['branch_name'] ?></span>

            <span class="down_line" style="width: 150px;float:right;"><?php echo $basic_info['contract_no'] ?></span>
            <span style="float:right;"><?php echo $lang['print_contract_no']; ?></span>
        </div>

        <div style="margin-top: 12px;margin-left: 0px">
            <span><?php echo $lang['print_name']; ?></span>
            <span class="down_line" style="width: 180px;"><?php echo $basic_info['display_name'].
                    ($basic_info['kh_display_name']?'/'.$basic_info['kh_display_name']:''); ?></span>
            <span><?php echo $lang['print_gender']; ?></span>
            <span class="down_line" style="width: 177px;"><?php echo $basic_info['gender'] ?></span>
            <span><?php echo $lang['print_kind_of_id']; ?></span>
            <span class="down_line" style="width: 176px;"><?php echo $lang['print_id_card']; ?></span>
            <span><?php echo $lang['print_no']; ?></span>
            <span class="down_line" style="width: 200px;"><?php echo $basic_info['id_sn']; ?></span>
        </div>

        <div style="margin-top: 12px;margin-left: 0px">
            <span><?php echo $lang['print_and_name']; ?></span>
            <span class="down_line" style="width: 180px;"><?php echo $other_owner['name']; ?></span>
            <span><?php echo $lang['print_is']; ?></span>
            <span class="down_line" style="width: 157px;"><?php echo $other_owner['relation_name'] ?></span>
            <span><?php echo $lang['print_current_address'].' '.$lang['print_house_no']; ?></span>
            <span class="down_line" style="width: 156px;"><?php echo $basic_info['house_number'] ?></span>
            <span><?php echo $lang['print_route']; ?></span>
            <span class="down_line" style="width: 160px;"><?php echo $basic_info['street']; ?></span>
        </div>


        <div style="margin-top: 12px;">
            <span><?php echo $lang['print_group']; ?></span>
            <span class="down_line" style="width: 80px;"><?php echo $basic_info['address_group']; ?></span>
            <span><?php echo $lang['print_village']; ?></span>
            <span class="down_line" style="width: 140px;"><?php echo $basic_info['id_4']; ?></span>
            <span><?php echo $lang['print_commune']; ?></span>
            <span class="down_line" style="width: 140px;"><?php echo $basic_info['id_3']; ?></span>
            <span><?php echo $lang['print_district']; ?></span>
            <span class="down_line" style="width: 140px;"><?php echo $basic_info['id_2']; ?></span>
            <span><?php echo $lang['print_province']; ?></span>
            <span class="down_line" style="width: 140px;"><?php echo $basic_info['id_1']; ?></span>
        </div>


        <div style="text-align: center;margin-top: 10px;">
            <?php echo $lang['print_collateral_such_as']; ?>
        </div>

    </div>
    <div class="col-sm-12 guarantee-list" style="padding:10px;position: relative">

        <?php if($assets_info[0]['uid']){ ?>
            <?php $i = 0;foreach ($assets_info as $value){ $i++ ?>
                <div style="margin-top: 12px;">
                    <span><?php echo $i?>-</span>
                    <span class="down_line" style="width: 250px;"><?php echo $value['asset_name'];?></span>
                    <span><?php echo $lang['print_no']; ?></span>
                    <span class="down_line" style="width: 256px;"><?php echo $value['asset_sn'];?></span>
                    <span><?php echo $lang['print_date']; ?></span>
                    <span class="down_line" style="width: 265px;"><?php echo $value['cert_issue_time']; ?></span>
                </div>
            <?php }?>

            <!--小于4个自动填充-->
            <?php for($k=$i;$k<4;$k++){ ?>
                <div style="margin-top: 12px;">
                    <span><?php echo $k+1?>-</span>
                    <span class="down_line" style="width: 250px;"></span>
                    <span><?php echo $lang['print_no']; ?></span>
                    <span class="down_line" style="width: 256px;"></span>
                    <span><?php echo $lang['print_date']; ?></span>
                    <span class="down_line" style="width: 265px;"></span>
                </div>
            <?php }?>

        <?php }else{ ?>
            <p><?php echo $lang['print_guarantee_no_records']; ?></p>
        <?php }?>
    </div>
    <div class="col-sm-12" style="padding:10px 0px 0px 10px">
        <div class="col-sm-6" style="padding:0px">
            <table class="col-sm-11" style="padding:0px">
                <tr style="height: 15px">
                    <td colspan="2">____________________/____________________/____________________</td>
                </tr>
                <tr style="height: 30px">
                    <td class="col-sm-9" style="padding:0px;text-align: center"><?php echo $lang['print_withdrawal_thumb']; ?></td>
                    <td class="col-sm-3" style="padding:0px;text-align: center"><?php echo $lang['print_giver_signature']; ?></td>
                </tr>
                <tr style="height:120px">
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div class="col-sm-6" style="padding:0px">
            <table class="col-sm-11">
                <tr style="height: 15px">
                    <td colspan="2">____________________/____________________/____________________</td>
                </tr>
                <tr style="height: 30px">
                    <td class="col-sm-9" style="padding:0px;text-align: center"><?php echo $lang['print_withdrawal_thumb']; ?></td>
                    <td class="col-sm-3" style="padding:0px;text-align: center"><?php echo $lang['print_giver_signature']; ?></td>
                </tr>
                <tr style="height:120px">
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-sm-12" style="padding:10px;margin-top:10px;margin-bottom: 50px;position: relative">
        <p>
            <?php echo $lang['print_collateral_withdraw_note']; ?>
        </p>
        <div>
            <span><input type="checkbox"/><?php echo $lang['print_one_of_above']; ?></span>
            <span style="margin-left: 220px"><input type="checkbox"/><?php echo $lang['print_all_of_above']; ?></span>
            <span style="margin-left:220px"><input type="checkbox"/><?php echo $lang['print_a_authorize_is']; ?></span>
            <span class="down_line" style="width: 190px;left:760px;position: absolute"></span>
        </div>

    </div>
</div>