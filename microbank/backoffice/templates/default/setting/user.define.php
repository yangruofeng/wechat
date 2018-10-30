<style>
    .business-list tr td {
        vertical-align: middle !important;
        background-color: #FFF !important;
    }

    .business-list .table tr.table-header td {
        background: #DDD !important;
    }

    .business-list tr.tr_odd td {
        background-color: #EFEFEF !important;
    }

    .business-list .easyui-panel {
        height: 44px;
    }

    .business-list .easyui-panel table {
        margin-top: 1px;
    }

    .business-list .fa, .business-list .fa-span {
        cursor: pointer;
    }

    .business-list .category-item {
        display: none;
    }

    .business-list .define-item-title {
        font-weight: 500;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Short Code</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                          <div class="input-group">
                            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for...">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                          </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="business-content">
            <div class="business-list">

            </div>
        </div>
    </div>
</div>
<div class="modal" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Edit Category'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="category_form">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Category'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="category" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Category Name'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="category_name" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <?php foreach ($output['lang_list'] as $key => $lang) { ?>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Category Name(' . $lang . ')'?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control category-langs" name="<?php echo $key?>" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_category()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Edit Item'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal clearfix" id="item_form">
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Category'?></label>
                            <div class="col-sm-8">
                                <input type="hidden" name="uid" value="">
                                <input type="text" class="form-control" name="category" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Item Code'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="item_code" value="">
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Item Name'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="item_name" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <?php foreach ($output['lang_list'] as $key => $lang) { ?>
                            <div class="form-group col-sm-6">
                                <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Item Name(' . $lang . ')'?></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control item-langs" name="<?php echo $key?>" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Item Description'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="item_desc" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="inputEmail3" class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Item Value'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="item_value" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_item()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jq.json.js"></script>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "setting/user.define.list",
            dynamic: {
                api: "setting",
                method: "getDefineList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text, is_system: 0}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    $('.business-list').delegate('.on-off', 'click', function () {
        if ($(this).hasClass('fa-plus-circle')) {
            $(this).removeClass('fa-plus-circle').addClass('fa-minus-circle');
            var category = $(this).attr('category');
            $('.business-list .' + category).show();
        } else {
            $(this).removeClass('fa-minus-circle').addClass('fa-plus-circle');
            var category = $(this).attr('category');
            $('.business-list .' + category).hide();
        }
    });

    function edit_category(_e) {
        var e = $(_e);
        var category = e.attr('category');
        var category_name = e.attr('category_name');
        var category_name_json = e.attr('category_name_json');
        if(category_name_json){
            var category_name_arr = $.evalJSON(category_name_json);
        }

        var modal = $('#categoryModal');
        modal.find('input[name="category"]').val(category);
        modal.find('input[name="category_name"]').val(category_name);
        if (category_name_json) {
            modal.find('.category-langs').each(function () {
                var _lang = $(this).attr('name');
                $(this).val(category_name_arr[_lang])
            })
        } else {
            modal.find('.category-langs').val('');
        }
        modal.modal('show');
    }

    function save_category() {
        if (!$("#category_form").valid()) {
            return;
        }
        var values = $('#category_form').getValues();
        yo.loadData({
            _c: 'setting',
            _m: 'editCategoryName',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    btn_search_onclick();
                    $('#categoryModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function add_item(_category) {
        var modal = $('#itemModal');
        modal.find('input').val('');
        modal.find('input[name="category"]').val(_category);
        modal.modal('show');
    }

    function edit_item(_e) {
        var e = $(_e);
        var uid = e.attr('uid');
        var item_code = e.attr('item_code');
        var item_name = e.attr('item_name');
        var item_desc = e.attr('item_desc');
        var item_value = e.attr('item_value');
        var item_name_json = e.attr('item_name_json');
        if(item_name_json){
            var item_name_arr = $.evalJSON(item_name_json);
        }

        var modal = $('#itemModal');
        modal.find('input[name="uid"]').val(uid);
        modal.find('input[name="item_code"]').val(item_code);
        modal.find('input[name="item_name"]').val(item_name);
        modal.find('input[name="item_desc"]').val(item_desc);
        modal.find('input[name="item_value"]').val(item_value);
        if (item_name_json) {
            modal.find('.item-langs').each(function () {
                var _lang = $(this).attr('name');
                $(this).val(item_name_arr[_lang])
            })
        } else {
            modal.find('.category-langs').val('');
        }
        modal.modal('show');
    }

    function save_item() {
        if (!$("#item_form").valid()) {
            return;
        }
        var values = $('#item_form').getValues();
        if (values.uid) {
            var _m = 'editDefineItem';
        } else {
            var _m = 'addDefineItem';
        }
        yo.loadData({
            _c: 'setting',
            _m: _m,
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    btn_search_onclick();
                    $('#itemModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function delete_item(_uid) {
        $.messager.confirm("<?php echo 'Remove'?>", "<?php echo 'Are you sure to remove?'?>", function (_r) {
            if (!_r) return;
            yo.loadData({
                _c: "setting",
                _m: "removeDefineItem",
                param: {uid: _uid},
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG);
                        btn_search_onclick();
                    } else {
                        alert(_o.MSG);
                    }
                }
            });
        });
    }

    $('#category_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            category_name: {
                required: true
            }
        },
        messages: {
            category_name: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

    $('#item_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            item_name: {
                required: true
            }
        },
        messages: {
            item_name: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>
