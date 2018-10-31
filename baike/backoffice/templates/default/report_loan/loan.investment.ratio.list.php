<div>
    <table class="table">
        <thead>
        <tr class="table-header">
            <td>Product Code</td>
            <td>Description</td>
            <td>No.of Accounts</td>
            <td>Balance</td>
            <td>Investment Ratio(%)</td>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php $sub_product_list = $data['sub_product_list'];?>
        <?php $list = $data['data'];?>
        <?php if ($sub_product_list) { ?>
            <?php foreach ($sub_product_list as $product) {
                $row = $list[$product['uid']]; ?>
                <tr>
                    <td>
                        <?php echo $product['sub_product_code']?>
                    </td>
                    <td>
                        <?php echo $product['sub_product_name']?>
                    </td>
                    <td>
                        <?php echo $row['loan_count']?>
                    </td>
                    <td>
                        <?php echo $row['loan_amount']?>
                    </td>
                    <td>
                        <?php echo ncPriceFormat($row['investment_ratio'])?>
                    </td>
                </tr>
            <?php } ?>
            <tr class="total_amount border_top">
                <td></td>
                <td><?php echo 'Totals' ?></td>
                <td><?php echo $data['amount_total']['loan_count'] ?></td>
                <td class=""><?php echo ncPriceFormat($data['amount_total']['loan_amount']) ?></td>
                <td class=""><?php echo 100 ?></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="5">No Record</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>