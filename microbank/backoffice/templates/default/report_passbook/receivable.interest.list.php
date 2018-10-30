<?php
$nbsp = '&nbsp;';
$nbsp_len = strlen($nbsp);
$currency = (new currencyEnum())->toArray();
?>

<div class="row">
    <div class="col-sm-12">
        <div class="basic-info">
            <div class="content">
                <table class="table table-hover table-bordered">
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
                        <tr class="fontweight700">
                            <td colspan="3">Individuals<=1 year</td>
                        </tr>
                        <?php foreach( $data['less'] as $k => $v ){ ?>
                            <tr>
                                <td>
                                    <?php
                                        switch($k){
                                            case 'normal':
                                                echo 'AIR - STDL - Individuals<=1 year';
                                                break;
                                            case 'standard':
                                                echo 'AIR - STDL30 - Individuals<=1 year';
                                                break;
                                            case 'substandard':
                                                echo 'AIR - Sub STDL - Individuals<=1 year';
                                                break;
                                            case 'doubtful':
                                                echo 'AIR - DFL - Individuals<=1 year';
                                                break;
                                            case 'loss':
                                                echo 'AIR - LL - Individuals<=1 year';
                                                break;
                                        }
                                    ?>
                                </td>
                                <?php foreach($currency as $ccy){ ?>
                                    <td>
                                        <?php echo $v[$ccy] ? ncPriceFormat($v[$ccy]):'-'; ?>
                                    </td>
                                <?php }?>
                                <td><?php echo $v['total_to_usd'] ? ncPriceFormat($v['total_to_usd']):'0.00'; ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="fontweight700">
                            <td colspan="3">Individuals>1 year</td>
                        </tr>
                        <?php foreach( $data['greater'] as $k => $v ){ ?>
                            <tr>
                                <td>
                                    <?php
                                    switch($k){
                                        case 'normal':
                                            echo 'AIR - STDL - Individuals>1 year';
                                            break;
                                        case 'standard':
                                            echo 'AIR - STDL30 - Individuals>1 year';
                                            break;
                                        case 'substandard':
                                            echo 'AIR - Sub STDL - Individuals>1 year';
                                            break;
                                        case 'doubtful':
                                            echo 'AIR - DFL - Individuals>1 year';
                                            break;
                                        case 'loss':
                                            echo 'AIR - LL - Individuals> year';
                                            break;
                                    }
                                    ?>
                                </td>
                                <?php foreach($currency as $ccy){ ?>
                                    <td>
                                        <?php echo $v[$ccy] ? ncPriceFormat($v[$ccy]):'-'; ?>
                                    </td>
                                <?php }?>
                                <td><?php echo $v['total_to_usd'] ? ncPriceFormat($v['total_to_usd']):'0.00'; ?></td>
                            </tr>
                        <?php } ?>
                    <tr class="total">
                        <td align="right">Total</td>
                        <?php foreach($currency as $ccy){ ?>
                            <td><?php echo ncPriceFormat($data['total'][$ccy]); ?></td>
                        <?php }?>
                        <td><?php echo ncPriceFormat($data['total']['total_to_usd']);?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
