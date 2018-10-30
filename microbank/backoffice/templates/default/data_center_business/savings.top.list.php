<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Current Savings</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Top 100 (<?php echo $output['currency'];?>)</span></a></li>
                <li><a onclick="javascript:history.back(-1);">Back</a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $top = $output['list']; ?>
        <div class="panel panel-info" style="width: 800px;">
            <div class="panel-heading">
                <h5 class="panel-title">Top 100 (<?php echo $output['currency'];?>)</h5>
            </div>
            <table class="table table-hover">
                <tr class="table-header">
                    <td></td>
                    <td>Member Name</td>
                    <td>CID</td>
                    <td>Phone</td>
                    <td>Branch Name</td>
                    <td>Balance</td>
                </tr>
                <?php $i = 0;
                foreach ($top as $item) {
                    ++$i; ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $item['login_code']?:$item['display_name'] ?></td>
                        <td><?php echo $item['obj_guid'] ?></td>
                        <td><?php echo $item['phone_id'] ?></td>
                        <td><?php echo $item['branch_name'] ?></td>
                        <td style="text-align:right"><?php echo ncPriceFormat($item['balance']) ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
