<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo ENTRY_COUNTER_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>


<style>
    html,body{
        background-color: #fff;

    }

    body *{
        font-size: 14px;
    }
    .btn {
        padding: 5px 12px;
    }
    .red{
        color:red;
    }
    tr{
        background:none !important;
    }

    .b2{
        font-size: 20px;
        font-weight: 700;
    }

    .n2{
        font-size: 20px;
        font-weight: 400;
    }

    .n4{
        font-size: 16px;
        font-weight: 500;
    }

    .b4{
        font-size: 16px;
        font-weight: 700;
    }

    .verify-row{
        width: 100%;
        text-align: center;
        padding: 0 5px;
        margin-top: 10px;
    }

    .verify-column{
        display: inline-block;
        width: 30%;
        text-align: left;
    }

    .verify-column .title{
        display: block;
        font-size: 14px;
        font-weight: 700;
        height: 20px;
    }

    .verify-column .line{
        display: block;
        height: 50px;
        border-bottom: solid 3px #000;
        width: 160px;
    }

    .verify-column .ep{
        display: block;
        text-align: center;
        width: 160px;
        height: 15px;
        margin: 5px 0;
    }
</style>

<?php
$trading_type_lang = enum_langClass::getPassbookTradingTypeLang();
$param = $output['param'];
$user_id = $output['user_id'];
$userObj = new objectUserClass($user_id);
$currency = $param['currency'];
$user_balance = $userObj->getPassbookBalance();
$chief_teller = $output['chief_teller'];
$branch_manager = $output['branch_manager'];
$data = $output['data'];
$list = $data['data'];
$book_id = $data['book_id'];
?>
<div class="page">

    <div>
        <table class="table">
            <tr>
                <td style="width: 33%;vertical-align: top;">
                    <img src="resource/img/login/c-logo.png" >
                </td>
                <td style="width: 33%;vertical-align: top;min-width: 300px;">
                    <div style="text-align: center;">
                        <div class="b2" >
                            SAMRITHISAK LIMITED
                            <br />
                            <?php echo $userObj->branch_name; ?>
                            <br >
                            <?php echo $param['day']; ?>
                        </div>

                        <br>
                        <div class="b4" style="margin-top: 20px;">
                            Teller - Daily Transaction Details
                        </div>
                        <div class="b4">
                            【<?php echo $userObj->user_code; ?>】 <?php echo $userObj->user_name; ?>
                        </div>
                    </div>


                </td>
                <td style="width: 33%;vertical-align: top;" >
                    <div style="text-align: right" class="n2">
                        <?php echo date('d/m/Y',strtotime($param['day'])); ?><br />
                        <?php echo date('H:i:s').' '.date('A'); ?><br />
                        Currency:<?php echo $param['currency']; ?>
                    </div>

                </td>
            </tr>
        </table>


    </div>

    <div class="container">

        <div id="day_voucher_list" class="business-content" style="margin-bottom: 5px">
            <div class="business-list">

                <?php
                $list = $data['data'];
                $book_id = $data['book_id'];
                ?>
                <table class="table">

                    <tr class="b4" >
                        <td></td>
                        <td colspan="2" align="left"></td>
                        <td align="right">Cash</td>
                    </tr>
                    <tr class="b4">
                        <td></td>
                        <td colspan="2" align="left">Balances Brought Forward</td>
                        <td align="right"><?php echo ncPriceFormat($data['balance_before_the_day']); ?></td>
                    </tr>
                    <tr class="b4">
                        <td></td>
                        <td colspan="2" align="left">From: <?php echo $param['day']; ?></td>
                        <td></td>
                    </tr>

                    <tr style="border-bottom:solid 5px #000;" class="b4" >

                        <td  class="number">Trn#</td>
                        <td  class="number" style="min-width: 120px;">Account</td>
                        <td  class="number">Type</td>
                        <td  class="number">Cash</td>
                        <td  class="number">Memo</td>


                    </tr>


                    <?php foreach( $list as $v ){ $first_flow = $v['flow_list'][0]; $count = count($v['flow_list']);  ?>


                        <tr  class="n4">

                            <td rowspan="<?php echo $count;?>" >
                                <?php echo $v['trade_id'];?>
                            </td>


                            <td style="min-width: 120px;">
                                <?php echo $first_flow['book_code'];?>
                            </td>
                            <td rowspan="<?php echo $count;?>" >
                                <?php echo $trading_type_lang[$v['trading_type']]?:ucwords(str_replace('_',' ',$v['trading_type']));?>
                            </td>
                            <td  rowspan="<?php echo $count;?>" class="<?php echo $v['trading_amount']<0?'red':''; ?>">
                                <?php echo ncPriceFormat($v['trading_amount']);?>
                            </td>
                            <td rowspan="<?php echo $count;?>" >
                                <?php echo $v['sys_memo'];?>
                            </td>

                        </tr>

                        <?php if( !empty($v['flow_list']) ){ foreach( $v['flow_list'] as $key=>$item ){ if( $key>0){  ?>
                            <tr class="n4" >


                                <td style="min-width: 120px;">
                                    <?php echo  $item['book_code'];?>
                                </td>

                            </tr>
                        <?php } } } ?>

                    <?php } ?>


                   <!-- <tr >
                        <td colspan="3" align="center">Total</td>
                        <td>
                            <?php /*echo ncPriceFormat($data['total_amount']); */?>
                        </td>
                        <td></td>
                    </tr>-->

                    <tr style="border-top:solid 5px #000;">
                            <td colspan="10"></td>
                    </tr>

                    <tr class="b4">
                        <td></td>
                        <td colspan="2" align="left">
                            Amounts Recorded
                        </td>
                        <td><?php echo ncPriceFormat($data['total_amount']); ?></td>
                    </tr>

                    <tr class="b4">
                        <td></td>
                        <td colspan="2" align="left">
                            Balances Brought Forward
                        </td>
                        <td><?php echo ncPriceFormat($data['balance_before_the_day']); ?></td>
                    </tr>

                    <tr class="b4">
                        <td></td>
                        <td colspan="2" align="left">
                            Amount on Hand
                        </td>
                        <td><?php echo ncPriceFormat($data['total_amount']+$data['balance_before_the_day']); ?></td>
                    </tr>

                    <tr class="b4">
                        <td></td>
                        <td colspan="2" align="left">
                            *** Balanced ***
                        </td>
                        <td></td>
                    </tr>

                    <tr class="b4">
                        <td></td>
                        <td colspan="2" align="left">
                            Summary - Receipts (In)
                        </td>
                        <td><?php echo ncPriceFormat($data['total_in']); ?></td>
                    </tr>

                    <tr class="b4">
                        <td></td>
                        <td colspan="2" align="left">
                            Summary - Payments (Out)
                        </td>
                        <td><?php echo ncPriceFormat($data['total_out']); ?></td>
                    </tr>


                </table>

                <div style="border-bottom:solid 5px #000;">
                </div>

                <div class="verify-row">
                    <div class="verify-column">
                        <span class="title">Verified By:</span>
                        <div class="line">

                        </div>
                        <div class="ep">
                            <?php echo $branch_manager['user_name']; ?>
                        </div>
                        <div>
                            Date:
                        </div>
                    </div>
                    <div class="verify-column">
                        <span class="title">Verified By:</span>
                        <div class="line">

                        </div>
                        <div class="ep">
                            <?php echo $chief_teller['user_name']; ?>
                        </div>
                        <div>
                            Date:
                        </div>
                    </div>
                    <div class="verify-column">
                        <span class="title">Prepared By:</span>
                        <div class="line">

                        </div>
                        <div class="ep">
                            <?php echo $userObj->user_name; ?>
                        </div>
                        <div>
                            Date:
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>