<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.css?v=1" rel="stylesheet"/>
<style>
    .container {
        max-width: 1200px !important;
    }

    .btn {
        border-radius: 0;
    }

    .table > tbody > tr > td {
        background-color: #ffffff !important;
    }

    .ibox-title {
        padding-top: 12px !important;
        min-height: 40px;
    }

    .sub-label {
        padding-left: 35px;
        padding-right: 0;
        font-weight: 400;
    }

    .pl-30 {
        padding-left: 30px;
    }

    .client_relative {
        padding-left: 0px !important;
    }

    .client_relative label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap
    }

</style>
<?php
$client_info = $output['client_info'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Client List</span></a>
                </li>
                <li>
                    <a href="<?php echo getUrl('web_credit', 'creditClient', array('uid' => $client_info['uid']), false, BACK_OFFICE_SITE_URL) ?>"><span>Client Detail</span></a>
                </li>
                <li><a class="current"><span>Business</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
            <?php require_once template("widget/item.member.summary") ?>
        </div>
        <div class="business-content">
            <?php $i = 0;
            foreach ($output['verify_type'] as $key => $cert_type) {
                ++$i; ?>
                <div class="col-sm-6"
                     style="position: relative;margin: 10px 0 0;padding: <?php echo $i % 2 == 0 ? '0 0 0 10px' : '0 10px 0 0' ?>;">
                    <div class="basic-info">
                        <div class="ibox-title" style="background-color: #DDD">
                            <h5 style="color: black"><i class="fa fa-id-card-o"></i><?php echo $cert_type ?></h5>
                        </div>
                        <div class="content">
                            <table class="table table-no-background">
                                <tbody>
                                <?php if ($output['cert_list'][$key]) {
                                    $cert = $output['cert_list'][$key] ?>
                                    <tr>
                                        <td>Source Type</td>
                                        <td>
                                            <label>
                                                <?php echo $lang['cert_source_type_' . $cert['source_type']]?>
                                                <?php if ($cert['creator_name']) { ?>
                                                    <span>【<?php echo $cert['creator_name']; ?>】</span>
                                                <?php } ?>
                                            </label>
                                        </td>
                                        <td>Auditor</td>
                                        <td>
                                            <label>
                                                <?php echo $cert['auditor_name'] ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Remark</td>
                                        <td>
                                            <label>
                                                <?php echo $cert['verify_remark'] ?>
                                            </label>
                                        </td>
                                        <td>Audit Time</td>
                                        <td>
                                            <label>
                                                <?php echo timeFormat($cert['auditor_time']) ?>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr style="border-top: 1px solid #CCC">
                                        <td>File Images</td>
                                        <td colspan="3">
                                            <?php
                                            $image_list = array();
                                            foreach ($cert['image_list'] as $img_item) {
                                                $image_list[] = array(
                                                    'url' => $img_item['image_url'],
                                                    'image_source' => $img_item['image_source'],
                                                );
                                            }
                                            include(template(":widget/item.image.viewer.list"));
                                            ?>
                                        </td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td>No Verify Certification</td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="col-sm-6" style="position: relative;margin: 10px 0;padding: <?php echo ($i + 1) % 2 == 0 ? '0 0 0 10px' : '0 10px 0 0' ?>;">
                <div class="basic-info">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i><?php echo 'Member Verify' ?></h5>
                    </div>
                    <div class="content">
                        <form id="frm_business">
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Verify Type</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="verify_type">
                                        <option value="">Please select</option>
                                        <?php foreach ($output['member_verify_type'] as $key => $type) { ?>
                                            <option value="<?php echo $key;?>"><?php echo $type;?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-4 control-label"><span class="required-options-xing">*</span>Verify Remark</label>
                                <div class="col-sm-8">
                                    <textarea name="verify_remark" class="form-control"></textarea>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        </form>
                        <div class="col-sm-12 form-group">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include(template(":widget/item.image.viewer.js")); ?>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.min.js?v=1"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    function btn_back_onclick() {
        window.history.back(-1);
    }

    function btn_submit_onclick() {
        var uid = '<?php echo $client_info['uid'];?>';
        if (!uid) {
            return;
        }

        if (!$("#frm_business").valid()) {
            return;
        }

        var _values = $('#frm_business').getValues();
        _values.uid = uid;

        yo.confirm('','are you sure change the member status to verified?', function (_r) {
            if(!_r) return false;
            yo.loadData({
                _c: 'operator',
                _m: 'changeMemberStateToVerified',
                param: _values,
                callback: function (_o) {
                    if (_o.STS) {
                        console.log(_o.MSG)
                        alert('Changed Successfully!', 1,function(){
                            window.location.href = '<?php echo getUrl('operator', 'pendingVerify', array(), false, BACK_OFFICE_SITE_URL)?>';
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }

    $('#frm_business').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            verify_type: {
                required: true
            },
            verify_remark: {
                required: true
            }
        },
        messages: {
            verify_type: {
                required: '<?php echo 'Required'?>'
            },
            verify_remark: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
</script>






