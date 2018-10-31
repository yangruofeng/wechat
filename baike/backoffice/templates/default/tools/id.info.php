<?php $info = $data['data']; ?>
<?php if($info){?>
    <div>
        <table class="table contract-table">
            <tbody class="table-body">
            <tr>
                <td><label class="control-label">Id-Sn</label></td>
                <td><?php echo $info['id_sn'] ?></td>
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
                <td><label class="control-label">Id Type</label></td>
                <td><?php echo $info['id_type'] == 1 ? "Foreign Country" : "Homeland"; ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Gender</label></td>
                <td><?php echo ucwords($info['gender']); ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Date of Birth</label></td>
                <td><?php echo dateFormat($info['birthday']); ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Nationality</label></td>
                <td><?php echo strtoupper($info['nationality']); ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Cert Address</label></td>
                <td><?php echo $info['address_detail']; ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Cert Expire Time</label></td>
                <td><?php echo timeFormat($info['id_expire_time']); ?></td>
            </tr>
            <tr>
                <td><label class="control-label">Member State</label></td>
                <td><?php echo $lang['client_member_state_' . $info['member_state']];?></td>
            </tr>
            </tbody>
        </table>
    </div>
<?php }else{?>
    <div style="padding: 10px 10px">Unused</div>
<?php }?>

