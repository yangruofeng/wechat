<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.css?v=1" rel="stylesheet"/>
<style>
    .container {
        max-width: 1200px!important;
    }

    .btn {
        border-radius: 0;
    }

    .table > tbody > tr > td {
        background-color: #ffffff!important;
    }

    .ibox-title {
        padding-top: 12px !important;
        min-height: 40px;
    }

    .sub-label {
        padding-left: 35px;
        padding-right: 0;
        font-weight: 400;
    }

    .pl-30 {
        padding-left: 30px;
    }

    .client_relative {
        padding-left: 0px!important;
    }

    .client_relative label{
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap
    }

    .modal-dialog {
        margin-top: 20px !important;
    }

   .map-canvas {
        width: 970px;
        height: 500px;
        margin: 0px;
        padding: 0px
    }
</style>
<?php
$client_info = $output['client_info'];
$industry_info = $output['industry_info'];
$industry_income_text = $industry_info['industry_income_text'];
$industry_expense_text = $industry_info['industry_expense_text'];
$industry_text = $industry_info['industry_text'];
$industry_place = $output['industry_place'];
$income_research = $output['business_income'];
$research_text = my_json_decode($income_research['research_text']);
$relative_id = array_column($income_research['relative_list'], 'relative_id');
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Business</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Business</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="col-sm-6" style="position: relative;margin: 10px 0 0;padding: 0 10px 0 0;">
                <div class="basic-info">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Business Research</h5>
                    </div>
                    <div class="content">
                        <form id="frm_business" method="POST" action="<?php echo getUrl('web_credit', 'editMemberBusinessIncome', array(), false, BACK_OFFICE_SITE_URL);?>">
                            <input type="hidden" name="income_id" value="<?php echo $income_research['uid']?>">
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Industry Name</label>
                                <div class="col-sm-8">
                                    <?php echo $industry_info['industry_name']?>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Business Name</label>
                                <div class="col-sm-8">
                                    <input name="branch_code" class="form-control" value="<?php echo $income_research['branch_code']?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Industry Place</label>
                                <div class="col-sm-8">
                                    <input name="industry_place" class="form-control" value="<?php echo $income_research['industry_place_text']?>">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <?php if (count($output['client_relative'])) { ?>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Relative</label>
                                    <div class="col-sm-8">
                                        <div class="col-sm-4 client_relative">
                                            <label class="checkbox-inline" title="Own"><input name="relative_id[]" type="checkbox" value="0" <?php echo in_array(0, $relative_id) ? 'checked' : '' ?>>Own</label>
                                        </div>
                                        <?php foreach ($output['client_relative'] as $rel) { ?>
                                            <div class="col-sm-4 client_relative">
                                                <label class="checkbox-inline" title="<?php echo $rel['name'] ?>"><input name="relative_id[]" type="checkbox" value="<?php echo $rel['uid']; ?>" <?php echo in_array($rel['uid'], $relative_id) ? 'checked' : ''; ?>><?php echo $rel['name'] ?></label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <input type="hidden" value="0" name="relative_id">
                                <input type="hidden" value="own" name="relative_name">
                            <?php } ?>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Employees</label>
                                <div class="col-sm-8">
                                    <input name="employees" class="form-control" value="<?php echo $income_research['employees']?>">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Income</label>
                                <div class="col-sm-8">
                                    <input type="number" name="income" class="form-control" value="<?php echo $income_research['income']?>" <?php echo count($industry_income_text) > 0 ? 'readonly' : ''?>>
                                    <div class="error_msg"></div>
                                </div>
                            </div>

                            <?php foreach ($industry_income_text as $key => $val) { ?>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label sub-label"><?php echo $val?></label>
                                    <div class="col-sm-8">
                                        <input type="number" name="research_text[<?php echo $key?>]" class="form-control income-sub" value="<?php echo $research_text[$key]; ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Expense</label>
                                <div class="col-sm-8">
                                    <input type="number" name="expense" class="form-control" value="<?php echo $income_research['expense']?>" <?php echo count($industry_expense_text) > 0 ? 'readonly' : ''?>>
                                    <div class="error_msg"></div>
                                </div>
                            </div>

                            <?php foreach ($industry_expense_text as $key => $val) { ?>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label sub-label"><?php echo $val?></label>
                                    <div class="col-sm-8">
                                        <input type="number" name="research_text[<?php echo $key?>]" class="form-control expense-sub" value="<?php echo $research_text[$key]; ?>">
                                        <div class="error_msg"></div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Profit</label>
                                <div class="col-sm-8">
                                    <input type="number" name="profit" class="form-control" value="<?php echo $income_research['profit']?>" readonly>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <?php foreach ($industry_text as $key => $val) { ?>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo $val['name']?></label>
                                    <div class="col-sm-8">
                                        <?php if($val['type'] == 'checkbox') {?>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="research_text[<?php echo $key?>]" value="1" <?php echo $research_text[$key] == 1 ? 'checked' : ''; ?>>
                                            </label>
                                        <?php } else {?>
                                            <textarea name="research_text[<?php echo $key?>]" class="form-control" ><?php echo $research_text[$key]; ?></textarea>
                                        <?php }?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label">Address</label>
                                <div class="col-sm-8">
                                    <?php $coord_array = array(
                                        0 => array('x' => $income_research['coord_x'], 'y' => $income_research['coord_y']),
                                    );
                                    $coord_json = my_json_encode($coord_array);
                                    ?>
                                    <div class="col-sm-8" style="padding-left: 0;padding-right: 0"><input type="text" name="address_detail" class="form-control" value="<?php echo $income_research['address_detail']?>"></div>
                                    <div class="col-sm-4" style="padding-right: 0;line-height: 34px"><a href="javascript:void(0)" onclick="showGoogleMap()" style="font-style: italic">Google Map</a></div>
                                    <input type="hidden" name="coord_x" value="<?php echo $income_research['coord_x']?>">
                                    <input type="hidden" name="coord_y" value="<?php echo $income_research['coord_y']?>">
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label">File Images</label>
                                <div class="col-sm-8 multiple-file-images clearfix">
                                    <?php if(count($income_research['image_list']) > 0){?>
                                        <?php foreach ($income_research['image_list'] as $v) {?>
                                            <div class="item">
                                                <span class="del-item"
                                                      onclick="delImageItem(this, '<?php echo $v['image_url']; ?>');">
                                                     <i class="fa fa-remove"></i>
                                                </span>
                                                <a href="<?php echo getImageUrl($v['image_url']); ?>" target="_blank" class="img-a">
                                                    <img src="<?php echo getImageUrl($v['image_url'], imageThumbVersion::MAX_120);?>" alt="">
                                                </a>

                                            </div>
                                        <?php }?>
                                    <?php }?>
                                    <div class="multiple-image-upload item" id="imageUpload">
                                        <div id="btnUpload"><img src="resource/image/cc-upload.png?v=1" alt=""></div>
                                        <?php $json = json_encode(array_column($income_research['image_list'],'image_url')?:array()); $json = str_replace("\\/", "/", $json); $json = str_replace('"', "'", $json);?>
                                        <input name="image_files" type="hidden" value="<?php echo $json;?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" style="text-align: center">
                                <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                <button type="button" class="btn btn-warning" onclick="btn_delete_onclick();"><i class="fa fa-close"></i>Delete</button>
                                <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php $co_list = $output['co_list'];?>
            <?php $income_research_co = $output['income_research_co'];?>
            <?php $income_research_text_co = $output['income_research_text_co'];?>
            <div class="col-sm-6" style="position: relative;margin: 10px 0 0;padding: 0 0 0 10px;">
                <div class="basic-info">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Co Research</h5>
                    </div>
                    <div class="content" style="padding: 0px">
                        <table class="table co_suggest_list">
                            <tbody class="table-body">
                            <tr>
                                <td>Research Item</td>
                                <?php foreach($co_list as $co){?>
                                    <td>
                                        <?php echo $co['officer_name']?>
                                    </td>
                                <?php }?>
                            </tr>
                            </tbody>
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Industry Place</label></td>
                                <?php foreach($co_list as $k => $co){?>
                                    <td>
                                        <em><?php echo $income_research_co[$k] ? $income_research_co[$k]['industry_place_text'] : ''?></em>
                                    </td>
                                <?php }?>
                            </tr>
                            <tr>
                                <td><label class="control-label">Relative</label></td>
                                <?php foreach($co_list as $k => $co){?>
                                    <td>
                                        <?php if ($income_research_co[$k]) { ?>
                                            <?php foreach ($income_research_co[$k]['relative_list'] as $owner) { ?>
                                                <span style="padding-right: 10px"><?php echo $owner['relative_name']; ?></span>
                                            <?php } ?>
                                        <?php } ?>
                                    </td>
                                <?php }?>
                            </tr>
                            <tr>
                                <td><label class="control-label">Employees</label></td>
                                <?php foreach($co_list as $k => $co){?>
                                    <td>
                                        <em><?php echo $income_research_co[$k] ? $income_research_co[$k]['employees'] : ''?></em>
                                    </td>
                                <?php }?>
                            </tr>
                            <tr>
                                <td><label class="control-label">Income</label></td>
                                <?php foreach($co_list as $k => $co){?>
                                    <td>
                                        <?php echo $income_research_co[$k] ? ncPriceFormat($income_research_co[$k]['income']) : ''?>
                                    </td>
                                <?php }?>
                            </tr>

                            <?php foreach ($industry_income_text as $key => $val) { ?>
                                <tr>
                                    <td><span class="pl-30"><?php echo $val?></span></td>
                                    <?php foreach($co_list as $k => $co){?>
                                        <td>
                                            <?php echo $income_research_co[$k] ? ncPriceFormat($income_research_text_co[$k][$key]) : ''?>
                                        </td>
                                    <?php }?>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td><label class="control-label">Expense</label></td>
                                <?php foreach($co_list as $k => $co){?>
                                    <td>
                                        <em><?php echo $income_research_co[$k] ? ncPriceFormat($income_research_co[$k]['expense']) : ''?></em>
                                    </td>
                                <?php }?>
                            </tr>
                            <?php foreach ($industry_expense_text as $key => $val) { ?>
                                <tr>
                                    <td><span class="pl-30"><?php echo $val?></span></td>
                                    <?php foreach($co_list as $k => $co){?>
                                        <td>
                                            <?php echo $income_research_co[$k] ? ncPriceFormat($income_research_text_co[$k][$key]) : ''?>
                                        </td>
                                    <?php }?>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td><label class="control-label">Profit</label></td>
                                <?php foreach($co_list as $k => $co){?>
                                    <td>
                                        <em><?php echo $income_research_co[$k] ? ncPriceFormat($income_research_co[$k]['profit']) : ''?></em>
                                    </td>
                                <?php }?>
                            </tr>

                            <?php foreach ($industry_text as $key => $val) { ?>
                            <tr>
                                <td><label class="control-label"><?php echo $val['name']?></label></td>
                                <?php foreach($co_list as $k => $co){?>
                                    <td>
                                        <?php if($val['type'] == 'checkbox') {?>
                                            <?php if($income_research_co[$k]) {?>
                                                <span class="col-second"><i class="fa fa-<?php echo $income_research_text_co[$k][$key] == 1 ? 'check' : 'close'; ?>" aria-hidden="true"></i></span>
                                            <?php }?>
                                        <?php } else {?>
                                            <span><?php echo $income_research_co[$k] ? $income_research_text_co[$k][$key] : ''?></span>
                                        <?php }?>
                                    </td>
                                <?php }?>
                            </tr>
                            <?php } ?>

                            <tr>
                                <td><label class="control-label">Address</label></td>
                                <?php foreach($co_list as $k => $co){?>
                                    <td>
                                    <?php if($income_research_co[$k]) { ?>
                                        <em><?php echo $income_research_co[$k]['address_detail'] ?></em>
                                        <a href="javascript:void(0)" onclick="showCoGoogleMap(<?php echo $k;?>)" style="font-style: italic;margin-left: 10px">Google Map</a>
                                    <?php } ?>
                                    </td>
                                <?php }?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-sm-6" style="position: relative;margin: 10px 0 0;padding: 0 0 0 10px;">
                <div class="basic-info">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>File Images</h5>
                    </div>
                    <div class="content" style="padding: 0px">
                        <table class="table">
                            <tbody class="table-body">
                            <?php unset($co_list[0]);foreach ($co_list as $k => $co) { ?>
                                <tr>
                                    <td><?php echo $co['officer_name']?></td>
                                    <td>
                                        <?php if ($income_research_co[$k]['business_image']) {
                                            $image_list = array();
                                            foreach ($income_research_co[$k]['business_image'] as $img_item) {
                                                $image_list[] = array(
                                                    'url' => $img_item['image_url'],
                                                    'image_source' => $img_item['image_source'],
                                                );
                                            }
                                            include(template(":widget/item.image.viewer.list"));
                                        } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="cloneImageItem" style="display: none;"><div class="item"><span class="del-item" onclick="delImageItem(this,'');"><i class="fa fa-remove"></i></span><a
                href="" target="_blank" class="img-a"><img src="" alt=""></a></div></div>
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 1000px;height: 660px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Asset Location' ?></h4>
            </div>
            <div class="modal-body">
                <div id="map-canvas" class="map-canvas">
                    <?php
                    $point = array('x' => $income_research['coord_x'], 'y' => $income_research['coord_y']);
                    include_once(template("widget/google.map.point"));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php foreach ($co_list as $k => $co) { ?>
    <?php if($income_research_co[$k]) { ?>
        <div class="modal" id="myModal<?php echo $k; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width: 1000px;height: 660px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php echo 'Asset Location' ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="map-canvas">
                            <?php
                            $map_id = 'map-' . $k;
                            $point = array('x' => $income_research_co[$k]['coord_x'], 'y' => $income_research_co[$k]['coord_y']);
                            include(template("widget/google.map.point"));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>
<?php include(template(":widget/item.image.viewer.js"));?>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.min.js?v=1"></script>
<script>
    $(function () {
        $('.income-sub').change(function () {
            profit_calc();
        })
        $('.expense-sub').change(function () {
            profit_calc();
        })
    })

    function btn_back_onclick() {
        window.history.back(-1);
    }

    function showGoogleMap() {
        $('#myModal').modal('show');
    }

    function showCoGoogleMap(id) {
        $('#myModal' + id).modal('show');
    }

    function btn_submit_onclick() {
        if (!$("#frm_business").valid()) {
            return;
        }

        if ($('input[name="relative_id[]"]').length != 0 && $('input[name="relative_id[]"]:checked').length == 0) {
            alert('Please select the relative.');
            return;
        }
        $("#frm_business").waiting();

        $('#frm_business').submit();
    }

    function btn_delete_onclick() {
        var uid = $('input[name="income_id"]').val();
        if (!uid) {
            return;
        }

        yo.confirm('Confirm','Are you sure to delete the research?', function (_r) {
            if(!_r) return false;
            yo.loadData({
                _c: 'web_credit',
                _m: 'deleteMemberBusinessIncome',
                param: {uid: uid},
                callback: function (_o) {
                    if (_o.STS) {
                        console.log(_o.MSG)
                        alert('Deleted success!', 1,function(){
                            window.location.href = '<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['client_info']['uid']), false, BACK_OFFICE_SITE_URL)?>';
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }

    function profit_calc() {
        var _income = 0;
        var _expense = 0;
        $('.income-sub').each(function () {
            var _income_sub = Number($(this).val());
            _income += _income_sub;
        })
        $('input[name="income"]').val(_income.toFixed(2));

        $('.expense-sub').each(function () {
            var _expense_sub = Number($(this).val());
            _expense += _expense_sub;
        })
        $('input[name="expense"]').val(_expense.toFixed(2));
        var _profit = Number(_income - _expense);
        $('input[name="profit"]').val(_profit.toFixed(2));
    }

    $('#frm_business').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            industry_place: {
                required: true
            },
            employees: {
                required: true
            },
            income: {
                required: true
            },
            expense: {
                required: true
            },
            profit: {
                required: true
            }
        },
        messages: {
            industry_place: {
                required: '<?php echo 'Required'?>'
            },
            employees: {
                required: '<?php echo 'Required'?>'
            },
            income: {
                required: '<?php echo 'Required'?>'
            },
            expense: {
                required: '<?php echo 'Required'?>'
            },
            profit: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
</script>

<!--图片上传 start-->
<?php require_once template(':widget/inc_multiple_upload_upyun');?>
<script type="text/javascript">
    webuploader2upyun('btnUpload', '<?php echo fileDirsEnum::MEMBER_BUSINESS;?>', 'image_files', '#imageUpload', '#cloneImageItem', true);
</script>
<!--图片上传 end-->






