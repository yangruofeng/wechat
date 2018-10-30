<?php
    $biz_item=$output['biz_item'];
?>
<style>
    .authorize_input {
        margin-top: -8px!important;
        margin-bottom: 10px;
    }

    .table{
        background-color: white!important;
    }

    #notCheck, #notCheckCashier {
        width: 20px;
        position: absolute;
        top: 6px;
        right: 10px;
    }

    #checkDone, #checkCashierDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }
</style>
<div class="page">
    <div class="content-nav">

        <ul class="nav nav-tabs">
            <li role="presentation">
                <a href="<?php
                echo getUrl("member_index","bizPendingCtApprove", array(), false, ENTRY_COUNTER_SITE_URL);
                ?>">
                    Task List
                </a>
            </li>
            <li role="presentation" class="active">
                <a href="#">
                    <?php echo $biz_item['biz_caption']?>
                </a>
            </li>
        </ul>
    </div>
    <div class="row">
        <form id="verifyForm">
            <table class="table table-no-background">
                <?php echo $biz_item['tpl']?>
                <tr>
                    <td></td>
                    <td style="background-color: white!important;position: relative;text-align: center;padding: 20px">
                        <a class="form-control authorize_input btn btn-default" onclick="verifyManger()" style="position: relative">Verify By Card
                            <img id="notCheck" src="resource/img/member/verify-1.png">
                            <img id="checkDone" src="resource/img/member/verify-2.png">
                        </a>
                        <input type="hidden" name="chief_teller_card_no"  class="form-control authorize_input" value="">
                        <input type="hidden" name="chief_teller_key" class="form-control authorize_input" value="">
                        <div class="error_msg"></div>
                    </td>

                    <td colspan="10" style="padding: 20px"">
                        <input type="hidden" value="<?php echo $biz_item['biz_code']?>" name="biz_code">
                        <input type="hidden" value="<?php echo $biz_item['uid']?>" name="biz_id">
                        <input type="hidden" id="txt_is_reject" value="0" name="is_reject">
                        <button type="button" class="btn btn-primary" onclick="btn_submit_approve_onclick()"> Approve </button>
                        &nbsp;&nbsp;
                        <button type="button" class="btn btn-default"> Reject </button>
                    </td>
                </tr>
            </table>
        </form>

    </div>

</div>
<script>
    function verifyManger() {
        var card = window.external.swipeCard();
        var card_info=card.split("|");
        $("input[name='chief_teller_card_no']").val(card_info[0]);
        $("input[name='chief_teller_key']").val(card_info[1]);
        $('#notCheck').hide();
        $('#checkDone').show();
    }

    function btn_submit_reject_onclick(){
        $("txt_is_reject").val(1);
        btn_submit_onclick();
    }
    function btn_submit_approve_onclick() {
        $("txt_is_reject").val(0);
        btn_submit_onclick();
    }
    function btn_submit_onclick(){
        if (!$("#verifyForm").valid()) {
            return false;
        }
        $(document).waiting();
        var values = $('#verifyForm').getValues();
        yo.loadData({
            _c: 'member_index',
            _m: 'bizCtApproveSubmit',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert("<?php echo $biz_item['biz_caption'] ?> Successful!");
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('member_index', 'bizPendingCtApprove', array(), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }


    $('#verifyForm').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules: {
            chief_teller_card_no : {
                required: true
            },
            chief_teller_key : {
                required: true
            }
        },
        messages: {
            chief_teller_card_no : {
                required: '<?php echo 'Required'?>'
            },
            chief_teller_key : {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>