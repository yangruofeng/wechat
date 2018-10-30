<div class="important-info-1 clearfix">
    <div class="info">
        <p class="name">
            <?php echo $data['user_info']['user_name']?:$data['user_info']['login_code'] ?>
        </p>
        <p>
            <span>Department:
                <span style="font-weight: 700"><?php echo $data['user_info']['branch_name'] . ' ' . $data['user_info']['depart_name'] ?></span>
            </span>
        </p>
    </div>
    <div class="statistical-report clearfix">
        <div class="item">
            Point Total
            <p><?php echo ncPriceFormat($data['user_info']['point_total']) ?></p>
        </div>
        <div class="item">
            Point Total(Search)
            <p><?php echo ncPriceFormat($data['user_info']['point_total_curr'])?></p>
        </div>
        <div class="item">
            Audited Number(Search)
            <p><?php echo $data['user_info']['audit_count']?:0?></p>
        </div>
        <div class="item">
            To Audit Number(Search)
            <p><?php echo $data['user_info']['to_audit_count']?:0?></p>
        </div>
        <div class="item">
            Audit Score(Average)
            <p><?php echo ncPriceFormat($data['user_info']['average_point'])?></p>
        </div>
    </div>
</div>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><input type="checkbox" id="select_all"></td>
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
                    <?php if($row['state'] == 10 && $data['is_leader']){?>
                        <input type="checkbox" name="uid" value="<?php echo $row['uid']?>">
                    <?php }?>
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

