<style>
    .btn {
        border-radius: 0;
        padding: 5px 10px;
    }

    .table > tbody > tr > td {
        background-color: #ffffff !important;
    }

    .ibox-title {
        padding-top: 12px !important;
        min-height: 40px;
    }

    #myModal .modal-dialog {
        margin-top: 20px!important;
    }

    .modal-header, .modal-footer {
        padding: 10px 15px!important;
    }
</style>
<?php
$client_info=$output['client_info'];
$client_request=$output['client_request'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['is_bm']) { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Request</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>My Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Client List</span></a></li>
                    <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                    <li><a  class="current"><span>Request</span></a></li>
                </ul>
            <?php }?>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 1300px">
        <div class="business-condition">
             <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="col-sm-5" style="margin-top: 10px;padding: 0 10px 0 0!important;">
                <div class="basic-info">
                    <div class="ibox-title" style="background-color: #DDD">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Client Request</h5>
                    </div>
                    <div class="content">
                        <form id="frm_request" method="POST" action="<?php echo getUrl('web_credit', 'editMemberRequest', array(), false, BACK_OFFICE_SITE_URL);?>">
                            <input type="hidden" name="uid" id="uid" value="<?php echo $client_request['uid']?>">
                            <input type="hidden" name="member_id" value="<?php echo $client_info['uid']?>">
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Credit</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="credit" value="<?php echo $client_request['credit'];?>">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Terms</label>
                                <div class="col-sm-7">
                                    <div class="input-group" style="width: 100%">
                                        <input type="number" class="form-control" name="credit_terms" value="<?php echo $client_request['terms'];?>">
                                        <span class="input-group-addon" style="min-width: 60px;border-left: 0">Months</span>
                                    </div>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Interest Rate</label>
                                <div class="col-sm-7">
                                    <div class="input-group" style="width: 100%">
                                        <input type="number" class="form-control" name="interest_rate" value="<?php echo $client_request['interest_rate'];?>">
                                        <span class="input-group-addon" style="min-width: 60px;border-left: 0">%</span>
                                    </div>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-5 control-label"><span class="required-options-xing">*</span>Purpose</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="purpose" value="<?php echo $client_request['purpose'];?>">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <?php if($client_request){?>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Latest Operator</label>
                                    <div class="col-sm-7">
                                        <?php echo $client_request['update_operator_name'] ? : $client_request['operator_name'];?>
                                    </div>
                                </div>
                                <div class="col-sm-12 form-group">
                                    <label class="col-sm-5 control-label">Latest Update Time</label>
                                    <div class="col-sm-7">
                                        <?php echo $client_request['update_time'] ? timeFormat($client_request['update_time']) : timeFormat($client_request['create_time']);?>
                                    </div>
                                </div>
                            <?php }?>
                            <div class="col-sm-12 form-group" style="text-align: center;margin-top: 15px">
                                <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                                <button type="button" onclick="btn_submit_onclick()" class="btn btn-danger"><i class="fa fa-check"></i>Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-7" style="margin-top: 10px;padding: 0 0 0 10px!important;">
                <div class="basic-info">
                    <div class="ibox-title" style="background-color: #DDD;position: relative">
                        <h5 style="color: black"><i class="fa fa-id-card-o"></i>Request Relative</h5>
                        <a href="#" onclick="add_onclick()" title="<?php echo $client_request['uid'] ? '' : 'Please add the request first.';?>" style="position:absolute;right: 20px;font-weight: 500;color: #000">
                            <i class="fa fa-plus"></i>Add
                        </a>
                    </div>
                    <div class="content" style="padding: 0">
                        <table class="table">
                            <thead>
                            <tr class="table-header" style="background: #EFEFEF">
                                <td>Relation Type</td>
                                <td>Name</td>
                                <td>Phone</td>
                                <td>Id Number</td>
                                <td>Images</td>
                                <td>Function</td>
                            </tr>
                            </thead>
                            <tbody class="table-body">
                            <?php if ($client_request['relative_list']) { ?>
                                <?php foreach ($client_request['relative_list'] as $row) { ?>
                                    <tr data-uid="<?php echo $row['uid'] ?>"
                                        data-relation_type="<?php echo $row['relation_type'] ?>"
                                        data-relation_name="<?php echo $row['relation_name_code'] ?>"
                                        data-name="<?php echo $row['name'] ?>"
                                        data-country_code="<?php echo $row['country_code'] ?>"
                                        data-phone_number="<?php echo $row['phone_number'] ?>"
                                        data-id_sn="<?php echo $row['id_sn'] ?>"
                                        data-headshot="<?php echo $row['headshot'] ?>"
                                        data-headshot_url="<?php echo getImageUrl($row['headshot']) ?>"
                                        data-id_front_image="<?php echo $row['id_front_image'] ?>"
                                        data-id_front_image_url="<?php echo getImageUrl($row['id_front_image']) ?>"
                                        data-id_back_image="<?php echo $row['id_back_image'] ?>"
                                        data-id_back_image_url="<?php echo getImageUrl($row['id_back_image']) ?>"
                                        >
                                        <td><?php echo $row['relation_type'] . '(' . $row['relation_name'] . ')'?></td>
                                        <td><?php echo $row['name']?></td>
                                        <td><?php echo $row['contact_phone']?></td>
                                        <td><?php echo $row['id_sn']?></td>
                                        <td>
                                            <a href="<?php echo getImageUrl($row['headshot']) ?>" target="_blank" title="Head portraits">
                                                <img class="img-icon"
                                                     src="<?php echo getImageUrl($row['headshot'], imageThumbVersion::SMALL_ICON) ?>">
                                            </a>
                                            <a href="<?php echo getImageUrl($row['id_front_image']) ?>" target="_blank" title="The front of ID card">
                                            <img class="img-icon"
                                                 src="<?php echo getImageUrl($row['id_front_image'], imageThumbVersion::SMALL_ICON) ?>">
                                            </a>
                                            <a href="<?php echo getImageUrl($row['id_back_image']) ?>" target="_blank" title="The back of ID card">
                                                <img class="img-icon"
                                                 src="<?php echo getImageUrl($row['id_back_image'], imageThumbVersion::SMALL_ICON) ?>">
                                            </a>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger" onclick="edit_relative(this)" style="margin-bottom: 5px;min-width: 70px"><i class="fa fa-edit"></i>Edit</button>
                                            <button type="button" class="btn btn-default" onclick="del_relative(this)" style="margin-bottom: 5px;min-width: 70px"><i class="fa fa-remove"></i>Detele</button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="7">No Record</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
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
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Relation'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="relation_form">
                        <input type="hidden" name="relation_id" value="">
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Type</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="relation_type">
                                    <option value="">Please select</option>
                                    <?php foreach ($output['type_list'] as $key => $val) { ?>
                                        <option value="<?php echo $key?>"><?php echo ucwords(strtolower($val))?></option>
                                    <?php } ?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Relation Type</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="relation_name_code">
                                    <option value="">Please select</option>
                                    <?php foreach ($output['guarantee_list']['item_list'] as $key => $val) { ?>
                                        <option value="<?php echo $key?>"><?php echo $val?></option>
                                    <?php } ?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Phone</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon" style="padding: 0;border: 0;">
                                        <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                            <?php echo tools::getCountryCodeOptions(855)?>
                                        </select>
                                     </span>
                                    <input type="text" class="form-control" name="phone_number" value="">
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Id Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="id_sn" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Image</label>
                            <div class="col-sm-9 image-div">


                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="relation_submit()"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/html" id="image_html">
    <table class="table">
        <tr>
            <td style="text-align: center;font-weight: 700">Head portraits</td>
            <td style="text-align: center;font-weight: 700">ID Card Front</td>
            <td style="text-align: center;font-weight: 700">ID Card Back</td>
        </tr>
        <tr>
            <td style="text-align: center" class="td-key-file">
                <div class="image-uploader-item">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <img id="show_headshot" style="display: none;width: 100px;height: 100px;margin-bottom: 10px" >
                        </li>
                        <li class="list-group-item">
                            <button type="button" id="headshot">Upload</button>
                            <input name="headshot" type="hidden" value="">
                        </li>
                    </ul>


                </div>
            </td>
            <td style="text-align: center" class="td-key-file">
                <div class="image-uploader-item">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <img id="show_id_front_image" style="display: none;width: 100px;height: 100px;margin-bottom: 10px" >
                        </li>
                        <li class="list-group-item">
                            <button type="button" id="id_front_image">Upload</button>
                            <input name="id_front_image" type="hidden" value="">
                        </li>
                    </ul>




                </div>

            </td>
            <td style="text-align: center" class="td-key-file">
                <div class="image-uploader-item">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <img id="show_id_back_image" style="display: none;width: 100px;height: 100px;margin-bottom: 10px" >
                        </li>
                        <li class="list-group-item">
                            <button type="button" id="id_back_image">Upload</button>
                            <input name="id_back_image" type="hidden" value="">
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
    <div class="error_msg"></div>
</script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<?php require_once template(':widget/inc_upload_upyun');?>
<script>

    function btn_back_onclick() {
        window.history.back(-1);
    }

    function btn_submit_onclick() {
        $('#frm_request').submit();
    }
    function initFileControl(){
        var _file_dir = '<?php echo fileDirsEnum::MEMBER_RELATION; ?>';
        webuploader2upyun('headshot', _file_dir);
        webuploader2upyun('id_front_image', _file_dir);
        webuploader2upyun('id_back_image', _file_dir);
    }

    function add_onclick() {
        var _uid = $('#uid').val();
        if (!_uid) {
            alert('Please add the request first.');
            return;
        }

        $('#myModal .image-div').html($('#image_html').html());

        $('#myModal [name="relation_id"]').val('');
        $('#myModal [name="relation_type"] option:first').prop('selected',true);
        $('#myModal [name="relation_name_code"] option:first').prop('selected',true);
        $('#myModal [name="name"]').val('');
        $('#myModal [name="country_code"]').val(855);
        $('#myModal [name="phone_number"]').val('');
        $('#myModal [name="id_sn"]').val('');
        $('#myModal [name="headshot"]').val('');
        $('#myModal [name="id_front_image"]').val('');
        $('#myModal [name="id_back_image"]').val('');
        $('#show_headshot').attr('src', '').hide();
        $('#show_id_front_image').attr('src', '').hide();
        $('#show_id_back_image').attr('src', '').hide();
        $('#myModal').modal('show');
        initFileControl();


    }

    function edit_relative(_e) {
        var _tr = $(_e).closest('tr');
        var _uid = _tr.data('uid');
        var _relation_type = _tr.data('relation_type');
        var _relation_name = _tr.data('relation_name');
        var _name = _tr.data('name');
        var _country_code = _tr.data('country_code');
        var _phone_number = _tr.data('phone_number');
        var _id_sn = _tr.data('id_sn');
        var _headshot = _tr.data('headshot');
        var _headshot_url = _tr.data('headshot_url');
        var _id_front_image = _tr.data('id_front_image');
        var _id_front_image_url = _tr.data('id_front_image_url');
        var _id_back_image = _tr.data('id_back_image');
        var _id_back_image_url = _tr.data('id_back_image_url');

        $('#myModal .image-div').html($('#image_html').html());

        $('#myModal [name="relation_id"]').val(_uid);
        $('#myModal [name="relation_type"]').val(_relation_type);
        $('#myModal [name="relation_name_code"]').val(_relation_name);
        $('#myModal [name="name"]').val(_name);
        $('#myModal [name="country_code"]').val(_country_code);
        $('#myModal [name="phone_number"]').val(_phone_number);
        $('#myModal [name="id_sn"]').val(_id_sn);
        $('#myModal [name="headshot"]').val(_headshot);
        $('#myModal [name="id_front_image"]').val(_id_front_image);
        $('#myModal [name="id_back_image"]').val(_id_back_image);
        $('#show_headshot').attr('src', _headshot_url).show();
        $('#show_id_front_image').attr('src', _id_front_image_url).show();
        $('#show_id_back_image').attr('src', _id_back_image_url).show();
        $('#myModal').modal('show');
        initFileControl();

    }

    function relation_submit() {
        if (!$("#relation_form").valid()) {
            return;
        }
        var _values = $('#relation_form').getValues();
        if(!_values.headshot || !_values.id_front_image || !_values.id_back_image){
            alert('Please upload image.');
            return;
        }
        var _relation_name = $('select[name="relation_name_code"] option:selected').text();
        _values.relation_name = _relation_name;
        var _uid = $('#uid').val();
        _values.credit_request_id = _uid;
        yo.loadData({
            _c: "web_credit",
            _m: "editRequestRelation",
            param: _values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }

    function del_relative(_e) {
        var _tr = $(_e).closest('tr');
        var _uid = _tr.data('uid');
        yo.confirm("Delete", "Are you sure to delete it?", function (_r) {
            if (!_r) return;
            yo.loadData({
                _c: "web_credit",
                _m: "deleteRequestRelation",
                param: {relation_id: _uid},
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.reload();
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }

    $('#relation_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules : {
            relation_type : {
                required : true
            },
            relation_name_code : {
                required : true
            },
            name : {
                required : true
            },
            phone_number : {
                required : true
            },
            id_sn : {
                required : true
            }
        },
        messages : {
            relation_type : {
                required : 'Required'
            },
            relation_name_code : {
                required : 'Required'
            },
            name : {
                required : 'Required'
            },
            phone_number : {
                required : 'Required'
            },
            id_sn : {
                required : 'Required'
            }
        }
    });


</script>






