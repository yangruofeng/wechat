<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/raty/demo/css/application.css">
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/raty/lib/jquery.raty.js"></script>
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

    #star {
        width: 200px !important;
        margin-top: 4px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Department Point</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('user', 'departmentPoint', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Period List</span></a>
                </li>
                <li><a class="current"><span>Handle</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div style="height: 40px;margin: 15px 0;border-bottom: 1px solid #CCC">
            <span style="font-size: 18px">
                <span style="font-weight: 600;">Department: </span><?php echo $output['row']['branch_name'] . ' ' . $output['row']['depart_name'] ?>
            </span>
            <span style="font-size: 16px;margin-left: 25px">
                <span style="font-weight: 600;">Period: </span><?php echo $output['row']['start_date'] . ' -- ' . $output['row']['end_date'] ?>
            </span>
        </div>
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <input type="hidden" name="uid" value="<?php echo $output['row']['uid']; ?>">
                <table class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for user name">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" onclick="system_calculate(<?php echo $output['row']['uid'] ?>)">System Calculate
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" onclick="period_close(<?php echo $output['row']['uid'] ?>)" style="min-width: 80px">
                                Close
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

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 500px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Evaluation'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="evaluation_form">
                        <input type="hidden" id="user_id" name="user_id" value="">
                        <input type="hidden" id="event_id" name="event_id" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Point'?></label>
                            <div class="col-sm-8">
                                <div id="star"></div>
<!--                                <input id="input-id" type="number" class="rating" min=0 max=5 step=0.5 data-size="lg" >-->
<!--                                <input type="number" class="form-control" id="point" name="point" placeholder="" value="">-->
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>

<script>
    var uid = '<?php echo $output['row']['uid']?>';
    $(function () {
        btn_search_onclick();
    })

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var values = $('#frm_search_condition').getValues();
        values.pageNumber = _pageNumber;
        values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "user/point.depart.period.user",
            dynamic: {
                api: "user",
                method: "getDepartUserList",
                param: values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
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

    function period_close(uid) {
        yo.loadData({
            _c: 'user',
            _m: 'closeDepartPeriod',
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.href = '<?php echo getUrl('user', 'departmentPoint', array(), false, BACK_OFFICE_SITE_URL) ?>';
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function system_calculate(uid) {
        yo.loadData({
            _c: 'user',
            _m: 'calculateSystemPoint',
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

    function evaluation(_e) {
        var user_id = $(_e).attr('user_id');
        var event_id = $(_e).attr('event_id');
        var rate_score = $(_e).attr('rate_score');

        $('#myModal #user_id').val(user_id);
        $('#myModal #event_id').val(event_id);
        $('#star').raty({half: true, score: rate_score});
        $('#myModal').modal('show');
    }

    $('.btn-danger').click(function () {
        var values = $('#evaluation_form').getValues();
        values.depart_period = uid;
        yo.loadData({
            _c: 'user',
            _m: 'evaluateUserPoint',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    $('#myModal').modal('hide');
                    var data = _o.DATA;
                    var user_id = values.user_id;
                    var event_id = values.event_id;
                    $('.business-list tr.point-list-' + user_id + ' .function a').attr('rate_score', data.score);
                    $('.business-list tr[user_id="' + user_id + '"] .point-total').html(data.point_total);
                    $('.business-list td.point-' + user_id + '-' + event_id + ' .point').html(data.point);
                    $('.business-list td.point-' + user_id + '-' + event_id + ' .score').html(' (' + data.score + '/5)');
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })
</script>
