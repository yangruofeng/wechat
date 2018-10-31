
<link href="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/datepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL; ?>/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<style>
    .money-style {
        font-size: 18px;
        font-weight: 600;
        color: #EA544A;
    }
</style>
<?php $contract_info = $data['contract_info']; ?>
<?php $client_info = $contract_info['member_info']; ?>
<?php $left_payable_info = $data['left_payable_info']; ?>
<?php $billpay_info = $data['billpay_info']; ?>
<?php if($contract_info){?>
    <div>
        <form class="form-horizontal" id="billpay_form">
            <table class="table contract-table">
                <input type="hidden" id="bill_code" name="bill_code" value="<?php echo $billpay_info['bill_code']?>">
                <tbody class="table-body">
                <tr>
                    <td><label class="control-label">Client-ID</label></td>
                    <td><?php echo $client_info['uid'] ?></td>
                    <td><label class="control-label">Client-Name</label></td>
                    <td><?php echo $client_info['display_name'] ? : $client_info['login_code']?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Contract Sn</label></td>
                    <td><?php echo $contract_info['contract_info']['contract_sn'] ?></td>
                    <td><label class="control-label">Product-Name</label></td>
                    <td><?php echo $contract_info['contract_info']['sub_product_name']?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Loan Date</label></td>
                    <td><?php echo dateFormat($contract_info['contract_info']['start_date']) ?></td>
                    <td><label class="control-label">Terms</label></td>
                    <td><?php echo $contract_info['contract_info']['loan_period_value'] . " " . ucwords($contract_info['contract_info']['loan_period_unit']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">State</label></td>
                    <td><?php echo $lang['loan_contract_state_' . $contract_info['contract_info']['state']]?></td>
                    <td><label class="control-label">Currency</label></td>
                    <td><?php echo $billpay_info['currency'] ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Payable Principal</label></td>
                    <td class="money-style"><?php echo ncPriceFormat($left_payable_info['total_payable_principal']) ?></td>
                    <td><label class="control-label">Payable Amount</label></td>
                    <td class="money-style"><?php echo ncPriceFormat($left_payable_info['total_payable_amount']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Next Repayment</label></td>
                    <td class="money-style"><?php echo ncPriceFormat($left_payable_info['next_repayment_amount']) ?></td>
                    <td><label class="control-label">Next Repayment</label></td>
                    <td><?php echo dateFormat($left_payable_info['next_repayment_date']) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Bank Name</label></td>
                    <td><?php echo $billpay_info['bank_name'] ?></td>
                    <td><label class="control-label">Account No</label></td>
                    <td><?php echo $billpay_info['bank_account_no'] ?></td>
                </tr>
                <?php if ($contract_info['contract_info']['state'] >= loanContractStateEnum::PENDING_DISBURSE && $contract_info['contract_info']['state'] < loanContractStateEnum::COMPLETE) { ?>
                <tr>
                    <td><label class="control-label">BillPay Amount</label></td>
                    <td>
                        <div class="input-group">
                            <input type="number" class="form-control" name="amount">
                            <span class="input-group-addon" style="width: 50px"><?php echo $billpay_info['currency'] ?></span>
                        </div>
                        <div class="error_msg"></div>
                    </td>
                    <td><label class="control-label">BillPay Time</label></td>
                    <td>
                        <input type="text" class="form-control datepicker" name="time">
                        <div class="error_msg"></div>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Remark</label></td>
                    <td colspan="3">
                        <textarea class="form-control" name="remark" style="width: 70%;height: 70px"></textarea>
                        <div class="error_msg"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center">
                        <a type="button" class="btn btn-info" onclick="paybill_submit()" style="width: 25%"><i class="fa fa-check"></i>Submit</a>
                    </td>
                </tr>
                <?php }?>
                </tbody>
            </table>
        </form>
    </div>
    <script>
        $(function () {
            /*$('.datepicker').datetimepicker({
                language: 'zh',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1,
                minuteStep: 1
            }).on('changeDate', function (ev) {
                $(this).datetimepicker('hide');
            });*/

            $(".datepicker").datepicker({
                format: "yyyy-mm-dd",
                autoclose: true
            });
            $(".datepicker").datepicker("update", "<?php echo date("Y-m-d");?>");



        });





        $('#billpay_form').validate({
            errorPlacement: function (error, element) {
                error.appendTo(element.closest('td').find('.error_msg'));
            },
            rules: {
                amount: {
                    required: true
                },
                time: {
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
                time: {
                    required: '<?php echo 'Required!'?>'
                },
                remark: {
                    required: '<?php echo 'Required!'; ?>'
                }
            }
        });
    </script>
<?php }else{?>
    <div style="padding: 10px 10px"><?php echo $data['msg']?></div>
<?php }?>

