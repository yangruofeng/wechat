<?php include_once(template('widget/inc_header_weui'));?>
<?php
    $task=$output['task'];
    $msg_list=$output['msg_list'];
    if(!$task || $task['state']==commonApproveStateEnum::REJECT){
?>
        <div>
            <form>
                <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                <div class="weui-cells__title">
                    When you finished research, Please commit to manager to check,
                    Not allowed to edit client's information after submit.
                </div>
                <div class="weui-cells weui-cells_form">
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <textarea id="txt_comment" class="weui-textarea" placeholder="Input Comment" rows="3" name="msg" required="required"></textarea>
                        </div>
                    </div>
                </div>
                <div class="weui-btn-area">
                    <button class="weui-btn weui-btn_primary" onclick="btn_submit_onclick();" type="button">
                        Submit To Branch Manager
                    </button>
                </div>
            </form>
        </div>
<?php }else{?>
        <div class="weui-msg">
            <div class="weui-msg__icon-area">
                <i class="<?php if($task['state']==commonApproveStateEnum::PASS){ echo 'weui-icon-success';}else{ echo 'weui-icon-waiting';}?> weui-icon_msg"></i>
            </div>
            <div class="weui-msg__text-area">
                <h3>
                    <?php if($task['state']==commonApproveStateEnum::PASS){ echo 'Accepted';}else{ echo 'Pending Accept';}?>
                </h3>
            </div>
        </div>
<?php }?>


<div class="weui-panel">
    <div class="weui-panel__hd">
        <h4>Comment History</h4
    </div>
    <div class="weui-panel__bd">

        <?php foreach($msg_list as $msg){?>
            <div class="weui-media-box weui-media-box_text">
                <div class="weui-media-box__desc">
                    <?php echo $msg['msg']?>
                </div>
                <ul class="weui-media-box__info">
                    <li class="weui-media-box__info__meta">
                        <?php echo $msg['operator_name']?>
                    </li>
                    <li class="weui-media-box__info__meta weui-media-box__info__meta_extra">
                        <?php echo $msg['msg_time']?>
                    </li>
                </ul>

            </div>

        <?php }?>
    </div>

</div>
<script>
    function btn_submit_onclick(){
        var _comment=$("#txt_comment").val();
        if(!_comment){
            toast("Please Input Comment");
            return;
        }
        showMask();
        yo.loadData({
            _c:"report",
            _m:"submitToBM",
            param:{msg:_comment,member_id:'<?php echo $_GET["member_id"]?>'},
            callback:function(_o){
                hideMask();
                if(_o.STS){
                    toast(_o.MSG);
                    window.location.reload();
                }else{
                    toast(_o.MSG);
                }
            }
        })


    }
</script>
