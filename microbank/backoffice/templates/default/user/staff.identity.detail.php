<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=10" rel="stylesheet" type="text/css"/>
<style>
    .custom-btn-group {
        float: inherit;
    }

    .cerification-history {
        margin-top: 20px;
    }

    .verify-table img {
        width: 80px;
    }

    .cerification-history .table .table-header {
        background: none;
    }

    #select_area .col-sm-6 {
        width: 200px;
        padding-left: 0;
    }
</style>
<?php 
$info = $output['identity_info'];
$staff_info = $output['staff_info']; 
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="item-title">
                <h3>Staff</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('user', 'staff', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                    <li><a id="anchor_back" href="<?php echo getUrl('user', 'showStaffInfo', array('uid' => $staff_info['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Staff Detail</span></a></li>
                    <li><a  class="current"><span>Detail</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <table class="table">
            <tbody class="table-body">
            <tr>
                <td><label class="control-label">Display Name</label></td>
                <td colspan="3"><?php echo $staff_info['display_name']; ?></td>
            </tr>
            <?php if ($output['cert_sample_images'][$info['identity_type']]) { ?>
                <tr>
                    <td><label class="control-label">Sample</label></td>
                    <td colspan="3">
                        <?php foreach( $output['cert_sample_images'][$info['identity_type']] as $sample ){  ?>
                            <div style="display:inline-block;width: 200px;text-align: center;margin-right: 5px;">
                                <a target="_blank" href="<?php echo $sample['image']; ?>">
                                    <img src="<?php echo $sample['image']; ?>" style="width: 150px;height: 150px" />
                                </a>
                                <h5 style="color:red;">
                                    <?php echo $sample['des']; ?>
                                </h5>
                            </div>
                        <?php }  ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td><label class="control-label">Images</label></td>
                <td colspan="3">
                    <?php
                    $viewer_width = 460;
                    $cert_image = $info['images'];
                    $image_list = array();
                    foreach ($cert_image as $img_item) {
                        $image_list[] = array(
                            'url' => $img_item['image_url'],
                            'image_source' => $img_item['image_source'],
                        );
                    }
                    include(template(":widget/item.image.viewer.list"));
                    ?>
                </td>
            </tr>

            <?php if ($info['identity_type'] == certificationTypeEnum::ID) { ?>
                <tr>
                    <td><label class="control-label">English Name</label></td>
                    <td>
                        <?php echo implode(' ', my_json_decode($staff_info['id_en_name_json'])); ?>
                    </td>
                    <td>
                        <label class="control-label">Khmer Name</label>
                    </td>
                    <td>
                        <?php echo implode(' ', my_json_decode($staff_info['id_kh_name_json'])); ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Id Number</label></td>
                    <td>
                        <?php echo $staff_info['id_number']; ?>
                    </td>
                    <td><label class="control-label">Identity Type</label></td>
                    <td>
                        <?php echo $staff_info['id_type'] == 1 ? "Foreign Country" : "Homeland"; ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Gender</label></td>
                    <td>
                        <?php echo ucwords($staff_info['gender']); ?>
                    </td>
                    <td><label class="control-label">Date of Birth</label></td>
                    <td>
                        <?php echo dateFormat($staff_info['birthday']); ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Nationality</label></td>
                    <td>
                        <?php echo strtoupper($staff_info['nationality']); ?>
                    </td>
                    <td><label class="control-label">Expire Time</label></td>
                    <td>
                        <?php echo dateFormat($info['expire_time']); ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Birth Address</label></td>
                    <td>
                        <?php echo $staff_info['full_address']; ?>
                    </td>
                    <td><label class="control-label">Identity State</label></td>
                    <td>
                        <?php echo $info['identity_state'] == 1 ? 'Normal' : 'Expired'; ?>
                    </td>
                </tr>
            <?php } else { ?>
                <tr>
                    <td><label class="control-label">Identity State</label></td>
                    <td colspan="3">
                        <?php echo $info['identity_state'] == 1 ? 'Normal' : 'Expired'; ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="4" style="text-align: center;border-top: 1px solid #ddd;padding-top: 10px">
                    <div class="custom-btn-group approval-btn-group">
                        <?php if ($info['identity_state'] == 1) { ?>
                            <button type="button" class="btn btn-danger" id="btn_expire" onclick="expire(<?php echo $info['uid']; ?>, 0);"><i
                                    class="fa fa-vcard-o"></i>Set Expired
                            </button>
                        <?php } ?>
                        <button type="button" class="btn btn-normal" onclick="javascript:history.go(-1);"><i
                                class="fa fa-vcard-o"></i>Back
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js"));?>
<script>
    function expire(info_uid, btn){
        $.messager.confirm("Set Identity To Expired", "Are you sure to set this Identity to expired?", function (_r) {
            if (!_r) return;
            $("div.page").waiting();
            yo.loadData({
                _c: "user",
                _m: "setIdentityExpired",
                param: {uid: info_uid},
                callback: function (_obj) {
                    $("div.page").unmask();
                    if (!_obj.STS) {
                        alert(_obj.MSG);
                    } else {
                        if(btn != 0){
                            $(btn+' .current').click();
                        }else{
                            location.href = $("#anchor_back").attr("href");
                        }
                    }
                }
            })
        });
        //重新計算彈出框位置，以當前點擊的按鈕為準
        var style = $('.messager-window').attr('style'),  shadow_style = $('.window-shadow').attr('style');
        var top = ($( ".messager-window" ).offset().top - $('.messager-window').outerHeight()) + "px";
        style = style+'top: '+top+';';
        shadow_style = shadow_style+'top: '+top+';';
        $("div.messager-window").css("cssText", style);
        $("div.window-shadow").css("cssText", shadow_style);
    }
</script>


