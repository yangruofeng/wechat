<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<style>
    #myModal .modal-dialog {
        margin-top: 100px!important;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>User</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'bindCard', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
<!--                <li><a href="--><?php //echo getUrl('user', 'addUser', array(), false, BACK_OFFICE_SITE_URL) ?><!--"><span>Add</span></a></li>-->
                <li><a class="current"><span>IC Cards</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <table  class="search-table">
                <tr>
                    <td>
                        <?php echo 'Current Selected User'?>: <?php echo $output['user_info']['user_code']?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-default" onclick="bind_card()" style="min-width: 70px">
                            <i class="fa fa-plus"></i>
                            <?php echo 'Add'; ?>
                        </button>
                    </td>
                </tr>
            </table>
        </div>

        <div class="business-content">
            <div class="business-list">

            </div>
        </div>
    </div>
</div>

<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Add User IC Card'?></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="bind_card_form">
                        <input type="hidden" name="user_uid" value="<?php echo $output['user_info']['uid']?>">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="required-options-xing">*</span><?php echo 'Card No'?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="card_no" placeholder="" value="">
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
    $(document).ready(function () {
        load_list();
    });

    function load_list() {
        yo.dynamicTpl({
            tpl: "user/user.cards.list",
            dynamic: {
                api: "user",
                method: "getUserCardList",
                param: {uid: '<?php echo $output['user_info']['uid']?>'}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function unbind_card(uid) {
        if(!uid){
            return;
        }

        $.messager.confirm("Confirm", "<?php echo 'Are you sure you want to unbind this card?' ?>", function(r){
            if (r) {
                yo.loadData({
                    _c: 'user',
                    _m: 'deleteUserCard',
                    param: {uid: uid},
                    callback: function (_o) {
                        if (_o.STS) {
                            load_list();
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });
            }
        });
    }

    function bind_card() {
        if (window.external) {
            var initialInfo = "<?php echo $output['user_info']['uid']?>-<?php echo $output['operator_info']['uid']?>-" + (+new Date()).toString();
            var cardNo = window.external.initializeCard(initialInfo, "");
            if (cardNo) {
                yo.loadData({
                    _c: 'user',
                    _m: 'addUserCard',
                    param: {
                        "user_uid": "<?php echo $output['user_info']['uid']?>",
                        "card_no": cardNo,
                        "initial_info": initialInfo
                    },
                    callback: function (_o) {
                        if (_o.STS) {
                            $('#myModal').modal('hide');
                            load_list();
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });
            }
        } else {
            $('#myModal').modal('show');
        }
    }

    $('.btn-danger').click(function () {
        if (!$("#bind_card_form").valid()) {
            return;
        }

        var values = $('#bind_card_form').getValues();
        yo.loadData({
            _c: 'user',
            _m: 'addUserCard',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    $('#myModal').modal('hide');
                    load_list();
                } else {
                    alert(_o.MSG);
                }
            }
        });
    });
</script>