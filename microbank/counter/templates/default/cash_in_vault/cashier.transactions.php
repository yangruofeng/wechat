<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        padding: 5px 12px;
    }
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <input type="hidden" name="cashier_id" value="<?php echo $output['cashier_id']?>">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <select id="choose_currency" class="form-control" name="currency" onclick="btn_search_onclick();">
                                <option value="USD">USD</option>
                                <option value="KHR">KHR</option>
                            </select>
                        </td>
                        <td style="padding-left: 10px">
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>

        <div class="business-content" style="margin-bottom: 40px">
            <div class="business-list">

            </div>
        </div>
        <div style="text-align: center;margin-bottom: 30px">
            <button class="btn btn-default"  style="min-width: 80px;margin-left: -40px" onclick="javascript:history.go(-1);">Back</button>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        btn_search_onclick();

        $('#choose_currency').change(function () {
            btn_search_onclick();
        })
    });

    //  分页展示贷款申请列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        yo.dynamicTpl({
            tpl: "cash_in_vault/cashier.transactions.list",
            control:'counter_base',
            dynamic: {
                api: "cash_in_vault",
                method: "getCashierTransactionsList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>