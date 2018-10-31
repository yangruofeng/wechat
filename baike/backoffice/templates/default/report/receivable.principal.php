<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/report.css?v=5" rel="stylesheet" type="text/css"/>
<style>
    .lv0 {
        font-weight: bold;
    }

    .lv1 label {
        padding-left: 10px;
        font-weight: normal;
    }

    .lv2 label {
        padding-left: 20px;
        font-weight: normal;
    }

    .lv3 label {
        padding-left: 30px;
        font-weight: normal;
    }

    .amount {
        text-align: right;
    }
    .input-search-box {
        margin-bottom: 15px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Balance Sheet</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('report', 'balance_sheet', array(), false, BACK_OFFICE_SITE_URL);?>"><span>Main</span></a></li>
                <li><a class="current"><span>Receivable</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="col-sm-12">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-money"></i>&nbsp;<?php echo $output['title'];?></h5>
                </div>
                <div class="table-form">
                    <div class="business-condition">
                        <form class="form-inline input-search-box" id="frm_search_condition">
                            <table class="search-table">
                                <tr>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for code/name/phone" style="min-width: 200px">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default btn-search" id="btn_search_list" onclick="btn_search_onclick();">
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
                    <hr>
                    <div class="business-content"></div>
                </div>
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

        var _search_text = $('#search_text').val();
        yo.dynamicTpl({
            tpl: "report/receivable.principal.list",
            dynamic: {
                api: "report",
                method: "<?php echo $output['method'];?>",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text
                }
            },
            callback: function (_tpl) {
                $('.business-content').html(_tpl);
            }
        });
    }
</script>