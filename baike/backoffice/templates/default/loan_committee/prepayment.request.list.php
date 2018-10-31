<div>
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Contract Sn'; ?></td>
            <td><?php echo 'Client Account'; ?></td>
            <td><?php echo 'Loan Product'; ?></td>
            <td><?php echo 'Currency'; ?></td>
<!--            <td>--><?php //echo 'Principal'; ?><!--</td>-->
<!--            <td>--><?php //echo 'Interest'; ?><!--</td>-->
<!--            <td>--><?php //echo 'Operation Fee'; ?><!--</td>-->
<!--            <td>--><?php //echo 'Penalty'; ?><!--</td>-->
            <td><?php echo 'Amount'; ?></td>
            <td><?php echo 'Type'; ?></td>
            <td><?php echo 'Apply Amount/Period'; ?></td>
            <?php if ($data['state'] >= prepaymentApplyStateEnum::AUDITING) { ?>
                <td><?php echo 'Auditor'; ?></td>
            <?php } ?>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!$data['data']) { ?>
            <tr>
                <td colspan="20">No Record</td>
            </tr>
        <?php } else { ?>
            <?php foreach ($data['data'] as $apply) { ?>
                <tr>
                    <td>
                        <?php echo $apply['contract_sn']; ?>
                    </td>
                    <td>
                        <?php echo $apply['login_code']; ?>
                    </td>
                    <td>
                        <?php echo $apply['sub_product_name']; ?>
                    </td>
                    <td>
                        <?php echo $apply['currency']; ?>
                    </td>
<!--                    <td>-->
<!--                        --><?php //echo ncPriceFormat($apply['payable_principal']); ?>
<!--                    </td>-->
<!--                    <td>-->
<!--                        --><?php //echo ncPriceFormat($apply['payable_interest']); ?>
<!--                    </td>-->
<!--                    <td>-->
<!--                        --><?php //echo ncPriceFormat($apply['payable_operation_fee']); ?>
<!--                    </td>-->
<!--                    <td>-->
<!--                        --><?php //echo ncPriceFormat($apply['payable_penalty']); ?>
<!--                    </td>-->
                    <td>
                        <?php echo ncPriceFormat($apply['total_payable_amount']); ?>
                    </td>
                    <td>
                        <?php echo $lang['prepayment_request_type_' . $apply['prepayment_type']]; ?>
                    </td>
                    <td>
                        <?php
                        if ($apply['prepayment_type'] == prepaymentRequestTypeEnum::PARTLY) {
                            echo ncPriceFormat($apply['apply_principal_amount']);
                        } else if ($apply['prepayment_type'] == prepaymentRequestTypeEnum::LEFT_PERIOD) {
                            echo $apply['repay_period'];
                        }
                        ?>
                    </td>
                    <?php if ($data['state'] >= prepaymentApplyStateEnum::AUDITING) { ?>
                        <td><?php echo $apply['auditor_name']; ?></td>
                    <?php } ?>
                    <td>
                        <?php if ($data['state'] == prepaymentApplyStateEnum::CREATE || ($data['state'] == prepaymentApplyStateEnum::AUDITING && $data['user_id'] == $apply['auditor_id'])) { ?>
                        <div class="custom-btn-group">
                            <a title="" class="custom-btn custom-btn-secondary"
                               href="<?php echo getUrl('loan_committee', 'handlerRepaymentRequest', array('uid' => $apply['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                <span><i class="fa fa-vcard-o"></i>Handle</span>
                            </a>
                        </div>
                        <?php } ?>
                        <?php if ($data['state'] > prepaymentApplyStateEnum::AUDITING) { ?>
                            <div class="custom-btn-group">
                                <a title="" class="custom-btn custom-btn-secondary"
                                   href="<?php echo getUrl('loan_committee', 'showRepaymentRequest', array('uid' => $apply['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <span><i class="fa fa-vcard-o"></i>Detail</span>
                                </a>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager")); ?>
