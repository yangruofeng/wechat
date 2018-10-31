
<?php $list = $data['data']; ?>
<div class="page">
    <div>
        <?php if( empty($list) ){ ?>
            <div class="order-no-data">No data</div>
        <?php }else{ ?>
            <table class="table">
                <thead style="background-color: #ddd;">
                <tr>
                    <th>Contract SN</th>
                    <th>Client CID</th>
                    <th>Client Name</th>
                    <th>Client Phone</th>
                    <!--<th>Loan Amount</th>
                    <th>Loan Date</th>
                    <th>End Date</th>
                    <th>Contract State</th>-->
                    <th>Apply For Written Off</th>
                    <th>Function</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach( $list as $v ){ ?>
                    <tr>
                        <td><?php echo $v['contract_sn']; ?></td>
                        <td><?php echo $v['member_cid']; ?></td>
                        <td><?php echo $v['display_name'].'/'.$v['kh_display_name']; ?></td>
                        <td><?php echo $v['phone_id']; ?></td>
                        <!--<td><?php /*echo ncPriceFormat($v['apply_amount']).$v['currency']; */?></td>
                        <td><?php /*echo timeFormat($v['start_date']); */?></td>
                        <td><?php /*echo timeFormat($v['end_date']); */?></td>
                        <td><?php /*echo $lang['loan_contract_state_'.$v['state']] */?></td>-->
                        <td><?php echo $v['is_apply_off']?'Yes':'No'; ?></td>
                        <td>
                            <a href="<?php echo getBackOfficeUrl('branch_manager','contractWrittenOffDetail',array('uid'=>$v['uid'])); ?>">
                                <?php echo $v['is_apply_off']?'View':'Apply'; ?>
                            </a>
                        </td>

                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>

    </div>
</div>