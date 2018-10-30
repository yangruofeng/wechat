
<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>

    .square{
        border-radius: 0px !important;
    }

    .td2{
        padding-left: 5px;
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
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" style="height: 34px" name="search_text" placeholder="Search for client code">
                                <span class="input-group-btn">
                                     <button type="button" class="btn btn-default square" id="btn_search_list" onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      Search
                                    </button>
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
        <div class="form-group button" style="text-align: center">
            <a class="btn btn-default" style="min-width: 80px;margin-top:20px;margin-bottom: 40px;" href="<?php echo getUrl('mortgage', 'index', array(), false, ENTRY_COUNTER_SITE_URL) ?>"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
        </div>
    </div>
</div>



<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 700px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Takeout Mortgage</h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="my_form">
                        <input type="hidden" id ='asset_id' name="asset_id" value="">
                        <input type="hidden" id ='member_id' name="member_id" value="">
                        <div class="col-sm-12" style="margin-top: 15px">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Cashier</label>
                            <div class="col-sm-8">
                                <select class="form-control" name='cashier_id'>
                                    <?php foreach ($output['cashier'] as $cashier) { ?>
                                        <option value="<?php echo $cashier['uid'] ?>"><?php echo $cashier['user_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12" style="margin-top: 15px">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Remark</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="remark" value="" style="width: 400px">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="takeout_modal_submit();"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>



<script>

    function showModal(asset_id,member_id) {
        $('#myModal #asset_id').val(asset_id);
        $('#myModal #member_id').val(member_id);
        $('#myModal').modal('show');
    }


    function takeout_modal_submit(){
        var values = $("#my_form").getValues();
        yo.loadData({
            _c: 'mortgage',
            _m: 'takeoutMortgage',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });

    }

    $(document).ready(function () {
        btn_search_onclick();
    });

    //  分页展示贷款申请列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _search_text = $('#search_text').val();

        yo.dynamicTpl({
            tpl: "mortgage/mortgage.takeout.list",
            control:'counter_base',
            dynamic: {
                api: "mortgage",
                method: "getMortgageToTakeoutList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }


</script>