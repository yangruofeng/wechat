
    <div>
     <table class="table">
        <thead>
        <tr style="background-color: rgb(246, 246, 246)">
            <td>Member Code</td>
            <td>Currency</td>
            <td>Amount</td>
            <td>Operate Time</td>
        </tr>
        </thead>
        <tbody>
        <?php if($data['data']){ ?>
             <?php foreach ($data['data'] as $row) { ?>
                <tr>
                    <td><i class="fa fa-file-powerpoint-o" onclick="print_deposit(<?php echo $row['uid']?>)" style="cursor:pointer" title="Print"></i>
                        <?php echo $row['login_code'] ?>
                    </td>
                    <td>
                        <?php echo $row['currency'] ?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['amount']) ?>
                    </td>
                    <td>
                        <?php echo $row['update_time'] ?>
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
    <?php include_once(template("widget/inc_content_pager"));?>

