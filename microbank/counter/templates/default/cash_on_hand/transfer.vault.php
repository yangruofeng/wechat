<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .page .col-sm-7 {
        padding-left: 0px!important;
        margin-bottom: 60px;
    }

    .page .col-sm-5 {
        padding-right: 0px!important;
        margin-bottom: 60px;
    }

    .verify-state .btn {
        margin-left: -1px;
    }

    .verify-state .btn.active {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }

    .col-sm-5 .business-condition {
        margin-top: 20px;
    }

</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="row" style="max-width: 1300px">
        <div class="col-sm-12 col-md-8 col-lg-7">
            <div class="basic-info container">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Transfer To Vault</h5>
                </div>
                <div class="content">
                    <form id="transfer_Ct">
                        <div class="mincontent">
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Chief Teller'?></label>
                                <div class="col-sm-9">
                                    <select class="form-control" name='chief_id'>
                                        <?php foreach ($output['cashier'] as $cashier){ ?>
                                            <option value="<?php echo $cashier['uid']?>"><?php echo $cashier['user_name']?></option>
                                        <?php }?>
                                    </select>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label" ><span class="required-options-xing">*</span><?php echo 'Currency'?></label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="currency">
                                        <?php $ccy_list=(new currencyEnum())->Dictionary();
                                        foreach($ccy_list as $k=>$v){?>
                                            <option value="<?php echo $v;?>"><?php echo $k;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Amount'?></label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="amount" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Trading Password'?></label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" name="trading_password" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Remark'?></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="remark" value="">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group" style="text-align: center;margin-bottom: 0px">
                                <button type="button" class="btn btn-primary" onclick="submit_transfer_Ct()">
                                    <i class="fa fa-arrow-right"></i>Submit
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-8 col-lg-5" style="margin-top: 20px">
            <div class="business-condition">
                <form class="form-inline" id="frm_search_condition">
                    <table class="search-table">
                        <tbody>
                        <tr>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-btn verify-state">
                                       <button type="button" class="btn btn-default active" value="pending">Pending Receive</button>
                                       <button type="button" class="btn btn-default" value="received">Received</button>
                                       <button type="button" class="btn btn-default" value="rejected">Rejected</button>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>

            <div class="business-content" style="margin-top: 1px!important;margin-left: -1px!important;">
                <div class="business-list">

                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    $(document).ready(function () {
        btn_search_onclick();

        $('.verify-state .btn').on('click', function () {
            $('.verify-state .btn').removeClass('active');
            $(this).addClass('active');
            btn_search_onclick();
        });
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.state = $('.verify-state .btn.active').attr('value');
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "cash_on_hand/transfer.vault.list",
            control:'counter_base',
            dynamic: {
                api: "cash_on_hand",
                method: "getTransferVaultList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function submit_transfer_Ct() {
        if (!$("#transfer_Ct").valid()) {
            return;
        }

        var values = $('#transfer_Ct').getValues();
        yo.loadData({
            _c: 'cash_on_hand',
            _m: 'transferToCt',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href='<?php echo getUrl('cash_on_hand', 'transferToVault', array(), false, ENTRY_COUNTER_SITE_URL) ?>'
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('#transfer_Ct').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules : {
            chief_id : {
                required : true
            },
            currency : {
                required : true
            },
            amount : {
                required : true
            },
            trading_password : {
                required : true
            },
            remark : {
                required : true
            }
        },
        messages : {
            chief_id: {
                required: 'Required'
            },
            currency: {
                required: 'Required'
            },
            amount: {
                required: 'Required',
            },
            trading_password: {
                required: 'Required'
            },
            remark: {
                required: 'Required',
            }
        }
    });
</script>



