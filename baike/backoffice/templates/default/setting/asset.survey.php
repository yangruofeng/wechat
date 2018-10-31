<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Asset Survey</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-content">
            <div class="business-list">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr class="table-header">
                        <td><?php echo 'Asset Type';?></td>
                        <td><?php echo 'Survey Info';?></td>
<!--                        <td>--><?php //echo 'Credit Rate'?><!--</td>-->
<!--                        <td>--><?php //echo 'State';?><!--</td>-->
                        <td><?php echo 'Creator';?></td>
                        <td><?php echo 'Create Time';?></td>
                        <td><?php echo 'Function';?></td>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php $certification_type = enum_langClass::getCertificationTypeEnumLang();?>
                    <?php $asset_survey_list = $output['asset_survey'];?>
                    <?php foreach ($output['asset_type'] as $asset_type) { $asset_survey = $asset_survey_list[$asset_type]?>
                        <tr>
                            <td>
                                <?php echo $certification_type[$asset_type] ?><br/>
                            </td>
                            <td>
                                <?php
                                $survey_arr = my_json_decode($asset_survey['survey_json']);
                                $arr_values = array_values($survey_arr);
                                $str_values = join("/", $arr_values);
                                echo $str_values;
                                ?><br/>
                            </td>
                            <td>
                                <?php echo $asset_survey['creator_name'] ?><br/>
                            </td>
                            <td>
                                <?php echo timeFormat($asset_survey['create_time']); ?><br/>
                            </td>
                            <td>
                                <a href="<?php echo getUrl('setting', 'editAssetSurvey', array('asset_type'=>$asset_type), false, BACK_OFFICE_SITE_URL)?>" style="margin-right: 5px" >
                                    <i class="fa fa-edit"></i>
                                    Edit
                                </a>
                                <?php if ($asset_survey) { ?>
<!--                                    <a href="--><?php //echo getUrl('setting', 'editAssetSurvey', array('asset_type'=>$asset_type), false, BACK_OFFICE_SITE_URL)?><!--" >-->
<!--                                        <i class="fa fa-trash"></i>-->
<!--                                        Delete-->
<!--                                    </a>-->
                                    <a href="#" onclick="delete_asset_survey('<?php echo $asset_type?>')">
                                        <i class="fa fa-trash"></i>
                                        Delete
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function delete_asset_survey(asset_type) {
        $.messager.confirm("<?php echo $lang['common_delete']?>", "<?php echo $lang['common_confirm_delete']?>", function (_r) {
            if (!_r) return;
            $(".business-content").waiting();
            yo.loadData({
                _c: "setting",
                _m: "deleteAssetSurvey",
                param: {asset_type: asset_type},
                callback: function (_o) {
                    $(".business-content").unmask();
                    if (_o.STS) {
                        alert(_o.MSG);
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000)
                    } else {
                        alert(_o.MSG);
                    }
                }
            });
        });
    }
</script>