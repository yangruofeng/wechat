<style>
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

    .verify-state .btn.active {
        color: #fff;
        background-color: #5cb85c;
        border-color: #4cae4c;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan Consult</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                          <div class="input-group">
                            <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for name/phone">
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
                                <span class="input-group-btn verify-state">
                                   <button type="button" class="btn btn-default active" value="<?php echo loanConsultStateEnum::CREATE;?>">To audit</button>
                                   <button type="button" class="btn btn-default" value="<?php echo loanConsultStateEnum::LOCKED;?>">Auditing</button>
                                   <button type="button" class="btn btn-default" value="<?php echo loanConsultStateEnum::OPERATOR_APPROVED;?>">Approved</button>
                                   <button type="button" class="btn btn-default" value="<?php echo loanConsultStateEnum::OPERATOR_REJECT;?>">Rejected</button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <a class="btn btn-default" href="<?php echo getUrl('operator', 'addLoanConsult', array(), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-plus"></i>Add Consult</a>
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
//        setInterval(btn_search_onclick(),10000);

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

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        var verify_state = $('.verify-state .active').attr('value');
        _values.verify_state = verify_state;

        yo.dynamicTpl({
            tpl: "operator/loan.consult.list",
            dynamic: {
                api: "operator",
                method: "getLoanConsultList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
