<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/magnifier/magnifier.css" rel="stylesheet" type="text/css"/>
<?php
$certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
?>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .input-h30 {
        height: 30px !important;
    }

    .explain {
        padding-left: 10px;
        font-style: italic;
        color: #b3b3b3;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    #check_list td {
        width: 25%;
    }

    #check_list .num {
        width: 20px;
        height: 20px;
        display: inline-block;
        border: 1px solid #FFE299;
        border-radius: 10px;
        line-height: 18px;
        text-align: center;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        /*padding: 20px 15px 20px;*/
        background-color: #FFF;
        overflow: hidden;
    }

    .content td {
        padding-left: 15px!important;
        padding-right: 15px!important;
    }

    .nav-tabs {
        height: 34px!important;
    }

    .nav-tabs li a {
        padding: 7px 12px !important;
    }

    .tab-content label {
        margin-bottom: 0px!important;
    }

    .form-horizontal .control-label {
        text-align: left;
    }

    .content label {
        margin-bottom: 0px;
    }

</style>
<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <?php $mortgage = $output['mortgage'];?>
    <div class="container">
        <div class="register-div" style="width: 900px">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Mortgage Detail</h5>
                </div>
                <div class="content" style="padding: 0">
                    <table class="table">
                        <tbody class="table-body">
                        <tr>
                            <td><label class="control-label">Mortgage Name</label></td>
                            <td><?php echo $mortgage['asset_name']; ?></td>
                            <td><label class="control-label">Mortgage Type</label></td>
                            <td><?php echo $lang['certification_type_' . $mortgage['asset_type']]; ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Contract Id</label></td>
                            <td><?php echo $mortgage['contract_no']; ?></td>
                            <td><label class="control-label">Mortgage State</label></td>
                            <td>
                                <?php if($mortgage['type']==1){
                                    echo 'Being Stored';
                                } else {
                                    echo 'Took Out';
                                }?>
                        </tr>
                        <tr>
                            <td><label class="control-label">Client Code</label></td>
                            <td><?php echo $mortgage['login_code']; ?></td>
                            <td><label class="control-label">Client Phone</label></td>
                            <td><?php echo $mortgage['phone_id']; ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Handler Name</label></td>
                            <td><?php echo $mortgage['handler_name']; ?></td>
                            <td><label class="control-label">Handing Time</label></td>
                            <td><?php echo timeFormat($mortgage['operator_time']); ?></td>
                        </tr>
                        <?php if($mortgage['type']==-1){ ?>
                            <tr>
                                <td><label class="control-label">Keeper Name</label></td>
                                <td><?php echo $output['previous_info']['saver_name']; ?></td>
                                <td><label class="control-label">Keep Time</label></td>
                                <td><?php echo timeFormat($output['previous_info']['create_time']); ?></td>
                            </tr>
                        <?php }else{ ?>
                        <tr>
                            <td><label class="control-label">Keeper Name</label></td>
                            <td><?php echo $mortgage['saver_name']; ?></td>
                            <td><label class="control-label">Keep Time</label></td>
                            <td><?php echo timeFormat($mortgage['create_time']); ?></td>
                        </tr>
                        <?php }?>

                        <?php if($mortgage['type']==-1){ ?>
                        <tr>
                            <td><label class="control-label">Taker Name</label></td>
                            <td><?php echo $mortgage['saver_name']; ?></td>
                            <td><label class="control-label">Take out Time</label></td>
                            <td><?php echo timeFormat($mortgage['create_time']); ?></td>
                         </tr>
                        <?php }?>
                        <tr>
                            <td><label class="control-label">Remark</label></td>
                            <td colspan="3"><?php echo $mortgage['remark']; ?></td>
                        </tr>
                        <tr>
                            <td><label class="control-label">Mortgage Image</label></td>
                            <td colspan="3">
                                <?php if($output['pic']){?>
                                    <div class="magnifier" index="<?php echo $key; ?>">
                                        <div class="magnifier-container" style="display:none;">
                                            <div class="images-cover"></div>
                                            <div class="move-view"></div>
                                        </div>
                                        <div class="magnifier-assembly">
                                            <div class="magnifier-btn">
                                                <span class="magnifier-btn-left">&lt;</span>
                                                <span class="magnifier-btn-right">&gt;</span>
                                            </div>
                                            <!--按钮组-->
                                            <div class="magnifier-line">
                                                <ul class="clearfix animation03">
                                                    <?php foreach ($output['pic'] as $value) { ?>
                                                        <li>
                                                            <a target="_blank" href="<?php echo getImageUrl($value['image_path']); ?>">
                                                                <div class="small-img">
                                                                    <img src="<?php echo getImageUrl($value['image_path'], imageThumbVersion::SMALL_IMG); ?>"/>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                            <!--缩略图-->
                                        </div>
                                        <div class="magnifier-view"></div>
                                        <!--经过放大的图片显示容器-->
                                    </div>
                                <?php }else{ ?>
                                    <div>No Image Material</div>
                                 <?php }?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group button">
    <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 410px;margin-bottom: 40px;margin-top: -20px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
</div>
<script>
    $('.magnifier-btn-left').on('click', function () {
        var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
        move(el, thumbnail, index);
    });
    $('.magnifier-btn-right').on('click', function () {
        var el = $(this).parents('.magnifier'), thumbnail = el.find('.magnifier-line > ul'), index = $(this).index();
        move(el, thumbnail, index);
    });

    function move(magnifier, thumbnail, _boole) {
        magnifier.index = _boole;
        (_boole) ? magnifier.index++ : magnifier.index--;
        var thumbnailImg = thumbnail.find('>*'), lineLenght = thumbnailImg.length;
        var _deviation = Math.ceil(magnifier.width() / thumbnailImg.width() / 2);
        if (lineLenght < _deviation) {
            return false;
        }
        (magnifier.index < 0) ? magnifier.index = 0 : (magnifier.index > lineLenght - _deviation) ? magnifier.index = lineLenght - _deviation : magnifier.index;
        var endLeft = (thumbnailImg.width() * magnifier.index) - thumbnailImg.width();
        thumbnail.css({
            'left': ((endLeft > 0) ? -endLeft : 0) + 'px'
        });
    }
</script>


