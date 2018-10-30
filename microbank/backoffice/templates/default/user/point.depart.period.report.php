<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/jquery.validation.min.js"></script>
<style>
    .business-list tr td {
        vertical-align: middle !important;
        background-color: #FFF !important;
    }

    .business-list .table tr.table-header td {
        background: #DDD !important;
    }

    .business-list tr.tr_odd td {
        background-color: #EEE !important;
    }

    .business-list .easyui-panel {
        height: 44px;
    }

    .business-list .easyui-panel table {
        margin-top: 1px;
    }

    .business-list .define-item-title {
        font-weight: 500;
    }

    .business-list .point-list {
        display: none;
    }

    .business-list .fa-plus, .business-list .fa-minus {
        cursor: pointer;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Department Point</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('user', 'departmentPoint', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Period List</span></a>
                </li>
                <li><a class="current"><span>Report</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div style="height: 40px;margin: 15px 0;border-bottom: 1px solid #CCC">
            <span style="font-size: 18px">
                <span
                    style="font-weight: 600;">Department: </span><?php echo $output['row']['branch_name'] . ' ' . $output['row']['depart_name'] ?>
            </span>
            <span style="font-size: 16px;margin-left: 25px">
                <span
                    style="font-weight: 600;">Period: </span><?php echo $output['row']['start_date'] . ' -- ' . $output['row']['end_date'] ?>
            </span>
        </div>
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <input type="hidden" name="uid" value="<?php echo $output['row']['uid']; ?>">
                <table class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text"
                                       placeholder="Search for user name">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
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
    var uid = '<?php echo $output['row']['uid']?>';
    $(function () {
        btn_search_onclick();
    })

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var values = $('#frm_search_condition').getValues();
        values.pageNumber = _pageNumber;
        values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "user/point.depart.period.report.list",
            dynamic: {
                api: "user",
                method: "getDepartUserList",
                param: values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    $('.business-list').delegate('.fa-plus', 'click', function () {
        var uid = $(this).attr('uid');
        $('.business-list tr.point-list-' + uid).show();
        $(this).removeClass('fa-plus').addClass('fa-minus');
    })

    $('.business-list').delegate('.fa-minus', 'click', function () {
        var uid = $(this).attr('uid');
        $('.business-list tr.point-list-' + uid).hide();
        $(this).removeClass('fa-minus').addClass('fa-plus');
    })
</script>
