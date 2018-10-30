<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'No.';?></td>
            <td><?php echo 'Original State';?></td>
            <td><?php echo 'Current State';?></td>
            <td><?php echo 'Remark';?></td>
            <td><?php echo 'Operator';?></td>
            <td><?php echo 'Time';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']){ ?>
            <?php $i = 0 ;foreach($data['data'] as $row){ ++$i; ?>
                <tr>
                    <td>
                        <?php echo $i ?>
                    </td>
                    <td>
                        <?php echo $lang['client_member_state_' . $row['original_state']] ?>
                    </td>
                    <td>
                        <?php echo $lang['client_member_state_' . $row['current_state']] ?>
                    </td>
                    <td>
                        <?php echo $row['remark']; ?>
                    </td>
                    <td>
                        <?php echo $row['creator_name']; ?>
                    </td>
                    <td>
                        <?php echo timeFormat($row['create_time']); ?>
                    </td>
                </tr>
            <?php }?>
        <?php }else{ ?>
             <tr>
                 <td colspan="7">No records</td>
             </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
