<?php
$list = $data['data'];
?>
<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr class="table-header t1">
            <td class="number">No</td>
            <td class="number">ID</td>
            <td class="number">Name</td>
            <td class="number">Phone</td>
            <td class="number">Position</td>
            <td class="number">Status</td>
        </tr>
    </thead>
    <tbody class="table-body">
        <?php if( count($list) > 0 ){ ?>
            <?php foreach( $list as $v ){ ?>
                <tr>
                    <td class="number"><?php echo $v['no'];?></td>
                    <td class="number"><?php echo $v['uid'];?></td>
                    <td class="number"><?php echo $v['user_name'];?></td>
                    <td class="number"><?php echo $v['mobile_phone'];?></td>
                    <td class="number"><?php echo $v['user_position'];?></td>
                    <td class="number <?php echo $v['user_status'] ? 'green' : 'red';?>"><?php echo $v['user_status']?'Active':'Inactive';?></td>
                </tr>
            <?php } ?>
        <?php }else{ ?>
            <tr>
                <td colspan="6">
                    <div>
                        <?php include(template(":widget/no_record")); ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php if(count($list) > 0){?>
    <?php include_once(template("widget/inc_content_pager")); ?>
<?php }else{?>
    <?php if($data['pageNumber'] != 1){?>
        <?php include_once(template("widget/inc_content_pager")); ?>
    <?php }?>
<?php }?>

