<?php echo $output['msg']; ?>
<?php if ($output['url'] != '') { ?>
    <a href="<?php echo $output['url']; ?>" class="btns"><span><?php echo 'Back to previous'; ?></span></a>
    <script type="text/javascript"> window.setTimeout("javascript:location.href='<?php echo $output['url'];?>'", <?php echo $time;?>); </script>
<?php } else { ?>
    <a href="javascript:history.back()" class="btns"><span><?php echo 'Back to previous'; ?></span></a>
    <script type="text/javascript"> window.setTimeout("javascript:history.back()", <?php echo $time;?>); </script>
<?php }?>