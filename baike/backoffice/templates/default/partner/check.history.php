<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Check History</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('partner', 'bank', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a class="current"><span>Check History(<?php echo $output['partner']['partner_name']?>)</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">

        <div class="business-condition" style="margin-top: 20px">
            <form class="form-inline" id="frm_search_condition">
                <input type="hidden" name="uid" value="<?php echo $output['partner']['uid']?>">
                <table  class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <a type="button" class="btn btn-default" href="<?php echo getUrl('partner', 'checkTrace', array('uid' => $output['partner']['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                        <i class="fa fa-reply"></i>
                                        Back
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <a type="button" class="btn btn-danger" id="btn_check">
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

<div class="modal" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Compare'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="check_form">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                            <div class="col-sm-9">
                                <select name="currency" class="form-control valid">
                                    <option value="0">Please Currency</option>
                                    <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                        <option name="<?php echo $key;?>"><?php echo $currency;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing"></span><?php echo 'System Balance'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="system_balance" value="" disabled>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Api Balance'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="api_balance" value="" disabled>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><?php echo 'Difference'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="difference" value="" disabled>
                                <div class="error_msg" style="display:none">The account is incorrect, please check it!</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger check-balance"><?php echo 'Check'?></button>
            </div>
        </div>
    </div>
</div>

<script>
    var is_check = false;
    var uid = '<?php echo intval($output['partner']['uid'])?>';
    $(document).ready(function () {
        btn_search_onclick();

        $('#btn_check').click(function () {
            $('#check_form input').val('');
            $('#check_form select option:first').prop('selected', 'selected');
            $('#checkModal').modal('show');
        })

        $('#check_form select[name="currency"]').change(function () {
            is_check = false;
            $('#check_form input').val('');
            $('.error_msg').hide();
            var currency = $(this).val();
            yo.loadData({
                _c: "partner",
                _m: "getCheckData",
                param: {uid: uid, currency: currency},
                callback: function (_o) {
                    if (_o.STS) {
                        var data = _o.DATA;
                        $('#check_form input[name="system_balance"]').val(data.system_balance);
                        $('#check_form input[name="api_balance"]').val(data.api_balance);
                        $('#check_form input[name="difference"]').val(data.difference);
                        is_check = data.is_check;
                        if (is_check) {
                            $('.error_msg').hide();
                        } else {
                            $('.error_msg').show();
                        }
                    } else {
                        alert(_o.MSG);
                    }
                }
            });
        })

        $('.check-balance').click(function () {
            if (!is_check) {
                return;
            }
            var currency = $('#check_form select[name="currency"]').val();
            yo.loadData({
                _c: "partner",
                _m: "addCheckTrace",
                param: {uid: uid, currency: currency},
                callback: function (_o) {
                    alert(_o.MSG);
                    $('#checkModal').modal('hide');
                    btn_search_onclick();
                }
            });
        })
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        if (uid <= 0) {
            return;
        }

        yo.dynamicTpl({
            tpl: "partner/check.history.list",
            dynamic: {
                api: "partner",
                method: "getCheckHistoryList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, uid: uid}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
