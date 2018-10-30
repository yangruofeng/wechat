<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>
<style>
    ul li{
        list-style: none;
    }
</style>
<div style="width: 100%;text-align: center;padding-top: 200px">
    <p><b>Current version (<span id="version"><?php echo $output['version']?> </span>) is invalid, you must update your client.</b></p>
    <p>If you cannot update automatically, click follow url to download the latest version.</p>
    <p><a style="font-size: 16px" href="<?php echo getConf('app_download_url')."/".$output['download_url'] ?>">
            <?php echo getConf('app_download_url')."/".$output['download_url'] ?>
        </a>
    </p>
<!--    <p style="margin-top: 30px;font-size: 16px">-->
<!--        Setup steps:-->
<!--    </p>-->

    <div style="margin-top: 50px" class="container">
        <div class="col-sm-12" style="text-align: center">
            <div class="col-sm-8" style="margin-left: 190px">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Setup steps:</h5>
                    </div>
                    <div class="content">
                        <ul style="text-align: center;">
                            <li>(1). Download setup package.</li>
                            <li>(2). Close current program.</li>
                            <li>(3). Uninstall old Samrithisak-Client</li>
                            <li>(4). Unzip the setup package and then run setup.exe</li>
                            <li>(5). Finish.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
<script>
    $(function () {
        if(window.external){
            try{
                var version = window.external.getCurrentClientVersion();
                $('#version').text(version);
            }catch (ex){
                alert(ex.Message);
            }
        }
    });
</script>

