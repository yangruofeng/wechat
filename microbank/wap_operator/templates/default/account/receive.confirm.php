<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/home.css?v=2">
<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/inc_header.css?v=6">
<header class="top-header" id="header" style="display: <?php echo $_GET['source'] == 'app' ? 'none' : 'block';?>">
    <h2 class="title">Credit Officer Transfer</h2>
</header>
<div class="wrap assets-evalute-wrap">
    <?php $data = $output['data']; ?>

    <?php if( $data ){ ?>

        <div>
            <form class="custom-form" id="" method="post">
                <div class="cerification-input aui-margin-b-10">
                    <div class="loan-form">
                        <ul class="aui-list aui-form-list loan-item">

                            <li class="aui-list-item">
                                <div class="aui-list-item-inner">
                                    <div class="aui-list-item-label label">
                                        Credit Officer
                                    </div>
                                    <div class="aui-list-item-input label-on">
                                        <?php
                                        echo $data['sender_handler_name'];
                                        ?>
                                    </div>
                                </div>
                            </li>

                            <li class="aui-list-item">
                                <div class="aui-list-item-inner">
                                    <div class="aui-list-item-label label">
                                        Receiver
                                    </div>
                                    <div class="aui-list-item-input label-on">
                                        <?php
                                        echo $data['receiver_handler_name'];
                                        ?>
                                    </div>
                                </div>
                            </li>

                            <li class="aui-list-item">
                                <div class="aui-list-item-inner">
                                    <div class="aui-list-item-label label">
                                        Amount
                                    </div>
                                    <div class="aui-list-item-input label-on">
                                        <?php

                                        echo $data['amount'];
                                        ?>
                                    </div>
                                </div>
                            </li>

                            <li class="aui-list-item">
                                <div class="aui-list-item-inner">
                                    <div class="aui-list-item-label label">
                                        Currency
                                    </div>
                                    <div class="aui-list-item-input label-on">
                                        <?php

                                        echo $data['currency'];
                                        ?>
                                    </div>
                                </div>
                            </li>

                            <li class="aui-list-item">
                                <div class="aui-list-item-inner">
                                    <div class="aui-list-item-label label">
                                        Transfer Time
                                    </div>
                                    <div class="aui-list-item-input label-on">
                                        <?php

                                        echo $data['create_time'];
                                        ?>
                                    </div>
                                </div>
                            </li>

                            <li class="aui-list-item">
                                <div class="aui-list-item-inner">
                                    <div class="aui-list-item-label label">
                                        Remark
                                    </div>
                                    <div class="aui-list-item-input label-on">
                                        <?php

                                        echo $data['remark'];
                                        ?>
                                    </div>
                                </div>
                            </li>

                            <?php if( $data['state'] != bizStateEnum::DONE && $data['state'] != bizStateEnum::REJECT ){ ?>

                                <li class="aui-list-item">
                                    <div class="aui-list-item-inner">
                                        <div class="aui-list-item-label label">
                                            Trading Password
                                        </div>
                                        <div class="aui-list-item-input">
                                            <input type="password" class="mui_input" name="trading_password" id="trading_password" value="" />
                                        </div>
                                    </div>
                                </li>

                            <?php }else{ ?>
                                <li class="aui-list-item">
                                    <div class="aui-list-item-inner">
                                        <div class="aui-list-item-label label">
                                            Handle Result
                                        </div>
                                        <div class="aui-list-item-input label-on">
                                            <?php
                                               if( $data['state'] == bizStateEnum::DONE ){
                                                   echo 'Confirm Receive';
                                               }else{
                                                   echo 'Rejected';
                                               }
                                            ?>
                                        </div>
                                    </div>
                                </li>
                                <li class="aui-list-item">
                                    <div class="aui-list-item-inner">
                                        <div class="aui-list-item-label label">
                                            Handle Time
                                        </div>
                                        <div class="aui-list-item-input label-on">
                                            <?php

                                            echo $data['update_time'];
                                            ?>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>






                        </ul>
                    </div>
                </div>

                <?php if( $data['state'] != bizStateEnum::DONE && $data['state'] != bizStateEnum::REJECT ){ ?>
                    <div style="padding: 0 .8rem;">
                        <div class="aui-btn aui-btn-danger aui-btn-block custom-btn custom-btn-purple aui-margin-t-15" id="submit">Confirm</div>
                        <div class="aui-btn  aui-btn-block custom-btn  aui-margin-t-15" id="btn_reject">Reject</div>

                    </div>
                <?php } ?>

            </form>

            <div class="upload-success">
                <div class="content">
                    <img src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/image/gou.png" alt="">
                    <p class="title"><?php echo 'Handle Successfully';?></p>
                </div>
            </div>
        </div>
    <?php }else{ ?>
        <div style="text-align: center;font-size: 18px;padding: 20px 0;">
            Error Data!
        </div>
    <?php } ?>


</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/common.js?v=1"></script>
<script type="text/javascript">

    var _biz_id = '<?php echo $data['uid'];?>';

    $('#submit').on('click', function(){

        var trading_password = $.trim($('#trading_password').val());
        if(!trading_password){
            verifyFail('<?php echo 'Please input trading password.';?>');
            return;
        }

        toast.loading({
            title: '<?php echo $lang['label_loading'];?>'
        });

        $.ajax({
            type: 'post',
            url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=co_app&op=submitReceiveConfirm',
            data: {uid: _biz_id,trading_password: trading_password,type:1},
            dataType: 'json',
            success: function(data){

                toast.hide();

                if(data.STS){
                    $('.upload-success').show();

                    var times = setTimeout(function(){
                        $('.upload-success').hide();
                        window.location.reload();
                    },3000);


                }else{
                    verifyFail(data.MSG);
                }

            },
            error: function(xhr, type){
                toast.hide();
                verifyFail('<?php echo 'Handle fail!';?>');
            }
        });
    });

    $('#btn_reject').on('click', function(){

        var trading_password = $.trim($('#trading_password').val());
        if(!trading_password){
            verifyFail('<?php echo 'Please input trading password.';?>');
            return;
        }

        toast.loading({
            title: '<?php echo $lang['label_loading'];?>'
        });

        $.ajax({
            type: 'post',
            url: '<?php echo WAP_OPERATOR_SITE_URL;?>/index.php?act=co_app&op=submitReceiveConfirm',
            data: {uid: _biz_id,trading_password: trading_password,type:-1},
            dataType: 'json',
            success: function(data){

                toast.hide();

                if(data.STS){
                    $('.upload-success').show();

                    var times = setTimeout(function(){
                        $('.upload-success').hide();
                        window.location.reload();
                    },3000);


                }else{
                    verifyFail(data.MSG);
                }

            },
            error: function(xhr, type){
                toast.hide();
                verifyFail('<?php echo 'Handle fail!';?>');
            }
        });
    });



</script>
