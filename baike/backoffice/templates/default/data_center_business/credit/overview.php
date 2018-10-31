<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Credit</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Overview</span></a></li>
                <li><a onclick="javascript:history.back(-1);">Back</a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $credit_group = $output['credit_group']; ?>
        <div class="panel panel-info" style="width: 700px;float: left;margin-right: 20px">
            <div class="panel-heading">
                <h5 class="panel-title">Group By Category</h5>
            </div>
            <table class="table table-hover">
                <tr class="table-header">
                    <td>Category</td>
                    <td>Credit</td>
                    <td>Credit Balance</td>
                </tr>
                <?php foreach($credit_group['credit_arr'] as $item){?>
                    <tr>
                        <td><?php echo $item['category_name']?></td>
                        <td><?php echo ncPriceFormat($item['credit'])?></td>
                        <td><?php echo ncPriceFormat($item['credit_balance'])?></td>
                    </tr>
                <?php }?>
                <tr style="font-weight: bold;border-top: solid 2px #000">
                    <td> Total </td>
                    <td><?php echo ncPriceFormat($credit_group['total_credit'])?></td>
                    <td><?php echo ncPriceFormat($credit_group['total_credit_balance'])?></td>
                </tr>
            </table>
        </div>

        <?php $pending_credit = $output['pending_credit']; ?>
        <div class="panel panel-info" style="width: 400px;float: left;">
            <div class="panel-heading">
                <h5 class="panel-title">Pending Credit</h5>
            </div>
            <table class="table table-hover">
                <tr class="table-header">
                    <td>Category</td>
                    <td>Pending Credit</td>
                </tr>
                <?php foreach($pending_credit['pending_credit'] as $item){?>
                    <tr>
                        <td><?php echo $item['category_name']?></td>
                        <td><?php echo ncPriceFormat($item['credit'])?></td>
                    </tr>
                <?php }?>
                <tr style="font-weight: bold;border-top: solid 2px #000">
                    <td> Pending Total </td>
                    <td><?php echo ncPriceFormat($pending_credit['total_credit'])?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
