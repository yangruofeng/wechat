<?php
$asset = $data['data'];
$client_info = $data['client_info'];
$asset_evaluate = $data['asset_evaluate'];
$asset_rental = $data['asset_rental'];
$storage_list = $data['storage_list'];
$loan_list = $data['loan_list'];
?>
<?php if($asset){?>
    <div class="business-condition">
        <?php require_once template("widget/item.member.summary")?>
    </div>
    <div class="business-content">
        <?php include(template("widget/item.asset.reference"))?>
    </div>
<?php }else{?>
    <div style="width: 200px;padding: 10px 10px"><?php require template(":widget/no_record")?></div>
<?php }?>

