<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet"
      type="text/css"/>
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.css?v=1" rel="stylesheet"/>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Contract</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="table-form">
            <div class="business-condition" style="margin-bottom: 10px">
                <form class="form-inline" id="frm_search_condition">
                    <table class="search-table">
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="exampleInputName2">Contract SN</label>
                                    <input type="text" class="form-control" name="item" id="item">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="exampleInputName2">Name</label>
                                    <input type="text" class="form-control" name="name" id="name">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="exampleInputName2">Effective Date</label>
                                    <input type="text" class="form-control" name="date" id="date">
                                    <!--<span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>-->
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="exampleInputName2">State</label>
                                    <select class="form-control" id="state">
                                        <option value="-1">All</option>
                                        <option value="<?php echo loanContractStateEnum::CREATE; ?>">Create</option>
                                        <option value="<?php echo loanContractStateEnum::PENDING_APPROVAL; ?>">Pending
                                            Approval
                                        </option>
                                        <option value="<?php echo loanContractStateEnum::PENDING_DISBURSE; ?>">Pending
                                            Disburse
                                        </option>
                                        <option value="<?php echo loanContractStateEnum::PROCESSING; ?>">Processing
                                        </option>
                                        <option value="<?php echo loanContractStateEnum::PAUSE; ?>">Pause</option>
                                        <option value="<?php echo loanContractStateEnum::COMPLETE; ?>">Complete</option>
                                        <option value="<?php echo loanContractStateEnum::WRITE_OFF; ?>">Write Off
                                        </option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                  <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" id="btn_search_list"
                                            onclick="btn_search_onclick();">
                                        <i class="fa fa-search"></i>
                                        <?php echo 'Search'; ?>
                                    </button>
                                  </span>
                                </div>
                                <!-- /input-group -->
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
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/zeroModal/zeroModal.min.js?v=1"></script>
<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/js/common.js?v=1"></script>
<script>
    $(document).ready(function () {
        btn_search_onclick();
        $('#date').datepicker({
            format: 'yyyy-mm-dd'
        });
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var item = $('#item').val(), name = $('#name').val(), date = $('#date').val(), state = $('#state').val();

        yo.dynamicTpl({
            tpl: "loan/contract.list",
            dynamic: {
                api: "loan",
                method: "getContractList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, item: item, name: name, date: date, state: state}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
