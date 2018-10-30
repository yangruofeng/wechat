<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.config.js' ?>"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL . '/ueditor/utf8-php/ueditor.all.js' ?>"></script>
<style>
    .btn-release,.btn-unshelve{
        position: absolute;
        top: 0px;
        right: 0px;
        height: 30px;
        line-height: 30px;
        padding: 0px 15px;
    }
    .base-info .size-info .content{
        overflow: auto;
        padding: 5px 0 10px;
        margin: 5px 15px;
    }
</style>
<?php $product_info = $output['product_info']?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'product', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <?php /*if ($product_info['uid']) { */?><!--
                    <li><a href="<?php /*echo getUrl('loan', 'addProduct', array(), false, BACK_OFFICE_SITE_URL) */?>"><span>Add</span></a></li>
                    <li><a class="current"><span>Edit</span></a></li>
                <?php /*}else{ */?>
                    <li><a class="current"><span>Add</span></a></li>
                --><?php /*}*/?>
            </ul>
        </div>
    </div>
    <div class="container">
        <ul class="tab-top clearfix">
            <li class="active" page="page-1"><a>Base Info</a></li>
            <li page="page-2"><a>Condition</a></li>
            <li page="page-3"><a>Details</a></li>
        </ul>
        <button id="btn_release_product" class="btn btn-default btn-release" style="display:<?php echo $product_info['state'] != loanProductStateEnum::ACTIVE ? 'block;' : 'none;' ?>">Active</button>
        <button id="btn_unshelve_product" class="btn btn-default btn-unshelve" style="display:<?php echo $product_info['state'] == loanProductStateEnum::ACTIVE ? 'block;' : 'none;' ?>">Inactive</button>
        <div class="page-1">
            <div class="base-info clearfix">
                <div class="product-info" style="width: 100%;">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Product Info</h5></div>
                        <div class="col-sm-4">
                            <i class="fa fa-edit" title="Edit" onclick="edit_info()"></i>
                        </div>
                    </div>
                    <div class="content clearfix">
                        <div class="wrap1">
                            <table>
                                <tr style="font-weight: bold">
                                    <input type="hidden" id="uid" value="<?php echo $product_info['uid'];?>">
                                    <td>Product Name：</td>
                                    <td id="product_name"><?php echo $product_info['product_name']?></td>
                                </tr>

                                <tr>
                                    <td>Product Category: </td>
                                    <td id=""><?php echo enum_langClass::getLoanProductCategoryLang()[$product_info['category']]; ?></td>
                                </tr>


                                <tr>
                                    <td>Advance Interest：</td>
                                    <td id="is_advance_interest" val="<?php echo $product_info['is_advance_interest']?>"><?php echo (isset($product_info['is_advance_interest'])?($product_info['is_advance_interest']==1?'YES':'NO'):'')?></td>
                                </tr>
                                <tr>
                                    <td>Editable Grace Days：</td>
                                    <td id="is_editable_grace_days" val="<?php echo $product_info['is_editable_grace_days']?>"><?php echo (isset($product_info['is_editable_grace_days'])?($product_info['is_editable_grace_days']==1?'YES':'NO'):'')?></td>
                                </tr>


                            </table>
                        </div>
                        <div class="wrap2">
                            <table>
                                <tr style="font-weight: bold">
                                    <td>Product Code：</td>
                                    <td id="product_code"><?php echo $product_info['product_code']?></td>
                                </tr>
                                <tr>
                                    <td>Multi Contract：</td>
                                    <td id="is_multi_contract" val="<?php echo $product_info['is_multi_contract']?>"><?php echo (isset($product_info['is_multi_contract'])?($product_info['is_multi_contract']==1?'YES':'NO'):'')?></td>
                                </tr>
                                <tr>
                                    <td>Editable Interest：</td>
                                    <td id="is_editable_interest" val="<?php echo $product_info['is_editable_interest']?>"><?php echo (isset($product_info['is_editable_interest'])?($product_info['is_editable_interest']==1?'YES':'NO'):'')?></td>
                                </tr>

                                <tr>
                                    <td>State：</td>
                                    <td id="state" val="<?php echo $product_info['state']?>"><?php echo $lang['enum_loan_product_state_'.$product_info['state']]?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>



        </div>

        <div class="base-info clearfix page-2">
            <div class="condition-info">
                <div class="ibox-title">
                    <div class="col-sm-8"><h5>Condition</h5></div>
                    <div class="col-sm-4"><i class="fa fa-floppy-o <?php echo !$product_info['uid']?'not-allowed':''?> allow-state" title="Save" onclick="save_condition()"></i></div>
                </div>
                <div class="content">
                    <form class="form-horizontal" id="condition_form">
                        <?php
                        $condition = $product_info['condition'];
                        $condition_new = array();
                        foreach ($condition as $val) {
                            $condition_new[] = $val['definition_category'] . ',' . $val['definition_id'];
                        }
                        ?>
                        <?php foreach ($output['condition_list'] as  $key => $condition) { ?>
                            <div class="form-group col-sm-6">
                                <label for="inputEmail3" class="col-sm-3 control-label"><?php echo $output['condition_arr'][$key] ?></label>
                                <div class="col-sm-9 checkbox-div">
                                    <?php foreach($condition as $val){?>
                                        <label class="col-sm-4"><input type="checkbox" <?php echo in_array($key . ',' . $val['uid'], $condition_new)?'checked':''?> name="<?php echo $key . ',' . $val['uid'] ?>"><?php echo $val['item_name']?></label>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="page-3">
            <div class="base-info clearfix">
                <div class="description">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Description</h5></div>
                        <div class="col-sm-4">
                            <i class="fa fa-edit <?php echo !$product_info['uid']?'not-allowed':''?> allow-state" title="Save" onclick="edit_text('description')"></i>
                            <i class="fa fa-mail-reply" onclick="cancel_text('description')"></i>
                            <i class="fa fa-floppy-o" onclick="save_text('description')"></i>
                        </div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_description']?></div>
                        <textarea name="description" id="description" style="display: none;"><?php echo $product_info['product_description']?></textarea>
                    </div>
                </div>
                <div class="qualification">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Qualification</h5></div>
                        <div class="col-sm-4">
                            <i class="fa fa-edit <?php echo !$product_info['uid']?'not-allowed':''?> allow-state" title="Save" onclick="edit_text('qualification')"></i>
                            <i class="fa fa-mail-reply" onclick="cancel_text('qualification')"></i>
                            <i class="fa fa-floppy-o" onclick="save_text('qualification')"></i>
                        </div>

                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_qualification']?></div>
                        <textarea name="qualification" id="qualification" style="display: none;"><?php echo $product_info['product_qualification']?></textarea>
                    </div>
                </div>
            </div>
            <div class="base-info clearfix">
                <div class="feature">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Feature</h5></div>
                        <div class="col-sm-4">
                            <i class="fa fa-edit <?php echo !$product_info['uid']?'not-allowed':''?> allow-state" title="Save" onclick="edit_text('feature')"></i>
                            <i class="fa fa-mail-reply" onclick="cancel_text('feature')"></i>
                            <i class="fa fa-floppy-o" onclick="save_text('feature')"></i>
                        </div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_feature']?></div>
                        <textarea name="feature" id="feature" style="display: none;"><?php echo $product_info['product_feature']?></textarea>
                    </div>
                </div>
                <div class="required">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Required</h5></div>
                        <div class="col-sm-4">
                            <i class="fa fa-edit <?php echo !$product_info['uid']?'not-allowed':''?> allow-state" title="Save" onclick="edit_text('required')"></i>
                            <i class="fa fa-mail-reply" onclick="cancel_text('required')"></i>
                            <i class="fa fa-floppy-o" onclick="save_text('required')"></i>
                        </div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_required']?></div>
                        <textarea name="required" id="required" style="display: none;"><?php echo $product_info['product_required']?></textarea>
                    </div>
                </div>
            </div>
            <div class="base-info clearfix">
                <div class="notice">
                    <div class="ibox-title">
                        <div class="col-sm-8"><h5>Notice</h5></div>
                        <div class="col-sm-4">
                            <i class="fa fa-edit <?php echo !$product_info['uid']?'not-allowed':''?> allow-state" title="Save" onclick="edit_text('notice')"></i>
                            <i class="fa fa-mail-reply" onclick="cancel_text('notice')"></i>
                            <i class="fa fa-floppy-o" onclick="save_text('notice')"></i>
                        </div>
                    </div>
                    <div class="content clearfix">
                        <div><?php echo $product_info['product_notice']?></div>
                        <textarea name="notice" id="notice" style="display: none;"><?php echo $product_info['product_notice']?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Base Info'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="info_form">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Product Name'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="product_name" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Product Code'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="product_code" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Allow'?></label>
                            <div class="col-sm-9 checkbox-div">
                                <label class="col-sm-4"><input type="checkbox" name="is_multi_contract">Multi Contract</label>
                                <label class="col-sm-4"><input type="checkbox" name="is_advance_interest">Advance Interest</label>
                                <label class="col-sm-4"><input type="checkbox" name="is_editable_interest">Editable Interest</label>
                                <label class="col-sm-4"><input type="checkbox" name="is_editable_grace_days">Editable Grace Days</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_info()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="penaltyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Penalty'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="penalty_form">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Penalty On'?></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="penalty_on">
                                    <?php foreach ($output['penalty_on'] as $key => $val) { ?>
                                        <option value="<?php echo $key?>"><?php echo $lang['enum_' . $key]?></option>
                                    <?php } ?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Penalty Rate'?></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="penalty_rate" value="">
                                    <span class="input-group-addon" style="min-width: 55px;border-left: 0">%</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Divisor Days'?></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" required="true" name="penalty_divisor_days" value="">
                                    <span class="input-group-addon" style="min-width: 55px;border-left: 0">Days</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Editable Penalty'?></label>
                            <div class="col-sm-9 checkbox-div">
                                <label><input type="checkbox" name="is_editable_penalty"></label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_penalty()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>


<script>
    $(function () {
        var height = $('.product-info .content').height();
        $('.penalty-info .content').height(height);
        $('.tab-top li').click(function () {
            var _page = $(this).attr('page');
            $('.tab-top li').removeClass('active');
            $(this).addClass('active');
            $('.page-1,.page-2,.page-3').hide();
            $('.' + _page).show();
        })
    });

    var uid = '<?php echo intval($product_info['uid'])?>';


    function edit_info() {
        var _product_name = $('#product_name').html();
        var _product_code = $('#product_code').html();
        var is_multi_contract = $('#is_multi_contract').attr('val');
        var is_advance_interest = $('#is_advance_interest').attr('val');
        var is_editable_interest = $('#is_editable_interest').attr('val');
        var is_editable_grace_days = $('#is_editable_grace_days').attr('val');
        var _state = $('#state').attr('val');


        $('#infoModal input[name="product_name"]').val(_product_name);
        $('#infoModal input[name="product_code"]').val(_product_code);


        if (is_multi_contract == 1) {
            $('#infoModal input[name="is_multi_contract"]').attr('checked',true);
        } else {
            $('#infoModal input[name="is_multi_contract"]').attr('checked',false);
        }
        if (is_advance_interest == 1) {
            $('#infoModal input[name="is_advance_interest"]').attr('checked',true);
        } else {
            $('#infoModal input[name="is_advance_interest"]').attr('checked',false);
        }
        if (is_editable_interest == 1) {
            $('#infoModal input[name="is_editable_interest"]').attr('checked', true);
        } else {
            $('#infoModal input[name="is_editable_interest"]').attr('checked', false);
        }
        if (is_editable_grace_days == 1) {
            $('#infoModal input[name="is_editable_grace_days"]').attr('checked', true);
        } else {
            $('#infoModal input[name="is_editable_grace_days"]').attr('checked', false);
        }
        if (_state) {
            $('#infoModal input[name="state"][value="' + _state + '"]').attr('checked', true);
        }

        $('#infoModal').modal('show');
    }

    function save_info() {
        if (!$("#info_form").valid()) {
            return;
        }
        var _product_name = $('#infoModal input[name="product_name"]').val();
        var _product_code = $('#infoModal input[name="product_code"]').val();
        if (!_product_code || !_product_name) return;

        var is_multi_contract = $('#infoModal input[name="is_multi_contract"]').is(':checked');
        var is_advance_interest = $('#infoModal input[name="is_advance_interest"]').is(':checked');
        var is_editable_interest = $('#infoModal input[name="is_editable_interest"]').is(':checked');
        var is_editable_grace_days = $('#infoModal input[name="is_editable_grace_days"]').is(':checked');


        var values = $('#info_form').getValues();


        if (uid == 0) {
            var _m = 'insertProductMain';
        } else {
            values.uid = uid;
            var _m = 'updateProductMain';
        }
        yo.loadData({
            _c: 'loan',
            _m: _m,
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    if (data && data.uid > 0) {
                        window.location.href = '<?php echo getUrl('loan', 'editProduct', array(), false, BACK_OFFICE_SITE_URL);?>' + '&uid=' + data.uid;
                        return;
                    }
                    $('#product_name').html(_product_name);
                    $('#product_code').html(_product_code);

                    if (is_multi_contract) {
                        $('#is_multi_contract').attr('val', 1).html('YES');
                    } else {
                        $('#is_multi_contract').attr('val', 0).html('NO');
                    }
                    if (is_advance_interest) {
                        $('#is_advance_interest').attr('val', 1).html('YES');
                    } else {
                        $('#is_advance_interest').attr('val', 0).html('NO');
                    }
                    if (is_editable_interest) {
                        $('#is_editable_interest').attr('val', 1).html('YES');
                    } else {
                        $('#is_editable_interest').attr('val', 0).html('NO');
                    }
                    if (is_editable_grace_days) {
                        $('#is_editable_grace_days').attr('val', 1).html('YES');
                    } else {
                        $('#is_editable_grace_days').attr('val', 0).html('NO');
                    }

                    $('#state').html("Temporary");
                    if (uid == 0) {
                        uid = _o.DATA.uid;
                        $('#uid').val(uid);
                        $('.allow-state').removeClass('not-allowed');
                        $('.btn-release').show();
                        $('.product-info .ibox-title .fa-minus').show();
                    }

                    $('#infoModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#btn_release_product').click(function () {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "releaseMainProduct",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $(this).hide();
                    $('.btn-unshelve').show();
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });

    $('#btn_unshelve_product').click(function () {
        if (!uid) {
            return;
        }
        yo.loadData({
            _c: "loan",
            _m: "unShelveMainProduct",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $(this).hide();
                    $('.btn-release').show();
                    window.location.reload();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });




    function save_condition() {
        if (uid == 0) return;
        var values = $('#condition_form').getValues();
        values.product_id = uid;
        yo.loadData({
            _c: 'loan',
            _m: 'updateMainProductCondition',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    if (data && data.uid > 0) {
                        window.location.href = '<?php echo getUrl('loan', 'editProduct', array(), false, BACK_OFFICE_SITE_URL);?>' + '&uid=' + data.uid;
                        return;
                    }
                }
                alert(_o.MSG);
            }
        });
    }



    function edit_text(_name) {
        if (uid == 0) return;
        $('.' + _name).find('.fa-edit').hide();
        $('.' + _name).find('.fa-mail-reply').show();
        $('.' + _name).find('.fa-floppy-o').show();
        $('.' + _name).find('.content div').first().hide();
        $('.' + _name).find('#' + _name).show();
        ue(_name);
    }

    function cancel_text(_name) {
        $('.' + _name).find('.fa-edit').show();
        $('.' + _name).find('.fa-mail-reply').hide();
        $('.' + _name).find('.fa-floppy-o').hide();
        $('.' + _name).find('.content div').first().show();
        $('.' + _name).find('#' + _name).hide();
    }

    function save_text(_name) {
        var _val = ueArr[_name].getContent();
        yo.loadData({
            _c: "loan",
            _m: "updateMainProductDescription",
            param: {product_id: uid, name: _name, val: _val},
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    if (data && data.uid > 0) {
                        window.location.href = '<?php echo getUrl('loan', 'editProduct', array(), false, BACK_OFFICE_SITE_URL);?>' + '&uid=' + data.uid;
                        return;
                    }
                    $('.' + _name).find('.fa-edit').show();
                    $('.' + _name).find('.fa-mail-reply').hide();
                    $('.' + _name).find('.fa-floppy-o').hide();
                    $('.' + _name).find('.content div').first().html(_val).show();
                    $('.' + _name).find('#' + _name).hide();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    var ueArr = [];
    function ue(_name) {
        ueArr[_name] = UE.getEditor(_name, {
            toolbars: [[
                'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|',
                'link', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'simpleupload', 'background', '|',
                'horizontal', 'date', 'time', 'spechars','inserttable',
            ]],
            initialFrameHeight: 300,
            enableAutoSave: false,
            autoHeightEnabled: false,
            lang: 'en'
        });
    }

    $('#info_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            product_name: {
                required: true
            },
            product_code: {
                required: true,
                checkNumAndStr: true
            }
        },
        messages: {
            product_name: {
                required: '<?php echo 'Required!'?>'
            },
            product_code: {
                required: '<?php echo 'Required!'?>',
                checkNumAndStr: '<?php echo 'It can only be Numbers or letters!'?>'
            }
        }
    });




    jQuery.validator.addMethod("checkNumAndStr", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]+$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("checkRequired", function (value, element) {
        value = $.trim(value);
        if ($('input[name="is_full_interest"]').prop('checked')) {
            return true;
        } else {
            if (value) {
                return true;
            } else {
                return false;
            }
        }
    });

</script>
