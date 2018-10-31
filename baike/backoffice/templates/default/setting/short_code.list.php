<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Category';?></td>
            <td><?php echo 'Item Name';?></td>
            <td><?php echo 'Item Code';?></td>
            <td><?php echo 'Item Description';?></td>
            <td><?php echo 'Item Value';?></td>
            <td colspan="2"><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $i == 0;foreach ($data['data'] as $key => $rows) {++$i;$count = count($rows);$row = array_shift($rows);?>
            <tr class="<?php echo $i % 2 == 1 ? "tr_odd" : ""?>">
                <td rowspan="<?php echo $count?>">
                    <?php echo $key ?><br/>
                </td>
                <td><?php echo $row['item_name'];?></td>
                <td><?php echo $row['item_code'];?></td>
                <td><?php echo $row['item_desc'];?></td>
                <td><?php echo $row['item_value']>0?$row['item_value']:'';?></td>
                <td>
                    <a title="" href="<?php echo getUrl('setting', 'editShortCodeValue', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                    </a>
                    <a title="" href="<?php echo getUrl('setting', 'deleteShortCodeValue', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" >
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
                <td rowspan="<?php echo $count?>">
                    <a title="<?php echo $lang['common_add'] ;?>" href="<?php echo getUrl('setting', 'addShortCodeValue', array('category'=>$key), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-plus"></i>
                    </a>
                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('setting', 'editShortCodeCategory', array('category'=>$key), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                    </a>
                    <a title="<?php echo $lang['common_delete'];?>" href="<?php echo getUrl('setting', 'deleteShortCode', array('category'=>$key), false, BACK_OFFICE_SITE_URL)?>" >
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php foreach ($rows as $row) { ?>
                <tr class="<?php echo $i % 2 == 1 ? "tr_odd" : ""?>">
                    <td><?php echo $row['item_name']; ?></td>
                    <td><?php echo $row['item_code']; ?></td>
                    <td><?php echo $row['item_desc']; ?></td>
                    <td><?php echo $row['item_value']>0?$row['item_value']:''; ?></td>
                    <td>
                        <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('setting', 'editShortCodeValue', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                            <i class="fa fa-edit"></i>
                        </a>
                        <a title="<?php echo $lang['common_delete'];?>" href="<?php echo getUrl('setting', 'deleteShortCodeValue', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" >
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

