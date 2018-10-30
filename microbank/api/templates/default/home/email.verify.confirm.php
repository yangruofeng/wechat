<style>
    .clearfix:after{
        content: '.';
        height: 0;
        clear: both;
        visibility: hidden;
    }

    body{
        background-color: #eff2f5;
    }

    .fg-wrap{
        width: 100%;
        margin-top: 100px;
        padding: 80px 0;
        background-color: #fff;
    }

    .fg-msg{
        max-width: 600px;
        margin: 0 auto;
    }

    .fg-mail{
        width: 95px;
        height: 95px;
        margin-right: 20px;
        float:left;

    }


    .fg-cont{
        font-size: 14px;
        padding: 5px 0;
        color: #b8c4ce;
        margin-left: 115px;
    }

    .fg-cont-t{
        font-size: 20px;
        color: #5d6d7e;
    }
</style>

<?php
$result = $output['verify_result'];
?>
<div>
    <div class="fg-wrap">
        <div class="fg-msg clearfix">
            <div class="fg-mail">
                <img src="<?php echo getConf('project_site_url').'/api/resource/img/fg-mail-w.png'; ?>" alt="" />
            </div>

            <div class="fg-cont">
                <p class="fg-cont-t"><?php echo $result['msg']; ?></p>
                <div>
                    <?php echo $result['msg']; ?>
                </div>
            </div>
        </div>
    </div>
</div>