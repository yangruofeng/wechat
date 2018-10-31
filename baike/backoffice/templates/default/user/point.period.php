<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css">
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .modal-dialog {
        margin-top: 5px !important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Period</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
<!--                <li><a href="--><?php //echo getUrl('user', 'addPointPeriod', array(), false, BACK_OFFICE_SITE_URL)?><!--"><span>Add</span></a></li>-->
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
                              <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search';?>
                              </button>
                            </span>
                          </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" id="btn_search_list" onclick="add_period();">
                                <i class="fa fa-plus"></i>
                                <?php echo 'Add';?>
                            </button>
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
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Add Period'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="period_form">
                        <input type="hidden" id="uid" name="uid" value="">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Period'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="period" name="period" placeholder="" value="" onclick="javascript:return false;">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Start Date'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control datepicker1" id="start_date" name="start_date" value="" disabled>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'End Date'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control datepicker2" id="end_date" name="end_date" value="">
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

<script>
    var new_start_date = "<?php echo $output['new_start_date'];?>";
    $(document).ready(function () {
        if(!new_start_date){
            $(".datepicker1").datepicker({
                format: "yyyy-mm-dd",
                autoclose: true
            });
        }

        $(".datepicker2").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
        });

        $('.open').click(function () {
            $(this).prev().datepicker('show');
        });
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
            tpl: "user/point.period.list",
            dynamic: {
                api: "user",
                method: "getPeriodList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function add_period() {
        $('#myModal input').val('');
        if (new_start_date) {
            $('#myModal #start_date').val(new_start_date);
        } else {
            $('#myModal #start_date').val('').attr('disabled', false);
        }
        $("#myModal #end_date").attr('disabled', false);
        $('#myModal').modal('show');
    }

    function edit_period(e) {
        var _tr = $(e).closest('tr');
        var uid = _tr.attr('uid');
        var period = _tr.attr('period');
        var start_date = _tr.attr('start_date');
        var end_date = _tr.attr('end_date');
        var is_new = _tr.attr('is_new');

        $('#myModal #uid').val(uid);
        $('#myModal #period').val(period);
        $('#myModal #start_date').val(start_date);
        $("#myModal #end_date").datepicker("update", end_date);
        if (!is_new) {
            $("#myModal #end_date").attr('disabled', true);
        } else {
            $("#myModal #end_date").attr('disabled', false);
        }
        $('#myModal').modal('show');
    }

    $('.btn-danger').click(function () {
        if (!$("#period_form").valid()) {
            return;
        }

        var values = $('#period_form').getValues();
        if (values.uid > 0) {
            var m = 'editPeriod';
        } else {
            var m = 'addPeriod';
        }
        yo.loadData({
            _c: 'user',
            _m: m,
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.reload();

                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    jQuery.validator.methods.greaterThanStartDate = function (value, element) {
        var start_date = $("#start_date").val();
        var date1 = new Date(Date.parse(start_date.replace(/-/g, "/")));
        var date2 = new Date(Date.parse(value.replace(/-/g, "/")));
        return date1 <= date2;
    };

    $('#period_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.next());
        },
        rules: {
            period: {
                required: true
            },
            start_date: {
                required: true
            },
            end_date: {
                required: true,
                greaterThanStartDate: true
            }
        },
        messages: {
            period: {
                required: 'Required'
            },
            start_date: {
                required: 'Required'
            },
            end_date: {
                required: 'Required',
                greaterThanStartDate: 'The start date should not be greater than the end date.'
            }
        }
    });
</script>
