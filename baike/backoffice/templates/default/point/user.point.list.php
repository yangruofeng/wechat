<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'User Name';?></td>
            <td><?php echo 'Department';?></td>
            <td><?php echo 'Point';?></td>
            <td><?php echo 'Audited Number';?></td>
            <td><?php echo 'To Audit Number';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr uid="<?php echo $row['user_id']?>">
                <td>
                    <?php echo $row['user_name'] ?>
                </td>
                <td class="depart">
                    <?php echo $row['branch_name'] . ' ' . $row['depart_name'] ?>
                </td>
                <td class="point">
                    <?php echo ncPriceFormat($row['point_total'])?>
                </td>
                <td>
                    <?php echo $row['audit_count']?:0?>
                </td>
                <td>
                    <?php echo $row['to_audit_count']?:0?>
                </td>
                <td>
                    <a class="btn btn-default" style="padding: 5px 10px" title="<?php echo 'Detail' ;?>" href="<?php echo getUrl('point', 'userPointDetail', array('uid' => $row['user_id']), false, BACK_OFFICE_SITE_URL) ?>">
                        <i class="fa fa-search"></i>
                        Detail
                    </a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>

