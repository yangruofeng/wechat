<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Exchange</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Overview</span></a></li>
                <li><a onclick="javascript:history.back(-1);">Back</a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php
        $nbsp = '&nbsp;';
        $nbsp_len = strlen($nbsp);
        $data = $output['data'];
        ?>
        <div class="col-sm-6">
            <div class="basic-info">
                <div class="content">
                    <table id="table_left" class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>From Currency</th>
                            <th>Amount</th>
                            <th>To Currency</th>
                            <th>Exchange Amount</th>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php foreach( $data as $k => $v ){ ?>
                            <tr>
                                <td><?php echo $v['item'] ?></td>
                                <td><?php echo $v['from_currency'] ?></td>
                                <td><?php echo ncPriceFormat($v['amount']) ?></td>
                                <td><?php echo $v['to_currency'] ?></td>
                                <td><?php echo ncPriceFormat($v['exchange_amount']) ?></td>
                            </tr>

                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
