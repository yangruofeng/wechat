
<div>
    <?php if( empty($data['data']) ){ ?>
        <div class="no-record">
            No data.
        </div>
    <?php }else{ ?>
        <table class="table table-hover table-bordered">
            <tr class="table-header">
                <td>Client Photo</td>
                <td>Request Photo</td>
                <td>Client CID</td>
                <td>Client Account</td>
                <td>Client Phone</td>
                <td>Request Time</td>
                <?php if( $data['state'] == commonApproveStateEnum::REJECT || $data['state'] == commonApproveStateEnum::PASS ){ ?>
                    <td>
                        Operator
                    </td>
                    <td>
                        Operate Time
                    </td>
                    <td>
                        Operate Remark
                    </td>
                <?php }else{ ?>
                    <td>
                        Function
                    </td>
                <?php } ?>
            </tr>
            <?php foreach(  $data['data'] as $item ){ ?>
                <tr>
                    <td>
                        <a href="<?php echo getImageUrl($item['client_original_image']) ?>">
                            <img class="avatar-icon" src="<?php echo getImageUrl($item['client_original_image'],imageThumbVersion::AVATAR) ?>">
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo getImageUrl($item['member_image']) ?>">
                            <img class="avatar-icon" src="<?php echo getImageUrl($item['member_image'],imageThumbVersion::AVATAR) ?>">
                        </a>
                    </td>
                    <td>
                        <?php echo $item['obj_guid']; ?>
                    </td>
                    <td>
                        <?php echo $item['login_code']; ?>
                    </td>
                    <td>
                        <?php echo $item['phone_id']; ?>
                    </td>
                    <td>
                        <?php echo timeFormat($item['create_time']); ?>
                    </td>
                    <?php if( $data['state'] == commonApproveStateEnum::REJECT || $data['state'] == commonApproveStateEnum::PASS ){ ?>
                        <td>
                            <?php echo $item['operator_name']; ?>
                        </td>
                        <td>
                            <?php echo timeFormat($item['operate_time']); ?>
                        </td>
                        <td>
                            <?php echo $item['operate_remark']; ?>
                        </td>
                    <?php }else{ ?>
                        <td>
                            <?php if($item['state']==commonApproveStateEnum::CREATE){?>
                                <div class="custom-btn-group">
                                    <a class="custom-btn custom-btn-secondary"
                                       href="<?php echo getUrl('operator', 'getTaskOfClientChangeTradingPassword', array('uid' => $item['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <span><i class="fa fa-vcard-o"></i><?php echo 'Detail';?></span>
                                    </a>
                                </div>
                            <?php }else{?>
                                Operator : <?php echo $item['operator_name']?>
                            <?php }?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

</div>

<?php include_once(template("widget/inc_content_pager")); ?>