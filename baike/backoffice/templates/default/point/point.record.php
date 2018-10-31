<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Point Record</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text"
                                       placeholder="Search for user name">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default" id="btn_search_list"
                                          onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      <?php echo 'Search'; ?>
                                  </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select class="form-control" name="branch_id">
                                <option value="0">Please Branch</option>
                                <?php foreach ($output['branch_list'] as $branch) { ?>
                                    <option
                                        value="<?php echo $branch['uid'] ?>"><?php echo $branch['branch_name'] ?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" name="depart_id" disabled>
                                <option value="0">Please Department</option>
                                <?php foreach ($output['depart_list'] as $depart) { ?>
                                    <option class="branch_<?php echo $depart['branch_id'] ?>"
                                            value="<?php echo $depart['uid'] ?>"><?php echo $depart['depart_name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="checkbox" name="need_audit" id="need_audit"> Need To Audit
                            </div>
                            <div style="margin-left: 20px" class="form-group">
                                <input type="checkbox" name="own_department" id="own_department"> Own Department
                            </div>
                            <a style="margin-left: 20px;min-width: 80px" class="btn btn-default batch-audit">
                                Audit
                            </a>
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

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Audit'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="audit_form">
                        <input type="hidden" id="uid" name="uid" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Completion'?></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="point_factor" name="point_factor" placeholder="" value="">
                                    <span class="input-group-addon" style="min-width: 55px;border-left: 0">%</span>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Remark'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="remark" name="remark" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger btn-submit"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>
    $(document).ready(function () {
        btn_search_onclick();

        $('select[name="branch_id"]').change(function () {
            var _branch_id = $(this).val();
            $('select[name="depart_id"]').val(0);
            $('select[name="depart_id"] option[value!=0]').hide();
            if (_branch_id == 0) {
                $('select[name="depart_id"]').attr('disabled', true);
            } else {
                $('select[name="depart_id"] option.branch_' + _branch_id).show();
                $('select[name="depart_id"]').attr('disabled', false);
            }
            btn_search_onclick();
        })

        $('select[name="depart_id"]').change(function () {
            btn_search_onclick();
        })

        $('#need_audit,#own_department').click(function () {
            btn_search_onclick();
        })

        $('.business-list').delegate('#select_all', 'click', function () {
            if ($(this).is(':checked')) {
                $('input[name="uid"]').prop('checked', true);
            } else {
                $('input[name="uid"]').prop('checked', false);
            }
        })

        $('.business-list').delegate('input[name="uid"]', 'click', function () {
            if ($('.business-list input[name="uid"]').length == $('.business-list input[name="uid"]:checked').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        })

    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values._pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "point/point.record.list",
            dynamic: {
                api: "point",
                method: "getPointRecordList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function audit(uid,point_factor,remark){
        $('#myModal input').val('');
        $('#myModal #uid').val(uid);
        $('#myModal #remark').val(remark);
        if (point_factor > 0) {
            $('#myModal #point_factor').val(point_factor);
        }
        $('#myModal').modal('show');
    }

    $('.batch-audit').click(function () {
        if ($('.business-list input[name="uid"]:checked').length == 0) {
            return;
        }
        var uid = '';
        $('.business-list input[name="uid"]:checked').each(function (i) {
            if (i > 0) {
                uid += ',' + $(this).val();
            } else {
                uid += $(this).val();
            }
        })
        $('#myModal input').val('');
        $('#myModal #uid').val(uid);
        $('#myModal').modal('show');
    })

    $('.btn-submit').click(function () {
        if (!$("#audit_form").valid()) {
            return;
        }

        var values = $('#audit_form').getValues();
        yo.loadData({
            _c: 'point',
            _m: 'auditPoint',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.reload();
//                    $('#myModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    $('#audit_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules : {
            point_factor : {
                required : true,
                min: 0,
                max: 100
            }
        },
        messages : {
            point_factor : {
                required : '<?php echo 'Required'?>',
                min: '<?php echo 'It has to be between 1 and 100!'?>',
                max: '<?php echo 'It has to be between 1 and 100!'?>'
            }
        }
    });
</script>
