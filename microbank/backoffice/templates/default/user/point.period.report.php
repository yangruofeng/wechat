<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css">
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    .business-list tr td {
        vertical-align: middle !important;
        background-color: #FFF !important;
    }

    .business-list .table tr.table-header td {
        background: #DDD !important;
    }

    .business-list tr.tr_odd td {
        background-color: #EEE !important;
    }

    .business-list .easyui-panel {
        height: 44px;
    }

    .business-list .easyui-panel table {
        margin-top: 1px;
    }

    .business-list .define-item-title {
        font-weight: 500;
    }

    .business-list .point-list {
        display: none;
    }

    .business-list .fa-plus, .business-list .fa-minus {
        cursor: pointer;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Department Point</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'pointPeriod', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Period List</span></a></li>
                <li><a class="current"><span>Report</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                            <span class="form-control" style="line-height: 22px"><?php echo 'Period:' . ' ' . $output['period']['period'] . ' (' . $output['period']['start_date'] . '--' . $output['period']['end_date'] . ')'?></span>
                            <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for user name">
                            <select class="form-control" name="branch_id" <?php echo $output['depart'] ? "disabled" : ""?> >
                                <?php if ($output['depart']) { ?>
                                    <option value="<?php echo $output['depart']['branch_id']?>"><?php echo $output['depart']['branch_name']?></option>
                                <?php } else { ?>
                                    <option value="0">Select Branch</option>
                                    <?php foreach ($output['depart_list'] as $key => $branch) { ?>
                                        <option value="<?php echo $key?>"><?php echo $branch[0]['branch_name']?></option>
                                    <?php } ?>
                                <?php }?>
                            </select>
                            <select class="form-control" name="depart_id" disabled>
                                <?php if ($output['depart']) { ?>
                                    <option value="<?php echo $output['depart']['uid']?>"><?php echo $output['depart']['depart_name']?></option>
                                <?php } else { ?>
                                    <option value="0">Select Department</option>
                                    <?php foreach ($output['depart_list'] as $key => $branch) { ?>
                                        <?php foreach ($branch as $depart) { ?>
                                            <option class="option" value="<?php echo $depart['uid'] ?>" branch_id="<?php echo $key?>" style="display: none"><?php echo $depart['depart_name'] ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                <?php }?>
                            </select>
                            <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                <i class="fa fa-search"></i>
                                <?php echo 'Search';?>
                            </button>
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
    var uid = '<?php echo $output['uid']?>';
    $(function () {
        $('select[name="branch_id"]').change(function () {
            var branch_id = $(this).val();
            if(branch_id > 0){
                $('select[name="depart_id"]').attr('disabled', false);
                $('select[name="depart_id"] option.option').hide();
                $('select[name="depart_id"] option[branch_id="' + branch_id + '"]').show();
                $('select[name="depart_id"]').val(0);
            } else {
                $('select[name="depart_id"]').attr('disabled', true);
            }
        })

//        $('select[name="depart_id"]').change(function () {
//            var depart_id = $(this).val();
//            if(depart_id > 0){
//                btn_search_onclick();
//            }
//        })
    })

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var values = $('#frm_search_condition').getValues();
        var branch_id = $('#frm_search_condition select[name="branch_id"]').val();
        var depart_id = $('#frm_search_condition select[name="depart_id"]').val();

        if (branch_id > 0 || depart_id > 0 || values.search_text) {
            values.uid = uid;
            values.branch_id = branch_id;
            values.depart_id = depart_id;

            values.pageNumber = _pageNumber;
            values.pageSize = _pageSize;

            yo.dynamicTpl({
                tpl: "user/point.period.report.list",
                dynamic: {
                    api: "user",
                    method: "getPeriodReportList",
                    param: values
                },
                callback: function (_tpl) {
                    $(".business-list").html(_tpl);
                }
            });
        }
    }

    $('.business-list').delegate('.fa-plus', 'click', function () {
        var uid = $(this).attr('uid');
        $('.business-list tr.point-list-' + uid).show();
        $(this).removeClass('fa-plus').addClass('fa-minus');
    })

    $('.business-list').delegate('.fa-minus', 'click', function () {
        var uid = $(this).attr('uid');
        $('.business-list tr.point-list-' + uid).hide();
        $(this).removeClass('fa-minus').addClass('fa-plus');
    })
</script>
