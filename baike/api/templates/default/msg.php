<div class="container" style="padding-top: 100px">
    <div class="panel panel-primary" style="width: 300px;margin: auto;border-width: 2px">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-info"> </i>
                Message
            </h3>
        </div>
        <div class="panel-body" style="min-height: 200px;padding: 20px;font-size: 15px;font-weight: 500;">
            <?php echo $output['msg']; ?>
        </div>
        <div class="panel-footer text-right">
            <?php if ($output['url'] != '') { ?>
                <a href="<?php echo $output['url']; ?>" class="btn btn-primary btn-sm">
                    <?php echo 'Back to previous'; ?>
                </a>
                <script type="text/javascript"> window.setTimeout("javascript:location.href='<?php echo $output['url'];?>'", <?php echo 5000; //$time?:10000;?>); </script>
            <?php } else { ?>
                <a href="javascript:history.back()" class="btn btn-primary btn-sm">
                    <?php echo 'Back to previous'; ?>
                </a>
                <script type="text/javascript"> window.setTimeout("javascript:history.back()", <?php echo 5000;//$time?:10000;?>); </script>

            <?php }?>
        </div>
    </div>
</div>
