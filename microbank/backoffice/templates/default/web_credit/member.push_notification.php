<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/report.css?v=5" rel="stylesheet" type="text/css"/>
<style>
    .search_date {
        width: 100px !important;
    }

    .btn {
        border-radius: 0;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Message</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL ); ?>"><span>My Client</span></a></li>
                <li><a class="current"><span>Push</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" name="search_text" placeholder="Search for title">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default" id="btn_search_list" onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      <?php echo 'Search'; ?>
                                  </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" onclick="btn_push();">
                                <i class="fa fa-search"></i>
                                <?php echo 'Push'; ?>
                            </button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="business-content">
            <div class="member-info" style="height: 40px;line-height: 40px;font-size: 16px">
                <span>Member Name: </span><span style="font-weight: 600;margin-left: 10px"><?php echo $output['member_info']['display_name'] ?: $output['member_info']['login_code']; ?></span>
            </div>
            <div class="business-list">

            </div>
        </div>
    </div>
</div>

<div class="modal" id="pushModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Push Notification'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="push_form">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Message Title'?></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="message_title" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Message Content'?></label>
                            <div class="col-sm-9">
                                <textarea name="message_body" class="form-control" id="" cols="30" rows="10"></textarea>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="save_info()"><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>
    var _member_id = '<?php echo $output['member_info']['uid'];?>'
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_push() {
        $('#pushModal [name="message_title"]').val('');
        $('#pushModal [name="message_body"]').val('');
        $('#pushModal').modal('show');
    }

    function save_info() {
        if (!$("#push_form").valid()) {
            return;
        }
        var _values = $("#push_form").getValues();
        _values.member_id = _member_id;
        yo.loadData({
            _c: 'web_credit',
            _m: 'addPushNotification',
            param: _values,
            callback: function (_o) {
                alert(_o.MSG,1,function(){
                    if (_o.STS) {
                        $('#pushModal').modal('hide');
                        btn_search_onclick();
                    }
                });
            }
        })
    }

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
        _values.member_id = _member_id;

        yo.dynamicTpl({
            tpl: "web_credit/member.push_notification.list",
            dynamic: {
                api: "web_credit",
                method: "getPushNotificationList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    $('#pushModal').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            message_title : {
                required : true
            },
            message_body : {
                required : true
            }
        },
        messages : {
            message_title : {
                required : '<?php echo 'Required'?>'
            },
            message_body : {
                required : '<?php echo 'Required'?>'
            }
        }
    });
</script>
