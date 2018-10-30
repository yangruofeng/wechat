<div id="div_rate_list_<?php echo $prod['uid']?>"  aria-expanded="true" class="collapse in">
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td rowspan="2">Currency</td>
            <td rowspan="2">Min Amount</td>
            <td rowspan="2">Max Amount</td>
            <td rowspan="2">Min Days</td>
            <td rowspan="2">Max Days</td>
            <td colspan="4" align="center">Interest Rate</td>
            <td colspan="4" align="center">Operate Fee</td>
        </tr>
        <tr class="table-header">
            <td>No mortgage</td>
            <td>MortgageSoft</td>
            <td>MortgageHard</td>
            <td>Min value</td>
            <td>No mortgage</td>
            <td>MortgageSoft</td>
            <td>MortgageHard</td>
            <td>Min value</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php if (!empty($size_rate)) { ?>
            <?php foreach ($size_rate as $row) {
                if (!$row['is_active']) continue; ?>
                <tr class="tr-size-rate">
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo $row['loan_size_min']; ?>
                    </td>
                    <td>
                        <?php echo $row['loan_size_max']; ?>
                    </td>
                    <td>
                        <?php echo $row['min_term_days']; ?>
                    </td>
                    <td>
                        <?php echo $row['max_term_days']; ?>
                    </td>
                    <?php $arr_fld = array(
                        "interest_rate" => array("type" => "interest_rate_type", "unit" => "interest_rate_unit"),
                        "interest_rate_mortgage1" => array("type" => "interest_rate_type", "unit" => "interest_rate_unit"),
                        "interest_rate_mortgage2" => array("type" => "interest_rate_type", "unit" => "interest_rate_unit"),
                        "interest_min_value" => array(),
                        "operation_fee" => array("type" => "operation_fee_type", "unit" => "operation_fee_unit"),
                        "operation_fee_mortgage1" => array("type" => "operation_fee_type", "unit" => "operation_fee_unit"),
                        "operation_fee_mortgage2" => array("type" => "operation_fee_type", "unit" => "operation_fee_unit"),
                        "operation_min_value" => array()
                    );
                    ?>
                    <?php foreach($arr_fld as $fld=>$item){?>
                        <td>
                            <?php echo $row[$fld] ?>
                            <?php if ($item['type'] && $row[$item['type']] != 1) { ?>
                                %
                            <?php }?>
                            <?php if($item['unit']){?>
                                (<?php echo $lang['enum_' . $row[$item['unit']]]; ?>)
                            <?php }?>
                        </td>
                    <?php }?>
                </tr>
            <?php } ?>
        <?php }else{ ?>
            <tr>
                <td colspan="15" align="center" >No Data</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

