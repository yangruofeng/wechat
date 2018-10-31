<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<style>
    .important-info .item {
        background-color: #fff;
        text-align: center;
        float: left;
        width: 19.2%;
        margin-right: 1%;
        margin-bottom: 15px;
    }

    .important-info .item p {
        margin-bottom: 0;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        padding: 10px 10px 7px;
        border-bottom: 1px solid #e7eaec;
    }

    .important-info .item .c {
        font-size: 18px;
        padding: 15px 0;
        max-height: 65px;
        font-weight: 700;
    }

    .important-info .item:last-child {
        margin-right: 0;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Bank</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('partner', 'bank', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span><?php echo $output['partner']['partner_name']?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">

        <div class="important-info clearfix">
            <div class="item">
                <p>System Balance</p>
                <div class="c"><?php echo ncAmountFormat($output['balance_detail']['system_balance'], false, $output['currency'])?></div>
            </div>
            <div class="item">
                <p>Balance(Last Check)</p>
                <div class="c"><?php echo ncAmountFormat($output['balance_detail']['begin_balance'], false, $output['currency'])?></div>
            </div>
            <div class="item">
                <p>Time(Last Check)</p>
                <div class="c"><?php echo $output['last_check_time']?timeFormat($output['last_check_time']):'Null'?></div>
            </div>
            <div class="item">
                <p>Turnover(Income)</p>
                <div class="c"><?php echo ncAmountFormat($output['balance_detail']['income'], false, $output['currency'])?></div>
            </div>
            <div class="item">
                <p>Turnover(Pay Out)</p>
                <div class="c"><?php echo ncAmountFormat($output['balance_detail']['outcome'], false, $output['currency'])?></div>
            </div>
        </div>

        <div class="business-condition" style="margin-top: 20px">
            <form class="form-inline" id="frm_search_condition">
                <input type="hidden" name="uid" value="<?php echo $output['partner']['uid']?>">
                <table  class="search-table">
                    <tr>
                        <td>
                            <div class="form-group">
                                <label for="exampleInputName2">Date</label>
                                <input type="text" class="form-control datepicker startline" name="startline" value="<?php echo $output['startline']?>">&nbsp;-
                                <input type="text" class="form-control datepicker deadline" name="deadline" value="<?php echo $output['deadline']?>">
                            </div>
                        </td>
                        <td>
                            <select name="currency" class="form-control valid">
                                <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                    <option name="<?php echo $key;?>"><?php echo $currency;?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="btn_add_manual">
                                        <i class="fa fa-plus"></i>
                                        Manual
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="btn_adjust">
                                        <i class="fa fa-plus"></i>
                                        Adjust
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <a type="button" class="btn btn-default" href="<?php echo getUrl('partner', 'checkHistory', array('uid' => $output['partner']['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <i class="fa fa-check"></i>
                                        Compare
                                    </a>
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
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Manual Account'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="manual_form">
                        <input type="hidden" name="trace_id">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                            <div class="col-sm-9">
                                <select name="currency" class="form-control valid">
                                    <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                        <option name="<?php echo $key;?>"><?php echo $currency;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Amount'?></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="amount" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Type'?></label>
                            <div class="col-sm-9">
                                <label style="margin-top: 5px"><input type="radio" name="trx_type" value="deposit" checked><?php echo 'Deposit'?></label>
                                <label style="margin-top: 5px;margin-left: 10px"><input type="radio" name="trx_type" value="withdrawal"><?php echo 'Withdrawal'?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Handler'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="operator_name" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Time'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control datepicker" name="trx_time" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Remark'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="remark" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger save-manual"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="adjustModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Adjust'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="adjust_form">
                        <input type="hidden" name="trace_id">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                            <div class="col-sm-9">
                                <select name="currency" class="form-control valid">
                                    <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                        <option name="<?php echo $key;?>"><?php echo $currency;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Amount'?></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="amount" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Remark'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="remark" value="" placeholder="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Type'?></label>
                            <div class="col-sm-9">
                                <label style="margin-top: 5px"><input type="radio" name="trx_type" value="plus" checked><?php echo 'Plus'?></label>
                                <label style="margin-top: 5px;margin-left: 10px"><input type="radio" name="trx_type" value="minus"><?php echo 'Minus'?></label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger save-adjust"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {

        $('.datepicker').datetimepicker({
            language: 'zh',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1,
            minuteStep: 10
        }).on('changeDate', function (ev) {
            $(this).datetimepicker('hide');
        });

        btn_search_onclick();

        $('#frm_search_condition select[name="currency"]').change(function () {
            btn_search_onclick();
        })

        $('#btn_add_manual').click(function(){
            $('#manual_form input[type!="radio"]').val('');
            $('#manual_form input[name="trx_type"]').first().prop('checked', true);
            $('#manual_form input[name="api_state"]').first().prop('checked', true);
            $('#myModal').modal('show');
        })

        $('.save-manual').click(function(){
            if (!$("#manual_form").valid()) {
                return;
            }

            var values = $('#manual_form').getValues();
            var partner_id = $('input[name="uid"]').val();
            if (!partner_id) {
                return;
            } else {
                values.partner_id = partner_id;
            }

            if(values.trace_id){
                var _m = 'editManual';
            } else {
                var _m = 'addManual';
            }

            yo.loadData({
                _c: 'partner',
                _m: _m,
                param: values,
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG);
                        $('#myModal').modal('hide');
                        window.location.reload();
                    } else {
                        alert(_o.MSG);
                    }
                }
            })
        })

        $('#btn_adjust').click(function(){
            $('#adjust_form input[type!="radio"]').val('');
            $('#adjust_form input[name="trx_type"]').first().prop('checked', true);
            $('#adjustModal').modal('show');
        })

        $('.save-adjust').click(function(){
            if (!$("#adjust_form").valid()) {
                return;
            }

            var values = $('#adjust_form').getValues();
            var partner_id = $('input[name="uid"]').val();
            if (!partner_id) {
                return;
            } else {
                values.partner_id = partner_id;
            }

            if(values.trace_id){
                var _m = 'editAdjust';
            } else {
                var _m = 'addAdjust';
            }

            yo.loadData({
                _c: 'partner',
                _m: _m,
                param: values,
                callback: function (_o) {
                    if (_o.STS) {
                        alert(_o.MSG);
                        $('#adjustModal').modal('hide');
                        window.location.reload();
                    } else {
                        alert(_o.MSG);
                    }
                }
            })
        })
    });

    function edit_manual(_e) {
        var _e = $(_e);
        var trace_id = _e.attr('trace_id');
        var currency = _e.attr('currency');
        var amount = _e.attr('amount');
        var trx_type = _e.attr('trx_type');
        var remark = _e.attr('remark');
        var operator_name = _e.attr('operator_name');
        var trx_time = _e.attr('trx_time');

        $('#manual_form input[name="trace_id"]').val(trace_id);
        $('#manual_form select[name="currency"]').val(currency);
        $('#manual_form input[name="amount"]').val(amount);
        $('#manual_form input[name="remark"]').val(remark);
        $('#manual_form input[name="operator_name"]').val(operator_name);
        $('#manual_form input[name="trx_type"][value="' + trx_type + '"]').prop('checked', true);
        $("input[name='trx_time']").val(trx_time);
        $('#myModal').modal('show');
    }

    function edit_adjust(_e) {
        var _e = $(_e);
        var trace_id = _e.attr('trace_id');
        var amount = _e.attr('amount');
        var trx_type = _e.attr('trx_type');
        var remark = _e.attr('remark');

        $('#adjust_form input[name="trace_id"]').val(trace_id);
        $('#adjust_form input[name="amount"]').val(amount);
        $('#adjust_form input[name="remark"]').val(remark);
        $('#adjust_form input[name="trx_type"][value="' + trx_type + '"]').prop('checked', true);
        $('#adjustModal').modal('show');
    }

    function change_state(uid, state) {
        if (state == 11) {
            var _message = 'Are you sure to change the status to success?';
            var _state = 100;
        } else {
            var _message = 'Are you sure to change the status to failure?';
            var _state = 11;
        }
        $.messager.confirm("<?php echo 'Change State';?>", _message, function (_r) {
            if(!_r) return;
            yo.loadData({
                _c: "partner",
                _m: "changeTraceState",
                param: {uid: uid, state: _state},
                callback:function(_o){
                    if(_o.STS){
                        alert(_o.MSG);
                        btn_search_onclick();
                    }else{
                        alert(_o.MSG);
                    }
                }
            });
        });
    }

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $("#frm_search_condition").getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        _values.startline = $('input[name="startline"]').val();
        _values.deadline = $('input[name="deadline"]').val();
        yo.dynamicTpl({
            tpl: "partner/trace.check.list",
            dynamic: {
                api: "partner",
                method: "getTraceList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    $('#manual_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            amount: {
                required: true,
                min:0
            }
        },
        messages: {
            amount: {
                required: '<?php echo 'Required!'?>',
                min: 'It can\'t be less than 0'
            }
        }
    });
    $('#adjust_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            amount: {
                required: true,
                min:0
            }
        },
        messages: {
            amount: {
                required: '<?php echo 'Required!'?>',
                min: 'It can\'t be less than 0'
            }
        }
    });
</script>
