<?php
$item = $data['detail'];
$credit_info = $data['credit_info'];
$source_mark = 'tools_client_detail';
?>
<?php if ($item) { ?>
    <div class="client-detail-wrap clearfix">
        <?php include(template("client/client.detail.top")); ?>
        <div class="other-detail clearfix">
            <?php include(template("client/client.detail.full.left")); ?>
            <?php include(template("client/client.detail.full.right")); ?>
        </div>
    </div>
<?php } else { ?>
    <div style="padding: 10px 10px;width: 200px"><?php require template(":widget/no_record") ?></div>
<?php } ?>
