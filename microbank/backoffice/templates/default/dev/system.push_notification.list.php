<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'No.';?></td>
            <td><?php echo 'Message Title';?></td>
            <td><?php echo 'Message Body';?></td>
            <td><?php echo 'Message Time';?></td>
            <td><?php echo 'Message State';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $i = 0;foreach($data['data'] as $row){ ++$i?>
            <tr>
                <td>
                    <?php echo $i ?>
                </td>
                <td>
                    <?php echo $row['message_title'] ?>
                </td>
                <td class="content">
                    <?php echo $row['message_body'] ?>
                </td>
                <td>
                    <?php echo timeFormat($row['message_time']) ?>
                </td>
                <td>
                    <?php echo $row['message_state'] == 0 ? 'Normal' : ''; ?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

