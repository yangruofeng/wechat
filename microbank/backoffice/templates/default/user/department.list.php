<div>
    <table class="table table-hover">
        <thead>
        <tr class="table-header">
<!--            <td>--><?php //echo 'Branch';?><!--</td>-->
            <td><?php echo 'Department Code';?></td>
            <td><?php echo 'Department Name';?></td>
<!--            <td>--><?php //echo 'Leader';?><!--</td>-->
<!--            <td>--><?php //echo 'Assistant';?><!--</td>-->
            <td><?php echo 'Creator';?></td>
            <td><?php echo 'Create Time';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $i = 0;foreach($data['data'] as $row){ ++$i;?>
            <tr>
<!--                --><?php //if($i == 1){?>
<!--                    <td rowspan="--><?php //echo count($data['data'])?><!--">-->
<!--                        --><?php //echo $row['branch_name'] ?><!--<br/>-->
<!--                    </td>-->
<!--                --><?php //}?>
                <td>
                    <?php echo $row['depart_code'] ?><br/>
                </td>
                <td>
                    <?php echo $row['depart_name'] ?><br/>
                </td>
<!--                <td>-->
<!--                    --><?php //echo $row['leader_name'] ?><!--<br/>-->
<!--                </td>-->
<!--                <td>-->
<!--                    --><?php //echo $row['assistant_name'] ?><!--<br/>-->
<!--                </td>-->
                <td>
                    <?php echo $row['creator_name'] ?><br/>
                </td>
                <td>
                    <?php echo timeFormat($row['create_time']) ?><br/>
                </td>
                <td>
                    <a title="<?php echo $lang['common_edit'] ;?>" href="<?php echo getUrl('user', 'editDepartment', array('uid'=>$row['uid'],'branch_id'=>$data['branch_id']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                        <i class="fa fa-edit"></i>
                        Edit
                    </a>
                    <a title="<?php echo $lang['common_delete'];?>" onclick="delDepart(<?php echo $row['uid'];?>)" >
                        <i class="fa fa-trash"></i>
                        Delete
                    </a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

