<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css">
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .modal-dialog {
        margin-top: 5px !important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Department Point</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Period List</span></a></li>
<!--                <li><a href="--><?php //echo getUrl('user', 'addPointPeriod', array(), false, BACK_OFFICE_SITE_URL)?><!--"><span>Add</span></a></li>-->
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
                            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for period">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search';?>
                              </button>
                            </span>
                          </div>
                        </td>
                        <td>
                            <select class="form-control" name="depart_id" <?php echo $output['depart'] ? "disabled" : ""?>>
                                <?php if ($output['depart']) { ?>
                                    <option value="<?php echo $output['depart']['uid']?>"><?php echo $output['depart']['branch_name'] . ' -- '. $output['depart']['depart_name']?></option>
                                <?php } else { ?>
                                    <option value="0">Select Department</option>
                                    <?php foreach ($output['depart_list'] as $depart) { ?>
                                        <option class="option" value="<?php echo $depart['uid'] ?>"><?php echo $depart['branch_name'] . ' -- '. $depart['depart_name'] ?></option>
                                    <?php } ?>
                                <?php }?>
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
    $(function () {
        if ('<?php echo $output['depart']?>') {
            btn_search_onclick();
        }

        $('select[name="depart_id"]').change(function () {
            var depart_id = $(this).val();
            if(depart_id > 0){
                btn_search_onclick();
            }
        })
    })

    function active_click(uid) {
        if (!(uid > 0)){
            return;
        }

        yo.loadData({
            _c: 'user',
            _m: 'activeDepartPeriod',
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    btn_search_onclick();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var values = $('#frm_search_condition').getValues();
        var depart_id = $('#frm_search_condition select[name="depart_id"]').val();
        values.depart_id = depart_id;
        if (!(values.depart_id > 0)) {
            return;
        }

        values.pageNumber = _pageNumber;
        values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "user/point.depart.period.list",
            dynamic: {
                api: "user",
                method: "getDepartPeriodList",
                param: values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
