<style>
    #myModal .modal-dialog {
        margin-top: 100px!important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Adjust Passbook Account</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Member</span></a></li>
                <li><a href="<?php echo getUrl('dev', 'passbookAccountAdjust', array('type' => 'branch'), false, BACK_OFFICE_SITE_URL ); ?>"><span>Branch</span></a></li>
                <li><a href="<?php echo getUrl('dev', 'passbookAccountAdjust', array('type' => 'bank'), false, BACK_OFFICE_SITE_URL ); ?>"><span>Bank</span></a></li>
                <li><a href="<?php echo getUrl('dev', 'passbookAccountAdjust', array('type' => 'user'), false, BACK_OFFICE_SITE_URL ); ?>"><span>Staff</span></a></li>
                <li><a href="<?php echo getUrl('dev', 'passbookAccountAdjust', array('type' => 'system'), false, BACK_OFFICE_SITE_URL ); ?>"><span>System</span></a></li>
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
                                <input type="text" class="form-control" style="width: 250px" id="search_text" name="search_text" placeholder="Search for cid/name/login code/phone">
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
            <div class="business-list"></div>
        </div>
    </div>
</div>

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Adjust Account Balance'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="adjust_form">
                        <input type="hidden" name="uid" value="">
                        <input type="hidden" id="balances" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="currency">
                                    <option value="USD">USD</option>
                                    <option value="KHR">KHR</option>
                                    <option value="CNY">CNY</option>
                                    <option value="VND">VND</option>
                                    <option value="THB">THB</option>
                                </select>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo 'Current Balance'?></label>
                            <div class="col-sm-8">
                                <span class="form-control" name="current_balance" readonly></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Change Amount'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="amount" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo 'After Balance'?></label>
                            <div class="col-sm-8">
                                <span class="form-control" name="after_balance" readonly></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo 'Remark'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="remark" placeholder="" value="">
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
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "dev/passbook_account.adjust.member.list",
            dynamic: {
                api: "dev",
                method: "getMemberListWithPassbookAccounts",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function adjust_account(uid, balances) {
        if(!uid){
            return;
        }
        $('#myModal input').val('');
        $('#myModal input[name="uid"]').val(uid);

        var currency = null;
        for (var k in balances) {
            currency = k;
            break;
        }
        if (!currency) currency = 'USD';
        $("#balances").val($.toJSON(balances));
        $('select[name="currency"]').val(currency).change();

        $('#myModal').modal('show');
    }

    $('#myModal select[name="currency"]').change(function(){
        var balances = $.evalJSON($("#balances").val());
        var currency = $(this).val();
        var balance = balances[currency];
        if (!balance) balance = 0;
        $('span[name="current_balance"]').text(balance.toFixed(2));
        $('input[name="amount"]').keyup();
    });

    $('#myModal input[name="amount"]').keyup(function(){
        var change = parseFloat($(this).val());
        var current_balance = parseFloat($('span[name="current_balance"]').text());
        if (!isNaN(change)) {
            $('span[name="after_balance"]').text((current_balance + change).toFixed(2));
        } else {
            $('span[name="after_balance"]').text(current_balance.toFixed(2));
        }
    });

    $('.btn-danger').click(function () {
        if (!$("#adjust_form").valid()) {
            return;
        }

        var values = $('#adjust_form').getValues();
        yo.loadData({
            _c: 'dev',
            _m: 'adjustMemberAccount',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    $('#myModal').modal('hide');
                    btn_search_onclick();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });

    $('#adjust_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            amount : {
                required : true
            }
        },
        messages : {
            amount : {
                required : '<?php echo 'Required'?>'
            }
        }
    });
</script>
