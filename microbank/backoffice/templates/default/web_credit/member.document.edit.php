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
$cert_info = $output['cert_info'];
$image_version = $image_version?:imageThumbVersion::SMALL_IMG;
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Add <?php echo $output['title']?></span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$output['member_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Edit <?php echo $output['title']?></span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <?php $member_id = $client_info['uid'];?>
    <div class="container" style="margin-top: 10px;max-width: 1200px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 20px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Add Item</h5>
                </div>
                <div class="content">
                    <form id="identity_form" class="form-horizontal" method="POST" action="<?php echo getUrl('web_credit', 'submitClientNewIdentity', array(), false, BACK_OFFICE_SITE_URL);?>">
                        <input type="hidden" name="client_id" value="<?php echo $client_info['uid']?>">
                        <input type="hidden" name="cert_type" value="<?php echo $output['identity_type']?>">
                        <table class="table">
                            <?php foreach ($output['image_structure'] as $k => $item) { $file_key = $item['file_key']; ?>
                                <tr>
                                    <td style="text-align: right"><?php echo $item['des']?></td>
                                    <td>Sample</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right" class="td-key-file">

                                        <div class="image-uploader-item">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    <img id="show_<?php echo $file_key; ?>" style="width: 100px;height: 100px;margin-bottom: 10px" src="<?php echo getImageUrl($cert_info['cert_images'][$file_key]['image_url'], $image_version);?>">
                                                </li>
                                                <li class="list-group-item">
                                                    <button type="button" id="<?php echo $file_key; ?>">Upload</button>
                                                    <input name="<?php echo $file_key; ?>" type="hidden" value="<?php echo $cert_info['cert_images'][$file_key]['image_url'];?>">
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td style="vertical-align: top">
                                        <a target="_blank" href="<?php echo $item['image']?>">
                                            <img style="width: 100px;height: 100px" src="<?php echo $item['image']?>">
                                        </a>
                                    </td>
                                </tr>
                            <?php }?>
                        </table>
                        <div class="col-sm-12 form-group" style="text-align: center;margin-top: 20px">
                            <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                            <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_submit_onclick() {
        <?php foreach($output['image_structure'] as $item){?>
        var img_name = '<?php echo $item['file_key']?>';
        var img_url = $('input[name="' + img_name + '"]').val();
        if (!img_url) {
            alert('Please upload the images.');
            return;
        }
        <?php }?>
        $("#identity_form").waiting();
        $("#identity_form").submit();
    }

    function btn_back_onclick(){
        window.history.back(-1);
    }
</script>
<!--图片上传 start-->
<?php require_once template(':widget/inc_upload_upyun');?>
<script type="text/javascript">
    <?php foreach($output['image_structure'] as $item){?>
        webuploader2upyun('<?php echo $item['file_key']?>','<?php echo fileDirsEnum::FAMILY_BOOK?>');
    <?php }?>
</script>
<!--图片上传 end-->






