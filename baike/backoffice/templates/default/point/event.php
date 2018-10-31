<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Event</h3>
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
                            <input type="text" class="form-control" id="search_text" name="search_text" style="width: 230px" placeholder="Search for branch/department">
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

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Edit Event'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="edit_form">
                        <input type="hidden" id="uid" name="uid" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Department'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="department" placeholder="" value="" readonly>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Event Code'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="event" placeholder="" value="" readonly>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Event Name'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="event_name" name="event_name" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Point'?></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="point" name="point" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing"></span><?php echo 'Is Audit'?></label>
                            <div class="col-sm-8">
                                <label style="margin-top: 6px"><input type="checkbox" id="is_audit" name="is_audit" value="1"></label>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
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
            tpl: "point/event.list",
            dynamic: {
                api: "point",
                method: "getEventList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function edit_event(e) {
        var _tr = $(e).closest('tr');
        var _uid = _tr.attr('uid');
        var _depart = _tr.find('.depart').html();
        var _event = _tr.find('.event').html();
        var _event_name = _tr.find('.event_name').html();
        var _point = _tr.find('.point').html();
        var _is_audit = _tr.find('.is_audit').attr('is_audit');
        $('#myModal').find('#uid').val(_uid);
        $('#myModal').find('#department').val($.trim(_depart));
        $('#myModal').find('#event').val($.trim(_event));
        $('#myModal').find('#event_name').val($.trim(_event_name));
        $('#myModal').find('#point').val($.trim(_point));
        if (_is_audit == 1) {
            $('#myModal').find('#is_audit').prop('checked', true);
        } else {
            $('#myModal').find('#is_audit').prop('checked', false);
        }
        $('#myModal').modal('show');
    }

    $('.btn-danger').click(function () {
        if (!$("#edit_form").valid()) {
            return;
        }

        var values = $('#edit_form').getValues();
        yo.loadData({
            _c: 'point',
            _m: 'editEvent',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    var data = _o.DATA;
                    var _tr = $('tr[uid="' + data.uid + '"]');
                    _tr.find('.event_name').html(data.event_name);
                    _tr.find('.point').html(data.point);
                    if (data.is_audit) {
                        _tr.find('.is_audit').attr('is_audit', data.is_audit).html('<i class="fa fa-check"></i>');
                    } else {
                        _tr.find('.is_audit').attr('is_audit', data.is_audit).html('<i class="fa fa-remove"></i>');
                    }
                    _tr.find('.creator_name').html(data.creator);
                    _tr.find('.create_time').html(data.time);
                    $('#myModal').modal('hide');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    $('#edit_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            point : {
                required : true
            },
            event_name : {
                required : true
            }
        },
        messages : {
            point : {
                required : '<?php echo 'Required'?>'
            },
            event_name : {
                required : '<?php echo 'Required'?>'
            }
        }
    });
</script>
