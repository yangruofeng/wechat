<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"/>
<style>
    .audit-table tr td:first-child {
        width: 200px;
    }

    .audit-table textarea {
        width: 300px;
        height: 80px;
        float: left;
    }

    .custom-btn-group {
        float: inherit;
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    #branchModal .modal-dialog {
        margin-top: 10px !important;
    }

    #branchModal .easyui-panel {
        border: 1px solid #DDD;
    }
</style>
<?php
$loanApplySourceLang = enum_langClass::getLoanApplySourceLang();
$unit_lang = enum_langClass::getLoanTimeUnitLang();
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan Consult</h3>
            <ul class="tab-base">
                <li>
                    <a href="<?php echo getUrl('operator', 'loanConsult', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a>
                </li>
                <li><a class="current"><span>Handle</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <form class="form-horizontal">
            <table class="table audit-table">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Name</label></td>
                    <td><?php echo $output['apply_info']['applicant_name'] ?></td>
                    <td><label class="control-label">Loan Product</label></td>
                    <td><?php echo $output['apply_info']['product_name']?:'Null'; ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Apply Amount</label></td>
                    <td><?php echo ncPriceFormat($output['apply_info']['apply_amount']).$output['apply_info']['currency']; ?></td>
                    <td><label class="control-label">Loan Time</label></td>
                    <td><?php  echo $output['apply_info']['loan_time'].' '.$unit_lang[$output['apply_info']['loan_time_unit']]; ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Purpose</label></td>
                    <td><?php echo $output['apply_info']['loan_purpose'] ?></td>
                    <td><label class="control-label">Loan Mortgage</label></td>
                    <td><?php echo $output['apply_info']['mortgage'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Contact Phone</label></td>
                    <td><?php echo $output['apply_info']['contact_phone'] ?></td>
                    <td><label class="control-label">Address</label></td>
                    <td><?php echo $output['apply_info']['applicant_address'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Memo</label></td>
                    <td colspan="3">
                        <?php echo $output['apply_info']['memo'] ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Apply Time</label></td>
                    <td><?php echo timeFormat($output['apply_info']['create_time']) ?></td>
                    <td><label class="control-label">Apply Source</label></td>
                    <td><?php echo ucwords(str_replace('_', '', $output['apply_info']['request_source'])); ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Audit Remark</label></td>
                    <td colspan="3">
                        <textarea class="form-control" name="remark" style="width: 400px;height: 100px"></textarea>
                        <div class="error_msg"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center">
                        <button type="button" class="btn btn-primary" onclick="checkSubmit();"><i class="fa fa-check"></i><?php echo 'Approve' ?></button>
                        <button type="button" class="btn btn-warning" onclick="checkAbandon();"><i class="fa fa-close"></i><?php echo 'Reject' ?></button>
                        <button type="button" class="btn btn-default" onclick="javascript:window.location.href = '<?php echo getUrl('operator', 'loanConsult', array(), false, BACK_OFFICE_SITE_URL) ?>';"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="uid" value="<?php echo $output['apply_info']['uid']; ?>">
        </form>
    </div>
</div>

<script type="text/javascript" src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/validform/jquery.validate.min.js?v=2"></script>
<script>

    function checkSubmit(){
        if (!$(".form-horizontal").valid()) {
            return;
        }
        var values = $(".form-horizontal").getValues();
        values.verify_state='<?php echo loanConsultStateEnum::OPERATOR_APPROVED;?>';
        submitHandle(values);
    }

    function checkAbandon() {
        var values = $(".form-horizontal").getValues();
        values.verify_state = '<?php echo loanConsultStateEnum::OPERATOR_REJECT?>';
        $.messager.confirm("Reject", "Are you sure to reject?", function (_r) {
            if (!_r) return;
            submitHandle(values);
        });
    }

    function submitHandle(values){
        yo.loadData({
            _c: 'operator',
            _m: 'submitLoanConsult',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    alert(_o.MSG);
                    window.location.href = '<?php echo getUrl('operator', 'loanConsult', array(), false, BACK_OFFICE_SITE_URL) ?>';
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    $('.form-horizontal').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            remark: {
                required: true
            }
        },
        messages: {
            remark: {
                required: '<?php echo 'Required'?>'
            }
        }
    });


</script>
