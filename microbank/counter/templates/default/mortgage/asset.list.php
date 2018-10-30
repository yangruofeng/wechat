<div class="col-sm-12" style="margin-top: 15px">
    <label class="col-sm-3 control-label"><span class="required-options-xing">*</span>Asset</label>
    <div class="col-sm-8">
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $asset) { ?>
                <input type="checkbox"  name="asset[]" value="<?php echo $asset['uid'] ?>"><?php echo $asset['asset_name'] ?>
            <?php } ?>
        <?php }else{ ?>
                <input type="text" class="form-control" value="<?php echo 'No  asset  To  Choose' ?>">
        <?php }?>

    </div>
</div>