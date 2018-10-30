<?php if (!empty($data['list'])) { ?>
    <div class="col-sm-6">
        <select class="form-control" name="id<?php echo reset($data['list'])['node_level']?>">
            <option value="0">Please Select</option>
            <?php foreach ($data['list'] as $area) { ?>
                <option value="<?php echo $area['uid'] ?>" is-leaf="<?php echo $area['is_leaf'] ?>"><?php echo $area['node_text'] ?></option>
            <?php } ?>
        </select>
    </div>
<?php } else { echo ' ';}?>