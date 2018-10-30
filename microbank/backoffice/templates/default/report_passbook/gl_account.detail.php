<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .total{
        background-color: red !important;
    }
    .total td{
        font-size: 18px;
        color:#fff;
    }
</style>
<?php
$nbsp = '&nbsp;';
$nbsp_len = strlen($nbsp);
$gl_account = $output['gl_account'];
$currency = $output['currency'];
$data = $output['data'];
$total_amount = $data['total_amount'];
$list = $data['list'];
?>

<div class="page">

    <div class="fixed-bar">
        <div class="item-title">
            <h3><?php if( $output['report_type'] == 'income' ){ echo 'Income Statement';}else{ echo 'Balance Sheet';} ?></h3>
            <ul class="tab-base">
                <?php $op = 'balanceSheet'; if($output['main_url']){$op = $output['main_url'];}?>
                <li><a  href="<?php echo getUrl('report_passbook',$op,array(),false,BACK_OFFICE_SITE_URL); ?>"><span>Main</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition">

            </form>
        </div>

        <div class="business-content">
            <div class="business-list">

            </div>
        </div>

        <div class="row">

            <div class="col-sm-8">
                <div class="basic-info">
                    <div class="ibox-title">
                        <h5>Detail</h5>
                        <button type="button" class="btn btn-info report-back" onclick="javascript:history.go(-1);">
                            Back
                        </button>
                    </div>
                    <div class="col-sm-12 user-info">
                        <?php if($gl_account){?>
                            <div class="col-sm-3">
                                Book Code: <label for=""><?php echo $gl_account['book_code'];?></label>
                            </div>
                            <div class="col-sm-3">
                                Book Name: <label for=""><?php echo $gl_account['book_name'];?></label>
                            </div>
                            <div class="col-sm-3">
                                Category: <label for=""><?php echo $gl_account['category'];?></label>
                            </div>
                        <?php }else{?>
                            <div class="tip">* The gl account does not exist or has been deleted</div>
                        <?php }?>
                    </div>
                    <div class="content">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th class=""><?php echo $currency; ?></th>
                            </tr>
                            </thead>
                            <tbody class="table-body">
                            <?php foreach( $list as $v ){ ?>
                                <tr>
                                    <td>
                                        <?php
                                        echo str_pad('',($v['level']-1)*4*$nbsp_len,$nbsp,STR_PAD_LEFT).
                                            ($gl_account['obj_type']<=0 ?$v['book_name'].'('.$v['book_code'].')':$v['passbook_book_name']);
                                        ; ?>
                                    </td>
                                    <td>

                                        <?php if( $v['multi_currency'][$currency] != 0 ){ ?>

                                            <?php if( $v['is_leaf'] || $v['is_leaf_book'] ){ ?>
                                                <a href="<?php echo getUrl('report_passbook','reportAccountFlow',array(
                                                    'book_id' => $v['book_id'],
                                                    'currency' => $currency,
                                                    'type' => $output['report_type']
                                                ),false,BACK_OFFICE_SITE_URL); ?>">
                                                    <?php echo ncPriceFormat($v['multi_currency'][$currency]); ?>

                                                </a>
                                            <?php }else{ ?>
                                                <a href="<?php echo getUrl('report_passbook','balanceSheetDetail',array(
                                                    'book_code' => $v['book_code'],
                                                    'currency' => $currency,
                                                    'type' => $output['report_type']
                                                ),false,BACK_OFFICE_SITE_URL); ?>">
                                                    <?php echo ncPriceFormat($v['multi_currency'][$currency]); ?>

                                                </a>
                                            <?php } ?>

                                        <?php }else{ ?>
                                            --
                                        <?php } ?>

                                    </td>

                                </tr>

                            <?php } ?>
                            <tr class="total">
                                <td align="right">Total</td>
                                <td><?php echo ncPriceFormat($total_amount[$currency]); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



        </div>

    </div>

</div>
<script>


</script>
