<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/client.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        height: 30px;
        min-width: 80px;
        padding: 5px 12px;
        border-radius: 0px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .activity-list .item {
        margin-top: 0;
        padding: 10px 20px 10px 15px;;
    }

    .activity-list .item div > span:first-child {
        font-weight: 500;
    }

    .activity-list .item span.check-state {
        float: right;
        font-size: 12px;
        margin-left: 5px;
    }

    .activity-list .item span.check-state .fa-check {
        font-size: 18px;
        color: green;
    }

    .activity-list .item span.check-state .fa-question {
        font-size: 18px;
        color: red;
        padding-right: 5px;
    }

    #cbcModal .modal-dialog {
        margin-top: 10px!important;
    }

    #cbcModal .modal-dialog input{
        height: 30px;
    }
    table .cert_img {
        width: 120px;
        max-height: 120px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a></li>
                <li><a class="current"><span>Assets Information</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="co-assets-wrap">
            <div class="content">
                <?php $co_assets_tab = $output['co_assets_tab']; ?>
                <?php $co_assets_list = $output['co_assets_list']; ?>
                    <div class="panel-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#co_house" aria-controls="co_house" role="tab" data-toggle="tab"><?php echo 'Housing & Store';?></a>
                            </li>
                            <li role="presentation">
                                <a href="#co_car" aria-controls="co_car" role="tab" data-toggle="tab"><?php echo 'Car';?></a>
                            </li>
                            <li role="presentation">
                                <a href="#co_land" aria-controls="co_land" role="tab" data-toggle="tab"><?php echo 'Land';?></a>
                            </li>
                            <li role="presentation">
                                <a href="#co_motorbike" aria-controls="co_motorbike" role="tab" data-toggle="tab"><?php echo 'Motorbike';?></a>
                            </li>
                        </ul>
                        <div class="tab-content" style="padding: 10px 0">
                            <?php $list = $output['list']; 
                                  $houseList = $list[certificationTypeEnum::HOUSE];
                                  $carList = $list[certificationTypeEnum::CAR];
                                  $landList = $list[certificationTypeEnum::LAND];
                                  $motorbikeList = $list[certificationTypeEnum::MOTORBIKE];
                             ?>
                             <div role="tabpanel" class="tab-pane active" id="co_house">
                                <div class="contract-wrap">
                                    <?php if(count($houseList) > 0){?>
                                        <table class="table">
                                            <thead>
                                                <tr class="table-header">
                                                    <td>Index</td>
                                                    <td>Create Time</td>
                                                    <td>Verify State</td>
                                                    <td>Auditor Name</td>
                                                    <td>Auditor Time</td>
                                                    <td>Remark</td>
                                                    <td>Mug Shot</td>
                                                </tr>
                                            </thead>
                                            <tbody class="table-body">
                                                <?php foreach ($houseList as $k => $v) { ?>
                                                    <tr>
                                                        <td><?php echo $k+1;?></td>
                                                        <td><?php echo timeFormat($v['create_time']);?></td>
                                                        <td>
                                                            <?php 
                                                                if($v['verify_state'] == 10){
                                                                    echo 'pass';
                                                                }elseif($v['verify_state'] == -1){
                                                                    echo 'Auditing';
                                                                }else{
                                                                    echo 'NO Audit';
                                                                }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $v['auditor_name'] ? timeFormat($v['auditor_name']) : '-';?></td>
                                                        <td><?php echo $v['auditor_time'] ? timeFormat($v['auditor_time']) : '-';?></td>
                                                        <td><?php echo $v['verify_remark'] ? $v['verify_remark'] : '-';?></td>
                                                        <td>
                                                            <?php foreach ($v['cert_img'] as $ck => $cv) { ?>
                                                                <a href="<?php echo getImageUrl($cv['image_url']);?>" target="_blank"><img src="<?php echo getImageUrl($cv['image_url']);?>" class="cert_img" alt="Mug Shot"></a>
                                                            <?php }?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    <?php }else{ ?>
                                        <div class="no-record">No Record.</div>
                                    <?php }?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="co_car">
                                <div class="contract-wrap">
                                    <?php if(count($carList) > 0){?>
                                        <table class="table">
                                            <thead>
                                                <tr class="table-header">
                                                    <td>Index</td>
                                                    <td>Create Time</td>
                                                    <td>Verify State</td>
                                                    <td>Auditor Name</td>
                                                    <td>Auditor Time</td>
                                                    <td>Remark</td>
                                                    <td>Mug Shot</td>
                                                </tr>
                                            </thead>
                                            <tbody class="table-body">
                                                <?php foreach ($carList as $k => $v) { ?>
                                                    <tr>
                                                        <td><?php echo $k+1;?></td>
                                                        <td><?php echo timeFormat($v['create_time']);?></td>
                                                        <td>
                                                            <?php 
                                                                if($v['verify_state'] == 10){
                                                                    echo 'pass';
                                                                }elseif($v['verify_state'] == -1){
                                                                    echo 'Auditing';
                                                                }else{
                                                                    echo 'NO Audit';
                                                                }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $v['auditor_name'] ? timeFormat($v['auditor_name']) : '-';?></td>
                                                        <td><?php echo $v['auditor_time'] ? timeFormat($v['auditor_time']) : '-';?></td>
                                                        <td><?php echo $v['verify_remark'] ? $v['verify_remark'] : '-';?></td>
                                                        <td>
                                                            <?php foreach ($v['cert_img'] as $ck => $cv) { ?>
                                                                <a href="<?php echo getImageUrl($cv['image_url']);?>" target="_blank"><img src="<?php echo getImageUrl($cv['image_url']);?>" class="cert_img" alt="Mug Shot"></a>
                                                            <?php }?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    <?php }else{ ?>
                                        <div class="no-record">No Record.</div>
                                    <?php }?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="co_land">
                                <div class="contract-wrap">
                                    <?php if(count($landList) > 0){?>
                                        <table class="table">
                                            <thead>
                                                <tr class="table-header">
                                                    <td>Index</td>
                                                    <td>Create Time</td>
                                                    <td>Verify State</td>
                                                    <td>Auditor Name</td>
                                                    <td>Auditor Time</td>
                                                    <td>Remark</td>
                                                    <td>Mug Shot</td>
                                                </tr>
                                            </thead>
                                            <tbody class="table-body">
                                                <?php foreach ($landList as $k => $v) { ?>
                                                    <tr>
                                                        <td><?php echo $k+1;?></td>
                                                        <td><?php echo timeFormat($v['create_time']);?></td>
                                                        <td>
                                                            <?php 
                                                                if($v['verify_state'] == 10){
                                                                    echo 'pass';
                                                                }elseif($v['verify_state'] == -1){
                                                                    echo 'Auditing';
                                                                }else{
                                                                    echo 'NO Audit';
                                                                }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $v['auditor_name'] ? timeFormat($v['auditor_name']) : '-';?></td>
                                                        <td><?php echo $v['auditor_time'] ? timeFormat($v['auditor_time']) : '-';?></td>
                                                        <td><?php echo $v['verify_remark'] ? $v['verify_remark'] : '-';?></td>
                                                        <td>
                                                            <?php foreach ($v['cert_img'] as $ck => $cv) { ?>
                                                                <a href="<?php echo getImageUrl($cv['image_url']);?>" target="_blank"><img src="<?php echo getImageUrl($cv['image_url']);?>" class="cert_img" alt="Mug Shot"></a>
                                                            <?php }?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    <?php }else{ ?>
                                        <div class="no-record">No Record.</div>
                                    <?php }?>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="co_motorbike">
                                <div class="contract-wrap">
                                    <?php if(count($motorbikeList) > 0){?>
                                        <table class="table">
                                            <thead>
                                                <tr class="table-header">
                                                    <td>Index</td>
                                                    <td>Create Time</td>
                                                    <td>Verify State</td>
                                                    <td>Auditor Name</td>
                                                    <td>Auditor Time</td>
                                                    <td>Remark</td>
                                                    <td>Mug Shot</td>
                                                </tr>
                                            </thead>
                                            <tbody class="table-body">
                                                <?php foreach ($motorbikeList as $k => $v) { ?>
                                                    <tr>
                                                        <td><?php echo $k+1;?></td>
                                                        <td><?php echo timeFormat($v['create_time']);?></td>
                                                        <td>
                                                            <?php 
                                                                if($v['verify_state'] == 10){
                                                                    echo 'pass';
                                                                }elseif($v['verify_state'] == -1){
                                                                    echo 'Auditing';
                                                                }else{
                                                                    echo 'NO Audit';
                                                                }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $v['auditor_name'] ? timeFormat($v['auditor_name']) : '-';?></td>
                                                        <td><?php echo $v['auditor_time'] ? timeFormat($v['auditor_time']) : '-';?></td>
                                                        <td><?php echo $v['verify_remark'] ? $v['verify_remark'] : '-';?></td>
                                                        <td>
                                                            <?php foreach ($v['cert_img'] as $ck => $cv) { ?>
                                                                <a href="<?php echo getImageUrl($cv['image_url']);?>" target="_blank"><img src="<?php echo getImageUrl($cv['image_url']);?>" class="cert_img" alt="Mug Shot"></a>
                                                            <?php }?>
                                                        </td>
                                                    </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    <?php }else{ ?>
                                        <div class="no-record">No Record.</div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>