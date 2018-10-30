<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="business-condition" style="margin-bottom: -5px!important;">
            <p>Only Display My Task</p>
        </div>
        <div class="business-content">
            <div class="business-list">
                <?php
                $certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
                $flow_type=(new assetStorageFlowType())->Dictionary();
                $list=$output['list'];
                ?>
                <div class="container">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td>Asset-SN</td>
                            <td>Asset-Name</td>
                            <td>Asset-Type</td>
                            <td>From</td>
                            <td>To</td>
                            <td>Storage Type</td>
                            <td>Time</td>
                            <td>Client</td>
                            <td>Function</td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if (count($list)) { ?>
                            <?php foreach ($list as $row) { ?>
                                <tr>
                                    <td>
                                        <?php echo $row['asset_sn'] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['asset_name'] ?>
                                    </td>
                                    <td>
                                        <?php echo $certificationTypeEnumLang[$row['asset_type']] ?>
                                    </td>
                                    <td>
                                        <label>
                                            <?php echo $row['form_operator_name']?:$row['member_name']?>
                                        </label>
                                        <em style="padding-left: 20px;font-size: 11px;font-style: italic">
                                            <?php echo $row['from_branch_name']?:'CLIENT'?>
                                        </em>
                                    </td>
                                    <td>
                                        <label>
                                            <?php echo $row['to_operator_name']?>
                                        </label>
                                        <em style="padding-left: 20px;font-size: 11px;font-style: italic">
                                            <?php echo $row['to_branch_name']?>
                                        </em>
                                    </td>
                                    <td>
                                        <?php echo $flow_type[$row['flow_type']] ?>
                                    </td>
                                    <td>
                                        <?php echo $row['create_time'] ?>
                                    </td>
                                    <td>
                                        <label>
                                            <?php echo $row['member_name']?>
                                        </label>
                                        <em style="padding-left: 20px;font-size: 11px;font-style: italic">
                                            <?php echo $row['phone_id']?>
                                        </em>
                                    </td>
                                    <?php if($output['user_position'] == userPositionEnum::CHIEF_TELLER){ ?>
                                    <td>
                                        <button class="btn btn-default"  onclick="showModal(<?php echo $row['uid'] ?>)"><i class="fa fa-id-card"></i><?php echo 'Receive' ?></button>
                                    </td>
                                    <?php } else{ ?>
                                        <td>
                                            <a class="btn btn-default" href="<?php echo getUrl('mortgage', 'showPendingReceiveDetailPageForTeller', array('uid'=>$row['uid'],"asset_id"=>$row['member_asset_id']), false, ENTRY_COUNTER_SITE_URL); ?>">
                                                <i class="fa fa-id-card"></i><?php echo 'Receive' ?>
                                            </a>
                                        </td>
                                    <?php }?>

                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="20"<?php include(template(":widget/no_record"))?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Choose Safe Box</h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="my_form" method="post" action="<?php echo getUrl('mortgage', 'showPendingReceiveDetailPage', array(), false, ENTRY_COUNTER_SITE_URL); ?>">
                        <input type="hidden" id ='uid' name="uid" value="">
                        <div class="col-sm-12" style="margin-bottom: 15px">
                            <label  class="col-sm-3 control-label"><span class="required-options-xing">*</span>Safe Box</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="safe_id">
                                    <?php foreach ($output['safe_box'] as $value){ ?>
                                        <option value="<?php echo $value['uid'] ?>"><?php echo $value['safe_code'] ?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12" style="margin-top: 15px">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Remark</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="remark" value="" style="width: 400px">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <a class="btn btn-danger" onclick="modal_submit()"><i class="fa fa-check"></i><?php echo 'Submit'?></a>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>

<script>
    function showModal(uid) {
        $('#myModal input[name="uid"]').val('');
        $('#myModal input[name="remark"]').val('');
        $('#myModal #uid').val(uid);
        $('#myModal').modal('show');
    }

    function modal_submit() {
        if (!$('#my_form').valid()) {
            return;
        }
        $('#my_form').submit();
    }

    $('#my_form').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules : {
            remark : {
                required : true
            }
        },
        messages : {
            remark : {
                required : '<?php echo 'Required'?>'
            }
        }
    });
</script>