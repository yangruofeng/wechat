
<style>
    .credit-amount{
        font-size: 16px;
        color:red;
        font-style: italic;
    }
    .ml-10{
        margin-left: 10px;
    }
</style>

<div>
   <!-- <div class="alert alert-success">
        Automatic create loan contract and do withdraw for client, <kbd>please give cash to client at here.</kbd>
    </div>-->
    <table class="table table-bordered table-hover">
        <?php if( !empty($output['one_time_loan_list'])){ foreach( $output['one_time_loan_list'] as $item ){ ?>

            <tr>
                <td width="50%" class="text-right">

                    <?php echo '<kbd>'.$item['member_category_info']['alias'].'</kbd>'.
                        ' / '.$item['member_category_info']['sub_product_name'].' / '.$item['credit_terms'].' Months';?>
                </td>
                <td>
                    <p><label for="">Loan Amount:</label></p>
                    <?php if( $item['grant_product_info']['credit_usd'] > 0 ){ ?>
                        <div>
                            <label for="">USD: </label>
                            <span class="credit-amount"><?php echo ncPriceFormat($item['grant_product_info']['credit_usd'],0); ?></span>
                            <label for="" class="label label-success ml-10">
                                <span class="ml-10">
                                     interest rate: <kbd><?php echo $item['grant_product_info']['interest_rate']; ?>%</kbd>
                                </span>

                                <span class="ml-10">
                                    operation fee: <kbd><?php echo $item['grant_product_info']['operation_fee']; ?>%</kbd>
                                </span>

                            </label>
                        </div>
                    <?php } ?>

                    <?php if( $item['grant_product_info']['credit_khr'] > 0 ){ ?>
                        <div>
                            <label for="">KHR: </label>
                            <span class="credit-amount"><?php echo ncPriceFormat($item['grant_product_info']['credit_khr'],0); ?></span>
                            <label for="" class="label label-success ml-10">
                                <span class="ml-10">
                                    interest rate: <kbd><?php echo $item['grant_product_info']['interest_rate_khr']; ?>%</kbd>
                                </span>
                                <span class="ml-10">
                                    operation fee: <kbd><?php echo $item['grant_product_info']['operation_fee_khr']; ?>%</kbd>
                                </span>

                            </label>
                        </div>
                    <?php } ?>

                </td>

            </tr>

        <?php } } ?>
        <tr>
            <td width="50%" class="text-right">Client Trading Password </td>
            <td width="50%">
                <?php if(  $client_info['trading_password'] ){ ?>
                    <div>
                        <p>
                            <label for=""><i style="color:red;">*</i><b>Client Trading Password</b></label>
                        </p>

                        <a class="form-control authorize_input btn btn-default" onclick="clientInputPassword(this,'member_trading_password')">Client Input
                            <img id="notCheckPassword" class="notCheckPassword" src="resource/img/member/verify-1.png">
                            <img id="checkPasswordDone" class="checkPasswordDone" src="resource/img/member/verify-2.png">
                            <img id="checkPasswordFailure" class="checkPasswordFailure" src="resource/img/member/verify-3.png">
                        </a>
                        <input type="hidden" name="member_trading_password" class="form-control authorize_input">
                        <div class="error_msg"></div>
                    </div>

                <?php }else{ ?>
                    <div>
                        <p>
                            <span><i class="fa fa-question-circle" style="color:green;font-size: 16px;"></i></span> Not set yet, please set now!
                        </p>
                        <p>
                            <label for=""><i style="color:red;">*</i><b>New Trading Password</b></label>
                        </p>

                        <a class="form-control authorize_input btn btn-default" onclick="clientInputPassword(this,'member_trading_password')">Client Input
                            <img id="notCheckPassword"  class="notCheckPassword" src="resource/img/member/verify-1.png">
                            <img id="checkPasswordDone" class="checkPasswordDone" src="resource/img/member/verify-2.png">
                            <img id="checkPasswordFailure" class="checkPasswordFailure" src="resource/img/member/verify-3.png">
                        </a>
                        <input type="hidden" name="member_trading_password" class="form-control authorize_input">
                        <div class="error_msg"></div>
                    </div>

                    <div style="margin-top: 10px;">
                        <p>
                            <label for=""><i style="color:red;">*</i><b>Confirm Trading Password</b></label>
                        </p>

                        <a class="form-control authorize_input btn btn-default" onclick="clientInputPassword(this,'member_confirm_trading_password')">Client Input
                            <img id="notCheckPassword" class="notCheckPassword" src="resource/img/member/verify-1.png">
                            <img id="checkPasswordDone" class="checkPasswordDone" src="resource/img/member/verify-2.png">
                            <img id="checkPasswordFailure" class="checkPasswordFailure" src="resource/img/member/verify-3.png">
                        </a>
                        <input type="hidden" name="member_confirm_trading_password" class="form-control authorize_input">
                        <div class="error_msg"></div>
                    </div>

                <?php } ?>
            </td>
        </tr>
        <tr>
            <td width="50%" class="text-right">Client FingerPrint</td>
            <td width="50%">
                <?php if(  $output['required_set_finger'] ){ ?>
                    <p>
                        <span><i class="fa fa-question-circle" style="color:green;font-size: 16px;"></i></span> Not register yet, please register now!
                    </p>
                    <div>
                        <input type="hidden" id="client_id" name="client_id" value="<?php echo $client_info['uid']?>">
                        <input type="hidden" id="obj_uid" name="obj_uid" value="<?php echo $client_info['obj_guid']?>">
                    </div>
                    <div class="snapshot_div" id="feature_img" style="height: 140px;width: 120px" onclick="callWin_registerFinger('feature_img');">
                        <img id="feature_img" src="<?php echo 'resource/img/member/photo.png';?>" style="width: 100px;height: 100px">
                        <div>Fingerprint</div>
                    </div>
                <?php }else{ ?>
                    <span>
                        <i class="fa fa-check" style="color:green;font-size: 24px;"></i>
                    </span>
                <?php } ?>
            </td>
        </tr>
    </table>
</div>

<script>

    function clientInputPassword(ele,input_name)
    {
        if( !input_name ){
            input_name = 'member_trading_password';
        }
        var _el = $(ele);
        var _input_ele = $("input[name='"+input_name+"']");
        if( window.external ){
            try{
                var client_password = window.external.inputPasswordWithKeyInfo('');
                // 如果是confirm先确认
                if( input_name == 'member_confirm_trading_password' ){
                    var _first_password = $('input[name="member_trading_password"]').val();
                    if( client_password != _first_password){
                        alert('Two password are not the same.');
                        return false;
                    }
                }

                _input_ele.val(client_password);
                if(_input_ele.val()){
                    _el.find('.notCheckPassword').hide();
                    _el.find('.checkPasswordFailure').hide();
                    _el.find('.checkPasswordDone').show();
                }else{
                    _el.find('.notCheckPassword').hide();
                    _el.find('.checkPasswordFailure').show();
                }
            }catch (ex )
            {
                alert(ex.Message);
            }
        }

    }

    function callWin_registerFinger(id) {
        var uid = $('input[name="obj_uid"]').val();
        if (!uid) {
            alert('Please select the client first.');
        }
        if (window.external) {
            try {
                var _img_path = window.external.registerFingerPrint(uid, "0");
                if (_img_path != "" && _img_path != null) {
                    $("#" + id + " img").attr("src", getUPyunImgUrl(_img_path));
                }
            } catch (ex) {
                alert(ex.Message);
            }
        }
    }

</script>