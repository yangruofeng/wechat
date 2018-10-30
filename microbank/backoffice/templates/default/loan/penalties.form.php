<?php $scheme_list = $data['data'];?>
<form class="form-horizontal" id="penalty_form">
    <input name="uid" value="<?php echo $data['deducting_penalties'] ? '' : $data['loan_contract']['uid']?>" type="hidden">
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><span
                class="required-options-xing"></span><?php echo 'Contract Sn' ?></label>

        <div class="col-sm-9">
            <input type="text" class="form-control" value="<?php echo $data['loan_contract']['contract_sn']?>" id="scheme_name" readonly>
            <div class="error_msg"></div>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><span
                class="required-options-xing"></span><?php echo 'Penalties Detail'; ?></label>

        <div class="col-sm-9">
            <table class="table table-bordered">
                <thead>
                <tr class="table-header" style="background: #EFEFEF">
                    <td>Scheme Name</td>
                    <td>Penalties</td>
                    <td>Deducted</td>
                </tr>
                </thead>
                <tbody class="table-body">
                <?php foreach($scheme_list as $scheme){?>
                <tr>
                    <td>
                        <?php echo $scheme['scheme_name']?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($scheme['penalties'])?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($scheme['deduction_penalty'])?>
                    </td>
                </tr>
                <?php }?>
                <tr style="font-weight: 700">
                    <td>
                        <?php echo 'Total'?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($data['penalties_total'])?>
                    </td>
                    <td>
                        <?php echo ncAmountFormat($data['deduction_total'])?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><span
                class="required-options-xing"></span><?php echo 'Deducting Penalties' ?></label>

        <div class="col-sm-9">
            <div class="input-group">
                <input type="number" class="form-control" id="deducting_penalties"
                       name="deducting_penalties" value="<?php echo $data['deducting_penalties']['deducting_penalties']?>" <?php echo $data['deducting_penalties'] ? 'readonly' : ''?>>
                <span class="input-group-addon" style="min-width: 55px;border-left: 0">$</span>
            </div>
            <div class="error_msg"><?php echo $data['deducting_penalties']['deducting_penalties'] > 0 ? "There has been an unaudited application!" : ""?></div>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><span
                class="required-options-xing"></span><?php echo 'Remark' ?></label>

        <div class="col-sm-9">
            <input type="text" class="form-control" value="<?php echo $data['deducting_penalties']['remark']?>" name="remark" <?php echo $data['deducting_penalties'] ? 'readonly' : ''?>>
            <div class="error_msg"></div>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-3 control-label"><span
                class="required-options-xing"></span><?php echo 'Remaining Penalties'; ?></label>

        <div class="col-sm-9">
            <div class="input-group">
                <input type="number" class="form-control" id="remaining_penalties" value="<?php echo $data['deducting_penalties'] ? round($data['penalties_total'] - $data['deducting_penalties']['deducting_penalties'],2) : $data['penalties_total']?>" readonly>
                <span class="input-group-addon" style="min-width: 55px;border-left: 0">$</span>
            </div>
        </div>
    </div>
</form>
<script>
    var penalties_total = Number('<?php echo $data['penalties_total']?>');
</script>