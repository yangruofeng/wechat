<style>
    .table-body .btn {
        padding: 3px 12px;
    }
</style>
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
<div class="page contentbox">
    <div class="container">
        <div class="business-condition">
            <div class="fixed-bar">
                <div class="item-title">
                    <h3>Complaint And Advice</h3>
                    <ul class="tab-base">
                        <li><a href="<?php echo getUrl('operator', 'addComplaintAdvice', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                        <li><a class="current"><span>List</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control input-search" id="search_text" style="height: 34px" name="search_text" placeholder="Search for title">
                                <span class="input-group-btn">
                                     <button type="button" class="btn btn-default square btn-search" id="btn_search_list" onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      Search
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                    <span class="input-group-btn verify-state type">
                                       <button type="button" class="btn btn-default active" value="">ALL</button>
                                       <button type="button" class="btn btn-default" value="complaint">Complaint</button>
                                       <button type="button" class="btn btn-default" value="advice">Advice</button>
                                    </span>
                            </div>
                        </td>
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

    $('.verify-state .btn').on('click', function () {
        $('.type .btn').removeClass('active');
        $(this).addClass('active');
        btn_search_onclick();
    });

    //  分页展示贷款申请列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();
        var _type = $('.type .active').attr('value');
        yo.dynamicTpl({
            tpl: "operator/complaint.advice.list",
            dynamic: {
                api: "operator",
                method: "getComplaintAdviceList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text,type:_type}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>
