<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'SN.';?></td>
            <td><?php echo 'Type';?></td>
            <td><?php echo 'Total Credit';?></td>
            <td><?php echo 'Fee';?></td>
            <td><?php echo 'Branch';?></td>
            <td><?php echo 'Officer';?></td>
            <td><?php echo 'Time';?></td>

        </tr>
        </thead>
        <tbody class="table-body">
        <?php if($data['data']){ ?>
            <?php foreach($data['data'] as $row){ ?>
                <tr>
                    <td>
                        <?php echo $row['contract_no'] ?>
                    </td>
                    <td>
                        <?php echo $row['contract_type'] == 1 ? 'Mortgage' : 'Redeem'?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['total_credit']) ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['fee']) ?>
                    </td>
                    <td>
                        <?php echo $row['branch_name']; ?>
                    </td>
                    <td>
                        <?php echo $row['update_operator_name'] ?: $row['officer_name']; ?>
                    </td>
                    <td>
                        <?php echo timeFormat($row['update_time']); ?>
                    </td>
                </tr>
            <?php }?>
        <?php }else{ ?>
             <tr>
                 <td colspan="7">
                     <?php include(template(":widget/no_record")); ?>
                 </td>
             </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<hr>
<?php include_once(template("widget/inc_content_pager"));?>
