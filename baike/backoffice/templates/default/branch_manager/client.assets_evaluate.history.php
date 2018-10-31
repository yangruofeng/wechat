<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/client.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .asset-info {
        height: 30px;line-height: 40px;font-size: 16px;
    }

    .asset-info .col-sm-4 {
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
    }

    .asset-info .col-sm-4 span {
        font-weight: 600
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showAssetsEvaluate', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Assets Evaluate</span></a></li>
                <li><a class="current"><span>Assets Evaluate History</span></a></li>
            </ul>
        </div>
    </div>
    <?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
    <?php $list = $output['list'];?>
    <div class="container" style="max-width: 1000px">
        <div class="asset-info">
            <div class="col-sm-4">
                Asset Name:
                <span title="<?php echo $output['asset_info']['asset_name']?>"><?php echo $output['asset_info']['asset_name']?></span>
            </div>
            <div class="col-sm-4">
                Asset Type:
                <span title="<?php echo $certification_type[$output['asset_info']['asset_type']]?><"><?php echo $certification_type[$output['asset_info']['asset_type']]?></span>
            </div>
            <div class="col-sm-4">
                Operator Name:
                <span title="<?php echo $output['operator_info']['user_name']?>"><?php echo $output['operator_info']['user_name']?></span>
            </div>
        </div>

        <table class="table audit-table">
            <thead>
                <tr class="table-header">
                    <td>Index</td>
                    <td>Valuation</td>
                    <td>Remark</td>
                    <td>Valuation Time</td>
                </tr>
            </thead>
            <tbody class="table-body">
                <?php if(count($list) > 0){ ?>
                    <?php $k = 0;foreach ($list as $v) { ++$k?>
                        <tr>
                            <td><?php echo $k;?></td>
                            <td><?php echo $v['evaluation']? ncPriceFormat($v['evaluation']) :'';?></td>
                            <td><?php echo $v['remark']?:'None';?></td>
                            <td><?php echo $v['evaluate_time']? timeFormat($v['evaluate_time']) :'';?></td>
                        </tr>
                    <?php } ?>
                <?php }else{ ?>
                    <tr><td colspan="4"><div class="no-record">No record.</div></td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>