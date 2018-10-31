<?php
$attachment = $output['attachment'];
?>
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td>Title</td>
                            <td>Type</td>
                            <td>Remark</td>
                            <td>Operator</td>
                            <td>Images</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if ($attachment){ ?>
                            <?php foreach ($attachment as $k => $v) {?>
                                <tr>
                                    <td><?php echo $v['title'] ?></td>
                                    <td>
                                        <?php if ($v['ext_type'] == 0) { ?>
                                            Certificate
                                        <?php } else if($v['ext_type'] == 1) { ?>
                                            <?php echo 'Income: ' . ncPriceFormat($v['ext_amount']) ?>
                                        <?php } else { ?>
                                            <?php echo 'Expense: ' . ncPriceFormat($v['ext_amount']) ?>
                                        <?php }  ?>
                                    </td>
                                    <td><?php echo $v['remark'] ?></td>
                                    <td><?php echo $v['update_operator_name'] ?: $v['operator_name']; ?></td>
                                    <td>
                                        <?php
                                        $image_list=array();
                                        foreach($v['image_list'] as $img_item){
                                            $image_list[]=$img_item['image_url'];
                                        }
                                        include(template(":widget/item.image.viewer.list"));
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5">
                                    <?php include(template(":widget/no_record")); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>