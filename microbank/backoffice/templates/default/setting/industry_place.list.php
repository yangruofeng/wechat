<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Place';?></td>
            <td><?php echo 'Remark';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['total'] > 0){ ?>
            <?php foreach($data['data'] as $row){ ?>
                <tr>
                    <td>
                        <?php echo $row['place'] ?><br/>
                    </td>
                    <td>
                        <?php echo $row['remark'] ?><br/>
                    </td>
                    <td>
                        <a href="<?php echo getUrl('setting', 'editIndustryPlace', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                            <i class="fa fa-edit"></i>
                            Edit
                        </a>
                        <a href="<?php echo getUrl('setting', 'deleteIndustryPlace', array('uid'=>$row['uid']), false, BACK_OFFICE_SITE_URL)?>" >
                            <i class="fa fa-trash"></i>
                            Delete
                        </a>
                    </td>
                </tr>
            <?php }?>
        <?php }else{ ?>
            <tr>
                <td colspan="3">
                    <div class="no-record">No Data.</div>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

