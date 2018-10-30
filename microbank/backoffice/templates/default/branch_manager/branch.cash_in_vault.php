<style>
    #top_counter {
        width: 100%;
        height: 80px;
        border: 2px solid #FFE499;
        background-color: white;
        margin-bottom: 20px;
        padding-left: 10px !important;
        padding-top: 10px !important;
        position: relative;
    }

    #top_counter .balance {
        position: absolute;
        top: 13px;
    }

    #top_counter .balance tr td {
        padding: 2px 8px 4px !important;
        background-color: #FFF !important;
        min-width: 200px;
    }

    #top_counter .balance tr td span.cash-in-hand, #top_counter .balance tr td span.cash-outstanding {
        font-weight: 600;
        font-size: 16px;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Counter Business</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Cash In Vault</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $currency_list = (new currencyEnum())->Dictionary();?>
        <?php $branch_balance = $output['branch_balance'];?>
        <div id="top_counter">
            <div class="balance ">
                <table class="table">
                    <tbody class="table-body">
                    <tr class="cash_in_hand">
                        <td><label class="control-label">Balance</label></td>
                        <?php foreach ($currency_list as $key => $currency) { ?>
                            <td>
                                <span><?php echo $currency;?> : </span>
                                <span class="cash-in-hand"><?php echo $branch_balance['cash_' . $key]?></span>
                            </td>
                        <?php }?>
                    </tr>
                    <tr class="outstanding">
                        <td><label class="control-label">Outstanding</label></td>
                        <?php foreach ($currency_list as $key => $currency) { ?>
                            <td>
                                <span><?php echo $currency;?> : </span>
                                <span class="cash-outstanding"><?php echo $branch_balance['out_' . $key]?></span>
                            </td>
                        <?php }?>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
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
            tpl: "branch_manager/branch.cash_in_vault.list",
            dynamic: {
                api: "branch_manager",
                method: "getCashInVaultList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
