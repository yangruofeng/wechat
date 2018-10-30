<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><input type="checkbox" id="select_all"></td>
            <td><?php echo 'User Name';?></td>
            <td><?php echo 'Department';?></td>
            <td><?php echo 'Event Name';?></td>
            <td><?php echo 'Point(Value*Factor)';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Auditor Name';?></td>
            <td><?php echo 'Audit Time';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr uid="<?php echo $row['uid']?>">
                <td>
                    <?php if($row['state'] == 10 && $row['leader'] == $data['operator_id']){?>
                        <input type="checkbox" name="uid" value="<?php echo $row['uid']?>">
                    <?php }?>
                </td>
                <td>
                    <?php echo $row['user_name'] ?>
                </td>
                <td class="depart">
                    <?php echo $row['branch_name'] . ' ' . $row['depart_name'] ?>
                </td>
                <td class="event">
                    <?php echo $row['event_name'] ?>
                </td>
                <td class="point">
                    <?php echo ncPriceFormat($row['point'] * $row['point_factor'] / 100)?>
                    <?php if($row['need_audit']){?>
                        <span style="font-style: italic;color: #0B0;"><?php echo '(' . $row['point'] . '*' . $row['point_factor'] . '%)'?></span>
                    <?php }?>
                </td>
                <td class="state">
                    <?php echo $row['state'] == 10 ? 'To audit' : 'Audited'; ?>
                </td>
                <td class="creator_name">
                    <?php echo $row['auditor_name'] ?>
                </td>
                <td class="create_time">
                    <?php echo timeFormat($row['audit_time']) ?>
                </td>
                <td class="create_time">
                    <?php echo timeFormat($row['create_time']) ?>
                </td>
                <td>
                    <?php if($row['need_audit'] && $row['leader'] == $data['operator_id']){?>
                        <button class="btn btn-default" style="padding: 5px 10px" title="<?php echo $lang['common_edit'] ;?>" onclick="audit(<?php echo $row['uid']?>,<?php echo $row['point_factor']?>,'<?php echo $row['remark']?>')">
                            <i class="fa fa-check"></i>
                            Audit
                        </button>
                    <?php }?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

