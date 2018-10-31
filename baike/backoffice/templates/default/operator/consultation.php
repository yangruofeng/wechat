<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Consultation</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <?php include(template("widget/inc.msg.task.list"))?>
        <div class="table-form">
            <div class="business-condition">
                <form class="form-inline input-search-box" id="frm_search_condition">
                    <table class="search-table">
                        <tr>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control  input-search" id="search_text" name="search_text" placeholder="Search for name/phone" style="min-width: 200px">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default  btn-search" id="btn_search_list" onclick="btn_search_onclick();">
                                            <i class="fa fa-search"></i>
                                        <?php echo 'Search'; ?>
                                        </button>
                                     </span>
                                </div>
                            </td>
                            <td>
                                All
                                <a class="btn btn-link" style="font-size: 20px;font-weight: bold" href="<?php echo getUrl("operator","consultation",array("no_register"=>0),false,BACK_OFFICE_SITE_URL)?>"><?php echo $output['all_count']?></a>
                            </td>
                            <td>
                                No Register
                                <a class="btn btn-link" style="font-size: 20px;font-weight: bold" href="<?php echo getUrl("operator","consultation",array("no_register"=>1),false,BACK_OFFICE_SITE_URL)?>"><?php echo $output['no_register_count']?></a>
                            </td>
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
            tpl: "operator/consultation.list",
            dynamic: {
                api: "operator",
                method: "getMyConsultation",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    no_register:'<?php echo $output['param_no_register']?>',
                    search_text: _search_text
                }
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
