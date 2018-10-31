<style>
    .verify-state .btn {
        min-width: 70px!important;
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
        <?php include(template("widget/inc.msg.task.list"))?>
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition" onkeydown="if(event.keyCode==13){return false;}">
                <table  class="search-table">
                    <tr>
                        <td>
                          <div class="input-group">
                            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for name/phone">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
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
                                   <button type="button" class="btn btn-default active" value="<?php echo loanConsultStateEnum::CREATE?>">New</button>
                                   <button type="button" class="btn btn-default" value="<?php echo loanConsultStateEnum::BRANCH_REJECT?>">Bm-Reject</button>
                                   <button type="button" class="btn btn-default" value="<?php echo loanConsultStateEnum::CO_HANDING?>">CO-Checking</button>
                                   <button type="button" class="btn btn-default" value="<?php echo loanConsultStateEnum::CO_APPROVED?>">CO-Check-Ok</button>
                                   <button type="button" class="btn btn-default" value="<?php echo loanConsultStateEnum::CO_CANCEL?>">CO-Check-Reject</button>
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

        $('#frm_search_condition').keyup(function (event) {
            if (event.keyCode == 13) {
                btn_search_onclick();
            }
        });

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
            tpl: "branch_manager/loan.consult.list",
            dynamic: {
                api: "branch_manager",
                method: "getLoanConsultList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
