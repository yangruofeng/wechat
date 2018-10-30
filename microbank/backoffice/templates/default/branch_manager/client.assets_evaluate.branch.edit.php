<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/client.css?v=1" rel="stylesheet" type="text/css"/>
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
    }
</style>
<?php $income_research = $output['income_research']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showAssetsEvaluate', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Assets Evaluate</span></a></li>
                <li><a class="current"><span>Edit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="col-sm-6">
            <div class="co-assets-wrap">
                <div class="ibox-title" style="position: relative">
                    <h5>Edit Assets Evaluate</h5>
                </div>
                <div class="content">
                    <?php $asset_info = $output['asset_info'];?>
                    <?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
                    <?php $data = $output['data'];?>
                    <?php
                    $co_assets_list = $output['co_assets_list'];
                    $total_evaluation = 0;foreach ($co_assets_list as $ck => $cv) {
                        $total_evaluation += round($cv['evaluation'], 2);
                    }
                    $avg_evaluation = count($co_assets_list) > 0 ? ($total_evaluation / count($co_assets_list)) : 0;
                    ?>
                    <form class="form-horizontal" id="assetForm" method="post" action="<?php echo getUrl('branch_manager', 'editBmAssetEvaluate', array(), false, BACK_OFFICE_SITE_URL) ?>">
                        <input type="hidden" name="form_submit" value="ok" />
                        <input type="hidden" name="asset_id" value="<?php echo $output['asset_id']?>" />
                        <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>" />
                        <table class="table audit-table">
                            <tbody class="table-body">
                                <tr>
                                    <td><span class="pl-25">Asset Name</span></td>
                                    <td>
                                        <span style="font-size: 16px;font-weight: 600"><?php echo $asset_info['asset_name'];?></span>
                                        <em>(<?php echo $certification_type[$asset_info['asset_type']];?>)</em>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Asset Images</span></td>
                                    <td>
                                        <?php foreach($output['asset_images'] as $image){?>
                                            <a href="<?php echo getImageUrl($image['image_url']);?>">
                                                <img src="<?php echo getImageUrl($image['image_url'], imageThumbVersion::MAX_120);?>" style="max-height: 100px;margin: 5px 10px 5px 0">
                                            </a>
                                        <?php }?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Valuation</span></td>
                                    <td>
                                        <input type="text" class="form-control input-h30" name="evaluation" value="<?php echo $data['evaluation'] ?: $avg_evaluation; ?>" style="width: 300px;" />
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Remark</span></td>
                                    <td>
                                        <textarea class="form-control" name="remark" style="width: 300px;height: 100px;"><?php echo $data['remark'];?></textarea>
                                        <div class="error_msg"></div>
                                    </td>
                                </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <button type="button" class="btn btn-danger" onclick="submitForm();"><i class="fa fa-check"></i><?php echo 'Submit' ?></button>
                                    <button type="button" class="btn btn-default" onclick="javascript :history.back(-1)"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="co-assets-wrap">
                <div class="ibox-title" style="position: relative">
                    <h5>Credit Officer Assets Evaluate</h5>
                </div>
                <div class="content">
                    <?php if(count($co_assets_list) > 0){ ?>
                        <table class="table">
                            <thead>
                                <tr class="table-header">
                                    <td>CO Name</td>
                                    <td>Valuation</td>
                                    <td>Remark</td>
                                    <td>Time</td>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                <?php foreach ($co_assets_list as $ck => $cv) { ?>
                                    <tr>
                                        <td><?php echo $cv['operator_name'];?></td>
                                        <td><?php echo $cv['evaluation'] ? ncPriceFormat($cv['evaluation']) :'';?></td>
                                        <td><?php echo $cv['remark']?:'None';?></td>
                                        <td><?php echo timeFormat($cv['evaluate_time']);?></td>
                                    </tr>
                                <?php } ?>
                                <tr style="font-weight: 600">
                                    <td>--AVG--</td>
                                    <td colspan="3"><?php echo ncPriceFormat($avg_evaluation);?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php }else{ ?>
                        <div class="no-record">No Record</div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script type="text/javascript">
    function submitForm(){
        $('#assetForm').submit();
    }
</script>