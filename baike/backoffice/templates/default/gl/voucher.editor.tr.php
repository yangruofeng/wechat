<?php $guid=uniqid();?>
<tr>
     <td>
        <input type="hidden" name="is_debit[]" value="<?php echo $data['is_debit']?>">
        <div class="form-group">
            <div class="input-group">
                <input type="text" class="form-control item-gl-code" onblur="checkGlCodeValidation(this)" name="gl_code[]" id="txt_gl_code_<?php echo $guid?>">
                <span class="input-group-btn">
                    <button class="btn btn-default"><i class="fa fa-search"></i>&nbsp;</button>
                </span>
            </div>
            <div class="error_msg"></div>
        </div>

    </td>
    <td>
        <label class="lbl-gl-name"></label>
    </td>
    <td>
        <div class="form-group">
            <input type="text" class="form-control item-gl-subject" name="gl_subject[]" value="<?php echo $data['gl_subject']?>"  id="txt_gl_subject_<?php echo $guid?>">
            <div class="error_msg"></div>
        </div>

    </td>
    <?php if($data['is_debit']){?>
        <td>
            <div class="form-group">
                <input type="text" class="form-control item-gl-amount gl-amount-<?php echo $data['is_debit']?'debit':'credit';?>" style="width: 100px"  id="txt_gl_amount_<?php echo $guid?>" value="<?php echo $data['gl_amount']?>" name="gl_amount[]">
                <div class="error_msg"></div>
            </div>
        </td>
        <td></td>
    <?php }else{?>
        <td></td>
        <td>
            <div class="form-group">
                <input type="text" class="form-control item-gl-amount gl-amount-<?php echo $data['is_debit']?'debit':'credit';?>" style="width: 100px"  id="txt_gl_amount_<?php echo $guid?>" value="<?php echo $data['gl_amount']?>" name="gl_amount[]">
                <div class="error_msg"></div>
            </div>
        </td>
    <?php }?>

    <td>
        <button type="button" class="btn btn-default" onclick="btn_remove_voucher_item_onclick(this)"><i class="fa fa-trash"></i></button>
    </td>
</tr>
