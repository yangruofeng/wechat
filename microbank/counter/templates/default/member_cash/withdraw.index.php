<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .container {
        width: 800px !important;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .collection-div {
        margin-bottom: 70px;
    }

    .authorize_input {
        margin-top: -8px !important;
        margin-bottom: 10px;
    }

    .account-basic {
        margin-bottom: 0;
    }

    .text-small {
        margin-bottom: 0;
    }

    .basic-info {
        margin-bottom: 20px;
    }
    .limitbox td{
        background-color: white;
    }

</style>
<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>
    <div class="row" style="max-width: 1300px">
        <div class="col-sm-12 col-md-10 col-lg-7">
            <div class="basic-info">
               <?php include(template("widget/item.member.summary.v2"))?>
            </div>
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Withdrawal</h5>
                </div>
                <div class="content">
                    <div class="col-sm-6 mincontent" style="margin-top: 5px">
                        <form id='client_withdrawal'>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span
                                        class="required-options-xing">*</span><?php echo 'Currency' ?></label>

                                <div class="col-sm-9">
                                    <select class="form-control" name="currency" onchange="showLimit()">
                                        <?php $ccy_list = (new currencyEnum())->Dictionary();
                                        foreach ($ccy_list as $k => $v) {
                                            ?>
                                            <option value="<?php echo $v;?>"><?php echo $k;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label class="col-sm-3 control-label"><span
                                        class="required-options-xing">*</span><?php echo 'Amount' ?></label>

                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="amount" value="">

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
                            <div class="col-sm-12 form-group" style="text-align: center;margin-top: 10px">
                                <button type="button" class="btn btn-primary btn-block" onclick="lockAmount()">NEXT
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-6 mincontent">
                        <?php $i = 0 ;foreach($ccy_list as $key => $currency){ ++$i; ?>
                            <table class="table table-bordered limitbox" id="limit_<?php echo $key?>" style="display: <?php echo $i == 1 ? '' : 'none'?>">
                                <tr>
                                    <td colspan="2" style="text-align: center;font-size: 15px;">
                                        <label><?php echo $key;?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $lang['limit_key_branch_per_day'];?>
                                    </td>
                                    <td><?php echo $output['branch_limit'][$key];?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $lang['limit_key_per_member_per_day'];?>
                                    </td>
                                    <td class="member_per_day" currency="<?php echo $key?>">
                                        <?php echo $output['member_limit']['per_day'][$key]?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $lang['limit_key_per_member_per_time'];?>
                                    </td>
                                    <td class="member_per_time" currency="<?php echo $key?>">
                                        <?php echo $output['member_limit']['per_time'][$key]?>
                                    </td>
                                </tr>
                            </table>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-10 col-lg-5">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5>History</h5>
                </div>
                <div class="business-content">
                    <div class="business-list">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<script>
    $(function () {
        btn_search_onclick();
    });

    function lockAmount() {
        var currency = $('select[name=currency]').val();
        var amount = $('input[name=amount]').val();
        var values = $('#client_withdrawal').getValues();
        var client_id = '<?php echo $output['member_id']?>';
        values.client_id = client_id;
        if (client_id != '0') {
            if ($('select[name=currency]').val() && $('input[name=amount]').val()) {
                yo.loadData({
                    _c: 'member_cash',
                    _m: 'createClientWithdrawal',
                    param: values,
                    callback: function (_o) {
                        if (_o.STS) {
                            var _biz_id = _o.DATA;
                            window.location.href = "<?php echo getUrl('member_cash', 'withdrawalCheck', array(), false, ENTRY_COUNTER_SITE_URL)?>&biz_id=" + _biz_id;
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });

            } else {
                alert('Please Input Currency/Amount ')
            }
        } else {
            alert('Please Select Client');
        }

    }

    //  展示成功withdrawal列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);


        yo.dynamicTpl({
            tpl: "member_cash/withdrawal.list",
            control: 'counter_base',
            dynamic: {
                api: "member_cash",
                method: "getWithdrawalList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function showLimit() {
        var limit_currency = $('select[name="currency"]').val()
        $('.limitbox').hide();
        $('#limit_' + limit_currency).show();
    }

    function print_withdraw(biz_id) {
//        window.location.href="<?php //echo getUrl('print_form', 'printWithdraw', array(), false, ENTRY_COUNTER_SITE_URL)?>//&biz_id="+biz_id;
        window.external.showSpecifiedUrlPrintDialog("<?php echo getUrl('print_form', 'printWithdraw', array(), false, ENTRY_COUNTER_SITE_URL)?>&biz_id="+biz_id);
    }


</script>



