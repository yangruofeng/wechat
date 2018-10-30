<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="business-condition" style="margin-bottom: -5px!important;">
            <p>All request those has been approved and pending withdraw</p>
        </div>
        <div class="business-content">
            <div class="business-list">
                <?php
                $certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
                $flow_type=(new assetStorageFlowType())->Dictionary();
                $list=$output['list'];
                ?>
                <div class="container">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td>Request-Time</td>
                            <td>Asset-SN</td>
                            <td>Asset-Name</td>
                            <td>Asset-Type</td>
                            <td>Client</td>
                            <td>Function</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if (count($list)) { ?>
                            <?php foreach ($list as $row) { ?>
                                <tr>
                                    <td>
                                        <?php echo $row['create_time']?>
                                    </td>
                                    <td>
                                        <?php echo $row['asset_sn'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['asset_name'] ?>
                                    </td>
                                    <td>
                                        <?php echo $certificationTypeEnumLang[$row['asset_type']] ?>
                                    </td>
                                    <td>
                                        <label>
                                            <?php echo $row['member_name']?>
                                        </label>
                                        <em style="padding-left: 20px;font-size: 11px;font-style: italic">
                                            <?php echo $row['phone_id']?>
                                        </em>
                                    </td>
                                    <td>
                                        <a class="btn btn-default"
                                           href="<?php echo getUrl('mortgage', 'showPendingWithdrawDetailPage', array('request_id'=>$row['uid']), false, ENTRY_COUNTER_SITE_URL); ?>">
                                            <i class="fa fa-id-card"></i><?php echo 'Withdraw' ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="20"<?php include(template(":widget/no_record"))?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
