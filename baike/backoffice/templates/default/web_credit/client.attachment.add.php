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
$client_info = $output['client_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Attachment</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Attachment</span></a></li>
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
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Attachment</h5>
                </div>
                <div class="content">
                    <form id="frm_attachment" method="POST" action="<?php echo getUrl('web_credit', 'addMemberAttachment', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Title</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="title" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Type</label>
                            <div class="col-sm-4">
                                <?php $ext_list=(new memberAttachmentTypeEnum())->Dictionary();?>
                                <select name="ext_type" class="form-control">
                                    <?php foreach($ext_list as $k=>$v){?>
                                        <option value="<?php echo $k;?>" <?php echo $attachment['ext_type'] == $k?'selected':'';?>><?php echo $v;?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group" id="div_amount" style="display: none">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Amount</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="ext_amount" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Remark</label>
                            <div class="col-sm-4">
                                <textarea name="remark" class="form-control"></textarea>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-2 control-label">File Images</label>
                            <div class="col-sm-10 multiple-file-images clearfix">
                                <div class="multiple-image-upload item" id="imageUpload">
                                    <div id="btnUpload"><img src="resource/image/cc-upload.png?v=1" alt=""></div>
                                    <input name="image_files" type="hidden" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-offset-2 col-sm-10 form-group">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                            <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                        </div>
                    </form>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="cloneImageItem" style="display: none;"><div class="item"><span class="del-item" onclick="delImageItem(this,'');"><i class="fa fa-remove"></i></span><a
                href="" target="_blank" class="img-a"><img src="" alt=""></a></div></div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
        console.log(1);
        if (!$("#frm_attachment").valid()) {
            console.log(2);
            return;
        }
        console.log(3);
        $('#frm_attachment').submit();
    }

    $('select[name="ext_type"]').change(function () {
        var _val = $(this).val();
        console.log(_val);
        if (_val == 0) {
            $('#div_amount').hide();
        } else {
            $('#div_amount').show();
        }
    })

    $('#frm_attachment').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            title: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            title: {
                required: '<?php echo 'Required'?>'
            },
            remark: {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>
<!--图片上传 start-->
<?php require_once template(':widget/inc_multiple_upload_upyun');?>
<script type="text/javascript">
     webuploader2upyun('btnUpload', '<?php echo fileDirsEnum::MEMBER_ATTACHMENT;?>', 'image_files', '#imageUpload', '#cloneImageItem', true);
</script>
<!--图片上传 end-->




