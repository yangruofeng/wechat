<?php $loan_principal = $item['loan_principal']; ?>
<?php $outstanding_principal = $item['outstanding_principal']; ?>
<?php $savings_balance = $output['savings_balance'] ?: $data['savings_balance'] ?>
<table class="table table-bordered">
    <tr>
        <td><label class="control-label">Account(GUID)</label></td>
        <td>
            <?php echo $item['login_code']; ?>
            <span class="green"> (<?php echo $item['obj_guid']; ?>)</span>
        </td>
        <td><label class="control-label">English Name</label></td>
        <td><?php echo $item['display_name']; ?></td>
        <td><label class="control-label">Khmer Name</label></td>
        <td><?php echo $item['kh_display_name']; ?></td>
    </tr>
    <tr>
        <td><label class="control-label">Phone</label></td>
        <td><?php echo $item['phone_id']; ?></td>
        <td><label class="control-label">Grade</label></td>
        <td><?php echo $item['grade_code']; ?></td>
        <td><label class="control-label">Status</label></td>
        <td><?php echo $lang['client_member_state_' . $item['member_state']]; ?></td>
    </tr>
    <tr>
        <td><label class="control-label">Branch</label></td>
        <td><?php echo $item['branch_name']; ?></td>
        <td><label class="control-label">Operator</label></td>
        <td><?php echo $item['operator']['officer_name']; ?></td>
        <td><label class="control-label">Credit Officer</label></td>
        <td>
            <?php
            $co_list = array_column($item['member_co_list'], 'officer_name');
            echo implode(', ', $co_list);
            ?>
        </td>
    </tr>
    <tr>
        <td><label class="control-label">Credit Limit</label></td>
        <td><?php echo ncPriceFormat($credit_info['credit']) . ' ' .currencyEnum::USD; ?></td>
        <td><label class="control-label">Credit Balance</label></td>
        <td><?php echo ncPriceFormat($credit_info['balance']) . ' ' . currencyEnum::USD; ?></td>
        <td><label class="control-label">Expire Time</label></td>
        <td><?php echo dateFormat($item['expire_time']); ?></td>
    </tr>
    <tr>
        <td><label class="control-label">All Enquiries</label></td>
        <td><?php echo $contract_info['all_enquiries'] ?: '-'; ?></td>
        <td><label class="control-label">Earliest Loan Issue Date</label></td>
        <td><?php echo dateFormat($contract_info['earliest_loan_issue_date']) ?: '-'; ?></td>
        <td><label class="control-label">Contracts Number</label></td>
        <td><?php echo $loan_summary['contract_num_summary']['total_contracts']; ?></td>
    </tr>
    <tr>
        <td><label class="control-label">Loan Principal</label></td>
        <td>
            <?php $loan_principal = $item['loan_principal'];?>
            <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                <span class="currency"><?php echo ncPriceFormat($loan_principal[$key]) ?></span>
                <span><?php echo $currency ?></span>
                <br/>
            <?php } ?>
        </td>
        <td><label class="control-label">Outstanding Principal</label></td>
        <td>
            <?php $outstanding_principal = $item['outstanding_principal'];?>
            <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                <span class="currency"><?php echo ncPriceFormat($outstanding_principal[$key]) ?></span>
                <span><?php echo $currency ?></span>
                <br/>
            <?php } ?>
        </td>
        <td><label class="control-label">Savings</label></td>
        <td>
            <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                <span class="currency"><?php echo ncPriceFormat($savings_balance[$key]) ?></span>
                <span><?php echo $currency ?></span>
                <br/>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td rowspan="2"><label>Icon</label></td>
        <td rowspan="2">
            <img
                src="<?php echo getImageUrl($item['member_icon'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg' ?>"
                class="avatar-lg">
        </td>
        <td><label>ID-Card Address</label></td>
        <td colspan="3">
            <?php echo $item['address_detail']; ?>
        </td>
    </tr>
    <tr>
        <td><label>Residence Address</label></td>
        <td colspan="3">
            <?php echo $item['residence']['full_text']; ?>
            <?php if ($item['residence']['coord_x'] > 0) {
                $residence_array = array(
                    0 => array('x' => $item['residence']['coord_x'], 'y' => $item['residence']['coord_y']),
                );
                $residence_json = my_json_encode($residence_array)
                ?>
                <a href="javascript:void(0)" onclick="showGoogleMap()" style="margin-left: 10px;font-style: italic">Google Map</a>
            <?php } ?>
        </td>
    </tr>
</table>
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 1000px;height: 660px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo 'Residence Location'?></h4>
            </div>
            <div class="modal-body">
                <div id="map-canvas">
                    <?php
                    $point=array('x' => $item['residence']['coord_x'], 'y' => $item['residence']['coord_y']);
                    include_once(template("widget/google.map.point"));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function showGoogleMap() {
        $('#myModal').modal('show');
    }
</script>
