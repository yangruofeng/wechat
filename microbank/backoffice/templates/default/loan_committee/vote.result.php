<?php foreach ($data['data'] as $v) { ?>
    <tr>
        <td><?php echo $v['user_name']; ?></td>
        <td><i class="fa fa-<?php echo $v['vote_result'] == commonApproveStateEnum::CREATE ? 'question' : ($v['vote_result'] == commonApproveStateEnum::PASS ? 'check' : 'close')?>"></i></td>
        <td><?php echo $v['update_time']?></td>
        <td><?php echo $v['vote_remark']; ?></td>
    </tr>
<?php } ?>