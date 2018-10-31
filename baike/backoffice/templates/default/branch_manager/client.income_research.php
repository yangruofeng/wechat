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

    #bm .pd-28 {
        padding: 2px 8px;
    }

    .form-horizontal .control-label {
        padding-top: 5px !important;
    }

    .nav-tabs > li > a {
        padding: 7px 12px;
    }

    #co tr > td:first-child {
        min-width: 150px;
    }

    .col-sm-4 tr > td:first-child {
        width: 35%;
    }
</style>

<?php
$member_industry_info = $output['member_industry_info'];
$income_research= $output['last_research_info'];
?>

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
        <div class="col-sm-4" style="padding-left: 0px">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#bm" aria-controls="bm" role="tab" data-toggle="tab" style="border-left: 0">BM Research</a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="bm">
                    <form class="form-horizontal" id="incomeForm">
                        <input type="hidden" name="member_id" value="<?php echo $output['member_id']?>">
                        <table class="table">
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label" style="font-size: 16px">Total Income</label></td>
                                <td class="total-income" style="font-size: 16px"><em id="em_total_income"><?php echo ncAmountFormat($output['total']);?></em></td>
                            </tr>

                            <tr>
                                <td colspan="2"><label class="control-label">Rental Income</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Housing</span></td>
                                <td class="pd-28">
                                    <input type="number" class="form-control input-h30 survey-income-item" onblur="calcTotalIncome()"  name="income_rental_housing" value="<?php echo $income_research['income_rental_housing']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Land</span></td>
                                <td class="pd-28">
                                    <input type="number" class="form-control input-h30 survey-income-item" onblur="calcTotalIncome()"   name="income_rental_land" value="<?php echo $income_research['income_rental_land']; ?>">
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <?php if (count($member_industry_info)){ ?>
                        <ul class="nav nav-tabs" role="tablist" style="margin-top: 20px">
                            <?php $i = 0;foreach ($member_industry_info as $industry) { ++$i?>
                                <li role="presentation" class="<?php echo $i == 1 ? 'active' : ''?>">
                                    <a href="#industry_<?php echo $industry['uid'];?>" aria-controls="industry_<?php echo $industry['uid'];?>" role="tab" data-toggle="tab" style="<?php echo $i == 1 ? 'border-left: 0' : ''?>"><?php echo $industry['industry_name'];?></a>
                                </li>
                            <?php } ?>
                        </ul>
                        <div class="tab-content">
                            <?php
                            $i = 0;
                            foreach ($member_industry_info as $ids_i => $ids_item) {
                                ++$i;
                                $child = my_json_decode($ids_item['industry_json']);
                                $child_kh = my_json_decode($ids_item['industry_json_kh']);
                                foreach ($child as $chd_k => $chk_v) {
                                    if ($child_kh[$chd_k]) $child[$chd_k] = $child_kh[$chd_k];
                                }
                                $recent_value = my_json_decode($ids_item['research_json']);
                                ?>
                            <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : ''?> industry-item" id="industry_<?php echo $ids_item['uid'];?>" data-industry-name="【<?php echo $ids_item['industry_name']?>】" data-industry-uid="<?php echo $ids_item['uid']?>">
                                <table class="table">
                                    <tbody class="table-body">
                                    <tr>
                                        <td><span class="pl-25">Place</span></td>
                                        <td>
                                            <select class="form-control input-h30 industry-item-survey" data-item-code="place">
                                                <option value=""><?php echo $lang['common_select'];?></option>
                                                <?php foreach($output['business_place'] as $place_item){?>
                                                    <option value="<?php echo $place_item['uid']?>" <?php if($recent_value['place']==$place_item['uid']) echo 'selected'?>><?php echo $place_item['place'];?></option>
                                                <?php }?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="pl-25">Employees</span></td>
                                        <td>
                                            <input type="number" class="form-control input-h30 input-employees survey-employees industry-item-survey" data-item-code="employees" onblur="calcTotalEmployees()" value="<?php echo $recent_value['employees']?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="pl-25">Profit</span></td>
                                        <td>
                                            <input type="number" class="form-control input-h30 input-profit survey-profit industry-item-survey" data-item-code="profit" onblur="calcTotalProfit()" value="<?php echo $recent_value['profit']?>">
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
                                    <input type="text" class="form-control input-h30 total-employees" name="business_employees" id="total_survey_employees" value="<?php echo $income_research['business_employees']?:'';?>"  readonly>
                                    <div class="error_msg"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Total Profit</span></td>
                                <td class="pd-28">
                                    <input type="text" class="form-control input-h30 total-profit  survey-income-item"  name="income_business" id="total_survey_profit"  value="<?php echo $income_research['income_business']?:'';?>"  readonly>
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
                                    <input type="number" class="form-control input-h30  survey-income-item" onblur="calcTotalIncome()"  name="income_salary" value="<?php echo $income_research['income_salary']; ?>">
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
                                    <input type="number" class="form-control input-h30  survey-income-item" onblur="calcTotalIncome()" name="income_others" value="<?php echo $income_research['income_others']; ?>">
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
                                    <a type="button" class="btn btn-primary" href="<?php echo getUrl('branch_manager', 'showIncomeResearchHistory', array('member_id' => $output['member_id'], 'operator_id' => $output['operator_id']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-list"></i><?php echo 'History' ?></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-8" style="padding-right: 0px">
            <?php $business_place = resetArrayKey($output['business_place'], 'uid'); ?>
            <?php if ($output['co_research']) {$co_research=$output['co_research']; ?>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#co" aria-controls="co" role="tab" data-toggle="tab" style="border-left: 0">CO Research</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="co">
                        <table class="table">
                            <tr>
                                <td>Research Item</td>
                                <?php foreach($co_research as $co){?>
                                    <td>
                                        <?php echo $co['operator_name']?>
                                    </td>
                                <?php }?>
                            </tr>
                            <tbody class="table-body">
                            <tr>
                                <td><label class="control-label" style="font-size: 16px">Total Income</label></td>
                                <?php foreach($co_research as $co){?>
                                    <td style="font-size: 16px"><em><?php echo ncAmountFormat($co['total_income']);?></em></td>
                                <?php }?>
                            </tr>

                            <tr>
                                <td colspan="10"><label class="control-label">Rental Income</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Housing</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td><em><?php echo ncAmountFormat($co['income_rental_housing']); ?></em></td>
                                <?php }?>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Land</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td><em><?php echo ncAmountFormat($co['income_rental_land']); ?></em></td>
                                <?php }?>
                            </tr>
                            <?php if(count($member_industry_info)){?>
                                <tr>
                                    <td colspan="10"><label class="control-label">Business Income</label></td>
                                </tr>
                                <tr>
                                    <td colspan="10" style="padding: 0px 0 20px">
                                        <ul class="nav nav-tabs" role="tablist" style="margin-top: 20px">
                                            <?php $i = 0;foreach ($member_industry_info as $industry) { ++$i?>
                                                <li role="presentation" class="<?php echo $i == 1 ? 'active' : ''?>">
                                                    <a href="#co_industry_<?php echo $industry['uid'];?>" aria-controls="co_industry_<?php echo $industry['uid'];?>" role="tab" data-toggle="tab" style="<?php echo $i == 1 ? 'border-left: 0' : ''?>"><?php echo $industry['industry_name'];?></a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <div class="tab-content">
                                            <?php
                                            $i = 0;
                                            foreach ($member_industry_info as $ids_i => $ids_item) {
                                                ++$i;
                                                $child = my_json_decode($ids_item['industry_json']);
                                                $child_kh = my_json_decode($ids_item['industry_json_kh']);
                                                foreach ($child as $chd_k => $chk_v) {
                                                    if ($child_kh[$chd_k]) $child[$chd_k] = $child_kh[$chd_k];
                                                }
                                                $recent_value = my_json_decode($ids_item['research_json']);
                                                ?>
                                                <div role="tabpanel" class="tab-pane <?php echo $i == 1 ? 'active' : ''?>" id="co_industry_<?php echo $ids_item['uid'];?>">
                                                    <table class="table">
                                                        <tbody class="table-body">
                                                        <tr>
                                                            <td>Survey Item</td>
                                                        <?php
                                                        $co_survey_arr = array();
                                                        foreach ($co_research as $co) {
                                                            $survey_1 = $co['member_industry_research'];
                                                            $survey_arr = array();
                                                            if (count($survey_1)) {
                                                                foreach ($survey_1 as $survey_12) {
                                                                    if ($survey_12['industry_id'] == $ids_i) {
                                                                        $survey_arr = my_json_decode($survey_12['research_text']);
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            $co_survey_arr[$co['operator_id']] = $survey_arr;
                                                            ?>
                                                            <td>
                                                                <?php echo $co['operator_name']?>
                                                            </td>
                                                        <?php }?>
                                                        </tr>

                                                        <?php foreach($child as $chd_k=>$chd_v){?>
                                                            <tr>
                                                                <td><?php echo $chd_v?></td>
                                                                <?php foreach($co_research as $co){?>
                                                                    <td>
                                                                        <?php
                                                                            echo $co_survey_arr[$co['operator_id']][$chd_k];
                                                                        ?>
                                                                    </td>
                                                                <?php }?>
                                                            </tr>
                                                        <?php }?>
                                                        <tr>
                                                            <td>Place</td>
                                                            <?php foreach($co_research as $co){?>
                                                                <td>
                                                                    <?php echo $business_place[$co_survey_arr[$co['operator_id']]["place"]]['place']; ?>
                                                                </td>
                                                            <?php }?>
                                                        </tr>
                                                        <tr>
                                                            <td>Employees</td>
                                                            <?php foreach($co_research as $co){?>
                                                                <td>
                                                                    <?php echo ncAmountFormat($co_survey_arr[$co['operator_id']]["employees"]); ?>
                                                                </td>
                                                            <?php }?>
                                                        </tr>
                                                        <tr>
                                                            <td>Profit</td>
                                                            <?php foreach($co_research as $co){?>
                                                                <td>
                                                                    <?php echo ncAmountFormat($co_survey_arr[$co['operator_id']]["profit"]); ?>
                                                                </td>
                                                            <?php }?>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php }?>

                            <tr>
                                <td colspan="10"><label class="control-label">Salary Income</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Company Name</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td><?php echo $co['company_name']; ?></td>
                                <?php }?>

                            </tr>
                            <tr>
                                <td><span class="pl-25">Position</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td><?php echo $co['work_position']; ?></td>
                                <?php }?>

                            </tr>
                            <tr>
                                <td><span class="pl-25">Salary</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td><em><?php echo ncAmountFormat($co['income_salary']); ?></em></td>
                                <?php }?>
                            </tr>

                            <tr>
                                <td colspan="10"><label class="control-label">Income Others</label></td>
                            </tr>
                            <tr>
                                <td><span class="pl-25">Income Others</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td><em><?php echo ncAmountFormat($co['income_others']); ?></em></td>
                                <?php }?>
                            </tr>

                            <tr>
                                <td><span class="pl-25">Others Remark</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td><span style="display: inline-block;height: 38px"><?php echo $co['others_remark']; ?></span></td>
                                <?php }?>
                            </tr>
                            <tr>
                                <td colspan="10"><label class="control-label">Remark</label></td
                            </tr>

                            <tr>
                                <td><span class="pl-25">Research Remark</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td><span style="display: inline-block;height: 38px"><?php echo $co['research_remark']; ?></span></td>
                                <?php }?>

                            </tr>
                            <tr>
                                <td><span class="pl-25">Research Time</span></td>
                                <?php foreach($co_research as $co){?>
                                    <td>
                                        <?php echo timeFormat($co['research_time']); ?>
                                        <?php if($co['operator_id']){?>
                                            <a type="button" class="btn btn-primary" href="<?php echo getUrl('branch_manager', 'showIncomeResearchHistory', array('operator_id' => $co['operator_id'], 'member_id' => $co['member_id']), false, BACK_OFFICE_SITE_URL) ?>"><i class="fa fa-list"></i><?php echo 'History' ?></a>
                                        <?php }?>
                                    </td>
                                <?php }?>
                            </tr>
                            </tbody>
                        </table>


                    </div>
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
    function calcTotalEmployees() {
        var _total = 0;
        $("#incomeForm").find(".survey-employees").each(function () {
            var _iv = $(this).val();
            if (_iv) {
                _total = parseInt(_total) + parseInt(_iv);
            }
        });
        $("#total_survey_employees").val(_total);
        calcTotalIncome();
    }

    function calcTotalProfit() {
        var _total = 0;
        $("#incomeForm").find(".survey-profit").each(function () {
            var _iv = $(this).val();
            if (_iv) {
                _total = parseInt(_total) + parseInt(_iv);
            }
        });
        $("#total_survey_profit").val(_total);
        calcTotalIncome();
    }

    function calcTotalIncome() {
        var _total = 0;
        $("#incomeForm").find(".survey-income-item").each(function () {
            var _iv = $(this).val();
            if (_iv) {
                _total = parseInt(_total) + parseInt(_iv);
            }
        });
        $("#em_total_income").html(_total.toString());
    }

    $('#bm-submit').click(function () {
        var formData = {
            member_id: '<?php echo $output['member_id']?>'
        };
        var params = {},
            business_employees = $('#total_survey_employees').val(),
            income_business = $('#total_survey_profit').val();


        var _arr_survey = [];
        var _chk_survey = true;

        $("#incomeForm").find(".industry-item").each(function () {
            var _survey_item = {};
            var _new_item = {};
            var _survey_item_uid = $(this).data("industry-uid");
            var _survey_item_name = $(this).data('industry-name');
            _new_item.industry_id = _survey_item_uid;

            $(this).find(".industry-item-survey").each(function () {
                var _item_key = $(this).data("item-code");
                var _item_val = $(this).val();
                _survey_item[_item_key] = _item_val;
                if (_item_key == 'employees') {
                    if (!_item_val) {
                        alert('required to input Employees for ' + _survey_item_name);
                        _chk_survey = false;
                    }
                    if (parseInt(_item_val) < 0) {
                        alert(_survey_item_name + ' employees must be more than 0');
                        _chk_survey = false;
                    }
                }
                if (_item_key == 'profit') {
                    if (!_item_val) {
                        alert('required to input profit for ' + _survey_item_name);
                        _chk_survey = false;
                    }
                    if (parseInt(_item_val) <= 0) {
                        alert(_survey_item_name + ' profit must be more than 0');
                        _chk_survey = false;
                    }
                }
            });
            _new_item.industry_name = _survey_item_name;
            _new_item.business_place = _survey_item.place;
            _new_item.business_employees = _survey_item.employees;
            _new_item.income_business = _survey_item.profit;
            _new_item.industry_research_json = encodeURI($.toJSON(_survey_item));
            _arr_survey.push(_new_item);
        });
        if (_chk_survey == false) {
            return;
        }
        var _research_json = encodeURI($.toJSON(_arr_survey));
        formData['business_research'] = _research_json;
        formData['business_employees'] = business_employees;
        formData['income_business'] = income_business;

        var _frm_values = $("#incomeForm").getValues();
        formData = $.extend({}, _frm_values, formData);

        $("#incomeForm").waiting();
        yo.loadData({
            _c: "branch_manager",
            _m: "editBmIncomeResearch",
            param: formData,
            callback: function (_obj) {
                $("#incomeForm").unmask();
                if (!_obj.STS) {
                    alert(_obj.MSG,2);
                } else {
                    alert("Saved Success!",1);
                }
            }
        });
    })

</script>