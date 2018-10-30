<style>
    .verify-state .btn {
        min-width: 70px!important;
    }

    .verify-state .btn.active {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }

    .business-list .btn {
        padding: 5px 12px;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Withdraw Mortgage Request</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>

                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-btn verify-state">
                                   <button type="button" class="btn btn-default active" value="<?php echo assetRequestWithdrawStateEnum::PENDING_APPROVE?>">To audit</button>
                                   <button type="button" class="btn btn-default" value="<?php echo assetRequestWithdrawStateEnum::PENDING_WITHDRAW?>">Approved</button>
                                   <button type="button" class="btn btn-default" value="<?php echo assetRequestWithdrawStateEnum::REJECT?>">Rejected</button>
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
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        var _state = $('.verify-state .active').attr('value');
        _values.state = _state;

        yo.dynamicTpl({
            tpl: "loan_committee/mortgage.withdraw.request.list",
            dynamic: {
                api: "loan_committee",
                method: "getWithdrawMortgageRequestList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
