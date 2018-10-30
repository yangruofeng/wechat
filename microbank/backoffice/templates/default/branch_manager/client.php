<style>
    .avatar-icon {
        width: 50px;
        height: 50px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Client List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php include(template("widget/inc.msg.task.list"))?>
        <div class="table-form">
            <div class="business-condition" style="margin-bottom: 10px">
                <form class="form-inline" id="frm_search_condition" onkeydown="if(event.keyCode==13){return false;}">
                    <table class="search-table">
                        <tr>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search_text" name="search_text" placeholder="CID/login account/name/phone" style="min-width: 200px">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                            <i class="fa fa-search"></i>
                                        <?php echo 'Search'; ?>
                                        </button>
                                     </span>
                                </div>
                            </td>
                            <td>
                                <select class="form-control" name="is_credit" onchange="btn_search_onclick();">
                                    <option value="0">All</option>
                                    <option value="1">Had Credit</option>
                                    <option value="2">No Credit</option>
                                </select>
                            </td>
                            <td>
                                <label><input type="checkbox" name="pending_committee_approve" onclick="btn_search_onclick()">Pending committee approve</label>
                            </td>
                            <td>
                                <label><input type="checkbox" name="member_state_cancel" onclick="btn_search_onclick()">Canceled Member</label>
                            </td>
                            <td>
                                <label><input type="checkbox" name="member_state_new" onclick="btn_search_onclick()">New Client</label>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <hr>
            <div class="business-content">
                <div class="business-list">

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
        $('#frm_search_condition').keyup(function (event) {
            if (event.keyCode == 13) {
                btn_search_onclick();
            }
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
        $('body').waiting();
        yo.dynamicTpl({
            tpl: "branch_manager/client.list",
            dynamic: {
                api: "branch_manager",
                method: "getClientList",
                param: _values
            },
            callback: function (_tpl) {
                $('body').unmask();
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
