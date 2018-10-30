<style>
    .title {
        margin-bottom: 10px;
    }
    .title span:nth-child(2n){
        font-weight: 600;
        margin-right: 50px
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Overdue</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'overdueContract', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'overdueContractForCo', array('co_id' => $output['co_id']), false, BACK_OFFICE_SITE_URL)?>"><span>Detail</span></a></li>
                <li><a class="current"><span>Check List</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="table-form">
            <div class="business-content">
                <div class="col-sm-12 title">
                    <span><?php echo 'Contract：'?></span>
                    <span><?php echo $output['scheme_info']['virtual_contract_sn'] . ' ' . $output['scheme_info']['scheme_name'] ?></span>
                    <span><?php echo 'Client：' ?></span>
                    <span><?php echo $output['scheme_info']['display_name'] ?></span>
                    <span><?php echo 'Payable Amount：' ?></span>
                    <span><?php echo ncPriceFormat($output['scheme_info']['payable_amount']) ?></span>
                    <span><?php echo 'State：' ?></span>
                    <span><?php echo $output['scheme_info']['state'] == 0 ? 'Undone' : 'Done' ?></span>
                    <?php if($output['scheme_info']['state'] == 0) {?>
                        <span><?php echo 'Receivable Date：' ?></span>
                        <span><?php echo dateFormat($output['scheme_info']['receivable_date']) ?></span>
                    <?php }?>
                </div>
                <div class="business-list">
                    <table class="table">
                        <thead>
                        <tr class="table-header">
                            <td><?php echo 'Credit Officer'; ?></td>
                            <td><?php echo 'Dun Response'; ?></td>
                            <td><?php echo 'Time'; ?></td>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php if ($output['check_list'] || $output['overdue_contract_task']['state'] == 2) { ?>
                        <?php foreach ($output['check_list'] as $row) { ?>
                            <tr>
                                <td>
                                    <?php echo $row['officer_name']; ?>
                                </td>
                                <td>
                                    <?php echo $row['dun_response']; ?>
                                </td>
                                <td>
                                    <?php echo timeFormat($row['dun_time']); ?>
                                </td>
                            </tr>
                        <?php } ?>
                            <?php if($output['overdue_contract_task']['state'] == 2){?>
                                <tr>
                                    <td>
                                        <?php echo $output['overdue_contract_task']['co_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $output['overdue_contract_task']['remark']; ?>
                                    </td>
                                    <td>
                                        <?php echo $output['overdue_contract_task']['update_time']; ?>
                                    </td>
                                </tr>
                            <?php }?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3">
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
</div>