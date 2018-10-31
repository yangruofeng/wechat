<style>
    em {
        font-weight: 500;
        font-size: 15px;
    }

    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .input-h30 {
        height: 30px !important;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px !important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .ibox-title i {
        margin-right: 5px;
    }

    .content {
        width: 100%;
        min-height: 40px;
        background-color: #FFF;
        overflow: hidden;
    }

    .content .table td {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Request Written Off</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Request</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>

                    <tr>
                        <td>
                            <div class="input-group">

                               <!-- <select name="key_type" id="" class="form-control input-h30" style="width: 120px;">
                                    <option value="">Contract SN</option>
                                </select>-->

                                <input id="input_key_type" type="hidden" name="key_type" value="1">

                                <input type="text" class="form-control input-h30" id="search_text" name="search_text" style="width: 200px" onkeydown="if(event.keyCode==13){return false;}" value="<?php echo $output['sn']?>" placeholder="Contract No.">
                                <span class="input-group-btn">
                                    <a class="btn btn-success" id="btn_search_list" onclick="search_contract();">
                                        <i class="fa fa-search"></i>
                                        Search
                                    </a>
                                </span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>

            <div id="key_type_div" class="btn-group" style="margin: 10px 0">
                <label for="" class="btn btn-default active btn-key-type"  onclick="changeKeyType(this,1)" >Contract No.</label>
                <label for="" class="btn btn-default btn-key-type"  onclick="changeKeyType(this,4)">Client CID</label>
                <label for="" class="btn btn-default btn-key-type"  onclick="changeKeyType(this,2)">Client Phone</label>
                <label for="" class="btn btn-default btn-key-type"  onclick="changeKeyType(this,3)">Client Name</label>
            </div>
        </div>
        <div class="contract-info col-sm-12" style="padding-left: 0;">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Contract Information</h5>
                </div>
                <div class="content">
                </div>
            </div>
        </div>
        <div class="request-list col-sm-12" style="padding-left: 0;">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Request List</h5>
                </div>
                <div class="content">

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        btn_search_onclick();

        $('#frm_search_condition').keyup(function (event) {
            if (event.keyCode == 13) {
                search_contract();
            }
        });
    });


    function changeKeyType(ele,type)
    {
        var _ele = $(ele);
        $('#key_type_div .btn-key-type').removeClass('active');
        _ele.addClass('active');
        $('#input_key_type').val(type);
        $('#search_text').attr('placeholder',_ele.text());
    }

    function search_contract() {
        var search_text = $.trim($('#search_text').val());
        if (!search_text) {
            return;
        }
        var _key_type = $('#input_key_type').val();
        yo.dynamicTpl({
            tpl: "branch_manager/contract.apply.off.list",
            dynamic: {
                api: "branch_manager",
                method: "getContractListByKeySearch",
                param: {search_text: search_text,key_type:_key_type}
            },
            callback: function (_tpl) {
                $(".contract-info .content").html(_tpl);
            }
        });
    }

    //  展示申请列表
    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "branch_manager/written.off.list",
            dynamic: {
                api: "branch_manager",
                method: "getWrittenOffList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize}
            },
            callback: function (_tpl) {
                $(".request-list .content").html(_tpl);
            }
        });
    }

    function submit_written_off() {
        var values = $(".form-horizontal").getValues();
        yo.loadData({
            _c: 'branch_manager',
            _m: 'addWrittenOffRequest',
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