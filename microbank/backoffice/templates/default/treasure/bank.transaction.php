
<style>

    .square{
        border-radius: 0px !important;
    }

    .td2{
        padding-left: 5px;
    }

    .btn-default {
        padding: 5px 12px;
    }

</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Bank-Transaction</h3>
            <ul class="tab-base">
                <?php if($output['bank']['branch_id']>0){?>
                    <li><a href="<?php echo getUrl("treasure","branchList",array(),false,BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                    <li><a href="<?php echo getUrl("treasure","branchIndex",array('branch_id'=>$output['bank']['branch_id']),false,BACK_OFFICE_SITE_URL)?>"><span>Branch</span></a></li>
                <?php }else{?>
                    <li><a href="<?php echo getUrl("financial","hqBank",array(),false,BACK_OFFICE_SITE_URL)?>"><span>Public-Bank</span></a></li>
                <?php }?>
                <li><a class="current"><span>Transaction - <?php echo $output['bank']['bank_name']?></span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <input type="hidden" name='bank_id' id='bank_id' value="<?php echo $output['bank_id']?>">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <?php include(template("widget/inc_condition_datetime")); ?>
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

<div class="form-group button">
    <button type="button" class="btn btn-default" style="min-width: 80px;margin-left: 760px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
</div>

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

        var bank_id = $('#bank_id').val();
        var _values = $('#frm_search_condition').getValues();
        _values.bank_id = bank_id;
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "treasure/bank.transaction.list",
            dynamic: {
                api: "treasure",
                method: "getBankTransactionList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>