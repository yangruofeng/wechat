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
        <div class="business-condition">
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
                            <input type="text" class="form-control" name="username" id="username">
                          </div>
                        </td>
                        <td>
                          <div class="form-group">
                            <label for="exampleInputName2">State</label>
                            <select class="form-control" id="state">
                              <option value="-1">All</option>
                              <option value="<?php echo insuranceContractStateEnum::CREATE;?>">Create</option>
                              <option value="<?php echo insuranceContractStateEnum::PENDING_APPROVAL;?>">Pending Approval</option>
                              <option value="<?php echo insuranceContractStateEnum::PENDING_RECEIPT;?>">Pending Receipt</option>
                              <option value="<?php echo insuranceContractStateEnum::PROCESSING;?>">Processing</option>
                              <option value="<?php echo insuranceContractStateEnum::PAUSE;?>">Pause</option>
                              <option value="<?php echo insuranceContractStateEnum::COMPLETE;?>">Complete</option>
                              <option value="<?php echo insuranceContractStateEnum::WRITE_OFF;?>">Write Off</option>
                            </select>
                          </div>
                        </td>
                        <td>
                          <div class="input-group">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search';?>
                              </button>
                            </span>
                          </div><!-- /input-group -->
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

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var item = $('#item').val(), member_name =  $('#username').val(), state =  $('#state').val();

        yo.dynamicTpl({
            tpl: "insurance/contract.list",
            dynamic: {
                api: "insurance",
                method: "getContractList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, item: item, member_name: member_name, state: state}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
