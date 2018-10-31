<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/client.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        height: 30px;
        min-width: 80px;
        padding: 5px 12px;
        border-radius: 0px;
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
        height: 45px;
    }

</style>
<?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
<?php $bm_assets_list = $output['bm_assets_list']; ?>
<?php $co_assets_list = $output['co_assets_list']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a></li>
                <li><a class="current"><span>Assets Evaluate</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 1300px">
        <div class="branch-assets-wrap col-sm-6" style="padding-left: 0px">
            <div class="ibox-title" style="position: relative">
                <h5>Branch Assets Evaluate</h5>
            </div>
            <div class="content" style="padding: 0">
                <table class="table">
                    <thead>
                        <tr class="table-header">
                            <td>Asset Name</td>
                            <td>Asset Type</td>
                            <td>Valuation</td>
<!--                            <td>Operator</td>-->
                            <td>Function</td>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php foreach ($bm_assets_list as $k => $v) { ?>
                            <tr>
                                <td><?php echo $v['asset_name'];?></td>
                                <td><?php echo $certification_type[$v['asset_type']];?></td>
                                <td><?php echo $v['valuation'] ? ncPriceFormat($v['valuation']) : ''; ?></td>
<!--                                <td>--><?php //echo $v['operator_name'];?><!--</td>-->
                                <td>
                                    <a type="button" class="btn btn-danger" href="<?php echo getUrl('branch_manager', 'editBmAssetEvaluate', array('uid' => $v['uid'],'member_id'=>$output['member_id'],'type'=>$v['asset_type']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-edit"></i><?php echo 'Edit' ?></a>
                                    <a type="button" class="btn btn-primary" href="<?php echo getUrl('branch_manager', 'showAssetEvaluateHistory', array('uid' => $v['uid'], 'member_id' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-list"></i><?php echo 'History' ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="co-assets-wrap col-sm-6" style="padding-right: 0">
            <div class="ibox-title" style="position: relative">
                <h5>Credit Officer Assets Evaluate</h5>
            </div>
            <div class="content" style="padding: 0;">
                <table class="table">
                    <thead>
                    <tr class="table-header">
                        <td>Asset Name</td>
                        <td>Asset Type</td>
                        <?php foreach ($output['co_list'] as $co) { ?>
                            <td><?php echo $co['officer_name'] ?></td>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php foreach ($bm_assets_list as $k => $v) { ?>
                        <tr>
                            <td><?php echo $v['asset_name'];?></td>
                            <td><?php echo $certification_type[$v['asset_type']];?></td>
                            <?php foreach ($output['co_list'] as $co) { $evaluation = $co_assets_list[$v['uid']][$co['officer_id']]['evaluation'];?>
                                <td>
                                    <?php if($evaluation){?>
                                        <?php echo ncPriceFormat($evaluation);?>
                                        <a href="<?php echo getUrl('branch_manager', 'showAssetEvaluateHistory', array('uid' => $v['uid'], 'operator_id' => $co['officer_id'], 'member_id' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><?php echo 'History' ?></a>
                                    <?php }?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>