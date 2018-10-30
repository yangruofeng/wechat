<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.css?v=1" rel="stylesheet"/>
<style>
    .btn {
        border-radius: 0;
        padding: 5px 12px;
        min-width: 60px;
    }

    .table > tbody > tr > td {
        background-color: #ffffff !important;
    }

    .ibox-title {
        padding-top: 12px !important;
        min-height: 40px;
    }

    .table > tbody > tr > td:last-child {
        width: 80px;
        /*text-align: center;*/
    }

    #myModal .modal-dialog, #infoModal .modal-dialog {
        margin-top: 20px !important;
    }

    #map-canvas {
        width: 970px;
        height: 500px;
        margin: 0px;
        padding: 0px
    }

</style>
<?php
$client_info = $output['client_info'];
$asset = $output['assets_info'];
$asset_state_lang = enum_langClass::getAssetStateLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li>
                    <a href="<?php echo getUrl('web_credit', 'creditClient', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a>
                </li>
                <li><a class="current"><span>Asset Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container test-mask"  style="margin-top: 10px;max-width: 1200px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary") ?>
        </div>
        <div class="business-content row">
            <div class="col-sm-7">
                <div class="basic-info container table-responsive" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Detail</h5>
                    </div>
                    <div class="content table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                            <?php if ($asset['relative_list']) { ?>
                                <tr>
                                    <td>Owner</td>
                                    <td colspan="10">
                                        <?php $relative_id = array();
                                        foreach ($asset['relative_list'] as $relative) {
                                            $relative_id[] = $relative['relative_id']; ?>
                                            <span
                                                style="display: inline-block;margin: 0 15px 0 0;<?php echo $relative['relative_id'] == 0 ? 'font-style: italic' : '' ?>">
                                                <?php echo $relative['relative_id'] == 0 ? 'Own' : $relative['relative_name']; ?>
                                            </span>
                                        <?php } ?>

                                        <!--                                        --><?php //$rel_item=$output['client_relative'];?>
                                        <!--                                        <a href="-->
                                        <?php //echo getImageUrl($rel_item['headshot']) ?><!--" target="_blank" title="Head portraits">-->
                                        <!--                                            <img class="img-icon" src="-->
                                        <?php //echo getImageUrl($rel_item['headshot'], imageThumbVersion::SMALL_ICON) ?><!--">-->
                                        <!--                                        </a>-->
                                        <!--                                        <label>-->
                                        <!--                                            --><?php //echo $rel_item['name']?>
                                        <!--                                        </label>-->
                                        <!--                                        <span>-->
                                        <!--                                            --><?php //echo $rel_item['relation_type']." / ". $rel_item['relation_name']?>
                                        <!--                                        </span>-->
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td>Asset Type</td>
                                <td><?php echo $output['title'] ?></td>
                                <td>Certification Type</td>
                                <td ><?php echo $asset['asset_cert_type'] ?></td>
                                <td>
                                    <button class="btn btn-default" onclick="editMemberAssetCertType();">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Asset No.</td>
                                <td><?php echo $asset['asset_sn'] ?></td>
                                <td>Asset Name</td>
                                <td><?php echo $asset['asset_name'] ?></td>
                                <td><a class="btn btn-default" onclick="editMemberAssetInfo();">Edit</a></td>
                            </tr>
                            <tr>
                                <td>Asset State</td>
                                <td>
                                    <?php
                                        echo $asset_state_lang[$asset['asset_state']];
                                    ?>
                                </td>
                                <td>Issued Date</td>
                                <td colspan="2"><?php echo $asset['cert_issue_time']?date('Y-m-d',strtotime($asset['cert_issue_time'])):''; ?></td>
                            </tr>
                            <tr>
                                <td>Update Time</td>
                                <td colspan="10"><?php echo $asset['update_time']; ?></td>

                            </tr>
                            <tr>
                                <td>Remark</td>
                                <?php if ($asset['coord_x'] != 0 || $asset['coord_y'] != 0) { ?>
                                    <?php $coord_array = array(
                                        0 => array('x' => $asset['coord_x'], 'y' => $asset['coord_y']),
                                    );
                                    $coord_json = my_json_encode($coord_array);

                                    ?>
                                    <td colspan="3"><?php echo $asset['remark'] ?></td>
                                    <td><a href="javascript:void(0)" onclick="showGoogleMap()" style="font-style: italic">Google Map</a></td>
                                <?php } else { ?>
                                    <td colspan="4"><?php echo $asset['remark'] ?></td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td>Mortgaged</td>
                                <td>
                                    <label>
                                        <?php echo $asset['mortgage_state'] ? 'Yes' : 'No' ?>
                                    </label>
                                </td>
                                <td>Hold</td>
                                <td>
                                    <label>
                                        <?php echo $asset['hold_state'] ? 'Yes' : 'No' ?>
                                    </label>
                                </td>
                                <td>
                                    <?php
                                    $request_withdraw_msg = '';
                                    $allow_request = false;
                                    if ($asset['hold_state']) {
                                        if (count($output['request_withdraw'])) {
                                            $request_withdraw_state = $output['request_withdraw']['state'];
                                            if ($request_withdraw_state > assetRequestWithdrawStateEnum::REJECT) {
                                                $dict_sts = (new assetRequestWithdrawStateEnum())->Dictionary();
                                                $request_withdraw_msg = $dict_sts[$request_withdraw_state];
                                            } else {
                                                $allow_request = true;
                                            }
                                        } else {
                                            $allow_request = true;
                                        }
                                    }
                                    ?>
                                    <?php if ($allow_request) { ?>
                                        <a class="btn btn-default"
                                           href="<?php echo getUrl("web_credit", "showRequestWithdrawMortgagePage", array("asset_id" => $asset['uid']), false, BACK_OFFICE_SITE_URL) ?>">Request
                                            Withdraw</a>
                                    <?php } elseif ($request_withdraw_msg) { ?>
                                        <span>Withdraw</span>
                                        <label><?php echo $request_withdraw_msg ?></label>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Evaluation</td>
                                <td colspan="3"><b><?php echo $asset['officer_evaluation'] ?: 0; ?></b></td>
                                <td><a class="btn btn-default"
                                       href="<?php echo getUrl('web_credit', 'showAssetsEvaluatePage', array('asset_id' => $output['asset_id']), false, BACK_OFFICE_SITE_URL) ?>">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Rental</td>
                                <td colspan="3"><b><?php echo $asset['officer_rent'] ?></b></td>
                                <td><a class="btn btn-default"
                                       href="<?php echo getUrl('web_credit', 'showAssetsRentalPage', array('asset_id' => $output['asset_id']), false, BACK_OFFICE_SITE_URL) ?>">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Survey</td>
                                <td colspan="3">
                                    <?php
                                    $survey_info = $asset['survey_info'];
                                    $survey_items = my_json_decode($survey_info['survey_json']);
                                    $survey_items_type = my_json_decode($survey_info['survey_json_type']);
                                    $survey_result = my_json_decode($asset['research_text']);
                                    if (count($survey_items)) {
                                        ?>
                                        <ul class="list-group">
                                            <?php foreach ($survey_items as $item_k => $item_name) { ?>
                                                <li class="list-group-item">
                                                        <span class="badge">
                                                            <?php
                                                            if ($survey_items_type[$item_k] == assetSurveyType::CHECKBOX) {
                                                                if ($survey_result[$item_k]) {
                                                                    echo 'Yes';
                                                                } else {
                                                                    echo 'No';
                                                                }
                                                            } else {
                                                                echo $survey_result[$item_k];
                                                            }
                                                            ?>
                                                        </span>
                                                    <?php echo $item_name ?>
                                                </li>
                                            <?php }?>
                                        </ul>

                                    <?php } ?>
                                </td>
                                <td>
                                    <a class="btn btn-default"
                                       href="<?php echo getUrl('web_credit', 'showAssetsSurveyPage', array('asset_id' => $output['asset_id']), false, BACK_OFFICE_SITE_URL) ?>">Edit</a>
                                </td>

                            </tr>
                            <?php if (!$asset['mortgage_state']) { ?>
                                <tr>
                                    <td>
                                        <!--<a class="btn btn-primary btn-block" href="<?php echo getUrl('web_credit', 'showAssetItemEditPage', array('asset_id' => $output['asset_id']), false, BACK_OFFICE_SITE_URL) ?>">Modify</a>-->
                                    </td>
                                    <td colspan="4">
                                        <a class="btn btn-warning" onclick="deleteMemberAsset();">Delete</a>
                                        <?php if ($asset['asset_state'] != assetStateEnum::INVALID) { ?>
                                            <a class="btn btn-primary" onclick="changeAssetState();">ChangeState</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php $all_images_list=array(); foreach ($asset['image_list_group_creator'] as $images) { $val = reset($images)?>
                                <tr>
                                    <td><?php echo $val['creator_name']?:'Asset Images';?></td>
                                    <td colspan="4">
                                        <?php
                                        $viewer_width = 460;
                                        $cert_image = $images;
                                        $image_list = array();
                                        foreach ($cert_image as $img_item) {
                                            $image_list[] = array(
                                                'uid'=>$img_item['uid'],
                                                'url' => $img_item['image_url'],
                                                'image_source' => $img_item['image_source'],
                                            );
                                        }
                                        $all_images_list=array_merge($all_images_list,$image_list);
                                        include(template(":widget/item.image.viewer.list"));
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            <!--show出来抵押时拍的照片-->
                            <?php if( !empty($output['asset_mortgage_images'])){  ?>
                                <tr>
                                    <td><?php echo 'Mortgaged Images';?></td>
                                    <td colspan="4">
                                        <?php
                                        $viewer_width = 460;
                                        $image_list = $output['asset_mortgage_images'];
                                        include(template(":widget/item.image.viewer.list"));
                                        ?>
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="basic-info container" style="margin-top: 10px">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Manage Images</h5>
                    </div>
                    <div class="content">
                        <?php
                        include(template("web_credit/asset.item.images"))
                        ?>
                        <div class="text-center">
                            <button class="btn btn-danger" onclick="addImageForeAsset();">Add Image</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js")); ?>


<div class="modal" id="assetCertTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Asset Cert Type' ?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="assetCertTypeInfoForm">
                        <input type="hidden" name="asset_id" value="<?php echo $asset['uid'] ?>">

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><span
                                    class="required-options-xing">*</span><?php echo 'Asset No.' ?></label>

                            <div class="col-sm-8">
                                <select name="asset_cert_type" class="form-control" id="">
                                    <?php
                                        $cert_type_list=$output['cert_type_list'];
                                    foreach( $cert_type_list as $k=>$v ){ ?>
                                        <option value="<?php echo $k; ?>" <?php if( $k == $asset['asset_cert_type']){ echo 'selected';} ?> ><?php echo $v; ?></option>
                                    <?php } ?>
                                </select>

                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel' ?></button>
                <button type="button" class="btn btn-danger" onclick="editAssetCertTypeSubmit();"><?php echo 'Submit' ?></button>
            </div>
        </div>
    </div>
</div>



<div class="modal" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Base Info' ?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="infoForm">
                        <input type="hidden" name="asset_id" value="<?php echo $asset['uid'] ?>">
                        <input type="hidden" name="asset_type" value="<?php echo $asset['asset_type'] ?>">

                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span
                                    class="required-options-xing">*</span><?php echo 'Asset No.' ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="asset_sn" placeholder=""
                                       value="<?php echo $asset['asset_sn'] ?>">

                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span
                                    class="required-options-xing">*</span><?php echo 'Asset name' ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="asset_name" placeholder=""
                                       value="<?php echo $asset['asset_name'] ?>">

                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span
                                    class="required-options-xing">*</span><?php echo 'Issued Date' ?></label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="cert_issue_time" placeholder=""
                                       value="<?php echo $asset['cert_issue_time']?date('Y-m-d',strtotime($asset['cert_issue_time'])):''; ?>">

                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <?php if (count($output['client_relative'])) { ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Relative</label>

                                <div class="col-sm-8">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="relative_id"
                                               value="0" <?php echo in_array(0, $relative_id) ? 'checked' : '' ?>><?php echo 'Own'; ?>
                                    </label>
                                    <?php foreach ($output['client_relative'] as $rel) { ?>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="relative_id"
                                                   value="<?php echo $rel['uid'] ?>"  <?php echo in_array($rel['uid'], $relative_id) ? 'checked' : '' ?>><?php echo $rel['name']; ?>
                                        </label>
                                    <?php } ?>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="relative_id" value="0">
                        <?php } ?>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel' ?></button>
                <button type="button" class="btn btn-danger" onclick="saveInfo();"><?php echo 'Submit' ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 1000px;height: 660px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Asset Location' ?></h4>
            </div>
            <div class="modal-body">
                <div id="map-canvas">
                    <?php
                    $point=array('x' => $asset['coord_x'], 'y' => $asset['coord_y']);
                    include_once(template("widget/google.map.point"));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="cloneImageItem" style="display: none;"><div class="item"><span class="del-item" onclick="delImageItem(this,'image_files');"><i class="fa fa-remove"></i></span><img src="" alt=""></div></div>
<div class="modal" id="assetAddMoreImage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Add Image' ?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="assetAddMoreImageForm">
                        <input type="hidden" name="asset_id" value="<?php echo $asset['uid'] ?>">
                        <div class="multiple-file-images clearfix">

                            <div class="multiple-image-upload item" id="imageUpload">
                                <div id="btnUpload"><img src="resource/image/cc-upload.png?v=1" alt=""></div>
                                <input name="image_files" type="hidden" value="">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel' ?></button>
                <button type="button" class="btn btn-danger" onclick="assetAddImageSubmit();"><?php echo 'Submit' ?></button>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.min.js?v=1"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    $(function () {
        $('[name="cert_issue_time"]').datepicker({
            format: 'yyyy-mm-dd'
        });
    });

    function showGoogleMap() {
        $('#myModal').modal('show');
    }

    function editMemberAssetCertType()
    {
        $('#assetCertTypeModal').modal('show');
    }

    function editMemberAssetInfo() {
        $('#infoModal').modal('show');
    }

    function editAssetCertTypeSubmit(){
        var _values = getFormJson('#assetCertTypeInfoForm');

        $('body').waiting();
        yo.loadData({
            _c: 'web_credit',
            _m: 'editAssetCertType',
            param: _values,
            callback: function (_o) {
                $('#assetCertTypeModal').modal('hide');
                $('body').unmask();

                if (_o.STS) {
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function saveInfo() {
        var values = getFormJson('#infoForm');
        var asset_sn = $.trim(values.asset_sn), asset_name = $.trim(values.asset_name), cert_issue_time = $.trim(values.cert_issue_time);
        if (!asset_sn) {
            alert('Please enter asset No.');
            return;
        }
        if (!asset_name) {
            alert('Please enter asset name.');
            return;
        }

        if (!cert_issue_time) {
            alert('Please enter  issued date.');
            return;
        }

        if ($('input[name="relative_id"][type="checkbox"]').length != 0 && $('input[name="relative_id"][type="checkbox"]:checked').length == 0) {
            alert('Please select the relative.');
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

    function deleteMemberAsset() {
        yo.confirm('Confirm','Are you sure to delete member asset?', function (_r) {
            if(!_r) return false;
            yo.loadData({
                _c: 'web_credit',
                _m: 'deleteMemberAsset',
                param: {asset_id: '<?php echo $output['asset_id'];?>'},
                callback: function (_o) {
                    if (_o.STS) {
                        alert('Deleted successfully!',1,function(){
                            window.location.href = '<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>';
                        });
                    } else {
                        alert(_o.MSG, 2);
                    }
                }
            });
        });
    }

    function changeAssetState() {
        yo.confirm('Confirm','Are you sure to change asset invalid?',function (_r) {
            if(!_r) return false;
            yo.loadData({
                _c: 'web_credit',
                _m: 'changeMemberAssetInvalid',
                param: {asset_id: '<?php echo $output['asset_id'];?>'},
                callback: function (_o) {
                    if (_o.STS) {
                        alert('Changed success!',1,function(){
                            window.location.href = '<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>';
                        });
                    } else {
                        alert(_o.MSG, 2);
                    }
                }
            });
        });
    }

    function addImageForeAsset()
    {
        $('#assetAddMoreImage').modal('show');
    }

    function assetAddImageSubmit()
    {
        var _values = getFormJson('#assetAddMoreImageForm');
        $('body').waiting();
        yo.loadData({
            _c:'web_credit',
            _m: 'assetAddMoreImage',
            param:_values,
            callback: function(_o){
                $('body').unmask();
                $('#assetAddMoreImage').modal('hide');
                if( _o.STS ){
                    alert('Add success.',1,function(){
                        window.location.reload();
                    });
                }else{
                    alert(_o.MSG,2);
                }
            }
        });
    }
</script>


<!--图片上传 start-->
<?php require_once template(':widget/inc_multiple_upload_upyun');?>
<script type="text/javascript">
    webuploader2upyun('btnUpload', '<?php echo fileDirsEnum::MEMBER_ASSETS;?>', 'image_files', '#imageUpload', '#cloneImageItem', true);
</script>
<!--图片上传 end-->