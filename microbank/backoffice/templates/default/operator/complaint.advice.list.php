<?php
$complaintAdviceStateLang = enum_langClass::getComplaintAdviceStateLang();
?>
<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Type</td>
            <td>Title</td>
            <td>Client Name</td>
            <td>Client Phone</td>
            <td>State</td>
            <td>Create Time</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php  foreach ($data['data'] as $row) {?>
            <tr>
                <td>
                    <?php echo $row['type'] ?>
                </td>
                <td>
                    <?php echo $row['title'] ?>
                </td>
                <td>
                    <?php echo $row['contact_name'] ?>
                </td>
                <td>
                    <?php echo $row['contact_phone'] ?>
                </td>
                <td>
                    <?php echo $complaintAdviceStateLang[$row['state']]?>
                </td>
                <td>
                    <?php echo $row['create_time'] ?>
                </td>
                <td>
                    <a class="btn btn-default" href="<?php echo getUrl('operator','details',array('uid'=>$row['uid']),false, BACK_OFFICE_SITE_URL)?>">
                        <i class="fa fa-address-card-o"></i>
                        <?php echo 'Detail'?>
                    </a>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>