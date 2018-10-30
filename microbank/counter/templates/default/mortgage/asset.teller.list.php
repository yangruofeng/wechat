<div class="col-sm-12" style="margin-top: 15px">
    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Asset</label>
    <div class="col-sm-8">
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $info) { ?>
                <input type="checkbox"  name="asset[]" value="<?php echo $info['uid'] ?>"><?php echo $info['asset_name'] ?>
            <?php } ?>
        <?php }else{ ?>
                <input type="text" class="form-control" value="<?php echo 'No  asset  To  Choose' ?>">
        <?php }?>
    </div>
</div>
<div class="col-sm-12" style="margin-top: 15px">
    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Cashier</label>
    <div class="col-sm-8">
        <input type="text" class="form-control"  name="cashier_name" value="<?php echo $info['operator_name']?>">
        <input type="hidden" class="form-control"  name="cashier_id" value="<?php echo $info['operator_id']?>">
        <div class="error_msg"></div>
    </div>
</div>
