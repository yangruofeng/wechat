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
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Add <?php echo $output['title']?></span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a class="current"><span>Add <?php echo $output['title']?></span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Add Item</h5>
                </div>
                <div class="content">
                    <form id="cbc_form" method="POST" enctype="multipart/form-data" action="<?php echo getUrl('web_credit', 'submitNewAssetItem', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">
                        <input type="hidden" name="asset_type" value="<?php echo $output['asset_type']?>">

                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Certification No.</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="asset_sn" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Certification Name</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="asset_name" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Certification Type</label>
                            <div class="col-sm-6">
                                <?php $ct_list=$output['cert_type_list'];?>
                                <select name="asset_cert_type" class="form-control">
                                    <?php foreach($ct_list as $ct_k=>$ct_v){?>
                                        <option value="<?php echo $ct_k?>"><?php echo $ct_v?></option>
                                    <?php }?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Issued Date</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="cert_issue_time" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <?php if (count($output['client_relative'])) { ?>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Owner</label>
                                <div class="col-sm-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="relative_id[]" value="0"><?php echo 'Own'; ?>
                                    </label>
                                    <?php foreach ($output['client_relative'] as $rel) { ?>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="relative_id[]" value="<?php echo $rel['uid'] ?>"><?php echo $rel['name']; ?>
                                        </label>
                                    <?php } ?>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="relative_id" value="0">
                        <?php }?>
                        <table class="table">
                            <?php foreach($output['image_structure'] as $item){?>
                               <tr>
                                   <td style="text-align: right"><?php echo $item['des']?></td>
                                   <td>Sample</td>
                               </tr>
                                <tr>
                                    <td style="text-align: right" class="td-key-file">
                                        <input type="file" class="file-item" style="display: none" onchange="after_choose_file(this);" name="<?php echo $item['file_key']?>">
                                        <button type="button" onclick="choose_file_onclick(this);" class="btn btn-default" style="width: 100px;height: 100px;background-size: 100% 100%">
                                            + Photo
                                        </button>
                                    </td>
                                    <td>
                                        <a target="_blank" href="<?php echo $item['image']?>">
                                            <img style="width: 100px;height: 100px" src="<?php echo $item['image']?>">
                                        </a>
                                    </td>
                                </tr>
                            <?php }?>
                            <tr>
                                <td colspan="4" class="td-more-photo">
                                    <div class="div-more-photo">

                                    </div>
                                    <div>
                                        <button type="button" onclick="btn_add_more_photo_onclick(this)" class="btn btn-default" style="width: 100px;height: 100px">
                                            + Add More
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="col-sm-12 form-group" style="text-align: center">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                            <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script id="tpl_photo_item" type="text/html">
    <div class="div-more-photo-item" style="display: none">
        <input type="file"  style="display: none" class="file-more-item" onchange="after_add_more_photo(this);" name="tmp_image">
        <div class="multiple-file-images">
            <div class="item">
                <span class="del-item" onclick="del_image_item_onclick(this);">
                    <i class="fa fa-remove"></i>
                </span>
                <a href="" target="_blank" class="img-a">
                    <img src="" style="width: 100px;height: 100px;" alt="">
                </a>

            </div>
        </div>
    </div>
</script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        $('[name="cert_issue_time"]').datepicker({
            format: 'yyyy-mm-dd'
        });
    })

    function choose_file_onclick(e) {
        var _f = $(e).closest('.td-key-file').find('.file-item');
        _f.click();
    }

    function after_choose_file(e) {
        var _obj_url = getObjectURL(e.files[0]);
        var _b = $(e).closest('.td-key-file').find('.btn');
        _b.css("background-image", "url(" + _obj_url + ")");
    }

    function btn_back_onclick() {
        window.history.back(-1);
    }

    function btn_add_more_photo_onclick(e) {
        var _tpl = $("#tpl_photo_item").html();
        $(e).closest('.td-more-photo').find('.div-more-photo').append(_tpl);
        var _f = $(e).closest('.td-more-photo').find('.div-more-photo').find('.div-more-photo-item').last().find('.file-more-item');

        _f.click();
    }

    function after_add_more_photo(e) {
        if (e.files.length > 0) {
            $(e).closest('.div-more-photo-item').show();
            var _obj_url = getObjectURL(e.files[0]);
            $(e).closest('.div-more-photo-item').find('img').attr('src', _obj_url);
            $(e).closest('.div-more-photo-item').find('.img-a').attr('href', _obj_url);
            var _new_name_key1 = Math.floor(Math.random() * 100000);
            var _new_name_key2 = (new Date()).getSeconds();
            var _new_name = 'asset_image_' + _new_name_key1.toString() + _new_name_key2.toString();
            $(e).attr('name', _new_name);
        } else {
            $(e).closest(".div-more-photo-item").remove();
        }
    }

    function del_image_item_onclick(e) {
        $(e).closest(".div-more-photo-item").remove();
    }

    function getObjectURL(file) {
        var url = null;
        if (window.createObjectURL != undefined) { // basic
            url = window.createObjectURL(file);
        } else if (window.URL != undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file);
        } else if (window.webkitURL != undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file);
        }
        return url;
    }

    function btn_submit_onclick() {
        if (!$("#cbc_form").valid()) {
            return;
        }
        if ($('input[name="relative_id[]"]').length != 0 && $('input[name="relative_id[]"]:checked').length == 0) {
            alert('Please select the relative.');
            return;
        }
        $(".business-content").waiting();
        $("#cbc_form").submit();

    }

    $('#cbc_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            asset_sn: {
                required: true
            },
            asset_name: {
                required: true
            },
            asset_cert_type: {
                required: true
            },
            cert_issue_time: {
                required: true
            }
        },
        messages: {
            asset_sn: {
                required: '<?php echo 'Required'?>'
            },
            asset_name: {
                required: '<?php echo 'Required'?>'
            },
            asset_cert_type: {
                required: '<?php echo 'Required'?>'
            },
            cert_issue_time: {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>






