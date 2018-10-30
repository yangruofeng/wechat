<?php
$loanPenaltyReceiptStateLang = enum_langClass::getLoanPenaltyReceiptStateLang();
?>
<div>
    <form style="margin-bottom: 0">
        <table class="table table-bordered">
            <thead>
            <tr style="background-color: #DEDEDE">
                <td>No.</td>
                <td>Currency</td>
                <td>Due Amount</td>
                <td>Reduce Amount</td>
                <td>Actual Amount</td>
                <td>State</td>
                <td>Create Time</td>
                <td>Function</td>
            </tr>
            </thead>
            <tbody>
            <?php if ($data['data']) {  ?>
                <?php foreach ($data['data']as $key => $row) {; ?>
                    <tr>
                        <td>
                            <?php echo $row['uid'] ?>
                        </td>
                        <td>
                            <?php echo $row['currency'] ?>
                        </td>
                        <td>
                            <?php echo $row['receivable'] ?>
                        </td>
                        <td>
                            <?php echo $row['deducting'] ?>
                        </td>
                        <td>
                            <?php echo $row['paid'] ?>
                        </td>
                        <td>
                            <?php echo ucwords($loanPenaltyReceiptStateLang[$row['state']]) ?>
                        </td>
                        <td>
                            <?php echo $row['create_time'] ?>
                        </td>
                        <td>
                           <?php if($row['state']=='20'){?>
                               <a class="btn btn-primary" onclick="receive_money(<?php echo $row['uid']?>,<?php echo $output['member_id']?>)">Receive Money</a>
                           <?php } else{?>
                               <a disabled class="btn btn-default">Receive Money</a>
                           <?php } ?>

                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="8">No Record</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
</div>
<script>
    $(function () {
        penalty_select();
    })

    function penalty_select() {
        $('.currency_total').each(function () {
            var currency = $(this).attr('currency');
            var _total = 0;
            $('.checkbox-amount[currency="' + currency + '"]').each(function () {
                var _amount = $(this).attr('amount');
                _total += Number(_amount);
            })
            $(this).text(_total);
        })
    }
</script>