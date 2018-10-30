
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
    </div>

    <div class="form-group button" style="text-align: center">
        <a  class="btn btn-default" style="min-width: 80px;margin-top:20px;margin-bottom: 40px;" href="<?php echo getUrl('mortgage', 'index', array(), false, ENTRY_COUNTER_SITE_URL) ?>"><i class="fa fa-reply"></i><?php echo 'Back' ?></a>
    </div>


    <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 700px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Receive Mortgage</h4>
                </div>
                <div class="modal-body" style="margin-bottom: 20px">
                    <div class="modal-form clearfix">
                        <form class="form-horizontal" id="my_form" method="post" action="<?php echo getUrl('mortgage', 'receiveMortgage', array(), false, ENTRY_COUNTER_SITE_URL) ?>">
                            <div class="col-sm-12" style="margin-top: 15px">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Client Code</label>
                                <div class="col-sm-8">
                                    <input type="hidden" id ='member_id' name="member_id" value="">
                                    <input type="text"  class="form-control" id="member_code" value="" style="width: 400px">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="col-sm-12" style="margin-top: 15px">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Contract No</label>
                                <div class="col-sm-8 input-group" style="padding-left:15px;width:415px">
                                    <input type="text" id="contract_id" class="form-control" name="contract_id" value="">
                                    <span class="input-group-btn">
                                    <button type="button" class="btn btn-default" onclick="searchAssetAndTeller()" style="height: 30px;line-height: 14px;border-radius: 0">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </button>
                                    </span>

                                </div>
                                <div class="error_msg" style="margin-left: 180px"></div>
                            </div>
                            <div class="asset-list">

                            </div>

                            <div class="col-sm-12" style="margin-top: 15px">
                                <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Remark</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id='remark' name="remark" value="" style="width: 400px">
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                    <button type="button" class="btn btn-danger" onclick="receive_modal_submit();"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>

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
            tpl: "mortgage/member.info.list",
            control:'counter_base',
            dynamic: {
                api: "mortgage",
                method: "getMemberInfoList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, search_text: _search_text}
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function showModal(member_id,member_code) {
        $('#myModal #contract_id').val('');
        $('#myModal .asset-list').html('');
        $('#myModal #remark').val('');
        $('#myModal #member_id').val(member_id);
        $('#myModal #member_code').val(member_code);
        $('#myModal').modal('show');
    }

    $('#contract_id').bind('keydown',function(event){
        if(event.keyCode == "13") {
            searchAsset();
        }
    });

    function receive_modal_submit() {
        if (!$("#my_form").valid()) {
            return
        }
        $('#my_form').submit()
    }


    $('#my_form').validate({
        errorPlacement: function(error, element){
            element.closest('.col-sm-12').find(".error_msg").html(error)

        },
        rules: {
            contract_id : {
                required: true
            }
        },
        messages: {
            contract_id : {
                required: '<?php echo 'Required'?>'
            }
        }
    });

    function searchAssetAndTeller() {
        var contract_id = $('#contract_id').val();
        var member_id = $('#member_id').val()
        yo.dynamicTpl({
            tpl: "mortgage/asset.teller.list",
            control:'counter_base',
            dynamic: {
                api: "mortgage",
                method: "getContractAssetAndTeller",
                param: {
                    contract_id: contract_id,
                    member_id:member_id
                    }
            },
            callback: function (_tpl) {
                $(".asset-list").html(_tpl);
                var teller_name = $('.teller_name').val();
                var teller_id = $('.teller_id').val();
                $('#cashier_name').val(teller_name)
                $('#cashier_id').val(teller_id)

            }
        });
    }
</script>