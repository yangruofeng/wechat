<option value="0">Select Co</option>
<?php foreach ($data['data'] as $co) { ?>
    <option value="<?php echo $co['uid']?>"><?php echo $co['user_name']?></option>
<?php } ?>