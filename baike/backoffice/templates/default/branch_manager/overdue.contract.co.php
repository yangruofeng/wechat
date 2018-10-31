<style>
    .title {
        font-weight: 600;
        margin-bottom: 10px;
    }

    #myModal .modal-dialog, #cbcModal .modal-dialog {
        margin-top: 10px !important;
    }

    .warning {
        color: #da0000;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Overdue</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'overdueContract', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="table-form">
            <div class="business-content">
                <div class="col-sm-12 title">
                    <span><?php echo 'Credit Officer: '?></span>
                    <span><?php echo $output['co_info']['user_name'] ?></span>
                    <span style="margin-left: 50px"><?php echo 'Phone: ' ?></span>
                    <span><?php echo $output['co_info']['mobile_phone'] ?></span>
                </div>
                <div class="business-list">

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Credit Officer</h4>
            </div>
            <div class="modal-body" style="margin-bottom: 20px">
                <div class="modal-form clearfix">
                    <form class="form-horizontal" id="co_task_form">
                        <input type="hidden" name="scheme_id" id="scheme_id" value="0">
                        <input type="hidden" name="member_id" id="member_id" value="0">
                        <div class="col-sm-12">
                            <label  class="col-sm-2 control-label"><span class="required-options-xing">*</span>List</label>
                            <div class="col-sm-10">
                                <?php foreach ($output['co_list'] as $co) { ?>
                                    <div class="col-sm-4">
                                        <label class="checkbox-inline">
                                            <input type="radio" name="co_id" value="<?php echo $co['uid']; ?>" <?php echo in_array($co['uid'],$user_co) ? 'checked' : ''?>><?php echo $co['user_name']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="COSubmit();"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script>
    var _co_id = '<?php echo $output['co_info']['uid']?>';
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

        yo.dynamicTpl({
            tpl: "branch_manager/overdue.contract.co.list",
            dynamic: {
                api: "branch_manager",
                method: "getOverdueContractForCo",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    co_id: _co_id
                }
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function allot_to_check(uid, co_id, member_id) {
        $('#scheme_id').val(uid);
        $('#member_id').val(member_id);
        if (!co_id) {
            co_id = _co_id;
        }

        $(":radio[name='co_id'][value='" + co_id + "']").prop("checked", true);
        $('#myModal').modal('show');
    }

    function COSubmit(){
        var values = getFormJson($('#co_task_form'));

        if(!values.co_id){
            alert('Please select credit officer.');
            return;
        }
        yo.loadData({
            _c: 'branch_manager',
            _m: 'editOverdueContractCo',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });
    }
</script>
