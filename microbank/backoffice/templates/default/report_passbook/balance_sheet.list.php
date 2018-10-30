<?php
$nbsp = '&nbsp;';
$nbsp_len = strlen($nbsp);
$asset_data = $data['asset'];
$liabilities_data = $data['liabilities'];
$currency = (new currencyEnum())->toArray();
?>
<style>
    .amount {
        text-align: right;
    }
</style>
<div class="row">

    <div class="col-sm-6">
        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-money"></i>Assets</h5>
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
                    <?php foreach( $asset_data['list'] as $v ){ ?>
                        <tr>
                            <td>
                                <?php
                                echo str_pad('',($v['level']-1)*4*$nbsp_len,$nbsp,STR_PAD_LEFT).
                                    $v['book_name'].'('.$v['book_code'].')';
                                ; ?>
                            </td>
                            <?php foreach($currency as $ccy){ ?>
                                <td class="amount">
                                    <?php if( $v['multi_currency'][$ccy] != 0 ){ ?>
                                        <a href="<?php echo getUrl('report_passbook','balanceSheetDetail',array(
                                            'book_code' => $v['book_code'],
                                            'currency' => $ccy
                                        ),false,BACK_OFFICE_SITE_URL); ?>" class="<?php if($v['multi_currency'][$ccy] < 0 ){echo 'negative-number';}?>">
                                            <?php echo ncPriceFormat($v['multi_currency'][$ccy]); ?>

                                        </a>
                                    <?php }else{ ?>
                                        -
                                    <?php } ?>

                                </td>
                            <?php }?>
                            <td class="amount <?php if($v['multi_currency']['total'] < 0 ){echo 'negative-number';}?>"><?php echo $v['multi_currency']['total'] != 0 ? ncPriceFormat($v['multi_currency']['total']): '-';?></td>
                        </tr>

                    <?php } ?>
                    <tr class="total">
                        <td align="right">Total</td>
                        <?php foreach($currency as $ccy){ ?>
                            <td><?php echo ncPriceFormat($asset_data['total_amount'][$ccy]); ?></td>
                        <?php }?>
                        <td><?php echo ncPriceFormat($asset_data['total_amount']['total']);?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="basic-info">
            <div class="ibox-title">
                <h5><i class="fa fa-money"></i>Liabilities &amp; Equities</h5>
            </div>
            <div class="content">
                <table id="table_right" class="table  table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <?php foreach($currency as $v){ ?>
                            <th class="c-<?php echo strtolower($v);?>"><?php echo $v;?></th>
                        <?php }?>
                        <th>Total(<?php echo currencyEnum::USD;?>)</th>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php foreach( $liabilities_data['list'] as $v ){ ?>
                        <tr>
                            <td>
                                <?php
                                echo str_pad('',($v['level']-1)*4*$nbsp_len,$nbsp,STR_PAD_LEFT).
                                    $v['book_name'].'('.$v['book_code'].')';
                                ; ?>
                            </td>
                            <?php foreach($currency as $ccy){ ?>
                                <td class="amount">
                                    <?php if( $v['multi_currency'][$ccy] != 0 ){ ?>
                                        <a href="<?php echo getUrl('report_passbook','balanceSheetDetail',array(
                                            'book_code' => $v['book_code'],
                                            'currency' => $ccy
                                        ),false,BACK_OFFICE_SITE_URL); ?>" class="<?php if($v['multi_currency'][$ccy] < 0 ){echo 'negative-number';}?>">
                                            <?php echo ncPriceFormat($v['multi_currency'][$ccy]); ?>

                                        </a>
                                    <?php }else{ ?>
                                        -
                                    <?php } ?>

                                </td>
                            <?php }?>
                            <td class="amount <?php if($v['multi_currency']['total'] < 0 ){echo 'negative-number';}?>"><?php echo $v['multi_currency']['total'] != 0 ? ncPriceFormat($v['multi_currency']['total']): '-';?></td>
                        </tr>

                    <?php } ?>
                    <tr>
                        <td>
                            Profit/Loss
                        </td>
                        <?php foreach($currency as $ccy){ ?>
                            <td class="amount"><?php echo ncPriceFormat($liabilities_data['total_profit_loss'][$ccy]); ?></td>
                        <?php }?>
                        <td class="amount"><?php echo ncPriceFormat($liabilities_data['total_profit_loss']['total']); ?></td>
                    </tr>
                    <tr class="total">
                        <td align="right">Total</td>
                        <?php foreach($currency as $ccy){ ?>
                            <td><?php echo ncPriceFormat($liabilities_data['total_amount'][$ccy]+$liabilities_data['total_profit_loss'][$ccy]); ?></td>
                        <?php }?>
                        <td><?php echo ncPriceFormat($liabilities_data['total_amount']['total'] + $liabilities_data['total_profit_loss']['total']);?></td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script>
    function adjustTrLength(){
        //两边调成一样高
        var _len1=$("#table_left").find("tr").length;
        var _len2=$("#table_right").find("tr").length;
        var _new_tr='<tr><td>&nbsp;</td><td></td><td></td><td></td></tr>';
        if(_len1>_len2){
            var _diff=_len1-_len2;
            for(var _i=0;_i<_diff;_i++){
                var _eq=parseInt(_i)+_len2-2;
                $("#table_right tr:eq("+_eq+")").after(_new_tr);
            }
        }

        if(_len1<_len2){
            var _diff=_len2-_len1;
            for(var _i=0;_i<_diff;_i++){
                var _eq=parseInt(_i)+_len1-2;
                $("#table_left tr:eq("+_eq+")").after(_new_tr);
            }
        }

    }
</script>
