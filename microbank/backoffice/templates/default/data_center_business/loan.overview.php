<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Loan</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Overview</span></a></li>
                <li><a onclick="javascript:history.back(-1);">Back</a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php
        $loan_arr = $output['data']['loan_arr'];
        $total_loan = $output['data']['total_loan'];
        $currency_list = $output['data']['currency_list'];
        ?>
        <div class="panel panel-info" style="width: 700px;float: left;margin-right: 20px">
            <div class="panel-heading">
                <h5 class="panel-title">Group By Category</h5>
            </div>
            <table class="table table-hover">
                <tr class="table-header">
                    <td>Category</td>
                    <?php foreach ($currency_list as $key => $currency) { ?>
                        <td><?php echo $currency ?></td>
                    <?php } ?>
                    <td>Total(USD)</td>
                </tr>
                <?php foreach($loan_arr as $item){?>
                    <tr>
                        <td><?php echo $item['category_name']?></td>
                        <?php foreach ($currency_list as $key => $currency) { ?>
                            <td><?php echo ncPriceFormat($item[$key])?></td>
                        <?php } ?>
                        <td><?php echo ncPriceFormat($item['usd_total'])?></td>
                    </tr>
                <?php }?>
                <tr style="font-weight: bold;border-top: solid 2px #000">
                    <td> Total </td>
                    <?php foreach ($currency_list as $key => $currency) { ?>
                        <td><?php echo ncPriceFormat($total_loan[$key])?></td>
                    <?php } ?>
                    <td><?php echo ncPriceFormat($total_loan['usd_total'])?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
