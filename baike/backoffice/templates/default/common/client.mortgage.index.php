<div class="business-condition">
    <form class="form-inline input-search-box" id="frm_search_condition">
        <input type="hidden" name="uid" value="<?php echo $output['uid']; ?>">
<!--        <table class="search-table">-->
<!--            <tbody>-->
<!--            <tr>-->
<!--                <td>--><?php //include(template("widget/inc_condition_datetime")); ?><!--</td>-->
<!--                <td>-->
<!--                    <select id="choose_state" class="form-control" name="state">-->
<!--                        <option value="0">--><?php //echo 'All Status'; ?><!--</option>-->
<!--                        --><?php //foreach ($output['contract_state'] as $key => $state) {
//                            if($key < loanContractStateEnum::PENDING_DISBURSE) continue;
//                            ?>
<!--                            <option value="--><?php //echo $key; ?><!--">--><?php //echo $state; ?><!--</option>-->
<!--                        --><?php //} ?>
<!--                    </select>-->
<!--                </td>-->
<!--            </tr>-->
<!--            </tbody>-->
<!--        </table>-->
    </form>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="business-content">
                <div class="business-list"></div>
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
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        $(".business-list").waiting();
        yo.dynamicTpl({
            tpl: "common/client.mortgage.list",
            dynamic: {
                api: "common",
                method: "getClientCreditMortgageList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").unmask();
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
