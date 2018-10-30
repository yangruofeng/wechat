<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Category'; ?></td>
            <td><?php echo 'Title'; ?></td>
<!--            <td>--><?php //echo 'Content'; ?><!--</td>-->
            <td><?php echo 'Questioner/Creator'; ?></td>
            <td><?php echo 'Is System'; ?></td>
            <td><?php echo 'Sort'; ?></td>
            <td><?php echo 'State'; ?></td>
            <td><?php echo 'Create Time'; ?></td>
            <td><?php echo 'Handler/Editor'; ?></td>
            <td><?php echo 'Handle/Edit Time'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td>
                    <?php echo ucwords($row['category']) ?><br/>
                </td>
                <td>
                    <?php echo $row['help_title'] ?><br/>
                </td>
<!--                <td>-->
<!--                    <div style="width: 200px;white-space: nowrap;overflow: hidden;text-overflow:ellipsis">--><?php //echo $row['help_content'] ?><!--</div>-->
<!--                </td>-->
                <td>
                    <?php echo $row['questioner_name'] ?><br/>
                </td>
                <td>
                    <i class="fa fa-<?php echo $row['is_system'] ? 'check' : 'times' ?>"></i>
                    <br/>
                </td>
                <td>
                    <?php echo $row['sort'] ?></i>
                    <br/>
                </td>
                <td>
                    <?php echo $row['state'] == 0 ? "Create" : ($row['state'] == 10 ? "Not Show" : "Show");?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?><br/>
                </td>
                <td>
                    <?php echo $row['handler_name'] ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['handle_time']) ?><br/>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <a class="custom-btn custom-btn-secondary" href="<?php echo getUrl('operator', 'editHelp', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL)?>">
                            <span><i class="fa fa-edit"></i>Detail</span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

