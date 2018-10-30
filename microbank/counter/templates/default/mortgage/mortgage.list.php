<?php
$certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Contract No</td>
            <td>Client Code</td>
            <td>Client Phone</td>
            <td>Mortgage Type</td>
            <td>Mortgage Name</td>
            <td>Material Amount</td>
            <td>Operator Time</td>
            <td>State</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['contract_no'] ?>
                    </td>
                    <td>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['phone_id'] ?>
                    </td>
                    <td>
                        <?php echo $certificationTypeEnumLang[$row['asset_type']] ?>
                    </td>
                    <td>
                        <?php echo $row['asset_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['img_amount'] ?>
                    </td>
                    <td>
                        <?php echo $row['create_time'] ?>
                    </td>
                    <td>
                        <?php if($row['type']==1){
                            echo 'Being Stored';
                        }else{
                            echo 'Took Out';
                        }?>
                    </td>
                    <td>
                        <a class="btn btn-default" href="<?php echo getUrl('mortgage', 'mortgageDetail', array('uid'=>$row['uid']), false, ENTRY_COUNTER_SITE_URL); ?>"><i class="fa fa-id-card"></i><?php echo 'detail' ?></a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="9">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager")); ?>
