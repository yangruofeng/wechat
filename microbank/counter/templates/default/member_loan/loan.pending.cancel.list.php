<div>
    <table class="table">
        <thead>
        <tr style="background-color: rgb(246, 246, 246)" >
            <td>Contract Sn</td>
            <td>Product Name</td>
            <td>Currency</td>
            <td>Amount</td>
            <td>Function</td>
        </tr>
        </thead>
        <tbody>
        <?php if($data['data']){ ?>
            <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td>
                     <?php echo $row['contract_sn'] ?>
                    </td>
                    <td>
                        <?php echo $row['alias'] ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['apply_amount']) ?>
                    </td>
                    <td>
                        <button style="padding: 5px 10px;min-width: 70px" class="btn btn-danger" onclick="cancel(<?php echo $row['uid'] ?>)">Cancel</button>
                    </td>
                </tr>
            <?php }?>
        <?php }else{ ?>
            <tr>
                <td colspan="4">No Record</td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>

