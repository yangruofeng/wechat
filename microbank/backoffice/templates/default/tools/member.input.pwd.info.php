<?php $info = $data['data']; ?>
<?php if($info){?>
    <div>
        <table class="table contract-table">
            <tbody class="table-body">
            <tr>
                <td><label class="control-label">Icon</label></td>
                <td>
                    <img id="member-icon" src="<?php echo getImageUrl($info['member_icon']);?>" class="avatar-lg">
                </td>
            </tr>
            <tr>
                <td><label class="control-label">Client-Account</label></td>
                <td><?php echo $info['login_code']?></td>
            </tr>
            <tr>
                <td><label class="control-label">English Name</label></td>
                <td>
                    <?php echo implode(' ', my_json_decode($info['id_en_name_json'])); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="control-label">Khmer Name</label>
                </td>
                <td>
                    <?php echo implode(' ', my_json_decode($info['id_kh_name_json'])); ?>
                </td>
            </tr>
            <tr>
                <td><label class="control-label">Member State</label></td>
                <td><?php echo $lang['client_member_state_' . $info['member_state']];?></td>
            </tr>
            <tr>
                <td><label class="control-label">Trading password error times</label></td>
                <td><?php echo $info['today_error_times']?></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center">
                    <button type="button" class="btn btn-danger" onclick="clear_times('<?php echo $info['uid']?>')" <?php echo $info['today_error_times'] == 0 ? 'disabled' : ''?>><i class="fa fa-close"></i><?php echo 'Clear' ?></button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php }else{?>
    <div style="padding: 10px 10px">Null</div>
<?php }?>

