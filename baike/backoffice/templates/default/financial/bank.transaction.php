<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 500;
    }

    em {
        font-weight: 500;
        font-size: 15px;
    }

    label {
        margin-bottom: 0px;
    }

    .basic-info {
        width: 100%;
        border: 1px solid #d5d5d5;
        margin-bottom: 20px;
    }

    .ibox-title {
        min-height: 34px!important;
        color: #d6ae40;
        background-color: #F6F6F6;
        padding: 10px 10px 0px;
        border-bottom: 1px solid #d5d5d5;
        font-weight: 100;
    }

    .content {
        padding: 0px!important;
    }

    .nav-tabs {
        height: 34px!important;
    }

    .nav-tabs li a {
        padding: 7px 12px !important;
    }

    .tab-content label {
        margin-bottom: 0px!important;
    }

    .form-horizontal .control-label {
        text-align: left;
    }

</style>
<div class="page" style="max-width: 1200px">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['type'] == 'hq') { ?>
                <h3>HQ Bank</h3>
                <ul class="tab-base">
                    <li>
                        <a href="<?php echo getUrl('financial', 'hqBank', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                    </li>
                    <li><a class="current"><span>Transaction</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>Branch Bank</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('financial', 'branchBank', array('group' => 'bank'), false, BACK_OFFICE_SITE_URL)?>"><span>Group By Bank</span></a></li>
                    <li><a href="<?php echo getUrl('financial', 'branchBank', array('group' => 'branch'), false, BACK_OFFICE_SITE_URL)?>"><span>Group By Branch</span></a></li>
                    <li><a class="current"><span>Transaction</span></a></li>
                </ul>
            <?php } ?>
        </div>
    </div>
    <div class="container">
        <div class="clearfix">
            <div class="col-sm-12">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Bank Info</h5>
                    </div>
                    <div class="content">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Bank Name</label></td>
                                <td><label class="control-label">Account No</label></td>
                                <td><label class="control-label">Account Name</label></td>
                                <td><label class="control-label">Account Phone</label></td>
                                <td><label class="control-label">Currency</label></td>
                                <td><label class="control-label">Balance</label></td>
                                <td><label class="control-label">State</label></td>
                            </tr>
                            <?php $bank_info = $output['bank_info']; ?>
                            <tr>
                                <td><?php echo $bank_info['bank_name']; ?></td>
                                <td><?php echo $bank_info['bank_account_no']; ?></td>
                                <td><?php echo $bank_info['bank_account_name']; ?></td>
                                <td><?php echo $bank_info['bank_account_phone']; ?></td>
                                <td><?php echo $bank_info['currency']; ?></td>
                                <td><?php echo $bank_info['balance']; ?></td>
                                <td><?php echo $bank_info['account_state'] == 1 ? 'Valid' : 'Invalid'; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#deposit" aria-controls="deposit" role="tab" data-toggle="tab" style="border-left: 0">Deposit</a>
                </li>
                <li role="presentation">
                    <a href="#withdrawal" aria-controls="withdrawal" role="tab" data-toggle="tab">Withdrawal</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="deposit">
                    <form class="form-horizontal" method="post" action="<?php echo getUrl('financial', 'deposit', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <input type="hidden" name="bank_id" value="<?php echo $bank_info['uid']?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Currency</label></td>
                                <td><?php echo $bank_info['currency']; ?></td>
                            </tr>

                            <tr>
                                <td><label class="control-label">Amount</label></td>
                                <td>
                                    <input type="number" class="form-control input-h30" name="amount" value="">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Remark</label></td>
                                <td>
                                    <textarea class="form-control" name="remark" style="width: 100%;height: 70px"></textarea>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <button type="button" class="btn btn-info" style="width: 50%"><i class="fa fa-check"></i><?php echo 'Commit' ?></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane" id="withdrawal">
                    <form class="form-horizontal" method="post" action="<?php echo getUrl('financial', 'withdrawal', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <input type="hidden" name="bank_id" value="<?php echo $bank_info['uid']?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Currency</label></td>
                                <td><?php echo $bank_info['currency']; ?></td>
                            </tr>

                            <tr>
                                <td><label class="control-label">Amount</label></td>
                                <td>
                                    <input type="number" class="form-control input-h30" name="amount" value="">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Remark</label></td>
                                <td>
                                    <textarea class="form-control" name="remark" style="width: 100%;height: 70px"></textarea>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <button type="button" class="btn btn-info" style="width: 50%"><i class="fa fa-check"></i><?php echo 'Commit' ?></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-id-card-o"></i>Transaction List</h5>
                </div>
                <div class="content transaction-list">

                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>

    $(function () {
        btn_search_onclick();
    })

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);
        var uid = '<?php echo $bank_info['uid'];?>';

        yo.dynamicTpl({
            tpl: "financial/bank.transaction.list",
            dynamic: {
                api: "financial",
                method: "getTransactionList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, uid: uid}
            },
            callback: function (_tpl) {
                $(".transaction-list").html(_tpl);
            }
        });
    }


    $('#deposit .btn-info').click(function () {
        if (!$("#deposit .form-horizontal").valid()) {
            return;
        }

        $('#deposit .form-horizontal').submit();
    })

    $('#withdrawal .btn-info').click(function () {
        if (!$("#withdrawal .form-horizontal").valid()) {
            return;
        }

        $('#withdrawal .form-horizontal').submit();
    })

    $('#deposit .form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            amount: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            amount: {
                required: '<?php echo 'Required!'?>'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

    $('#withdrawal .form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            amount: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            amount: {
                required: '<?php echo 'Required!'?>'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>