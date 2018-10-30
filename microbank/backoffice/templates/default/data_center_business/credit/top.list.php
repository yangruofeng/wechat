<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Credit</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Top 10</span></a></li>
                <li><a onclick="javascript:history.back(-1);">Back</a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php $credit_top = $output['credit_top']; ?>
        <div class="panel panel-info" style="width: 800px;">
            <div class="panel-heading">
                <h5 class="panel-title">Top 10</h5>
            </div>
            <table class="table table-hover">
                <tr class="table-header">
                    <td></td>
                    <td>Member Name</td>
                    <td>Product Name</td>
                    <td>Credit</td>
                    <td>Creator</td>
                    <td>Create Time</td>
                </tr>
                <?php $i = 0;
                foreach ($credit_top as $item) {
                    ++$i; ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $item['display_name'] ?></td>
                        <td><?php echo $item['alias'] ?></td>
                        <td><?php echo ncPriceFormat($item['credit']) ?></td>
                        <td><?php echo $item['creator_name'] ?></td>
                        <td><?php echo timeFormat($item['update_time']) ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
