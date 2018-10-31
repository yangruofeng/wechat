<style>
    .verify-table .verify-state {
        display: inline-block;
        width: 150px;
    }

    .verify-table .verify-state .title {
        font-weight: 600;
        color: #fff;
        background: #40B2DA;
        border: 1px solid #40B2DA;
        text-align: center;
        padding: 6px 0;
    }

    .verify-table .verify-state .content {
        text-align: center;
        border: 1px solid #40B2DA;
        height: 70px;
    }

    .verify-table .verify-state .state {
        height: 35px;
        line-height: 35px;
    }

    .verify-table .verify-state .state.other {
        line-height: 0;
    }

    .verify-table .verify-state .state.other p {
        padding-top: 3px;
    }

    .verify-table .verify-state .custom-btn-group {
        float: inherit;
    }

    .verify-state .btn.active {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Change Trading Password Request</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="table-form">
            <div class="business-condition">
                <form class="form-inline input-search-box" id="frm_search_condition">
                    <table class="search-table">
                        <tr>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for cid/account/phone" style="min-width: 200px">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-search" id="btn_search_list" onclick="btn_search_onclick();">
                                            <i class="fa fa-search"></i>
                                            <?php echo 'Search'; ?>
                                        </button>
                                     </span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-btn verify-state">
                                       <button type="button" class="btn btn-default active" value="<?php echo commonApproveStateEnum::CREATE; ?>">To Audit</button>
                                        <button type="button" class="btn btn-default" value="<?php echo commonApproveStateEnum::APPROVING;?>">Auditing</button>
                                       <button type="button" class="btn btn-default" value="<?php echo commonApproveStateEnum::PASS;?>">Approved</button>
                                       <button type="button" class="btn btn-default" value="<?php echo commonApproveStateEnum::REJECT;?>">Rejected</button>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <hr>
            <div class="business-content">
                <div class="business-list" id="change_request_list_div"></div>
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

        var _search_text = $('#search_text').val();
        var verify_state = $('.verify-state .active').attr('value');
        $('body').waiting();
        yo.dynamicTpl({
            tpl: "operator/client.change.trading.password.request.list",
            dynamic: {
                api: "operator",
                method: "getChangeTradingPasswordRequestList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text,
                    verify_state: verify_state
                }
            },
            callback: function (_tpl) {
                $('body').unmask();
                $("#change_request_list_div").html(_tpl);
            }
        });
    }
</script>
