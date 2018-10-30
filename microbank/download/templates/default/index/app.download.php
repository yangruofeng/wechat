<?php include_once(template('widget/inc_simple_header')); ?>
<style>
    #header {
        height: 3em;
        background-color: #FFF;
        color: #333333;
    }

    #header h2 {
        font-size: 20px;
        font-weight: 600;
    }

    .download-content {
        background: url(resource/img/download-bg.png) no-repeat;
        width: 100%;
        height: 100%;
        background-size: 100%;
        position: absolute;
    }

    .app-download {
        width: 60%;
        position: absolute;
        top: 8%;
        left: 10%;
    }

    .app-download a {
        display: inline-block;
        width: 100%;
        margin-top: 3%;
    }

    .app-download img {
        width: 100%;
    }
</style>
<div class="wrap">
    <div class="download-content">
        <div class="app-download">
            <a href="<?php echo $output['member_app']['download_url_ios'] ?: '#' ?>">
                <img src="resource/img/app-icon-1.png">
            </a>
            <a href="<?php echo $output['member_app']['download_url_android'] ?: '#' ?>">
                <img src="resource/img/app-icon-2.png">
            </a>

            <a href="<?php echo $output['operator_app']['download_url'] ?: '#' ?>">
                <img src="resource/img/app-icon-3.png">
            </a>
        </div>
    </div>
</div>