<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
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

    .vault-balance {
        font-size: 18px;
        font-weight: 600;
    }

</style>
<div class="page" style="max-width: 1200px">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>HQ Vault</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Handle</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="clearfix">
            <div class="col-sm-12">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5><i class="fa fa-id-card-o"></i>Balance</h5>
                    </div>
                    <div class="content">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <?php $civ_balance = $output['civ_balance'];?>
                                <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                    <td><em><?php echo $currency?></em>
                                        <a class="btn btn-link btn-sm"
                                           href="<?php echo getUrl("common","passbookAccountFlowPage",array('obj_key'=>systemAccountCodeEnum::HQ_CIV,'obj_type'=>objGuidTypeEnum::GL_ACCOUNT,'currency'=>$currency),false,BACK_OFFICE_SITE_URL)?>"
                                            >
                                            <span class="vault-balance"><?php echo  ncPriceFormat($civ_balance[$key]); ?></span>
                                        </a>
                                    </td>
                                <?php } ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active" style="min-width: 70px;text-align: center">
                    <a href="#div_from_capital" aria-controls="div_from_capital" role="tab" data-toggle="tab" style="border-left: 0">From Capital</a>
                </li>
                <li role="presentation" style="min-width: 70px;text-align: center">
                    <a href="#div_transfer_to_branch" aria-controls="div_transfer_to_branch" role="tab" data-toggle="tab">Transfer To Branch</a>
                </li>
                <li role="presentation" style="min-width: 70px;text-align: center">
                    <a href="#div_pending_receive" aria-controls="div_pending_receive" role="tab" data-toggle="tab">Receive From Branch</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="div_from_capital">
                    <form class="form-horizontal" method="post" action="<?php echo getUrl('financial', 'addHqVault', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Currency</label></td>
                                <td>
                                    <select name="currency" class="form-control input-h30">
                                        <option value=""><?php echo $lang['common_select']?></option>
                                        <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                            <option value="<?php echo $key?>"><?php echo $currency?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="error_msg"></div>
                                </td>
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
                                <td><label class="control-label">Tip</label></td>
                                <td>
                                    <span>
                                        Receive From Capital
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <button type="button" class="btn btn-info" id="btn_receive_from_capital" style="width: 50%"><i class="fa fa-check"></i><?php echo 'Submit' ?></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane" id="div_transfer_to_branch">
                    <form class="form-horizontal" method="post" action="<?php echo getUrl('financial', 'hqCIVTransferToBranchCIV', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Currency</label></td>
                                <td>
                                    <select name="currency" class="form-control input-h30">
                                        <option value=""><?php echo $lang['common_select']?></option>
                                        <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                            <option value="<?php echo $key?>"><?php echo $currency?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Branch</label></td>
                                <td>
                                    <select name="branch_id" class="form-control input-h30">
                                        <option value=""><?php echo $lang['common_select']?></option>
                                        <?php foreach ($output['branch_list'] as $key => $branch_item) { ?>
                                            <option value="<?php echo $branch_item['uid']?>"><?php echo $branch_item['branch_name']?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="error_msg"></div>
                                </td>
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
                                <td><label class="control-label">Tip</label></td>
                                <td>
                                    <span>
                                        Transfer To Branch(Cash In Vault)
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <button type="button" class="btn btn-info" id="btn_transfer_to_branch" style="width: 50%"><i class="fa fa-check"></i><?php echo 'Submit' ?></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div role="tabpanel" class="tab-pane" id="div_pending_receive">
                    <form class="form-horizontal" method="post" action="<?php echo getUrl('financial', 'hqCIVReceiveFromBranchCIV', array(), false, BACK_OFFICE_SITE_URL)?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label">Currency</label></td>
                                <td>
                                    <select name="currency" class="form-control input-h30">
                                        <option value=""><?php echo $lang['common_select']?></option>
                                        <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                            <option value="<?php echo $key?>"><?php echo $currency?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Branch</label></td>
                                <td>
                                    <select name="branch_id" class="form-control input-h30">
                                        <option value=""><?php echo $lang['common_select']?></option>
                                        <?php foreach ($output['branch_list'] as $key => $branch_item) { ?>
                                            <option value="<?php echo $branch_item['uid']?>"><?php echo $branch_item['branch_name']?></option>
                                        <?php } ?>
                                    </select>
                                    <div class="error_msg"></div>
                                </td>
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
                                <td><label class="control-label">Tip</label></td>
                                <td>
                                    <span>
                                        Receive From Branch(Cash In Vault)
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <button type="button" class="btn btn-info" id="btn_receive_from_branch" style="width: 50%"><i class="fa fa-check"></i><?php echo 'Submit' ?></button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="business-condition" style="padding-left: 10px">
                <form class="form-inline" id="frm_search_condition">
                    <table  class="search-table">
                        <tr>
                            <td>
                               <label class="checkbox-inline"><input type="checkbox" name="type" value="hq_bank" checked>HQ-Bank</label>
                               <label class="checkbox-inline"><input type="checkbox" name="type" value="branch_bank" checked>Branch-Bank</label>
                               <label class="checkbox-inline"><input type="checkbox" name="type" value="hq_capital" checked>HQ-Capital</label>
                               <label class="checkbox-inline"><input type="checkbox" name="type" value="hq_2_branch" checked>Branch CIV</label>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="transaction-list">

            </div>
        </div>
    </div>
</div>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>

    $(function () {
        btn_search_onclick();
        $('#frm_search_condition input[name="type"]').click(function () {
            btn_search_onclick();
        })
    })

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = getFormJson('#frm_search_condition');
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;

        yo.dynamicTpl({
            tpl: "financial/hq.vault.flow",
            dynamic: {
                api: "financial",
                method: "getHqVaultFlow",
                param: _values
            },
            callback: function (_tpl) {
                $(".transaction-list").html(_tpl);
            }
        });
    }


    $('#btn_receive_from_capital').click(function () {
        if (!$("#div_from_capital .form-horizontal").valid()) {
            return;
        }

        $('#div_from_capital .form-horizontal').submit();
    })

    $('#btn_transfer_to_branch').click(function () {
        if (!$("#div_transfer_to_branch .form-horizontal").valid()) {
            return;
        }

        $('#div_transfer_to_branch .form-horizontal').submit();
    });
    $('#btn_receive_from_branch').click(function () {
        if (!$("#div_pending_receive .form-horizontal").valid()) {
            return;
        }

        $('#div_pending_receive .form-horizontal').submit();
    });
    $('#div_from_capital .form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            currency: {
                required: true
            },
            amount: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            currency: {
                required: '<?php echo 'Required!'?>'
            },
            amount: {
                required: '<?php echo 'Required!'?>'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });

    $('#div_transfer_to_branch .form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            branch_id:{
                required: true
            },
            currency: {
                required: true
            },
            amount: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            branch_id: {
                required: '<?php echo 'Required!'?>'
            },
            currency: {
                required: '<?php echo 'Required!'?>'
            },
            amount: {
                required: '<?php echo 'Required!'?>'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
    $('#div_pending_receive .form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            branch_id:{
                required: true
            },
            currency: {
                required: true
            },
            amount: {
                required: true
            },
            remark: {
                required: true
            }
        },
        messages: {
            branch_id: {
                required: '<?php echo 'Required!'?>'
            },
            currency: {
                required: '<?php echo 'Required!'?>'
            },
            amount: {
                required: '<?php echo 'Required!'?>'
            },
            remark: {
                required: '<?php echo 'Required!'?>'
            }
        }
    });
</script>