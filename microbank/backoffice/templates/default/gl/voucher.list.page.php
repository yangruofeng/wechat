<?php $data=$data['data'];?>
<div>
    <table class="table">
        <tr class="table-header">
            <td>Time</td>
            <td>Currency</td>
            <td>Amount</td>
            <td>Remark</td>
            <td>Operator</td>
            <td>+/-</td>

        </tr>
        <?php foreach($data as $row){?>
            <tr>
                <td><?php echo $row['update_time'];?>   </td>
                <td><?php echo $row['currency'];?>   </td>
                <td><?php echo $row['amount'];?>   </td>
                <td><?php echo $row['remark'];?>   </td>
                <td><?php echo $row['operator_name'];?> </td>
                <td>
                    <button class="btn btn-default" data-uid="<?php echo $row['uid']?>" onclick="expendVoucherDetail(this)">
                        <i class="fa btn-i-style fa-chevron-circle-right"></i>
                    </button>
                </td>
            </tr>
            <tr class="tr-detail-<?php echo $row['uid']?>" >
                <td colspan="10">
                    <table class="table table-no-background">

                        <?php foreach($row['detail'] as $item){?>
                            <tr>
                                <td><?php if($item['is_debit']){ echo 'Dr';}else{echo 'Cr';}?></td>
                                <td><?php echo $item['gl_code']?></td>
                                <td><?php echo $item['gl_name']?></td>
                                <td><?php echo $item['gl_subject']?></td>
                                <?php if($item['is_debit']){?>
                                    <td><?php echo $item['gl_amount']?></td>
                                    <td></td>
                                <?php }else{?>
                                    <td></td>
                                    <td><?php echo $item['gl_amount']?></td>
                                <?php }?>
                            </tr>
                        <?php }?>
                    </table>
                </td>

            </tr>
        <?php }?>
    </table>
</div>
<?php include_once(template("widget/inc_content_pager"));?>