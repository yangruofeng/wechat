<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/loan.css?v=9" rel="stylesheet" type="text/css"
      xmlns="http://www.w3.org/1999/html"/>
<style>

    .input-h30 {
        height: 30px !important;
    }

    em {
        font-weight: 500;
    }

    .btn {
        min-width: 80px;
        height: 30px;
        border-radius: 0;
        padding: 5px 12px;
    }

    .pl-25 {
        padding-left: 25px;
    }

    .pl-50 {
        padding-left: 50px;
    }

    #bm .pd-28 {
        padding: 2px 8px;
    }

    .form-horizontal .control-label {
        padding-top: 5px !important;
    }

    .nav-tabs > li > a {
        padding: 7px 12px;
    }

    tr > td:last-child {
        width: 50%;
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
                <li><a class="current"><span>Income Research</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="col-sm-6">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#bm" aria-controls="bm" role="tab" data-toggle="tab" style="border-left: 0">BM Research</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="bm">
                    <form class="form-horizontal" method="post" action="<?php echo getUrl('branch_manager', 'editBmIncomeResearch', array(), false, BACK_OFFICE_SITE_URL) ?>">
                        <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label" style="font-size: 16px">Total Income</label></td>
                                <td class="total-income" style="font-size: 16px"><em><?php echo ncAmountFormat($income_research['income_rental_land'] + $income_research['income_rental_housing'] + $income_research['income_business'] + $income_research['income_salary'] + $income_research['income_others']);?></em></td>
                            </tr>

                            <tr>
                                <td colspan="2"><label class="control-label">Rental Income</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Housing</span></td>
                                <td class="pd-28">
                                    <input type="number" class="form-control input-h30" name="income_rental_housing" value="<?php echo $income_research['income_rental_housing']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Land</span></td>
                                <td class="pd-28">
                                    <input type="number" class="form-control input-h30" name="income_rental_land" value="<?php echo $income_research['income_rental_land']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <?php if ($output['member_industry']){ ?>
                        <ul class="nav nav-tabs" role="tablist" style="margin-top: 20px">
                            <?php $i = 0;foreach ($output['member_industry'] as $industry) { ++$i?>
                                <li role="presentation" class="<?php echo $i == 1 ? 'active' : ''?>">
                                    <a href="#industry_<?php echo $industry['uid'];?>" aria-controls="industry_<?php echo $industry['uid'];?>" role="tab" data-toggle="tab" style="<?php echo $i == 1 ? 'border-left: 0' : ''?>"><?php echo $industry['industry_name'];?></a>
                                </li>
                            <?php } ?>
                        </ul>
                        <div class="tab-content">
                            <?php $i = 0;$bm_total_employees = 0; $bm_total_profit = 0;foreach ($output['member_industry'] as $industry) { ++$i;$bm_industry_research = $output['bm_industry_research'][$industry['uid']];?>
                            <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : ''?>" id="industry_<?php echo $industry['uid'];?>">
                                <table class="table">
                                    <tbody class="table-body">
                                    <tr>
                                        <td><span class="pl-25">Place</span></td>
                                        <td>
                                            <input type="hidden" name="industry_id[]" value="<?php echo $industry['uid']; ?>">
                                            <input type="hidden" name="industry_name[]" value="<?php echo $industry['industry_name']; ?>">
                                            <select class="form-control input-h30" name="industry_place[]">
                                                <option value="0">Select</option>
                                                <?php foreach ($output['industry_place'] as $place) { ?>
                                                    <option value="<?php echo $place['uid']?>" <?php echo $place['uid'] == $bm_industry_research['industry_place'] ? 'selected' : ''; ?>><?php echo $place['place']?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="pl-25">Employees</span></td>
                                        <td>
                                            <input type="number" class="form-control input-h30 input-employees" name="employees[]" value="<?php $bm_total_employees += $bm_industry_research['employees'];;echo $bm_industry_research['employees']; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="pl-25">Profit</span></td>
                                        <td>
                                            <input type="number" class="form-control input-h30 input-profit" name="profit[]" value="<?php $bm_total_profit += $bm_industry_research['profit'];echo $bm_industry_research['profit']; ?>">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php } ?>
                        </div>

                        <table class="table" style="margin-top: 20px">
                            <tbody class="table-body">
                            <tr>
                                <td colspan="2"><label class="control-label">Business Income</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Total Employees</span></td>
                                <td class="pd-28">
                                    <input type="text" class="form-control input-h30 total-employees" value="<?php echo $bm_total_employees?>" readonly>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Total Profit</span></td>
                                <td class="pd-28">
                                    <input type="text" class="form-control input-h30 total-profit" value="<?php echo $bm_total_profit?>" readonly>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <?php }?>

                        <table class="table" style="margin-top: 20px">
                            <tbody class="table-body">
                            <tr>
                                <td colspan="2"><label class="control-label">Salary Income</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Company Name</span></td>
                                <td class="pd-28">
                                    <input type="text" class="form-control input-h30" name="company_name" value="<?php echo $income_research['company_name']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Position</span></td>
                                <td class="pd-28">
                                    <input type="text" class="form-control input-h30" name="work_position" value="<?php echo $income_research['work_position']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Salary</span></td>
                                <td class="pd-28">
                                    <input type="number" class="form-control input-h30" name="income_salary" value="<?php echo $income_research['income_salary']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table" style="margin-top: 20px">
                            <tbody class="table-body">
                            <tr>
                                <td colspan="2"><label class="control-label">Income Others</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Income Others</span></td>
                                <td class="pd-28">
                                    <input type="number" class="form-control input-h30" name="income_others" value="<?php echo $income_research['income_others']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Others Remark</span></td>
                                <td class="pd-28">
                                    <textarea class="form-control" name="others_remark" style="width: 100%;height: 50px"><?php echo $income_research['others_remark']; ?></textarea>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table" style="margin-top: 20px">
                            <tbody class="table-body">
                            <tr>
                                <td colspan="2"><label class="control-label">Remark</label></td
                            </tr>
                            <tr>
                                <td><span class="pl-25">Research Remark</span></td>
                                <td class="pd-28">
                                    <textarea class="form-control" name="research_remark" style="width: 100%;height: 50px"><?php echo $income_research['research_remark']; ?></textarea>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Research Time</span></td>
                                <td><?php echo timeFormat($income_research['research_time']); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center">
                                    <button type="button" class="btn btn-danger" id="bm-submit"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                                    <a type="button" class="btn btn-primary" href="<?php echo getUrl('branch_manager', 'showBmIncomeResearchHistory', array('uid' => $output['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-list"></i><?php echo 'History' ?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <?php if ($output['co_research']) { ?>
                <ul class="nav nav-tabs" role="tablist">
                <?php $i = 0;foreach ($output['co_research'] as $co) { ++$i; ?>
                    <li role="presentation" class="<?php echo $i == 1 ? 'active' : ''?>">
                        <a href="#<?php echo $co['uid']?>" aria-controls="<?php echo $co['uid']?>" role="tab" data-toggle="tab" style="<?php echo $i == 1 ? 'border-left: 0' : ''?>"><?php echo $co['operator_name']; ?></a>
                    </li>
                <?php }?>
                </ul>
                <div class="tab-content">
                    <?php $i = 0;foreach ($output['co_research'] as $co) { ++$i; ?>
                        <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : ''?>" id="<?php echo $co['uid']?>">
                            <table class="table">
                                <tbody class="table-body">
                                <tr>
                                    <td><label class="control-label" style="font-size: 16px">Total Income</label></td>
                                    <td style="font-size: 16px"><em><?php echo ncAmountFormat($co['income_rental_land'] + $co['income_rental_housing'] + $co['income_business'] + $co['income_salary'] + $co['income_others']);?></em></td>
                                </tr>

                                <tr>
                                    <td colspan="2"><label class="control-label">Rental Income</label></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Housing</span></td>
                                    <td><em><?php echo ncAmountFormat($co['income_rental_housing']); ?></em></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Land</span></td>
                                    <td><em><?php echo ncAmountFormat($co['income_rental_land']); ?></em></td>
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
                                    <?php $i = 0;foreach ($output['member_industry'] as $industry) { ++$i;$co_industry_research = $co['industry_research'][$industry['uid']]?>
                                        <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : ''?>" id="co_industry_<?php echo $industry['uid'];?>">
                                            <table class="table">
                                                <tbody class="table-body">
                                                <?php
                                                $child = my_json_decode($industry['industry_json']);
                                                $child_kh = my_json_decode($industry['industry_json_kh']);
                                                foreach ($child as $chd_k => $chk_v) {
                                                    if ($child_kh[$chd_k]) $child[$chd_k] = $child_kh[$chd_k];
                                                }
                                                $recent_value = my_json_decode($co_industry_research['research_text']);
                                                ?>
                                                <?php foreach ($child as $ck => $cv) { ?>
                                                    <tr>
                                                        <td><span class="pl-25"><?php echo $cv?></span></td>
                                                        <td><?php echo $recent_value[$ck]; ?></td>
                                                    </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td><span class="pl-25">Place</span></td>
                                                    <td><?php echo $output['industry_place'][$co_industry_research['industry_place']]['place']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="pl-25">Employees</span></td>
                                                    <td><?php $total_employees += $co_industry_research['employees'];echo ncPriceFormat($co_industry_research['employees']); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><span class="pl-25">Profit</span></td>
                                                    <td><?php $total_profit += $co_industry_research['profit'];echo ncPriceFormat($co_industry_research['profit']); ?></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } ?>
                                </div>

                                <table class="table" style="margin-top: 20px">
                                    <tbody class="table-body">
                                    <tr>
                                        <td colspan="2"><label class="control-label">Business Income</label></td>
                                    </tr>
                                    <tr>
                                        <td><span class="pl-25">Total Employees</span></td>
                                        <td><?php echo ncPriceFormat($total_employees)?></td>
                                    </tr>
                                    <tr>
                                        <td><span class="pl-25">Total Profit</span></td>
                                        <td><?php echo ncPriceFormat($total_profit)?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            <?php }?>

                            <table class="table" style="margin-top: 20px">
                                <tbody class="table-body">
                                <tr>
                                    <td colspan="2"><label class="control-label">Salary Income</label></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Company Name</span></td>
                                    <td><?php echo $co['company_name']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Position</span></td>
                                    <td><?php echo $co['work_position']; ?></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Salary</span></td>
                                    <td><em><?php echo ncAmountFormat($co['income_salary']); ?></em></td>
                                </tr>
                                </tbody>
                            </table>

                            <table class="table" style="margin-top: 20px">
                                <tbody class="table-body">
                                <tr>
                                    <td colspan="2"><label class="control-label">Income Others</label></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Income Others</span></td>
                                    <td><em><?php echo ncAmountFormat($co['income_others']); ?></em></td>
                                </tr>
                                </tbody>
                            </table>

                            <table class="table" style="margin-top: 20px">
                                <tbody class="table-body">
                                <tr>
                                    <td><span class="pl-25">Others Remark</span></td>
                                    <td><span style="display: inline-block;height: 38px"><?php echo $co['others_remark']; ?></span></td>
                                </tr>

                                <tr>
                                    <td colspan="2"><label class="control-label">Remark</label></td
                                </tr>
                                </tbody>
                            </table>

                            <table class="table" style="margin-top: 20px">
                                <tbody class="table-body">
                                <tr>
                                    <td><span class="pl-25">Research Remark</span></td>
                                    <td><span style="display: inline-block;height: 38px"><?php echo $co['research_remark']; ?></span></td>
                                </tr>
                                <tr>
                                    <td><span class="pl-25">Research Time</span></td>
                                    <td><?php echo timeFormat($co['research_time']); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: center">
                                        <a type="button" class="btn btn-primary" href="<?php echo getUrl('branch_manager', 'showCoIncomeResearchHistory', array('uid' => $co['operator_id'], 'member_id' => $co['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-list"></i><?php echo 'History' ?></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php }?>
                </div>
            <?php } else {?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#co" aria-controls="co" role="tab" data-toggle="tab" style="border-left: 0">CO Research</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="co">
                        <table class="table">
                            <tbody class="table-body">
                                <tr>
                                    <td>No Record</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
</div>
<script>
    $('#bm-submit').click(function () {
        $('.form-horizontal').submit();
    })

    $('input[type="number"]').change(function () {
        var total = 0;
        $('input[type="number"]').each(function(){
            var val = $(this).val();
            val = Number(val);
            total += Number(val);
        })
        $('.total-income em').html('$' + formatCurrency(total));
    })

    $('.input-employees').change(function () {
        var total = 0;
        $('.input-employees').each(function () {
            var val = $(this).val();
            val = Number(val);
            total += Number(val);
        })
        $('.total-employees').val(formatCurrency(total));
    })

    $('.input-profit').change(function () {
        var total = 0;
        $('.input-profit').each(function () {
            var val = $(this).val();
            val = Number(val);
            total += Number(val);
        })
        $('.total-profit').val(formatCurrency(total));
    })
</script>