<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Issue IC Card</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('dev', 'addIcCard', array(), false, BACK_OFFICE_SITE_URL ); ?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                          <div class="input-group">
                            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for...">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search';?>
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
            tpl: "dev/ic_card.list",
            dynamic: {
                api: "dev",
                method: "getIcCardList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function delete_card(uid) {
        if(!uid){
            return;
        }

        $.messager.confirm("Confirm", "<?php echo 'Are you sure you want to delete this card?' ?>", function(r){
            if (r) {
                yo.loadData({
                    _c: 'dev',
                    _m: 'deleteIcCard',
                    param: {uid: uid},
                    callback: function (_o) {
                        if (_o.STS) {
                            btn_search_onclick();
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });
            }
        });
    }
</script>
