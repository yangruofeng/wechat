<div>
    <table class="table table-bordered">
        <thead>
        <tr class="table-header">
            <td></td>
            <td colspan="2"><?php echo 'Category';?></td>
            <td colspan="2"><?php echo 'Category Name';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $i == 0;foreach ($data['data'] as $key => $rows) {++$i;$row = reset($rows);?>
            <tr class="<?php echo $i % 2 == 1 ? "tr_odd" : ""?>">
                <td>
                    <i category="<?php echo $key?>" class="on-off fa fa-plus-circle"></i>
                </td>
                <td colspan="2"><?php echo $row['category'] ?></td>
                <td colspan="2" category="<?php echo $row['category'] ?>"><?php echo $row['category_name'];?></td>
                <td>
                    <span class="fa-span" category="<?php echo $row['category'] ?>" category_name="<?php echo $row['category_name'] ?>" onclick="edit_category(this)">
                        <i class="fa fa-edit"></i>Edit
                    </span>
                </td>
            </tr>
            <?php if ($rows) {?>
            <tr class="category-item define-item-title <?php echo $key?> <?php echo $i % 2 == 1 ? "tr_odd" : ""?>">
                <td><?php echo 'No.'; ?></td>
                <td><?php echo 'Item Code'; ?></td>
                <td><?php echo 'Item Name'; ?></td>
                <td><?php echo 'Item Description'; ?></td>
                <td><?php echo 'Item Value'; ?></td>
                <td><?php echo 'Function';?></td>
            </tr>
            <?php $j = 0;foreach ($rows as $row) { ++$j?>
                <tr class="category-item <?php echo $key?> <?php echo $i % 2 == 1 ? "tr_odd" : ""?>">
                    <td><?php echo $j; ?></td>
                    <td><?php echo $row['item_code']; ?></td>
                    <td class="item_name"><?php echo $row['item_name']; ?></td>
                    <td class="item_desc"><?php echo $row['item_desc']; ?></td>
                    <td class="item_value"><?php echo $row['item_value'] > 0 ? $row['item_value'] : ''; ?></td>
                    <td>
                        <span class="fa-span" onclick="edit_item(this)"
                              uid="<?php echo $row['uid'] ?>"
                              category="<?php echo $row['category'] ?>"
                              item_code="<?php echo $row['item_code'] ?>"
                              item_name="<?php echo $row['item_name'] ?>"
                              item_desc="<?php echo $row['item_desc'] ?>"
                              item_value="<?php echo $row['item_value'] > 0 ? $row['item_value'] : '' ?>">
                            <i class="fa fa-edit"></i>Edit
                        </span>
                    </td>
                </tr>
            <?php } ?>
            <tr class="category-item <?php echo $key?> <?php echo $i % 2 == 1 ? "tr_odd" : ""?>">
                <td colspan="6"></td>
            </tr>
            <?php } ?>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

