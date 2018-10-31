<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=1.2" rel="stylesheet" type="text/css"/>
<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/print.css?v=1.2" rel="stylesheet" type="text/css"/>
<?php
$data = $output['data'];
$is_print = $output['is_print'];
?>
<div class="page">
    <div class="container">
        <div class="business-condition">
            <?php if ($output['filter']) { ?>
                <div id="print-filter" style="height: 30px;">
                    <?php echo $output['filter']; ?>
                </div>
                <hr>
            <?php } ?>
        </div>

        <div class="business-content">
            <div class="business-list">
                <?php include_once(template($output['tpl'])); ?>
            </div>
        </div>
    </div>
</div>