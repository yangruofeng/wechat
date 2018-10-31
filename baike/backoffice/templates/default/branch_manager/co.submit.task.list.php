<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>CO Finished Research</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <table class="table table-striped table-hover table-bordered">
            <tr class="table-header">
                <td>CID</td>
                <td>Client Name</td>
                <td>CO Name</td>
                <td>Comment</td>
                <td>Submit Time</td>
                <td>Function</td>
            </tr>
            <?php if(!$output['task_list']){?>
                <tr>
                    <td colspan="10"><?php include_once(template(":widget/no_record"))?></td>
                </tr>
            <?php }?>
            <?php foreach($output['task_list'] as $item){?>
                <tr>
                    <td><?php echo $item['obj_guid']?></td>
                    <td><?php echo $item['display_name']?></td>
                    <td><?php echo $item['co_name']?></td>
                    <td><?php echo $item['msg']?></td>
                    <td><?php echo $item['create_time']?></td>
                    <td>
                        <a class="btn btn-link" href="<?php echo getBackOfficeUrl("web_credit","getCoSubmitTask",array("task_id"=>$item['task_id'],"member_id"=>$item['member_id']))?>">
                            Handle
                        </a>
                    </td>
                </tr>
            <?php }?>
        </table>
    </div>
</div>
