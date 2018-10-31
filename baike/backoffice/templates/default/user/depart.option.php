<option value="" selected="selected">Select Department</option>
<?php foreach($data['data'] as $depart){?>
    <option value="<?php echo $depart['uid']?>" <?php echo $data['depart_id'] == $depart['uid'] ? 'selected' : ''?>><?php echo $depart['depart_name']?></option>
<?php }?>