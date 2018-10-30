<style>
    .asset-info {
        height: 30px;line-height: 40px;font-size: 16px;
    }

    .asset-info .col-sm-4 {
        overflow: hidden;
        text-overflow:ellipsis;
        white-space: nowrap;
    }

    .asset-info .col-sm-4 span {
        font-weight: 600
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <?php if ($output['source'] == 'credit_committee') { ?>
                <h3>Committee</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('loan_committee', 'approveCreditApplication', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                    <li><a onclick="javascript:history.go(-1)"><span>Credit Grant</span></a></li>
                    <li><a class="current"><span>History</span></a></li>
                </ul>
            <?php } else { ?>
                <h3>Client</h3>
                <ul class="tab-base">
                    <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                    <?php if ($output['source'] == 'request_credit') { ?>
                        <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Credit Process</span></a></li>
                    <?php } else { ?>
                        <li><a href="<?php echo getUrl('branch_manager', 'showIncomeResearch', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Income Research</span></a></li>
                    <?php } ?>
                    <li><a class="current"><span>History</span></a></li>
                </ul>
            <?php } ?>
        </div>
    </div>
    <div class="container" style="max-width: 1300px">
        <div class="asset-info">
            <div class="col-sm-4">
                Client Name:
                <span><?php echo $output['client_info']['display_name']?></span>
            </div>
            <?php if ($output['operator_info']) { ?>
                <div class="col-sm-4">
                    Operator Name:
                    <span><?php echo $output['operator_info']['user_name']?></span>
                </div>
            <?php } ?>
        </div>
        <div class="business-content">
            <div class="business-list">
                <table class="table">
                    <thead>
                    <tr class="table-header">
                        <td><?php echo 'Total Income';?></td>
                        <td><?php echo 'Housing Income';?></td>
                        <td><?php echo 'Land Income';?></td>
                        <td><?php echo 'Business Income';?></td>
                        <td><?php echo 'Salary Income';?></td>
                        <td><?php echo 'Others Income';?></td>
                        <td><?php echo 'Research Remark';?></td>
                        <?php if (!$output['operator_info']) { ?>
                            <td><?php echo 'Operator';?></td>
                        <?php } ?>
                        <td><?php echo 'Research Time';?></td>
                        <td><?php echo 'Function';?></td>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php if (!$output['income_research']) { ?>
                        <tr>
                            <td colspan="10">No Record</td>
                        </tr>
                    <?php } else { ?>
                    <?php foreach ($output['income_research'] as $income_research) { ?>
                        <tr>
                            <td>
                                <?php echo ncAmountFormat($income_research['income_rental_land'] + $income_research['income_rental_housing'] + $income_research['income_business'] + $income_research['income_salary'] + $income_research['income_others']);?>
                            </td>
                            <td>
                                <?php echo ncAmountFormat($income_research['income_rental_housing']);?>
                            </td>
                            <td>
                                <?php echo ncAmountFormat($income_research['income_rental_land']);?>
                            </td>
                            <td>
                                <?php echo ncAmountFormat($income_research['income_business']);?>
                            </td>
                            <td>
                                <?php echo ncAmountFormat($income_research['income_salary']);?>
                            </td>
                            <td>
                                <?php echo ncAmountFormat($income_research['income_others']);?>
                            </td>
                            <td>
                                <?php echo $income_research['research_remark']; ?>
                            </td>
                            <?php if (!$output['operator_info']) { ?>
                                <td>
                                    <?php echo $income_research['operator_name']; ?>
                                </td>
                            <?php } ?>
                            <td>
                                <?php echo timeFormat($income_research['research_time']); ?>
                            </td>
                            <td>
                                <a href="<?php echo getUrl('branch_manager', 'showIncomeResearchDetail', array('uid'=>$income_research['uid']), false, BACK_OFFICE_SITE_URL)?>">
                                    <span><i class="fa fa-address-card-o"></i>Detail</span>
                                </a>
                            </td>
                        </tr>
                    <?php }?>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>