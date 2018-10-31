<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
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
                                    <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for name/phone" style="min-width: 200px">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default btn-search" id="btn_search_list" onclick="btn_search_onclick();">
                                            <i class="fa fa-search"></i>
                                        <?php echo 'Search'; ?>
                                        </button>
                                     </span>
                                </div>
                            </td>
                            <td>
                                All
                                <a class="btn btn-link" style="font-size: 20px;font-weight: bold" href="<?php echo getUrl("web_credit","client",array(),false,BACK_OFFICE_SITE_URL)?>"><?php echo $output['all_count']?></a>
                            </td>
                            <td>
                                PendingCheck
                                <a class="btn btn-link" style="font-size: 20px;font-weight: bold" href="<?php echo getUrl("web_credit","client",array("param_pending_check"=>1),false,BACK_OFFICE_SITE_URL)?>"><?php echo $output['pending_check_count']?></a>
                            </td>
                            <td>
                                NoBranch
                                <a class="btn btn-link" style="font-size: 20px;font-weight: bold" href="<?php echo getUrl("web_credit","client",array("param_no_branch"=>1),false,BACK_OFFICE_SITE_URL)?>"><?php echo $output['no_branch_count']?></a>
                            </td>
                            <td>
                                NoCredit
                                <a class="btn btn-link" style="font-size: 20px;font-weight: bold" href="<?php echo getUrl("web_credit","client",array("param_no_credit"=>1),false,BACK_OFFICE_SITE_URL)?>"><?php echo $output['no_credit_count']?></a>
                            </td>
<!--                            <td>-->
<!--                                Suspended-->
<!--                                <a class="btn btn-link" style="font-size: 20px;font-weight: bold" href="--><?php //echo getUrl("web_credit","client",array("param_suspended"=>1),false,BACK_OFFICE_SITE_URL)?><!--">--><?php //echo $output['suspended_cnt']?><!--</a>-->
<!--                            </td>-->
                        </tr>
                    </table>
                </form>
            </div>
            <hr>
            <div class="business-content">
                <div class="business-list"></div>
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
        yo.dynamicTpl({
            tpl: "web_credit/client.list",
            dynamic: {
                api: "web_credit",
                method: "getMyClientList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text,
                    param_pending_check: '<?php echo $output['param_pending_check']?>',
                    param_no_branch: '<?php echo $output['param_no_branch']?>',
                    param_no_credit: '<?php echo $output['param_no_credit']?>',
                    param_suspended: '<?php echo $output['param_suspended']?>'
                }
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
