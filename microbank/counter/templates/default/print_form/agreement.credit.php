<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
   span {
        display: inline-block;
    }
   .down_line {
       height: 18px;
       line-height: 9px;
       border-bottom: 1px dashed black;
       padding: 12px;
       margin: 0px 1px;
       min-width: 100px;

   }

    p{
        margin:5px 10px;
    }
    .title {
        font-size: 18px;font-weight: 600;text-align: center;margin-bottom: 10px;
    }
    .align-center {
        text-align:center;
    }
    .padding8-0 {
        padding: 8px 0;
    }

    .line-larger {
        width: 600px;
    }

</style>
<?php
$asset_type_lang = enum_langClass::getAssetsType();
$member_info = $output['client_info'];
$member_address = $output['member_address'];
$grant_info = $output['grant_info'];
$grant_contract_info = $output['contract_info'];
$member_credit_request = $output['member_credit_request'];
?>

<?php include(template("print_form/inc.print.header")); ?>
<!--<div>
    <p>ព្រះរាជាណាចក្រកម្ពុជា</p>
    <p>ជាតិ  សាសនា  ព្រះមហាក្សត្រ</p>
    <p>rrÂss</p>
</div>-->
<div style="margin:0 10px">
    <div>
        <p class="title"><?php echo $lang['print_credit_agree_title']; ?></p>
        <p>
            <?php echo $lang['print_credit_agree_lend_party']; ?>
            <span class="down_line"><?php echo $output['branch_info']['branch_name']?></span>
            <?php echo $lang['print_credit_agree_legal']; ?>
            <span class="down_line"><?php echo $member_info['display_name'].'/'.$member_info['kh_display_name'];?></span>
            <?php echo $lang['print_credit_agree_position']; ?> <span class="down_line" style="min-width: 200px;"><?php echo $output['member_full_address']?:"&nbsp";?></span>              <?php echo $lang['print_credit_agree_party_a']; ?>.
        </p>

        <p class="align-center padding8-0"><?php echo $lang['print_credit_agree_and']; ?></p>
        <p>
            <?php if( $output['relative_list'] ){ ?>

                <?php $no=1; foreach( $output['relative_list'] as $item ){ ?>
                    <?php echo $no<=1?$lang['print_credit_agree_borrower_name']:$lang['print_and_name']; ?>
                    <span class="down_line" style="min-width: 100px;"><?php echo $item['name'];?></span>
                    <?php echo $lang['print_credit_agree_sex']; ?>
                    <span class="down_line" style="min-width: 100px;"><?php echo $item['gender']==memberGenderEnum::FEMALE?$lang['enum_gender_female']:$lang['enum_gender_male'];?></span>
                    <?php echo $lang['print_credit_agree_date_birth']; ?>
                    <span class="down_line" style="min-width: 100px;"><?php echo $item['birth_date'];?></span>
                    <?php echo $lang['print_credit_agree_nationality']; ?>
                    <span class="down_line" style="min-width: 100px;"><?php echo $item['nationality'];?></span>
                    <?php echo $lang['print_kind_of_id']; ?>
                    <span class="down_line" style="min-width: 100px;"><?php echo $lang['print_id_card'];?></span>
                    <?php echo $lang['print_credit_agree_id_no']; ?> <span class="down_line" style="min-width: 100px;"><?php echo $item['id_sn'];?></span>
                    <?php echo $lang['print_credit_agree_issued_by']; ?><span class="down_line" style="min-width: 100px;"><?php echo $item['issued_by'];?></span>
                    <?php echo $lang['print_credit_agree_issued_date']; ?> <span class="down_line" style="min-width: 100px;"><?php echo $item['issued_date'];?></span>
                    <?php echo $lang['print_is']; ?> <span class="down_line" style="min-width: 100px;"><?php echo $item['relation_name'];?></span>

                <?php } $no++; ?>


            <?php }else{ ?>

                <?php echo $lang['print_credit_agree_borrower_name']; ?>
                <span class="down_line" style="width: 100px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_sex']; ?>
                <span class="down_line" style="width: 100px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_date_birth']; ?>
                <span class="down_line" style="width: 110px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_nationality']; ?>
                <span class="down_line" style="width: 120px;"><?php echo '&nbsp;';?></span><br>
                <?php echo $lang['print_kind_of_id']; ?>
                <span class="down_line" style="width: 100px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_id_no']; ?> <span class="down_line" style="width: 120px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_issued_by']; ?> <span class="down_line" style="width: 100px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_issued_date']; ?> <span class="down_line" style="width: 130px;"><?php echo '&nbsp;';?></span><br>
                <?php echo $lang['print_is']; ?> <span class="down_line" style="width: 100px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_and_name']; ?>
                <span class="down_line" style="width: 130px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_sex']; ?>
                <span class="down_line" style="width: 110px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_date_birth']; ?>
                <span class="down_line" style="width: 145px;"><?php echo '&nbsp;';?></span><br>
                <?php echo $lang['print_credit_agree_nationality']; ?>
                <span class="down_line" style="width: 100px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_kind_of_id']; ?>
                <span class="down_line" style="width: 120px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_id_no']; ?> <span class="down_line" style="width: 125px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_credit_agree_issued_by']; ?><span class="down_line" style="width: 110px;"><?php echo '&nbsp;';?></span><br>
                <?php echo $lang['print_credit_agree_issued_date']; ?> <span class="down_line" style="width: 150px;"><?php echo '&nbsp;';?></span>
                <?php echo $lang['print_is']; ?> <span class="down_line" style="width: 120px;"><?php echo '&nbsp;';?></span>

            <?php } ?>
            <br>


            <?php echo $lang['print_current_address']; ?> :
            <?php echo $lang['print_house_no']; ?><span class="down_line" style="min-width: 100px;"><?php echo $member_address['house_number']?:'&nbsp;';?></span>
            <?php echo $lang['print_route']; ?> <span class="down_line" style="min-width: 100px;"><?php echo $member_address['street']?:'&nbsp;';?></span>
            <?php echo $lang['print_group']; ?> <span class="down_line" style="min-width: 100px;"><?php echo $member_address['address_group']?:'&nbsp;';?></span>
            <?php echo $lang['print_village']; ?> <span class="down_line" style="min-width: 115px;"><?php echo $member_address['id4']?:'&nbsp;';?></span><br>
            <?php echo $lang['print_commune']; ?> <span class="down_line" style="min-width: 100px;"><?php echo $member_address['id3']?:'&nbsp;';?></span>
            <?php echo $lang['print_district']; ?> <span class="down_line" style="min-width: 100px;"><?php echo $member_address['id2']?:'&nbsp;';?></span>
            <?php echo $lang['print_province']; ?> <span class="down_line" style="min-width: 100px;"><?php echo $member_address['id1']?:'&nbsp;';?></span> <?php echo $lang['print_credit_agree_party_b']; ?>.

        </p>

        <p>
            <?php echo $lang['print_credit_agree_part_b_amount']; ?>
            <span class="down_line" style="min-width: 120px;"><?php echo $grant_info['max_credit'].' $';?></span>
            <?php echo $lang['print_credit_agree_in_word']; ?> <span class="down_line" style="min-width: 210px;max-width: 200px;"><?php echo formatNumberAsSpell($grant_info['max_credit']);?></span>
        </p>
        <p>
            <?php echo $lang['print_credit_agree_period']; ?> <span class="down_line" style="min-width: 130px;"><?php echo $grant_info['credit_terms'];?></span> <?php echo $lang['print_credit_agree_month']; ?>,
            <?php echo $lang['print_credit_agree_cbc_fee']; ?> <span class="down_line" style="min-width: 130px;"><?php echo '&nbsp';?></span>$,
            <?php echo $lang['print_credit_agree_credit_fee']; ?> <span class="down_line" style="min-width: 130px;"><?php echo $grant_contract_info['fee'];?></span>$,


        </p>

        <p>
            <?php echo $lang['print_credit_agree_loan_purpose']; ?>
            <span class="down_line "><?php echo $member_credit_request['purpose'];?></span>
            <?php echo $lang['print_credit_agree_repayment_mode']; ?>
            <span class="down_line "><?php echo '';?></span>

        </p>

        <p style="margin-top: 10px;">
            <?php echo $lang['print_credit_agree_asset_title']; ?>
        </p>

        <div>
            <table class="table table-bordered table-no-background">

                <tr>
                    <th><?php echo $lang['print_credit_agree_collate_info']; ?></th>
                    <th><?php echo $lang['print_credit_agree_property_kind']; ?></th>
                    <th><?php echo $lang['print_credit_agree_cert_title']; ?></th>
                    <th><?php echo $lang['print_credit_agree_location']; ?></th>
                    <th><?php echo $lang['print_credit_agree_size']; ?></th>
                    <th><?php echo $lang['print_credit_agree_cert_no']; ?></th>
                    <th><?php echo $lang['print_credit_agree_date']; ?></th>
                    <th><?php echo $lang['print_credit_agree_issued_by']; ?></th>
                </tr>
                <?php $total_num=0; ?>
                <?php foreach( $output['grant_asset_list'] as $info ){ $total_num++; ?>
                    <tr>
                        <td><?php echo $lang['print_no'].' '.$total_num; ?></td>
                        <td><?php echo $asset_type_lang[$info['asset_type']]; ?></td>
                        <td><?php echo $info['asset_name']; ?></td>
                        <td><?php echo $info['address_detail']; ?></td>
                        <td><?php echo ''; ?></td>
                        <td><?php echo $info['asset_sn']; ?></td>
                        <td><?php echo $info['cert_issue_time']; ?></td>
                        <td><?php echo ''; ?></td>

                    </tr>
                <?php } ?>
                <?php for( $i=$total_num+1;$i<=2;$i++){ ?>
                    <tr>
                        <td><?php echo $lang['print_no'].' '.$i; ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
       <!-- <p>
            <table class="table table-bordered table-no-background">
                <tr>
                    <th><?php /*echo $lang['print_credit_agree_collate_info']; */?></th><th><?php /*echo $lang['print_no']; */?> 1</th>
                    <th><?php /*echo $lang['print_no']; */?> 2</th><th><?php /*echo $lang['print_no']; */?> 3</th><th><?php /*echo $lang['print_no']; */?> 4</th>
                </tr>
                <tr><td><?php /*echo $lang['print_credit_agree_property_kind']; */?></td><td></td><td></td><td></td><td></td></tr>
                <tr><td><?php /*echo $lang['print_credit_agree_cert_title']; */?></td><td></td><td></td><td></td><td></td></tr>
                <tr><td><?php /*echo $lang['print_credit_agree_location']; */?></td><td></td><td></td><td></td><td></td></tr>
                <tr><td><?php /*echo $lang['print_credit_agree_size']; */?></td><td></td><td></td><td></td><td></td></tr>
                <tr><td><?php /*echo $lang['print_credit_agree_cert_no']; */?></td><td></td><td></td><td></td><td></td></tr>
                <tr><td><?php /*echo $lang['print_credit_agree_date']; */?></td><td></td><td></td><td></td><td></td></tr>
                <tr><td><?php /*echo $lang['print_credit_agree_issued_by']; */?></td><td></td><td></td><td></td><td></td></tr>
            </table>
        </p>-->

        <div>
            <p style="font-style: italic;font-weight: 600;">
                <?php echo $lang['print_credit_agree_note']; ?>
            </p>
        </div>


        <div style="margin-right: 30px;">
            <p style="text-align: right;">
                <?php echo $lang['print_credit_agree_date']; ?>
                <span class="down_line" style="width: 160px;"><?php echo date('Y-m-d');?></span>
            </p>
            <p style="text-align: right;font-weight: 600;margin-top: 10px">
                <?php echo $lang['print_credit_agree_borrower_finger']; ?>
            </p>

            <div class="clearfix">
                <div style="float:right;width: 120px;height: 120px;border: solid 1px #000;margin-right: 10px">

                </div>
            </div>


           <!-- <div class="clearfix">
                <div style="float:right;border:1px solid #000;border-bottom: 0;width: 150px;height: 120px;"></div>
            </div>
            <div class="clearfix">
                <div style="float:right;width: 150px;">
                    <span class="down_line" style="width: 100px;">
                    &nbsp;</span>
                </div>
            </div>
            <div class="clearfix">
                <div style="float:right;border:1px solid #000;border-top:0;margin-top: 20px;width: 150px;height: 10px;"></div>
            </div>-->

        </div>
    </div>

</div>
