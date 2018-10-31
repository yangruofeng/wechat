<style>
    #myModal .modal-dialog {
        margin-top: 100px!important;
    }
</style>
<div class="page">
  <div class="fixed-bar">
      <div class="item-title">
          <h3>Reset Password</h3>
          <ul class="tab-base">
              <li><a class="current"><span>Client List</span></a></li>
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
                                <input type="text" class="form-control" style="width: 250px" id="search_text" name="search_text" placeholder="Search for cid/name/login code/phone">
                            <span class="input-group-btn">
                              <button type="button" class="btn btn-default" id="btn_search_list"
                                      onclick="btn_search_onclick();">
                                  <i class="fa fa-search"></i>
                                  <?php echo 'Search'; ?>
                              </button>
                            </span>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="business-content">
            <div class="business-list"></div>
        </div>
      </div>
</div>

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Reset Password'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="reset_form">
                        <input type="hidden" name="uid" value="">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'New Password'?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="new_password" placeholder="" value="">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Verify Password'?></label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="verify_password" placeholder="" value="">
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
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
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

        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "dev/reset.password.list",
            dynamic: {
                api: "dev",
                method: "getResetPasswordList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function reset_password(uid) {
        if(!uid){
            return;
        }
        $('#myModal input').val('');
        $('#myModal input[name="uid"]').val(uid);
        $('#myModal').modal('show');
    }

    $('.btn-danger').click(function () {
        if (!$("#reset_form").valid()) {
            return;
        }

        var values = $('#reset_form').getValues();
        yo.loadData({
            _c: 'dev',
            _m: 'apiResetPassword',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    $('#myModal').modal('hide');
                    alert(_o.MSG);
                } else {
                    alert(_o.MSG);
                }
            }
        });
    })

    $('#reset_form').validate({
        errorPlacement: function(error, element){
            error.appendTo(element.next());
        },
        rules : {
            new_password : {
                required : true,
                checkPwd : true
            },
            verify_password : {
                required : true,
                verifyPwd : true
            }
        },
        messages : {
            new_password : {
                required : '<?php echo 'Required'?>',
                checkPwd : '<?php echo 'The password must be 6-18 digits or letters!'?>'
            },
            verify_password : {
                required : '<?php echo 'Required'?>',
                verifyPwd : '<?php echo 'Verify password error!'?>'
            }
        }
    });

    jQuery.validator.addMethod("checkPwd", function (value, element) {
        value = $.trim(value);
        if (!/^[A-Za-z0-9]{6,18}$/.test(value)) {
            return false;
        } else {
            return true;
        }
    });

    jQuery.validator.addMethod("verifyPwd", function (value, element) {
        var new_password = $.trim($('input[name="new_password"]').val());
        value = $.trim(value);
        if (new_password == value) {
            return true;
        } else {
            return false;
        }
    });
</script>
