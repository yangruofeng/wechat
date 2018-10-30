<div class="pull-right">
    <div class="verify-wrap">
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#loan" aria-controls="loan" role="tab" data-toggle="tab">Loan</a>
                </li>
                <li role="presentation">
                    <a href="#insurance" aria-controls="insurance" role="tab" data-toggle="tab">Insurance</a>
                </li>
                <li role="presentation">
                    <a href="#savings" aria-controls="savings" role="tab" data-toggle="tab">Savings</a>
                </li>
            </ul>
            <?php
            $contract_info = $output['contract_info'] ?: $data['contract_info'];
            $loan_summary = $output['loan_summary'] ?: $data['loan_summary'];
            ?>
            <div class="tab-content client-verify-info">
                <div role="tabpanel" class="tab-pane active" id="loan">
                    <div class="contract-wrap">
                        <div class="contract-info">
                            <div class="item">
                                All Enquiries
                                <span class="t"><?php echo $contract_info['all_enquiries'] ?: '-'; ?></span>
                            </div>
                            <div class="item">
                                Earliest Loan Issue Date
                                <span class="t"><?php echo dateFormat($contract_info['earliest_loan_issue_date']) ?: '-'; ?></span>
                            </div>
                            <div class="item">
                                Total Contracts <span class="t"><?php echo $loan_summary['contract_num_summary']['total_contracts']; ?></span>
                            </div>
                            <div class="item clearfix">
                                <div class="d d1">
                                    <em><?php echo $loan_summary['contract_num_summary']['normal_processing_contracts']; ?></em>
                                    Normal
                                </div>
                                <div class="d d2">
                                    <em><?php echo $loan_summary['contract_num_summary']['delinquent_contracts']; ?></em>
                                    Delinquent
                                </div>
                                <div class="d d3">
                                    <em><?php echo $loan_summary['contract_num_summary']['complete_contracts']; ?></em>
                                    Closed
                                </div>
                                <div class="d d4">
                                    <em><?php echo $loan_summary['contract_num_summary']['rejected_contracts']; ?></em>
                                    Rejected
                                </div>
                                <div class="d d5">
                                    <em><?php echo $loan_summary['contract_num_summary']['write_off_contracts']; ?></em>
                                    Write off
                                </div>
                            </div>
                            <div class="item">
                                <table class="table">
                                    <thead>
                                    <tr class="table-header">
                                        <td><?php echo 'Contract No.';?></td>
                                        <td><?php echo 'Loan Time';?></td>
                                        <td><?php echo 'Product';?></td>
                                        <td><?php echo 'Principal';?></td>
                                        <td><?php echo 'State';?></td>
                                    </tr>
                                    </thead>
                                    <tbody class="table-body">
                                    <?php $contract_list = $output['contracts'] ?: $data['contracts']; ?>
                                    <?php if ($contract_list) { ?>
                                        <?php foreach ($contract_list as $row) { ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo getUrl('loan', 'contractDetail', array('uid' => $row['uid'], 'source' => $source_mark), false, BACK_OFFICE_SITE_URL) ?>">
                                                        <?php echo $row['contract_sn']?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php echo $row['create_time']?>
                                                </td>
                                                <td>
                                                    <?php echo $row['sub_product_name']?>
                                                </td>
                                                <td>
                                                    <?php echo ncPriceFormat($row['apply_amount'])?>
                                                </td>
                                                <td>
                                                    <?php echo $lang['loan_contract_state_' . $row['state']]?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td>
                                                No Record
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="insurance">
                    <div class="contract-wrap">
                        <?php
                        $contracts = $output['insurance_contracts'] ?: $data['insurance_contracts'];
                        $count = count($contracts);
                        ?>
                        <div class="contract-list">
                            <div class="">
                                <div class="activity-list">
                                    <?php if ($count > 0) { ?>
                                        <?php foreach ($contracts as $key => $value) { ?>
                                            <div class="item">
                                                <div>
                                                    <small class="pull-right text-navy"></small>
                                                    <strong><?php echo $value['contract_sn'] ?></strong>

                                                    <div><?php echo $value['product_name'] ?>&nbsp;&nbsp;&nbsp;<?php echo $value['currency'] ?>
                                                        &nbsp;&nbsp;&nbsp;<?php echo $value['price'] ?></div>
                                                    <div class="b">
                                                        <small class="text-muted"><?php echo timeFormat($value['create_time']) ?></small>
                                                        <?php
                                                        $class = '';
                                                        $label = '';
                                                        switch ($value['state']) {
                                                            case loanContractStateEnum::CREATE :
                                                                $class = 'label-primary';
                                                                $label = 'Create';
                                                                break;
                                                            case loanContractStateEnum::PROCESSING :
                                                                $class = 'label-success';
                                                                $label = 'Ongoing';
                                                                break;
                                                            case loanContractStateEnum::COMPLETE :
                                                                $class = 'label-warning';
                                                                $label = 'Complete';
                                                                break;
                                                            case loanContractStateEnum::WRITE_OFF :
                                                                $class = 'label-default';
                                                                $label = 'Write Off';
                                                                break;
                                                            default:
                                                                $class = 'label-default';
                                                                $label = 'Write Off';
                                                                break;
                                                        } ?>
                                                        <span class="label <?php echo $class; ?>"><?php echo $label; ?></span>
                                                        <a class="a-detail" href="<?php echo getUrl('insurance', 'contractDetail', array('uid' => 1, 'show_menu' => 'insurance-contract'), false, BACK_OFFICE_SITE_URL) ?>">Detail>></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <div class="no-record">
                                            No Record
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="savings">
                    <div class="no-record">
                        coming soon
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>