<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
   .btn {
        padding: 5px 12px;
    }

   .verify-state .btn {
       margin-left: -1px;
   }

   .verify-state .btn.active {
       color: #fff;
       background-color: #5cb85c;
       border-color: #4cae4c;
   }

   .verify-table .lab-name {
       width: 130px;
       text-align: right;
       margin-right: 8px;
   }

   .verify-table .cert-info {
       line-height: 10px;
       padding-top: 13px;
   }

   .verify-table .cert-type h3 {
       font-size: 20px;
       font-weight: 100;
       color: #000;
   }

   .verify-table .cert-type p {
       margin: 0;
   }

   .verify-table .cert-type label {
       margin-bottom: 0;
   }

   .verify-table .cert-type .lab-name {
       width: auto;
   }

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
</style>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                        <tr>
                            <td>
                                <?php include(template("widget/inc_condition_datetime")); ?>
                            </td>
                            <td style="padding-left: 10px">
                                <div class="input-group">
                                    <span class="input-group-btn verify-state">
                                       <button type="button" class="btn btn-default active" value="pending">Pending Receive</button>
                                       <button type="button" class="btn btn-default" value="received">Received</button>
                                       <button type="button" class="btn btn-default" value="rejected">Rejected</button>
                                    </span>
                                </div>
                            </td>
                         </tr>
                    </tbody>
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
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.state = $('.verify-state .btn.active').attr('value');
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "cash_on_hand/pending.receive.list",
            control:'counter_base',
            dynamic: {
                api: "cash_on_hand",
                method: "getReceiveList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
    
    function receive(id) {

        yo.loadData({
            _c: 'cash_on_hand',
            _m: 'receiveTransfer',
            param: {biz_id:id},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('cash_on_hand', 'pendingReceive', array(), false, ENTRY_COUNTER_SITE_URL)?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
    
    function reject(id) {
        yo.loadData({
            _c: 'cash_on_hand',
            _m: 'rejectTransfer',
            param: {biz_id:id},
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('cash_on_hand', 'pendingReceive', array(), false, ENTRY_COUNTER_SITE_URL)?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>