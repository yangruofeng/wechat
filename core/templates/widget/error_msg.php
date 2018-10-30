<?php if($output['error_msg']){?>
    <?php   if(is_string($output['error_msg'])){ ?>
    <div style="width: 100%;text-align: center;color: #bdacac">
        <span style="font-weight: bold;margin-left: 20px;font-size: 20px">
                <?php    echo strtoupper($output['error_msg']);?>
            </span>
    </div>
    <?php }else{  var_dump($output['error_msg']);?>

    <?php }?>
<?php exit;}?>
