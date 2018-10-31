<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Repayment</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Installment Forecaset Common</span></a></li>
                <li><a href="<?php echo getUrl('report_repayment', 'agingOfLoanArrear', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Aging of loan arrear</span></a></li>
                <li><a href="<?php echo getUrl('report_repayment', 'loanInFallingDue', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Loan in Falling Due</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                        <td>
                            <select id="choose_currency" class="form-control" name="currency" onclick="btn_search_onclick();">
                                <?php $currency_list = (new currencyEnum())->Dictionary();?>
                                <?php foreach ($currency_list as $key => $currency) { ?>
                                    <option value="<?php echo $key;?>"><?php echo $currency;?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><span class="label label-danger" style="font-size: 26px;">Sample</span></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="business-content">
            <div class="business-list">

            </div>
        </div>
    </div>
</div>
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

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "report_repayment/repayment.list",
            dynamic: {
                api: "report_repayment",
                method: "getRepaymentList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>
