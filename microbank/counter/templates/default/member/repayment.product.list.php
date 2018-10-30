<div>
    <input type="hidden" id="client_icon" value="<?php echo $data['client_info']['member_icon']?>">
    <input type="hidden" id="client_account" value="<?php echo $data['client_info']['login_code']?>">
    <input type="hidden" id="client_kh_name" value="<?php echo $data['client_info']['kh_display_name']?>">
    <input type="hidden" id="client_en_name" value="<?php echo $data['client_info']['display_name']?>">
    <input type="hidden" id="client_grade" value="<?php echo $data['client_info']['grade_code']?>">
    <input type="hidden" id="client_state" value="<?php echo $lang['client_member_state_' . $data['client_info']['member_state']]?>">
    <table class="table">
        <thead>
        <tr style="background-color: #DEDEDE">
            <td>Product Name</td>
            <td>Product Type</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody>

        <?php if ($data['data']) { ?>
            <?php foreach ($data['data'] as $k1 => $row) { ?>
                <tr>
                    <td>
                        <?php echo $row['sub_product_name'] ?>
                    </td>
                    <td>
                        <?php echo $row['product_name'] ?>
                    </td>
                    <td>
                        <button class="btn btn-default" onclick="showRepaymentProductDetail(<?php echo $data['client_info']['uid'] ?>,<?php echo $k1 ?>)"><i class="fa fa-address-card-o"></i>Detail</button>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

