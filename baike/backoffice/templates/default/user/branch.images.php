<style>
    .btn {
        border-radius: 0;
    }

    .table > tbody > tr > td {
        background-color: #ffffff;
    !important;
    }

    .ibox-title {
        padding-top: 12px !important;
        min-height: 40px;
    }

</style>
<?php
$client_info = $output['client_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('user', 'branch', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 800px">
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px;border: 1px solid #D5D5D5">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Images</h5>
                </div>
                <div class="content" style="padding: 10px">
                    <form id="cbc_form" method="POST" enctype="multipart/form-data"
                          action="<?php echo getUrl('user', 'saveBranchImages', array(), false, BACK_OFFICE_SITE_URL); ?>">
                        <input type="hidden" name="uid" value="<?php echo $output['uid'] ?>">
                        <table width="100%">
                            <tr>
                                <td class="td-more-photo">
                                    <div class="div-more-photo">
                                        <?php foreach($output['images_list'] as $img){?>
                                            <div class="div-more-photo-item">
                                                <input type="file" style="display: none" class="file-more-item" name="branch_image_old_<?php echo $img['uid']?>">
                                                <input type="hidden" name="branch_image_id_<?php echo $img['uid']?>" value="<?php echo $img['image_url']?>">
                                                <div class="multiple-file-images">
                                                    <div class="item">
                                                        <span class="del-item" onclick="del_image_item_onclick(this);">
                                                            <i class="fa fa-remove"></i>
                                                        </span>
                                                        <img src="<?php echo getImageUrl($img['image_url'])?>" style="width: 100px;height: 100px;" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }?>
                                    </div>
                                    <div>
                                        <button type="button" onclick="btn_add_more_photo_onclick(this)"
                                                class="btn btn-default" style="width: 100px;height: 100px">
                                            + Add More
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="col-sm-12 form-group" style="text-align: center">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i
                                    class="fa fa-reply"></i>Back
                            </button>
                            <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i
                                    class="fa fa-check"></i>Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script id="tpl_photo_item" type="text/html">
    <div class="div-more-photo-item" style="display: none">
        <input type="file" style="display: none" class="file-more-item" onchange="after_add_more_photo(this);"
               name="tmp_image">

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
<script>
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
            var _new_name_key1 = Math.floor(Math.random() * 100000);
            var _new_name_key2 = (new Date()).getSeconds();
            var _new_name = 'branch_image_' + _new_name_key1.toString() + _new_name_key2.toString();
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
        $("#cbc_form").submit();
    }

</script>






