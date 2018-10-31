<div class="page msg_success js_show">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="<?php if($output['msg_type']==100){ echo 'weui-icon-success';}else{ echo 'weui-icon-info';}?> weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">
                <?php echo $output['msg']?>
            </h2>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <?php if ($output['url'] != '') { ?>
                    <a href="<?php echo $output['url']; ?>" class="weui-btn weui-btn_default" onclick="appBack();"><span><?php echo 'BACK'; ?></span></a>
                    <?php if(!$output['without_timeout']){?>
                        <script type="text/javascript"> window.setTimeout("javascript:location.href='<?php echo $output['url'];?>'", <?php echo 3000;?>); </script>
                    <?php }?>

                <?php } else { ?>
                    <a href="javascript:history.back(-1);" class="weui-btn weui-btn_default" onclick="appBack();"><span><?php echo 'BACK'; ?></span></a>
                    <?php if(!$output['without_timeout']){?>
                        <script type="text/javascript"> window.setTimeout("javascript:self.location.replace(document.referrer);", <?php echo 3000;?>); </script>
                    <?php }?>
                <?php }?>
            </p>
        </div>


    </div>


    <script>
        function appBack(){
            try{
                if( window.operator ){
                    window.operator.back();
                }
            }catch ( ex ){

            }
        }
    </script>


</div>





