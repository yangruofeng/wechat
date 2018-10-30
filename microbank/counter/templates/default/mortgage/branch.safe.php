<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="business-condition" style="margin-bottom: -5px!important;">
           <a class="btn btn-default" href="<?php echo getUrl('mortgage', 'addBranchSafe', array(), false, ENTRY_COUNTER_SITE_URL)?>"><i class="fa fa-plus" style="margin-right: 2px"></i>Add Safe</a>
        </div>
        <div class="business-content">
            <div class="business-list">
                <?php $list = $output['safe_list']; ?>
                <div class="container">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td>Safe Code</td>
                            <td>Remark</td>
                            <td>Creator Name</td>
                            <td>Update Time</td>
                            <td>Function</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if (count($list)) { ?>
                            <?php foreach ($list as $row) { ?>
                                <tr>
                                    <td>
                                        <?php echo $row['safe_code'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['remark'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['operator_name'] ?>
                                    </td>
                                    <td>
                                        <?php echo timeFormat($row['update_time']) ?>
                                    </td>
                                    <td>
                                        <a class="btn btn-default"
                                           href="<?php echo getUrl('mortgage', 'editBranchSafe', array('uid' => $row['uid']), false, ENTRY_COUNTER_SITE_URL); ?>">
                                            <i class="fa fa-edit" style="margin-right: 2px"></i><?php echo 'Edit' ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5"><?php include(template(":widget/no_record"))?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
