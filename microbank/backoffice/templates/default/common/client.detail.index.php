<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .summary-div {
        width: 16.66%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-right: 10px;
        padding-left: 10px;
    }

    .summary-div h2 {
        margin-top: 10px!important;
    }

    .stats .stat {
        padding: 7px 12px!important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Detail </h3>
            <ul class="tab-base">
                <li><a class="current"><span style="cursor: pointer" onclick="javascript:history.go(-1);"> BACK </span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php include(template("common/client.detail")); ?>
    </div>
</div>
<script>


</script>
