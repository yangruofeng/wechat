<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="business-condition" style="margin-bottom: -5px!important;">
            <p>The Customer Authorized Contract at This Branch. </p>
        </div>
        <div class="business-content">
            <div class="business-list">
                <?php
                $certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
                $list=$output['list'];
                ?>
                <div class="container">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td>Contract-No</td>
                            <td>Authorize-Time</td>
                            <td>Asset-SN</td>
                            <td>Asset-Name</td>
                            <td>Asset-Type</td>
                            <td>Client</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if (count($list)) { ?>
                            <?php foreach ($list as $row) { ?>
                                <tr>
                                    <td><?php echo $row['contract_no']?></td>
                                    <td><?php echo $row['authorize_time']?></td>
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
