<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Fast Grant Credit</h3>
          <ul class="tab-base">
              <li><a class="current"><span>Client List</span></a></li>
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
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text" style="min-width: 200px" placeholder="Search for cid/name/phone">
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
                            <select class="form-control" name="work_type" id="work_type" onchange="btn_search_onclick();">
                                <option value="">Select Work Type</option>
                                <?php foreach ($output['work_type'] as $key => $type) { ?>
                                    <option value="<?php echo $key?>"><?php echo $type; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="grade_id" id="grade_id" onchange="btn_search_onclick();">
                                <option value="">Select Grade</option>
                                <?php foreach ($output['grade_list'] as $grade) { ?>
                                    <option value="<?php echo $grade['uid']?>"><?php echo $grade['grade_code']; ?></option>
                                <?php } ?>
                            </select>
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
        $('#ck').click(function () {
            btn_search_onclick();
        })
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var values = $('#frm_search_condition').getValues();
        values.pageNumber = _pageNumber;
        values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "loan_committee/fast.client.list",
            dynamic: {
                api: "loan_committee",
                method: "getClientList",
                param: values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
