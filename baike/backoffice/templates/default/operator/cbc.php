<style>
    .business-condition {
        margin-bottom: 20px;
    }

    .verify-table .verify-img {
        width: 80px;
    }

    .verify-table .locking {
        color: red;
        font-style: normal;
    }

    .verify-table .locking i {
        margin-right: 3px;
    }

    .verify-table .fa-user {
        margin-right: 3px;
    }

    .img-list {
        display: inline-block;
    }

    .img-list span {
        width: 79px;
        height: 62px;
        display: block;
        float: left;
        border: 1px solid #f7d2d2;
    }

    .img-list span {
        margin-right: 2px;
    }

    .img-list span:last-child {
        margin-right: 0;
    }

    .verify-state .btn {
        margin-left: -1px;
    }

    .verify-state .btn.active {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }

    .verify-table .lab-name {
        width: 130px;
        text-align: right;
        margin-right: 8px;
    }

    .verify-table .cert-info {
        line-height: 10px;
        padding-top: 13px;
    }

    .verify-table .cert-type h3 {
        font-size: 20px;
        font-weight: 100;
        color: #000;
    }

    .verify-table .cert-type p {
        margin: 0;
    }

    .verify-table .cert-type label {
        margin-bottom: 0;
    }

    .verify-table .cert-type .lab-name {
        width: auto;
    }

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

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client CBC</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
    <div class="table-form">
            <div class="business-condition">
                <form class="form-inline input-search-box" id="frm_search_condition">
                    <input type="hidden" id="cert_type" value="<?php echo $output['type']?>">
                    <table class="search-table">
                        <tr>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control  input-search" id="search_text" name="search_text" placeholder="Search for member name">
                                    <span class="input-group-btn">
                                      <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                              onclick="btn_search_onclick();">
                                          <i class="fa fa-search"></i>
                                          <?php echo 'Search'; ?>
                                      </button>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-btn verify-state search-state">
                                       <button type="button" class="btn btn-default active" value="1">New</button>
                                       <button type="button" class="btn btn-default" value="2">Checked</button>
                                    </span>
                                </div>
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
    });

    $('.search-state .btn').on('click',function(){
        $('.search-state .btn').removeClass('active');
        $(this).addClass('active');
        var state = $(this).attr('value');
        _pageNumber = 1;
        _pageSize = 20;
        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        _values.state = state;
        state == 1 ? getNew(_values) : getChecked(_values);
    });

    function getNew(_values){
        yo.dynamicTpl({
            tpl: "operator/cbc.list",
            dynamic: {
                api: "operator",
                method: "getCbcList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function getChecked(_values){
        yo.dynamicTpl({
            tpl: "operator/cbc.list.checked",
            dynamic: {
                api: "operator",
                method: "getCbcList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function btn_search_onclick() {
        var state = $('.search-state .active').attr('value');
        _pageNumber = 1;
        _pageSize = 20;
        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        _values.state = state;
        state == 1 ? getNew(_values) : getChecked(_values);
    }
</script>




