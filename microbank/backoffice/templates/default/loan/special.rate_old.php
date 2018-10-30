<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/product.css?v=5" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('loan', 'product', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('loan', 'editSubProduct', array("uid"=>$output['product_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Sub Product</span></a></li>
                <li><a class="current"><span>Special Rate</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                            <a type="button" class="btn btn-default" href="<?php echo getUrl('loan', 'editSubProduct', array('uid' => $output['product_id']), false, BACK_OFFICE_SITE_URL)?>">
                                <i class="fa fa-mail-reply" style="display: inline-block;margin-left: 0"></i>
                                <?php echo 'Back'; ?>
                            </a>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" id="btn_add">
                                <i class="fa fa-plus" style="margin-left: 0"></i>
                                <?php echo 'Add'; ?>
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="business-content">
            <div class="business-list">
                <div>
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo 'Client Grade';?></td>
                            <td><?php echo 'Client Type';?></td>
                            <td><?php echo 'Interest Rate';?></td>
                            <td><?php echo 'Min Interest';?></td>
                            <td><?php echo 'Operation Fee';?></td>
                            <td><?php echo 'Min Operation Fee';?></td>
                            <td><?php echo 'Admin Fee';?></td>
                            <td><?php echo 'Loan Fee';?></td>
                            <td><?php echo 'Prepayment';?></td>
                            <td><?php echo 'Function';?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if(!empty($output['special_rate_list'])){?>
                        <?php foreach($output['special_rate_list'] as $row){?>
                            <tr uid="<?php echo $row['uid'] ?>"
                                special_grade="<?php echo $row['special_grade'] ?>"
                                special_type="<?php echo $row['special_type'] ?>"
                                interest_payment="<?php echo $row['interest_payment'] ?>"
                                interest_rate_type="<?php echo $row['interest_rate_type'] ?>"
                                interest_min_value="<?php echo $row['interest_min_value'] ?>"
                                interest_rate_period="<?php echo $row['interest_rate_period'] ?>"
                                interest_rate="<?php echo $row['interest_rate'] ?>"
                                interest_rate_type="<?php echo $row['interest_rate_type'] ?>"
                                admin_fee="<?php echo $row['admin_fee'] ?>"
                                admin_fee_type="<?php echo $row['admin_fee_type'] ?>"
                                loan_fee="<?php echo $row['loan_fee'] ?>"
                                loan_fee_type="<?php echo $row['loan_fee_type'] ?>"
                                operation_fee="<?php echo $row['operation_fee'] ?>"
                                operation_fee_type="<?php echo $row['operation_fee_type'] ?>"
                                operation_min_value="<?php echo $row['operation_min_value'] ?>"
                                is_full_interest="<?php echo $row['is_full_interest'] ?>"
                                prepayment_interest="<?php echo $row['prepayment_interest'] ?>"
                                prepayment_interest_type="<?php echo $row['prepayment_interest_type'] ?>"
                                >
                                <td>
                                    <?php echo $output['special_grade']['item_list'][$row['special_grade']] ?><br/>
                                </td>
                                <td>
                                    <?php echo $output['special_type_lang'][$row['special_type']] ?><br/>
                                </td>
                                <td>
                                    <?php echo $row['interest_rate_type'] == 1 ? '$' . $row['interest_rate'] : $row['interest_rate'] . '%';  ?>
                                    <br/>
                                </td>
                                <td>
                                    <?php echo  '$' . $row['interest_min_value'];  ?>
                                    <br/>
                                </td>
                                <td>
                                    <?php echo $row['operation_fee_type'] == 1 ? '$' . $row['operation_fee'] : $row['operation_fee'] . '%';  ?>
                                    <br/>
                                </td>
                                <td>
                                    <?php echo  '$' . $row['operation_min_value'];  ?>
                                    <br/>
                                </td>
                                <td>
                                    <?php echo $row['admin_fee_type'] == 1 ? '$' . $row['admin_fee'] : $row['admin_fee'] . '%';  ?>
                                    <br/>
                                </td>
                                <td>
                                    <?php echo $row['loan_fee_type'] == 1 ? '$' . $row['loan_fee'] : $row['loan_fee'] . '%';  ?>
                                    <br/>
                                </td>
                                <td>
                                    <?php echo $row['is_full_interest'] == 1 ? 'Full Interest' : ($row['prepayment_interest_type'] == 1 ? '$' . $row['prepayment_interest'] : $row['prepayment_interest'] . '%');  ?>
                                    <br/>
                                </td>
                                <td>
                                    <a title="<?php echo $lang['common_edit'] ;?>" style="margin-right: 5px" onclick="edit_special_size_rate(this)">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a title="<?php echo $lang['common_delete'];?>" onclick="remove_special_size_rate(this)">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php }?>
                        <?php } else {?>
                            <tr>
                                <td colspan="40"> Null </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="sizeRateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 1000px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Special Size Rate'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="size_rate_form">
                        <input type="hidden" name="uid" value="">
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span>Client Grade</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="special_type" value="0">
                                <select class="form-control" name="special_grade">
                                    <option value="0"><?php echo $lang['common_select']?></option>
                                    <?php foreach ($output['package_list'] as $package_item) { ?>
                                        <option value="<?php echo $package_item['uid']?>"><?php echo $package_item['package']?></option>
                                    <?php } ?>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Interest Rate'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="interest_rate" value="">
                                    <!--<select class="form-control" name="interest_rate_type" style="width: 60px">
                                        <option value="0">%</option>
                                        <option value="1">$</option>
                                    </select>-->
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">%</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Min Interest'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="interest_min_value" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">$</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Operation Fee'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width:100%;">
                                    <input type="number" class="form-control" required="true" name="operation_fee" value="">
                                    <!--<select class="form-control" name="operation_fee_type" style="width: 60px" >
                                        <option value="0">%</option>
                                        <option value="1">$</option>
                                    </select>-->
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">%</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Min Operation Fee'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="operation_min_value" value="">
                                    <span class="input-group-addon" style="min-width: 60px;border-left: 0">$</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Admin Fee'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="admin_fee" value="" style="width:212px;">
                                    <select class="form-control" name="admin_fee_type" style="width: 60px">
                                        <option value="1">$</option>
                                        <option value="0">%</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Loan Fee'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" required="true" name="loan_fee" value="" style="width:212px;">
                                    <select class="form-control" name="loan_fee_type" style="width: 60px">
                                        <option value="1">$</option>
                                        <option value="0">%</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Prepayment'?></label>
                            <div class="col-sm-8">
                                <label style="margin-top: 7px;padding-left: 0px"><input type="checkbox" name="is_full_interest">Is paying full interest</label>
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Prepayment Interest'?></label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input type="number" class="form-control" name="prepayment_interest" value="" style="width:212px;">
                                    <select class="form-control" name="prepayment_interest_type" style="width: 60px">
                                        <option value="0">%</option>
                                        <option value="1">$</option>
                                    </select>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_size_rate()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script>
    var product_id = '<?php echo $output['product_id']?>';
    var size_rate_id = '<?php echo $output['size_rate_id']?>';
    $('#btn_add').click(function () {
        $('#sizeRateModal input').val('');
        $('#sizeRateModal select[name="special_grade"] option:first').prop('selected', 'selected');
        $('#sizeRateModal select[name="special_type"] option:first').prop('selected', 'selected');
        $('#sizeRateModal select[name="prepayment_interest_type"] option:first').prop('selected', 'selected');
        $('input[name="is_full_interest"]').prop('checked', false);
        $('input[name="prepayment_interest"]').closest('.form-group').show();
        $('#sizeRateModal').modal('show');
    })

    $('input[name="is_full_interest"]').click(function () {
        if ($(this).prop('checked')) {
            $('input[name="prepayment_interest"]').closest('.form-group').hide();
        } else {
            $('input[name="prepayment_interest"]').closest('.form-group').show();
        }
    })

    function save_size_rate() {
        if (!$("#size_rate_form").valid()) {
            return;
        }

        var values = $('#size_rate_form').getValues();
        values.product_id = product_id;
        values.size_rate_id = size_rate_id;
        if (values.uid) {
            var _m = 'updateSpecialSizeRate';
        } else {
            var _m = 'insertSpecialSizeRate';
        }
        yo.loadData({
            _c: 'loan',
            _m: _m,
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    window.location.href = _o.DATA.url;
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function edit_special_size_rate (_e) {
        var _tr = $(_e).closest('tr');
        var _uid = _tr.attr('uid');
        var _special_grade = _tr.attr('special_grade');
        var _special_type = _tr.attr('special_type');
        var _interest_rate = _tr.attr('interest_rate');
        var _interest_rate_type = _tr.attr('interest_rate_type');
        var _interest_min_value = _tr.attr('interest_min_value');

        var _admin_fee = _tr.attr('admin_fee');
        var _admin_fee_type = _tr.attr('admin_fee_type');
        var _loan_fee = _tr.attr('loan_fee');
        var _loan_fee_type = _tr.attr('loan_fee_type');
        var _operation_fee = _tr.attr('operation_fee');
        var _operation_fee_type = _tr.attr('operation_fee_type');
        var _operation_min_value = _tr.attr('operation_min_value');
        var _is_full_interest = _tr.attr('is_full_interest');
        var _prepayment_interest = _tr.attr('prepayment_interest');
        var _prepayment_interest_type = _tr.attr('prepayment_interest_type');

        $('#sizeRateModal').find('input[name="uid"]').val(_uid);
        $('#sizeRateModal select[name="special_grade"]').val(_special_grade);
        $('#sizeRateModal select[name="special_type"]').val(_special_type);
        $('#sizeRateModal input[name="interest_rate"]').val(_interest_rate);
        $('#sizeRateModal select[name="interest_rate_type"]').val(_interest_rate_type);
        $('#sizeRateModal input[name="interest_min_value"]').val(_interest_min_value);
        $('#sizeRateModal input[name="admin_fee"]').val(_admin_fee);
        $('#sizeRateModal select[name="admin_fee_type"]').val(_admin_fee_type);
        $('#sizeRateModal input[name="loan_fee"]').val(_loan_fee);
        $('#sizeRateModal select[name="loan_fee_type"]').val(_loan_fee_type);
        $('#sizeRateModal input[name="operation_fee"]').val(_operation_fee);
        $('#sizeRateModal select[name="operation_fee_type"]').val(_operation_fee_type);
        $('#sizeRateModal input[name="operation_min_value"]').val(_operation_min_value);
        if (_is_full_interest == 1) {
            $('input[name="is_full_interest"]').prop('checked', true);
            $('input[name="prepayment_interest"]').val('').closest('.form-group').hide();
            $('select[name="prepayment_interest_type"] option').first().prop("selected", 'selected');
        } else {
            $('input[name="is_full_interest"]').prop('checked', false);
            $('input[name="prepayment_interest"]').val(_prepayment_interest).closest('.form-group').show();
            $('select[name="prepayment_interest_type"]').val(_prepayment_interest_type)
        }
        $('#sizeRateModal').modal('show');
    }

    function remove_special_size_rate (_e) {
        var uid = $(_e).closest('tr').attr('uid');
        $.messager.confirm("<?php echo 'Remove'?>", "<?php echo 'Are you sure to remove?'?>", function (_r) {
            if (!_r) return;
            yo.loadData({
                _c: "loan",
                _m: "removeSpecialSizeRate",
                param: {uid: uid, product_id: product_id, size_rate_id: size_rate_id},
                callback: function (_o) {
                    if (_o.STS) {
                        window.location.href = _o.DATA.url;
                    } else {
                        alert(_o.MSG);
                    }
                }
            });
        });
    }

    $('#size_rate_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            mortgage_type: {
                required: true
            },
            special_type: {
                required: true
            },
            interest_rate: {
                required: true
            },
            interest_min_value: {
                required: true
            },
            admin_fee: {
                required: true
            },
            loan_fee: {
                required: true
            },
            operation_fee: {
                required: true
            },
            operation_min_value: {
                required: true
            },
            prepayment_interest: {
                checkRequired: true
            }
        },
        messages: {
            mortgage_type: {
                required: '<?php echo 'Required!'?>'
            },
            special_type: {
                required: '<?php echo 'Required!'?>'
            },
            interest_rate: {
                required: '<?php echo 'Required!'?>'
            },
            interest_min_value: {
                required: '<?php echo 'Required!'?>'
            },
            admin_fee: {
                required: '<?php echo 'Required!'?>'
            },
            loan_fee: {
                required: '<?php echo 'Required!'?>'
            },
            operation_fee: {
                required: '<?php echo 'Required!'?>'
            },
            operation_min_value: {
                required: '<?php echo 'Required!'?>'
            },
            prepayment_interest: {
                checkRequired: '<?php echo 'Required!'?>'
            }
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
