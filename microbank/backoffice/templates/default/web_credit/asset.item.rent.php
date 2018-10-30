<style>
    .btn {
        border-radius: 0;
        padding: 5px 10px;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

    .ibox-title {
        padding-top: 12px!important;
        min-height: 40px;
    }
    .fw-600 {
        font-weight: 600;
    }
</style>
<?php
$client_info=$output['client_info'];
$asset=$output['asset'];
$rental=$output['rental'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('web_credit', 'assetItemDetail', array('asset_id'=>$asset['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Asset Detail</span></a></li>
                <li><a  class="current"><span>Evaluate</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-condition">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Asset Info</h5>
                </div>
                <div class="content">
                    <table class="table table-bordered">
                        <tr>
                            <td>Asset Name</td>
                            <td class="fw-600"><?php echo $asset['asset_name']?></td>
                            <td>Asset No.</td>
                            <td class="fw-600"><?php echo $asset['asset_sn']?></td>
                            <td>Type</td>
                            <td class="fw-600"><?php echo $output['asset_type']?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Evaluate</h5>
                </div>
                <div class="content">
                    <form id="frm_item"  method="POST" enctype="multipart/form-data" action="<?php echo getUrl('web_credit', 'submitAssetRental', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="asset_id" value="<?php echo $asset['uid']?>">
                        <input type="hidden" name="uid" value="<?php echo $rental['uid']?>">

                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label">Renter</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="renter" value="<?php echo $rental['renter']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Monthly Rental</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="monthly_rent" value="<?php echo $rental['monthly_rent']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label">Remark</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="remark" value="<?php echo $rental['remark']?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <?php if($rental['operator_name']){?>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label">Creator</label>
                                <div class="col-sm-8">
                                    <label><?php echo $rental['operator_name']?></label>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label">Create Time</label>
                                <div class="col-sm-8">
                                    <label><?php echo $rental['create_time']?></label>
                                </div>
                            </div>
                        <?php }?>
                        <?php if($rental['update_operator_name']){?>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label">Update By</label>
                                <div class="col-sm-8">
                                    <label><?php echo $rental['update_operator_name']?></label>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label">Update Time</label>
                                <div class="col-sm-8">
                                    <label><?php echo $rental['update_time']?></label>
                                </div>
                            </div>
                        <?php }?>

                        <div class="col-sm-12 form-group td-more-photo">
                            <div class="div-more-photo">
                                <?php foreach($rental['image_list'] as $img){?>
                                    <div class="div-more-photo-item">
                                        <div class="multiple-file-images">
                                            <input type="hidden" name="old_image[]" value="<?php echo $img['uid']?>">
                                            <div class="item">
                                            <span class="del-item" onclick="del_image_item_onclick(this);">
                                                <i class="fa fa-remove"></i>
                                            </span>
                                                <img src="<?php echo $img['image_path']?>" style="width: 100px;height: 100px;" alt="">
                                            </div>
                                        </div>
                                    </div>

                                <?php }?>
                            </div>
                            <div>
                                <button type="button" onclick="btn_add_more_photo_onclick(this)" class="btn btn-default" style="width: 100px;height: 100px">
                                    + Add Photo
                                </button>
                            </div>
                        </div>

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
                <img src="" style="width: 100px;height: 100px;" alt="">
            </div>
        </div>
    </div>
</script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }
    function btn_submit_onclick() {
        if (!$("#frm_item").valid()) {
            return;
        }
        $("#frm_item").waiting();
        $("#frm_item").submit();

    }
    function btn_add_more_photo_onclick(e){
        var _tpl=$("#tpl_photo_item").html();
        $(e).closest('.td-more-photo').find('.div-more-photo').append(_tpl);
        var _f=$(e).closest('.td-more-photo').find('.div-more-photo').find('.div-more-photo-item').last().find('.file-more-item');

        _f.click();

    }
    function after_add_more_photo(e){
        if(e.files.length>0){
            $(e).closest('.div-more-photo-item').show();
            var _obj_url=getObjectURL(e.files[0]);
            $(e).closest('.div-more-photo-item').find('img').attr('src',_obj_url);
            var _new_name_key1=Math.floor(Math.random()*100000);
            var _new_name_key2=(new Date()).getSeconds();
            var _new_name='rent_image_'+_new_name_key1.toString()+_new_name_key2.toString();
            $(e).attr('name',_new_name);
        }else{
            $(e).closest(".div-more-photo-item").remove();
        }
    }
    function del_image_item_onclick(e){
        $(e).closest(".div-more-photo-item").remove();
    }

    function getObjectURL(file) {
        var url = null;
        if(window.createObjectURL != undefined) { // basic
            url = window.createObjectURL(file);
        } else if(window.URL != undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file);
        } else if(window.webkitURL != undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file);
        }
        return url;
    }
    $('#frm_item').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            monthly_rent: {
                required: true
            }
        },
        messages: {
            monthly_rent: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
</script>






