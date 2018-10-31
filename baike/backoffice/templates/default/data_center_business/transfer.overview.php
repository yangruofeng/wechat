<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Withdraw</h3>
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
        $currency = (new currencyEnum())->toArray();
        $member_data = $output['member_data']['list'];
        $member_total = $output['member_data']['total'];
        $obj_data = $output['obj_data']['list'];
        $obj_total = $output['obj_data']['total'];
        ?>

        <div class="col-sm-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h5 class="panel-title">Member</h5>
                </div>
                <div class="content">
                    <table id="table_left" class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <?php foreach($currency as $ccy){ ?>
                                <th class="c-<?php echo strtolower($ccy);?>"><?php echo $ccy;?></th>
                            <?php }?>
                            <th>Total(<?php echo currencyEnum::USD;?>)</th>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php foreach( $member_data as $k => $v ){ ?>
                            <tr>
                                <td>
                                    <?php echo $k ?>
                                </td>
                                <?php foreach($currency as $ccy){ ?>
                                    <td class="amount">
                                        <?php if( $v['amount'][$ccy] != 0 ){ ?>
                                            <?php echo ncPriceFormat($v['amount'][$ccy]); ?>
                                        <?php }else{ ?>
                                            -
                                        <?php } ?>

                                    </td>
                                <?php }?>
                                <td class="amount <?php if($v['amount']['total_to_usd'] < 0 ){echo 'negative-number';}?>"><?php echo $v['amount']['total_to_usd'] != 0 ? ncPriceFormat($v['amount']['total_to_usd']): '-';?></td>
                            </tr>

                        <?php } ?>
                        <tr style="font-weight: bold;border-top: solid 2px #000">
                            <td align="right">Total</td>
                            <?php foreach($currency as $ccy){ ?>
                                <td><?php echo ncPriceFormat($member_total[$ccy]); ?></td>
                            <?php }?>
                            <td><?php echo ncPriceFormat($member_total['total_to_usd']);?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h5 class="panel-title">Object</h5>
                </div>
                <div class="content">
                    <table id="table_left" class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <?php foreach($currency as $ccy){ ?>
                                <th class="c-<?php echo strtolower($ccy);?>"><?php echo $ccy;?></th>
                            <?php }?>
                            <th>Total(<?php echo currencyEnum::USD;?>)</th>
                        </tr>
                        </thead>
                        <tbody class="table-body">
                        <?php foreach( $obj_data as $k => $v ){ ?>
                            <tr>
                                <td>
                                    <?php echo $k ?>
                                </td>
                                <?php foreach($currency as $ccy){ ?>
                                    <td class="amount">
                                        <?php if( $v['amount'][$ccy] != 0 ){ ?>
                                            <?php echo ncPriceFormat($v['amount'][$ccy]); ?>
                                        <?php }else{ ?>
                                            -
                                        <?php } ?>

                                    </td>
                                <?php }?>
                                <td class="amount <?php if($v['amount']['total_to_usd'] < 0 ){echo 'negative-number';}?>"><?php echo $v['amount']['total_to_usd'] != 0 ? ncPriceFormat($v['amount']['total_to_usd']): '-';?></td>
                            </tr>

                        <?php } ?>
                        <tr style="font-weight: bold;border-top: solid 2px #000">
                            <td align="right">Total</td>
                            <?php foreach($currency as $ccy){ ?>
                                <td><?php echo ncPriceFormat($obj_total[$ccy]); ?></td>
                            <?php }?>
                            <td><?php echo ncPriceFormat($obj_total['total_to_usd']);?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
