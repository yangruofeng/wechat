<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Event</h3>
            <ul class="tab-base">
                <?php if(intval($_GET['is_system']) == 1){?>
                    <li><a href="<?php echo getUrl('user', 'pointEvent', array('is_system' => 0), false, BACK_OFFICE_SITE_URL) ?>"><span>Evaluation Event</span></a></li>
                    <li><a class="current"><span>System Event</span></a></li>
                <?php } else {?>
                    <li><a class="current"><span>Evaluation Event</span></a></li>
                    <li><a href="<?php echo getUrl('user', 'pointEvent', array('is_system' => 1), false, BACK_OFFICE_SITE_URL) ?>"><span>System Event</span></a></li>
                <?php }?>
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
                            <input type="text" class="form-control" id="search_text" name="search_text" style="width: 230px" placeholder="Search for code">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                          </div>
                        </td>
                        <?php if(intval($_GET['is_system']) != 1){?>
                        <td>
                            <button type="button" class="btn btn-default" onclick="add_event()" style="min-width: 70px">
                                <i class="fa fa-plus"></i>
                                <?php echo 'Add'; ?>
                            </button>
                        </td>
                        <?php }?>
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
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Add Event'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="event_form">
                        <input type="hidden" id="uid" name="uid" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Event Code'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="event_code" name="event_code" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Description'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="description" name="description" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Min-Point'?></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="min_point" name="min_point" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Max-Point'?></label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" id="max_point" name="max_point" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Status'?></label>
                            <div class="col-sm-8">
                                <label style="margin-top: 6px"><input type="radio" name="status" value="100" checked>Active</label>
                                <label style="margin-top: 6px"><input type="radio" name="status" value="0">Inactive</label>
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
    var is_system = '<?php echo intval($_GET['is_system'])?>';
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
            tpl: "user/point.event.list",
            dynamic: {
                api: "user",
                method: "getPointEventList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text, is_system: is_system}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function add_event() {
        $('#event_form input[type!="radio"]').val('');
        $('#event_form input[type="radio"][value=1]').prop('checked', true);
        $('#myModalLabel').html('Add Event');
        $('#myModal').modal('show');
    }

    function edit_event(_e) {
        var _tr = $(_e).closest('tr');
        var uid = _tr.attr('uid');
        var event_code = _tr.attr('event_code');
        var description = _tr.attr('description');
        var min_point = _tr.attr('min_point');
        var max_point = _tr.attr('max_point');
        var status = _tr.attr('status');

        $('#myModalLabel').html('Edit Event');
        $('#myModal #uid').val(uid);
        $('#myModal #event_code').val(event_code);
        $('#myModal #description').val(description);
        $('#myModal #min_point').val(min_point);
        $('#myModal #max_point').val(max_point);
        $('#myModal input[type="radio"][value=' + status + ']').prop('checked', true);
        $('#myModal').modal('show');
    }

    $('.btn-danger').click(function () {
        if (!$("#event_form").valid()) {
            return;
        }

        var values = $('#event_form').getValues();
        if (values.uid > 0) {
            var m = 'editEvent';
        } else {
            var m = 'addEvent';
        }
        yo.loadData({
            _c: 'user',
            _m: m,
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $('#myModal').modal('hide');
                    btn_search_onclick();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    $('#event_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            event_code : {
                required : true
            },
            description : {
                required : true
            },
            min_point : {
                required : true
            },
            max_point : {
                required : true,
                comparePoint : true
            }
        },
        messages : {
            event_code : {
                required : '<?php echo 'Required'?>'
            },
            description : {
                required : '<?php echo 'Required'?>'
            },
            min_point : {
                required : '<?php echo 'Required'?>'
            },
            max_point : {
                required : '<?php echo 'Required'?>',
                comparePoint : '<?php echo 'Max point must be greater than min point!'?>'
            }
        }
    });

    jQuery.validator.addMethod("comparePoint", function (value, element) {
        var max_point = Number(value);
        var min_point = Number($('#min_point').val());
        if (max_point > min_point) {
            return true;
        } else {
            return false;
        }
    });
</script>
