
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/zeroModal/zeroModal.min.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/common.js"></script>
<style>

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Ace Account</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
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
                                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Phone Number">
                                <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list">
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

        <div class="business-content" id="client_ace_list">
            <div class="business-list">

               <!-- List -->
            </div>

        </div>
    </div>
</div>
<script>


    $(function (){

        $('#btn_search_list').click(function(){
            btn_search_onclick(1,20);
        });
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        var _ele = $('#client_ace_list');
        if (!_pageNumber) _pageNumber = _ele.data('pageNumber');
        if (!_pageSize) _pageSize = _ele.data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        _ele.data("pageNumber", _pageNumber);
        _ele.data("pageSize", _pageSize);

        var _values = {};
        var _keywords = $('#search_text').val();

        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        _values.phoneNumber = _keywords;

        yo.dynamicTpl({
            tpl: "dev/ace.account.list",
            dynamic: {
                api: "dev",
                method: "getClientAceAccountList",
                param: _values
            },
            callback: function (_tpl) {
                $("#client_ace_list .business-list").html(_tpl);
            }
        });
    }

    function aceAccountUnbind(id){
        yo.confirm('','Are you sure to unbind this account ?',function (_r) {
            if(!_r) return false;
            //$('body').mask();
            yo.loadData({
                _c: 'dev',
                _m: 'unbindClientAceAccount',
                param: {uid: id},
                callback: function (_o) {
                    if (_o.STS) {
                        alert('Unbind success!',1,function(){
                            window.location.reload();
                        });
                    } else {
                        alert(_o.MSG, 2);
                    }
                }
            });
        });
    }

    $(document).ready(function () {
        btn_search_onclick();
    });

</script>
