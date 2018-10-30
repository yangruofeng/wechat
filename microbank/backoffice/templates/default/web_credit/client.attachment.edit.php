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
$attachment=$output['attachment'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Attachment</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$client_info['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
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
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Attachment
                     <?php if($attachment['state']>=100){?>
                        &nbsp;&nbsp;&nbsp;<kbd>USED</kbd>
                     <?php }?>
                    </h5>
                </div>
                <div class="content">
                    <form id="frm_attachment" method="POST" action="<?php echo getUrl('web_credit', 'editMemberAttachment', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="research_id" value="<?php echo $attachment['uid']?>">
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Title</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="title" value="<?php echo $attachment['title'];?>">
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
                        <div class="col-sm-12 form-group" id="div_amount" style="display: <?php echo $attachment['ext_type'] == 0 ? 'none' : 'block'?>">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Amount</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="ext_amount" value="<?php echo $attachment['ext_amount'];?>">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-2 control-label"><span class="required-options-xing">*</span>Remark</label>
                            <div class="col-sm-4">
                                <textarea name="remark" class="form-control"><?php echo $attachment['remark'];?></textarea>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-2 control-label">File Images</label>
                            <div class="col-sm-10 multiple-file-images clearfix">
                                <?php if(count($attachment['images']) > 0){?>
                                    <?php foreach ($attachment['images'] as $v) { $image_url = getImageUrl($v['image_url']); ?>
                                        <div class="item">
                                            <span class="del-item" onclick="delImageItem(this, '<?php echo $v['image_url'];?>');"><i class="fa fa-remove"></i></span>
                                            <a href="<?php echo $image_url; ?>" class="img-a" target="_blank">
                                                <img src="<?php echo getImageUrl($v['image_url'], imageThumbVersion::MAX_120);?>" alt="">
                                            </a>

                                        </div>
                                    <?php }?>
                                <?php }?>
                                <div class="multiple-image-upload item" id="imageUpload">
                                    <div id="btnUpload"><img src="resource/image/cc-upload.png?v=1" alt=""></div>
                                    <?php $json = json_encode(array_column($attachment['images'],'image_url'),true); $json = str_replace("\\/", "/", $json); $json = str_replace('"', "'", $json);?>
                                    <input name="image_files" type="hidden" value="<?php echo $json;?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-offset-2 col-sm-10 form-group">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                            <?php if($attachment['state']<100){?>
                                <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                            <?php }?>

                        </div>
                    </form>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="cloneImageItem" style="display: none;"><div class="item"><span class="del-item" onclick="delImageItem(this,'');"><i class="fa fa-remove"></i></span><a
                href="" class="img-a" target="_blank"><img src="" alt=""></a></div></div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
        if (!$("#frm_attachment").valid()) {
            return;
        }
        $('#frm_attachment').submit();
    }

    $('select[name="ext_type"]').change(function () {
        var _val = $(this).val();
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
            ext_amount: {
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
            ext_amount: {
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




