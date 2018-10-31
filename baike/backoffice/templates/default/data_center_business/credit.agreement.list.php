<?php
$list = $data['list'];
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr class="table-header t1">
        <td class="number">Contract No</td>
        <td class="number">Contract Image</td>
        <td class="number">CID</td>
        <td class="number">Member</td>
        <td class="number">Credit</td>
        <td class="number">Fee</td>
        <td class="number">Time</td>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php if(count($list)>0){?>
        <?php foreach ($list as $v) { ?>
            <tr>
                <td class="number"><?php echo $v['contract_no']; ?></td>
                <td width="400">
                    <?php
                    $images = $v['images'];
                    $image_list = array();
                    foreach($images as $img_item){
                        $image_list[] = array(
                            'url' => $img_item,
                        );
//                            $image_list[]=$img_item['image_url'];
                    }
                    include(template(":widget/item.image.viewer.list"));
                    ?>
                </td>
                <td class="number"><?php echo $v['obj_guid']; ?></td>
                <td class="number"><?php echo $v['login_code']; ?></td>
                <td class="number"><?php echo ncPriceFormat($v['total_credit']); ?></td>
                <td class="number"><?php echo ncPriceFormat($v['fee']); ?></td>
                <td class="number"><?php echo timeFormat($v['create_time']); ?></td>
            </tr>

        <?php } ?>
    <?php }else{ ?>
        <tr>
            <td colspan="9">
                <div>
                    <?php include(template(":widget/no_record")); ?>
                </div>
            </td>
        </tr>
    <?php } ?>



    </tbody>
</table>
<?php include_once(template("widget/inc_content_pager")); ?>
<?php include(template(":widget/item.image.viewer.js"));?>
