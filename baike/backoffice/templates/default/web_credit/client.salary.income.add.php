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
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Salary</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Salary</span></a></li>
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
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Salary</h5>
                </div>
                <div class="content">
                    <form id="frm_salary" method="POST" action="<?php echo getUrl('web_credit', 'addMemberSalaryIncome', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">
                        <?php if(count($output['client_relative'])){?>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Relative</label>
                                <div class="col-sm-5">
                                    <select name="relative_id" class="form-control">
                                        <option value="0">Own</option>
                                        <?php foreach($output['client_relative'] as $rel){?>
                                            <option value="<?php echo $rel['uid']?>"><?php echo $rel['name']?></option>
                                        <?php }?>
                                    </select>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        <?php }else{?>
                            <input type="hidden" value="0" name="relative_id">
                            <input type="hidden" value="own" name="relative_name">
                        <?php }?>

                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Company Name</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="company_name" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Company Phone</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="company_phone" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Position</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" name="position" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Salary</label>
                            <div class="col-sm-5">
                                <input type="number" class="form-control" name="salary" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label">Address</label>
                            <div class="col-sm-5">
                                <input type="text" name="address_detail" class="form-control" value="">
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label">File Images</label>
                            <div class="col-sm-9 multiple-file-images clearfix">
                                <div class="multiple-image-upload item" id="imageUpload">
                                    <div id="btnUpload"><img src="resource/image/cc-upload.png?v=1" alt=""></div>
                                    <input name="image_files" type="hidden" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-offset-3 col-sm-5 form-group">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                            <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                        </div>
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
        if (!$("#frm_salary").valid()) {
            return;
        }
        $('#frm_salary').submit();
    }

    $('#frm_salary').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            company_name: {
                required: true
            },
            company_phone: {
                required: true
            },
            position: {
                required: true
            },
            salary: {
                required: true
            }
        },
        messages: {
            company_name: {
                required: '<?php echo 'Required'?>'
            },
            company_phone: {
                required: '<?php echo 'Required'?>'
            },
            position: {
                required: '<?php echo 'Required'?>'
            },
            salary: {
                required: '<?php echo 'Required'?>'
            }
        }
    });

</script>
<!--图片上传 start-->
<?php require_once template(':widget/inc_multiple_upload_upyun');?>
<script type="text/javascript">
     webuploader2upyun('btnUpload', '<?php echo fileDirsEnum::MEMBER_SALARY;?>', 'image_files', '#imageUpload', '#cloneImageItem', true);
</script>
<!--图片上传 end-->





