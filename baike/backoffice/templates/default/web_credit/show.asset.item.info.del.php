<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css?v=1" rel="stylesheet" />
<style>
    .btn {
        border-radius: 0;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

    .ibox-title {
        padding-top: 12px!important;
        min-height: 40px;
    }

</style>
<?php
$client_info=$output['client_info'];
$asset=$output['assets_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                <li><a  class="current"><span>Asset Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Detail</h5>
                </div>
                <div class="content">
                    <table class="table table-bordered">
                        <tbody>
                            <?php if($asset['relative_id']>0){?>
                                <tr>
                                    <td>OWNER</td>
                                    <td colspan="10">
                                        <?php $rel_item=$output['client_relative'];?>
                                        <a href="<?php echo getImageUrl($rel_item['headshot']) ?>" target="_blank" title="Head portraits">
                                            <img class="img-icon"
                                                 src="<?php echo getImageUrl($rel_item['headshot'], imageThumbVersion::SMALL_ICON) ?>">
                                        </a>
                                        <label>
                                            <?php echo $rel_item['name']?>
                                        </label>
                                        <span>
                                            <?php echo $rel_item['relation_type']." / ". $rel_item['relation_name']?>
                                        </span>

                                    </td>
                                </tr>
                            <?php }?>
                            <tr>
                                <td>Asset Type</td>
                                <td><?php echo $output['title'] ?></td>
                                <td>Certification Type</td>
                                <td><?php echo $asset['asset_cert_type']?></td>
                            </tr>
                            <tr>
                                <td>Asset No.</td>
                                <td><?php echo $asset['asset_sn']?></td>
                                <td>Asset Name</td>
                                <td><?php echo $asset['asset_name']?></td>
                            </tr>
                            <tr>
                                <td>Asset State</td>
                                <td>
                                    <?php switch ($asset['asset_state']) {
                                        case assetStateEnum::CANCEL:
                                            echo $lang['asset_state_cancel'];
                                            break;
                                        case assetStateEnum::CREATE:
                                            echo $lang['asset_state_create'];
                                            break;
                                        case assetStateEnum::INVALID:
                                            echo $lang['asset_state_invalid'];
                                            break;
                                        case assetStateEnum::CERTIFIED:
                                            echo $lang['asset_state_certified'];
                                            break;
                                        case assetStateEnum::GRANTED:
                                            echo $lang['asset_state_granted'];
                                            break;
                                        default:
                                            # code...
                                            break;
                                    }?>
                                </td>
                                <td>Update Time</td>
                                <td><?php echo $asset['update_time'] ?></td>
                            </tr>
                            <tr>
                                <td>Remark</td>
                                <td colspan="3"><?php echo $asset['remark']?></td>
                            </tr>
                            <tr>
                                <td>Mortgaged</td>
                                <td >
                                    <label>
                                        <?php echo $asset['mortgage_state']?'Yes':'No'?>
                                    </label>
                                </td>
                                <td>Hold</td>
                                <td >
                                    <label>
                                        <?php echo $asset['hold_state']?'Yes':'No'?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>Valuation</td>
                                <td colspan="3"><b><?php echo $asset['officer_evaluation']?:0;?></b></td>
                            </tr>
                            <tr>
                               <td>Rental</td>
                                <td colspan="3"><b><?php echo $asset['officer_rent']?></b></td>
                            </tr>
                            <tr>
                                <td>Survey</td>
                                <td colspan="3">
                                    <?php
                                        $survey_info=$asset['survey_info'];
                                        $survey_items=my_json_decode($survey_info['survey_json']);
                                        $survey_items_type=my_json_decode($survey_info['survey_json_type']);
                                        $survey_result=my_json_decode($asset['research_text']);
                                        if(count($survey_items)){
                                    ?>
                                            <ul class="list-group">
                                                <?php foreach($survey_items as $item_k=>$item_name){?>
                                                    <li class="list-group-item">
                                                        <span class="badge">
                                                            <?php
                                                                if($survey_items_type[$item_k]==assetSurveyType::CHECKBOX){
                                                                    if($survey_result[$item_k]){
                                                                        echo 'Yes';
                                                                    }else{
                                                                        echo 'No';
                                                                    }
                                                                }else{
                                                                    echo $survey_result[$item_k];
                                                                }
                                                            ?>
                                                        </span>
                                                        <?php echo $item_name?>
                                                    </li>
                                                <?php }?>
                                            </ul>

                                    <?php }?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <div class="multiple-file-images">
                                        <?php foreach($asset['image_list'] as $img_item){?>
                                            <div class="item" style="margin: 10px;">
                                                <a href="<?php echo $img_item['image_url']?>" target="_blank">
                                                    <img style="width: 100px;height: 100px" src="<?php echo $img_item['image_url']?>">
                                                </a>
                                            </div>
                                        <?php }?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Base Info'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="infoForm">
                        <input type="hidden" name="asset_id" value="<?php echo $asset['uid']?>">
                        <input type="hidden" name="asset_type" value="<?php echo $asset['asset_type']?>">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Asset No.'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="asset_sn" placeholder="" value="<?php echo $asset['asset_sn']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span><?php echo 'Asset name'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="asset_name" placeholder="" value="<?php echo $asset['asset_name']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="saveInfo();"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript">
    function editMemberAssetInfo(){
        $('#infoModal').modal('show');
    }
    function saveInfo(){
        var values = $('#infoForm').getValues();
        var asset_sn = $.trim(values.asset_sn), asset_name = $.trim(values.asset_name);
        if(!asset_sn){
            alert('Please enter asset No.')
            return;
        }
        if(!asset_name){
            alert('Please enter asset name.')
            return;
        }
        yo.loadData({
            _c: 'web_credit',
            _m: 'editAssetBaseInfo',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    $('#infoModal').modal('hide');
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
    function deleteMemberAsset(){
        yo.confirm('Confirm','Are you sure to delete member asset?',function (_r) {
            if(!_r) return false;
            yo.loadData({
                _c: 'web_credit',
                _m: 'deleteMemberAsset',
                param: {asset_id: '<?php echo $output['asset_id'];?>'},
                callback: function (_o) {
                    if (_o.STS) {
                        alert('Deleted success!', 1, function(){
                            window.location.href = '<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>';
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }
    function changeAssetState(){
        yo.confirm('Confirm','Are you sure to change asset invalid?', function (_r) {
            if(!_r) return false;
            yo.loadData({
                _c: 'web_credit',
                _m: 'changeMemberAssetInvalid',
                param: {asset_id: '<?php echo $output['asset_id'];?>'},
                callback: function (_o) {
                    if (_o.STS) {
                        alert('Changed success!', 1, function(){
                            window.location.href = '<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>';
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }
</script>







