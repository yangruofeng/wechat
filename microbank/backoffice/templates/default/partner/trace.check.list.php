<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Type'; ?></td>
            <td><?php echo 'Amount'; ?></td>
            <td><?php echo 'Account id'; ?></td>
            <td><?php echo 'Account name'; ?></td>
            <td><?php echo 'Is Manual'; ?></td>
            <td><?php echo 'State'; ?></td>
            <td><?php echo 'Remark'; ?></td>
            <td><?php echo 'Handler(Creator)'; ?></td>
            <td><?php echo 'Time'; ?></td>
            <td><?php echo 'Function'; ?></td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php foreach($data['data'] as $row){?>
            <tr>
                <td><?php echo ucwords($row['trx_type']); ?></td>
                <td><?php echo ($row['trx_flag'] == -1 ? '-' : ($row['trx_flag'] == 1 ? '+' : '')) . ncAmountFormat($row['trx_amount'], false, $data['currency']); ?></td>
                <td><?php echo $row['partner_account_id']; ?></td>
                <td><?php echo $row['partner_account_name']; ?></td>
                <td><span><i class="fa fa-<?php echo $row['is_manual'] == 1 ?'check':'close ';?>"></i></span></td>
                <td><?php echo $row['api_state'] == '11' ? 'Failure' : 'Success'; ?></td>
                <td><?php echo $row['remark']; ?></td>
                <td><?php echo $row['operator_name']?:$row['creator_name']; ?></td>
                <td><?php echo timeFormat($row['trx_time']); ?></td>
                <td>
                    <?php if ($row['is_manual']) { ?>
                        <a onclick="<?php echo ($row['trx_type'] == 'plus' || $row['trx_type'] == 'minus') ? 'edit_adjust(this)' : 'edit_manual(this)'?>" href="javascript:void(0)"
                           trace_id="<?php echo $row['uid']?>"
                           currency="<?php echo $row['currency']?>"
                           amount="<?php echo $row['trx_amount']?>"
                           trx_type="<?php echo $row['trx_type']?>"
                           operator_name="<?php echo $row['operator_name']?>"
                           trx_time="<?php echo date('Y-m-d H:i',strtotime($row['trx_time']))?>"
                           remark="<?php echo $row['remark']?>"
                           api_state="<?php echo $row['api_state']?>"
                            ><i class="fa fa-edit"></i>Edit</a>
                    <?php } else { ?>
                        <!--<a onclick="change_state('<?php echo $row['uid'] ?>','<?php echo $row['api_state'] ?>')" href="javascript:void(0)"><i class="fa fa-exchange"></i>Change State</a>-->
                    <?php } ?>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>
