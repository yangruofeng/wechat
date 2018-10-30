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

    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .pl-25 {
        padding-left: 25px;
        font-weight: 600;
    }

    em {
        font-weight: 500;
    }

    tr td {
        width: 25%;
    }
</style>
<?php $income_research = $output['income_research']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('branch_manager', 'client', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('branch_manager', 'showClientDetail', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><span>Credit Process</span></a></li>
                <li><a class="current"><span>Income Research Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="max-width: 1200px">
        <table class="table">
            <tbody class="table-body">
            <?php if (!$income_research) { ?>
                <tr>
                    <td style="height: 40px">No Record</td>
                </tr>
            <?php } else { ?>
            <tr>
                <td><label class="control-label" style="font-size: 16px">Total Income</label></td>
                <td style="font-size: 16px"><em><?php echo ncAmountFormat($income_research['income_rental_land'] + $income_research['income_rental_housing'] + $income_research['income_business'] + $income_research['income_salary'] + $income_research['income_others']);?></em></td>
                <td><label class="control-label" style="font-size: 16px">Client Name</label></td>
                <td style="font-size: 16px"><?php echo $output['client_info']['display_name'] ?></td>
            </tr>

            <tr>
                <td colspan="4"><label class="control-label">Rental Income</label></td>
            </tr>
            <tr>
                <td><span class="pl-25">Housing</span></td>
                <td><em><?php echo ncAmountFormat($income_research['income_rental_housing']); ?></em></td>
                <td><span class="pl-25">Land</span></td>
                <td><em><?php echo ncAmountFormat($income_research['income_rental_land']); ?></em></td>
            </tr>
            </tbody>
        </table>

        <?php if ($output['member_industry']){ $total_employees = 0;$total_profit = 0;?>
            <ul class="nav nav-tabs" role="tablist" style="margin-top: 20px">
                <?php $i = 0;foreach ($output['member_industry'] as $industry) { ++$i?>
                    <li role="presentation" class="<?php echo $i == 1 ? 'active' : ''?>">
                        <a href="#co_industry_<?php echo $industry['uid'];?>" aria-controls="co_industry_<?php echo $industry['uid'];?>" role="tab" data-toggle="tab" style="<?php echo $i == 1 ? 'border-left: 0' : ''?>"><?php echo $industry['industry_name'];?></a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <?php $i = 0;foreach ($output['member_industry'] as $industry) { ++$i;$industry_research = $output['industry_research'][$industry['uid']]?>
                    <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : ''?>" id="co_industry_<?php echo $industry['uid'];?>">
                        <table class="table">
                            <tbody class="table-body">
                            <?php if($output['income_research']['researcher_type'] == 0) {?>
                                <?php
                                $child = my_json_decode($industry['industry_json']);
                                $child_kh = my_json_decode($industry['industry_json_kh']);
                                foreach ($child as $chd_k => $chk_v) {
                                    if ($child_kh[$chd_k]) $child[$chd_k] = $child_kh[$chd_k];
                                }
                                $recent_value = my_json_decode($industry_research['research_text']);
                                ?>
                                <?php foreach ($child as $ck => $cv) { ?>
                                    <tr>
                                        <td><span class="pl-25"><?php echo $cv?></span></td>
                                        <td><?php echo $recent_value[$ck]; ?></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php } ?>
                            <?php }?>
                            <tr>
                                <td><span class="pl-25">Place</span></td>
                                <td><?php echo $output['industry_place'][$industry_research['industry_place']]['place']; ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Employees</span></td>
                                <td><?php $total_employees += $industry_research['employees'];echo ncPriceFormat($industry_research['employees']); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Profit</span></td>
                                <td><?php $total_profit += $industry_research['profit'];echo ncPriceFormat($industry_research['profit']); ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <table class="table" style="margin-top: 20px">
                <tbody class="table-body">
                <tr>
                    <td colspan="4"><label class="control-label">Business Income</label></td>
                </tr>
                <tr>
                    <td><span class="pl-25">Total Employees</span></td>
                    <td><?php echo ncPriceFormat($total_employees)?></td>
                    <td><span class="pl-25">Total Profit</span></td>
                    <td><?php echo ncPriceFormat($total_profit)?></td>
                </tr>
                </tbody>
            </table>
        <?php }?>

        <table class="table" style="margin-top: 20px">
            <tbody class="table-body">
            <tr>
                <td colspan="4"><label class="control-label">Salary Income</label></td>
            </tr>
            <tr>
                <td><span class="pl-25">Company Name</span></td>
                <td><?php echo $income_research['company_name']; ?></td>
                <td><span class="pl-25">Position</span></td>
                <td><?php echo $income_research['work_position']; ?></td>
            </tr>
            <tr>
                <td><span class="pl-25">Salary</span></td>
                <td colspan="3"><em><?php echo ncAmountFormat($income_research['income_salary']); ?></em></td>
            </tr>

            <tr>
                <td colspan="4"><label class="control-label">Income Others</label></td>
            </tr>

            <tr>
                <td><span class="pl-25">Income Others</span></td>
                <td><em><?php echo ncAmountFormat($income_research['income_others']); ?></em></td>
                <td><span class="pl-25">Others Remark</span></td>
                <td><?php echo $income_research['others_remark']; ?></td>
            </tr>

            <tr>
                <td colspan="4"><label class="control-label">Remark</label></td
            </tr>

            <tr>
                <td><span class="pl-25">Operator</span></td>
                <td><?php echo $income_research['operator_name']; ?></td>
                <td><span class="pl-25">Research Time</span></td>
                <td><?php echo timeFormat($income_research['research_time']); ?></td>
            </tr>
            <tr>
                <td><span class="pl-25">Research Remark</span></td>
                <td colspan="3"><?php echo $income_research['research_remark']; ?></td>
            </tr>
            <?php } ?>

            <tr>
                <td colspan="4" style="text-align: center">
                    <button type="button" class="btn btn-default" onclick="javascript :history.back(-1)"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>