<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>

    .custom-btn-group {
        float: inherit;
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .tr_odd td {
        background-color: #FFF!important;
    }

    .tr_even td {
        background-color: #F3F4F6!important;
    }

</style>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Branch Bank</h3>
            <ul class="tab-base">
                <?php if($output['group'] != 'branch') { ?>
                    <li><a class="current"><span>Group By Bank</span></a></li>
                    <li><a href="<?php echo getUrl('financial', 'branchBank', array('group' => 'branch'), false, BACK_OFFICE_SITE_URL)?>"><span>Group By Branch</span></a></li>
                <?php } else { ?>
                    <li><a href="<?php echo getUrl('financial', 'branchBank', array('group' => 'bank'), false, BACK_OFFICE_SITE_URL)?>"><span>Group By Bank</span></a></li>
                    <li><a class="current"><span>Group By Branch</span></a></li>
                <?php } ?>
                <li><a href="<?php echo getUrl('financial', 'addBank', array('type' => 'branch'), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
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
                                <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for...">
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
                            <select class="form-control" name="branch_id" id="branch_id" onchange="btn_search_onclick();">
                                <option value="0">Select Branch</option>
                                <?php foreach ($output['branch_list'] as $branch) { ?>
                                    <option value="<?php echo $branch['uid']?>"><?php echo $branch['branch_name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="bank_code" id="bank_code" onchange="btn_search_onclick();">
                                <option value="0">Select Bank</option>
                                <?php foreach ($output['bank_list'] as $bank) { ?>
                                    <option value="<?php echo $bank['bank_code']?>"><?php echo $bank['bank_name']; ?></option>
                                <?php } ?>
                            </select>
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
    var group_by = '<?php echo $output['group']?>';
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

        var _search_text = $('#search_text').val();
        var _branch_id = $('#branch_id').val();
        var _bank_code = $('#bank_code').val();

        yo.dynamicTpl({
            tpl: "financial/bank.list",
            dynamic: {
                api: "financial",
                method: "getBankList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    search_text: _search_text,
                    branch_id: _branch_id,
                    bank_code: _bank_code,
                    group: group_by
                }
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
