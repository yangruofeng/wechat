<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .summary-div {
        width: 16.66%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-right: 10px;
        padding-left: 10px;
    }

    .summary-div h2 {
        margin-top: 10px!important;
    }

    .stats .stat {
        padding: 7px 12px!important;
    }
</style>
<?php $client = $output['summary']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Member</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('data_center_member', 'index', array(), false, BACK_OFFICE_SITE_URL);?>"><span>Main</span></a></li>
                <li><a class="current"><span>Client List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="table-form">
            <table class="search-table">
                <tr>
                    <td>
                        <button class="btn btn-success">
                            <?php echo $output['title'].'('.$output['count'].')';?>
                        </button>
                    </td>
                </tr>
            </table>
        </div>

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

    $("#choose_state").change(function () {
        if (typeof(btn_search_onclick) != "undefined") {
            btn_search_onclick(1, 20);
        }
    })

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
        _values.type = '<?php echo $output['type'];?>';
        $(".business-list").waiting();
        yo.dynamicTpl({
            tpl: "data_center_member/client.state.list",
            dynamic: {
                api: "data_center_member",
                method: "getClientList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").unmask();
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
