<?php if (!$data['sts']) { ?>
    <span class="error-msg"><?php echo $data['msg']?></span>
<?php } else { ?>
    <form class="form-horizontal" id="penalty-form-two">
        <input type="hidden" id="member_image" name="member_image" value="<?php echo $data['member_scene_image']; ?>">
        <input type="hidden" name="biz_id" value="<?php echo $data['data']['uid']?>">
        <table class="table">
            <tbody class="table-body">
            <tr>
                <td><label class="control-label">Receive Money From</label></td>
                <td><span class="repayment-currency"><?php echo $lang['prepayment_way_'.$data['data']['payment_way']]?></span></td>
            </tr>
            <?php if($data['data']['payment_way']==repaymentWayEnum::PASSBOOK){ ?>
                <?php $i = 0;foreach($data['default_currency_amount'] as $currency => $amount){ ++$i?>
                    <tr>
                        <td>
                            <?php if($i == 1) {?>
                                <label class="control-label">Repayment Amount</label>
                            <?php }?>
                        </td>
                        <td><span class="repayment-amount money-style"><?php echo $currency . ' ' . ncPriceFormat($amount)?></span></td>
                    </tr>
                <?php }?>
            <?php }else{ ?>

                <?php $i = 0;foreach($data['currency_amount'] as $currency => $amount){ ++$i?>
                    <tr>
                        <td>
                            <?php if($i == 1) {?>
                                <label class="control-label">Repayment Amount</label>
                            <?php }?>
                        </td>
                        <td><span class="repayment-amount money-style"><?php echo $currency . ' ' . ncPriceFormat($amount)?></span></td>
                    </tr>
                <?php }?>
            <?php }?>

            <tr>
                <td><label class="control-label"><?php echo 'Scence Photo'?></label></td>
                <td style="margin-left: 10px!important;">
                    <div class="snapshot_div" onclick="callWin_snapshot_slave();">
                        <img id="img_slave" src="<?php echo $data['member_scene_image']?getImageUrl($data['member_scene_image']):'resource/img/member/photo.png'; ?>" style="width: 150px;height: 90px">
                    </div>
                    <div class="error_msg"></div>
                </td>
            </tr>
            <tr>
                <td><label class="control-label"><?php echo 'My Trading Password' ?></label></td>
                <td>
                    <a class="form-control authorize_a btn btn-default" onclick="cashierPassword()">
                        Cashier Verify
                        <img id="notCheckCashier" src="resource/img/member/verify-1.png">
                        <img id="checkCashierDone" src="resource/img/member/verify-2.png">
                        <img id="checkCashierFailure" src="resource/img/member/verify-3.png">
                    </a>
                    <input type="hidden" name="cashier_card_no"  class="form-control authorize_input" value="">
                    <input type="hidden" name="cashier_key" class="form-control authorize_input" value="">
                    <div class="error_msg"></div>
                </td>
            </tr>

            <!--<tr>
                <td><label class="control-label"><?php /*echo 'Client Trading Password' */?></label></td>
                <td>
                    <a class="form-control authorize_a btn btn-default" onclick="clientPassword()">Client Verify
                        <img id="notCheckPassword" src="resource/img/member/verify-1.png">
                        <img id="checkPasswordDone" src="resource/img/member/verify-2.png">
                        <img id="checkPasswordFailure" src="resource/img/member/verify-3.png">
                    </a>
                    <input type="hidden" name="client_trade_pwd" class="form-control authorize_input">
                    <div class="error_msg"></div>
                </td>
            </tr>-->

            <?php if( $data['is_ct_check'] ){ ?>
                <tr>
                    <td><label class="control-label"><?php echo 'Manager Password' ?></label></td>
                    <td>
                        <a class="form-control authorize_a btn btn-default" onclick="verifyManger()">
                            Manager Verify
                            <img id="notCheckManager" src="resource/img/member/verify-1.png">
                            <img id="checkDoneManager" src="resource/img/member/verify-2.png">
                            <img id="checkManagerFailure" src="resource/img/member/verify-3.png">
                        </a>
                        <input type="hidden" name="chief_teller_card_no"  class="form-control authorize_input" value="">
                        <input type="hidden" name="chief_teller_key" class="form-control authorize_input" value="">
                        <div class="error_msg"></div>
                    </td>
                </tr>
            <?php } ?>


            <tr>
                <td colspan="2" style="text-align: center;padding-top: 15px">
                    <a type="button" class="btn btn-default" onclick="showReceiveMoney()"><i class="fa fa-angle-double-left"></i>Back</a>
                    <a type="button" class="btn btn-primary" onclick="submit_Penalty()"><i class="fa fa-angle-double-right"></i>Submit</a>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
<?php } ?>