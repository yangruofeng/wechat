<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/report.css?v=5" rel="stylesheet" type="text/css"/>
<style>
    .search_date {
        width: 100px !important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>SMS</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control  input-search" id="search_text" name="search_text" placeholder="Search for phone">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default  btn-search" id="btn_search_list" onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      <?php echo 'Search'; ?>
                                  </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
<!--                        <td>-->
<!--                            <div class="form-group">-->
<!--                                <input type="checkbox" name="need_resend" id="need_resend"> Need to resend-->
<!--                            </div>-->
<!--                        </td>-->
                        <td>
                            <a style="margin-left: 20px;min-width: 80px" class="btn btn-default batch-resend">
                                Batch Resend
                            </a>
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
        $('#need_resend').click(function () {
            btn_search_onclick();
        })

        $('.business-list').delegate('#select_all', 'click', function () {
            if ($(this).is(':checked')) {
                $('input[name="uid"]').prop('checked', true);
            } else {
                $('input[name="uid"]').prop('checked', false);
            }
        })

        $('.business-list').delegate('input[name="uid"]', 'click', function () {
            if ($('.business-list input[name="uid"]').length == $('.business-list input[name="uid"]:checked').length) {
                $('#select_all').prop('checked', true);
            } else {
                $('#select_all').prop('checked', false);
            }
        })

        $('.batch-resend').click(function () {
            if ($('.business-list input[name="uid"]:checked').length == 0) {
                return;
            }

            $('.business-list input[name="uid"]:checked').each(function (i) {
                var _uid = $(this).val();
                sms_resend(_uid);
            })
        })
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

        yo.dynamicTpl({
            tpl: "operator/sms.list",
            dynamic: {
                api: "operator",
                method: "getSmsList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function sms_resend(uid) {
        if (uid <= 0) {
            return;
        }
        var _tr = $('tr[uid="' + uid + '"]');
        _tr.find('.resend').hide();
        _tr.find('.resending').show();
        yo.loadData({
            _c: "dev",
            _m: "resendSms",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    var _data = _o.DATA;
                    _tr.find('.content').html(_data.content);
                    _tr.find('.task_state').html(_data.state);
                    _tr.find('.resending').hide();
                    _tr.find('.resend_success').show();
                } else {
                    var _data = _o.DATA;
                    _tr.find('.task_state').html('<span style="color: red">' + _data.state + '</span>');
                    _tr.find('.resend').show();
                    _tr.find('.resending').hide();
                }
            }
        });
    }
</script>
