<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    /*.authorize_input {*/
        /*margin-top: -8px!important;*/
        /*margin-bottom: 10px;*/
    /*}*/

    #notCheck {
        width: 20px;
        position: absolute;
        top: 6px;
        right: 10px;
    }

    #checkFailure {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }

    #checkDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Reset Security Cards</h3>
        </div>
    </div>
    <div class="container" style="width: 500px;">
        <form id="reset_security_card" class="form-horizontal" style="margin-top: 15px">
            <input type="hidden" name="form_submit" value="ok" >
            <input type="hidden" name="act" value="user">
            <input type="hidden" name="op" value="securityCardReset">
            <input type="hidden" name="initial_info" >
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Card No'?></label>
                <div class="col-sm-8">
                    <a class="form-control authorize_input btn btn-default" onclick="readCardNo()" style="position: relative">Swipe Card
                        <img id="notCheck" src="resource/img/member/verify-1.png">
                        <img id="checkDone" src="resource/img/member/verify-2.png">
                        <img id="checkFailure" src="resource/img/member/verify-3.png">
                    </a>
                    <input type="hidden" name="card_no"  class="form-control authorize_input" value="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Reset Reason'?></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="reset_reason" value="">
                    <div class="error_msg"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 15px">
                    <button type="button" class="btn btn-danger"><?php echo 'Submit' ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var user_card_list = <?php echo $output['user_card_list'] ? my_json_encode($output['user_card_list']) : [] ?>;

    function readCardNo() {
        var card_no = window.external.readCardNo();
        if (card_no != null) {
            if (user_card_list.indexOf(card_no) < 0) {
                $('#notCheck').hide();
                $('#checkFailure').show();
                $('#checkDone').hide();
            } else {
                $("input[name='card_no']").val(card_no);
                $('#notCheck').hide();
                $('#checkFailure').hide();
                $('#checkDone').show();
            }
        } else {
            $('#notCheck').show();
            $('#checkFailure').hide();
            $('#checkDone').hide();
        }
    }

    $('.btn-danger').click(function () {
        if (!$("#reset_security_card").valid()) {
            return;
        }

        var values = $('#reset_security_card').getValues();
        yo.loadData({
            _c: 'user',
            _m: 'checkUserCard',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    var initialInfo = "<?php echo $output['user_info']['uid']?>-<?php echo $output['operator_info']['uid']?>-" + (+new Date()).toString();
                    initialInfo += "-" + values['reset_reason'];
                    var cardNo = window.external.initializeCard(initialInfo, values['card_no']);
                    if (cardNo == values['card_no']) {
                        $("input[name='initial_info']").val(initialInfo);
                        $("#reset_security_card").submit();
                    } else {
                        alert("Reset failed, please try again");
                    }
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });

    $('#reset_security_card').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules: {
            card_no : {
                required: true
            },
            reset_reason : {
                required: true
            }
        },
        messages: {
            card_no : {
                required: '<?php echo 'Required'?>'
            },
            reset_reason : {
                required: '<?php echo 'Required'?>'
            }
        }
    });
</script>