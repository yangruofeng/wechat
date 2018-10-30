<?php foreach ($data['biz_code_limit_new'] as $key=>$value){ ?>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-4 control-label"><?php echo $value ?></label>
        <div class="col-sm-8">
            <div class="input-group">
                <input type="number" class="form-control" name="<?php echo $key ?>[per_time]" value="<?php echo $data['limit_list'][$key]['per_time'] >=0 ? $data['limit_list'][$key]['per_time'] : '' ?>">
                <span class="input-group-addon" style="min-width: 80px;border-left: 0;border-right: 0">$ Per Time</span>
                <input type="number" class="form-control" name="<?php echo $key ?>[per_day]" value="<?php echo $data['limit_list'][$key]['per_day'] >=0 ? $data['limit_list'][$key]['per_day'] : ''?>">
                <span class="input-group-addon" style="min-width: 80px;border-left: 0">$ Per Day</span>
            </div>
        </div>
    </div>
<?php }?>
