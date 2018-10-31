<div id="div_rate_list_<?php echo $prod['uid']?>"  aria-expanded="false" class="collapse table-responsive">
    <table class="table table-hover table-bordered">
        <thead>
        <tr class="table-header">
            <td rowspan="2">Active</td>
            <td rowspan="2">Show For<br/> Client</td>
            <td rowspan="2"><?php echo 'Currency';?></td>
            <td rowspan="2">Min Amount</td>
            <td rowspan="2">Max Amount</td>
            <td rowspan="2">Min Days</td>
            <td rowspan="2">Max Days</td>
            <!--
            <td rowspan="2"><?php echo 'Admin Fee';?></td>
            <td rowspan="2"><?php echo 'Loan Fee';?></td>
            -->
            <td colspan="4" align="center">
                Interest Rate
            </td>
            <td colspan="4" align="center">Operate Fee</td>
            <td rowspan="2">
                Service Charges
            </td>
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

        <?php if(!empty($data['data'])){?>
            <?php foreach($data['data'] as $row){?>
                <tr class="tr-size-rate <?php if($row['is_active']){ echo 'tr-rate-active';}else{echo 'tr-rate-not-active';}?>">
                    <td>
                        <input type="checkbox" onclick="setActiveForSpecialItem(this, <?php echo $row['uid']?>)" <?php echo $row['is_active'] == 1 ? 'checked' : ''?>>
                    </td>
                    <td>
                        <input type="checkbox" onclick="showForClient(this, <?php echo $row['uid']?>)" <?php echo $row['is_show_for_client'] == 1 ? 'checked' : ''?>>
                    </td>
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
                    <?php $arr_fld=array(
                        //"admin_fee"=>array("type"=>"admin_fee_type"),
                        //"loan_fee"=>array("type"=>"loan_fee_type"),
                        "interest_rate"=>array("type"=>"interest_rate_type","unit"=>"interest_rate_unit"),
                        "interest_rate_mortgage1"=>array("type"=>"interest_rate_type","unit"=>"interest_rate_unit"),
                        "interest_rate_mortgage2"=>array("type"=>"interest_rate_type","unit"=>"interest_rate_unit"),
                        "interest_min_value"=>array(),
                        "operation_fee"=>array("type"=>"operation_fee_type","unit"=>"operation_fee_unit"),
                        "operation_fee_mortgage1"=>array("type"=>"operation_fee_type","unit"=>"operation_fee_unit"),
                        "operation_fee_mortgage2"=>array("type"=>"operation_fee_type","unit"=>"operation_fee_unit"),
                        "operation_min_value"=>array(),
                        "service_fee" => array('type'=>'service_fee_type'),

                    );?>
                    <?php foreach($arr_fld as $fld=>$item){?>
                        <td class="td-editable <?php if($row[$fld]!=$row['default_setting'][$fld]) echo 'td-new' ?>"
                            title="Default : <?php echo $row['default_setting'][$fld]?>"
                            data-state="0"
                            data-old_value="<?php echo $row['default_setting'][$fld]?>"
                            data-size_rate_id="<?php echo $row['uid']?>"
                            data-fld_name="<?php echo $fld?>">
                            <div class="input-group form-group div_editor" style="width: auto;margin-bottom: auto">
                                <input type="text" class="form-control input-val" value="<?php echo $row[$fld]?>" style="display: none;">
                                <span class="input-group-addon span-val"  ondblclick="start_edit_rate_onclick(this);">
                                    <?php echo $row[$fld];?>
                                </span>
                                <?php if($item['type'] && $row[$item['type']]!=1){?>
                                    <span class="input-group-addon">%</span>
                                <?php }?>
                                <?php if($item['unit']){?>
                                    <span class="input-group-addon span-unit">(<?php echo $lang['enum_'.$row[$item['unit']]];?>)</span>
                                <?php }?>
                                <span class="input-group-btn" style="display: none">
                                    <button class="btn btn-primary" onclick="save_edit_rate_onclick(this)"><i class="fa fa-save"></i></button>
                                    <button class="btn btn-default" onclick="cancel_edit_rate_onclick(this)"><i class="fa fa-close"></i></button>
                                </span>
                            </div>
                        </td>

                    <?php }?>

                </tr>
            <?php } ?>
        <?php }else{ ?>
            <tr>
                <td colspan="<?php echo $data['type'] != 'info' ? 16 : 15; ?>" align="center" >No data!</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

