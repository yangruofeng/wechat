
<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>

    .square{
        border-radius: 0px !important;
    }

    .td2{
        padding-left: 5px;
    }

</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
    <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" style="height: 34px" name="search_text" placeholder="Search for name">
                                <span class="input-group-btn">
                                     <button type="button" class="btn btn-default square" id="btn_search_list" onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      Search
                                    </button>
                                    </span>
                            </div>
                        </td>
                        <td class="td2">
                            <a class="btn btn-default square" href="<?php echo getUrl('service', 'addLoanConsult', array(), false, ENTRY_COUNTER_SITE_URL)?>"><i class="fa fa-plus"></i>Add Consult</a>
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

//  分页展示贷款申请列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "service/loan.consult.list",
            control:'counter_base',
            dynamic: {
                api: "service",
                method: "getLoanConsultList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>