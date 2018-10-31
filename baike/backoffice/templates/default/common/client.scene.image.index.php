
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list">
                    <table class="table table-hover">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo 'No.';?></td>
                            <td><?php echo 'Scene Code';?></td>
                            <td><?php echo 'Biz Code';?></td>
                            <td><?php echo 'Image';?></td>
                            <td><?php echo 'Create Time';?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if($data){ ?>
                            <?php $i = 0 ;foreach($data as $row){ ++$i; ?>
                                <tr>
                                    <td>
                                        <?php echo $i ?>
                                    </td>
                                    <td>
                                        <?php echo $row['scene_code'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['biz_code']; ?>
                                    </td>
                                    <td>
                                        <img
                                            src="<?php echo getImageUrl($row['member_image'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg' ?>"
                                            class="avatar-lg">
                                    </td>
                                    <td>
                                        <?php echo timeFormat($row['create_time']); ?>
                                    </td>
                                </tr>
                            <?php }?>
                        <?php }else{ ?>
                            <tr>
                                <td colspan="7">No records</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

