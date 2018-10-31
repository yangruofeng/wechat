<style>
    .btn {
        border-radius: 0;
    }

    .table>tbody>tr>td{
        background-color: #ffffff;!important;
    }

     .ibox-title {
         padding-top: 12px!important;
         min-height: 40px;
     }
     .flow-item .titile {
        background: #f2f2f2;
        padding: 8px 12px;
        font-weight: 500;
     }
     .flow-child-items {
        padding: 8px 12px;
     }
     .flow-child-items .item {
         padding: 5px 0
     }
     .flow-child-items .t {
         float: right;
         color: #949494;
     }
     .flow-child-items .d {
         font-size: 18px;
         margin-left: 10px;
     }
     .flow-child-items .d.red {
         color: #da0000;
     }
     .flow-child-items .b {
         margin-left: 10px;
     }
</style>
<?php
$client_info = $output['client_info'];
$data = $output['data'];
$list = $data['list'];
?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>My Client</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('web_credit', 'client', array(), false, BACK_OFFICE_SITE_URL)?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('web_credit', 'creditClient', array('uid'=>$_GET['uid']), false, BACK_OFFICE_SITE_URL)?>"><span>Client Detail</span></a></li>
                <li><a  class="current"><span>Flow</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="margin-top: 10px;max-width: 800px">
        <div class="business-condition">
             <?php require_once template("widget/item.member.summary")?>
        </div>
        <div class="business-content">
            <div class="basic-info container" style="margin-top: 10px">
                <div class="ibox-title" style="background-color: #DDD">
                    <h5 style="color: black"><i class="fa fa-id-card-o"></i>Savings of <?php echo $_GET['currency'];?></h5>
                </div>
                <div class="content">
                    <?php if(count($list) > 0){?>
                        <?php foreach ($list as $k => $v) {?>
                            <div class="flow-item">
                                <div class="titile"><?php echo $v['month'];?></div>
                                <div class="flow-child-items">
                                    <?php if(count($v['list']) > 0){?>
                                        <?php foreach ($v['list'] as $lk => $lv) {?>
                                            <div class="item">
                                                <?php 
                                                    $str = 'label-success';
                                                    switch ($lv['trading_type']) {
                                                        case 'Payment':
                                                            $str = 'label-success';
                                                            break;
                                                        case 'Transfer':
                                                            $str = 'label-warning';
                                                            break;
                                                        case 'Deposit':
                                                            $str = 'label-info';
                                                            break;
                                                        case 'Loan Fee':
                                                            $str = 'label-danger';
                                                            break;
                                                        case 'Operation Fee':
                                                            $str = 'label-danger';
                                                            break;
                                                        case 'Loan Disburse':
                                                            $str = 'label-warning';
                                                            break; 
                                                        case 'Loan Prepayment':
                                                            $str = 'label-primary';
                                                            break;
                                                        case 'Loan Repayment':
                                                            $str = 'label-primary';
                                                            break;
                                                        default:
                                                            # code...
                                                            break;
                                                    }
                                                ?>
                                                <span class="label <?php echo $str;?>"><?php echo strtoupper($lv['trading_type']);?></span>
                                                <span class="d <?php echo $lv['debit']== 0 ? 'red' : '';?>"><?php echo $lv['debit']== 0 ? '+' : '-';?><?php echo $lv['debit']== 0 ? ncPriceFormat($lv['credit']) : ncPriceFormat($lv['debit']);?></span>
                                                <span class="b">(Balance: <?php echo ncPriceFormat($lv['end_balance']);?>)</span>
                                                <span class="t"><?php echo timeFormat($lv['update_time']);?></span>
                                            </div>
                                        <?php  }?>
                                    <?php }?>
                                </div>
                            </div>
                        <?php }?>
                    <?php }else{?>
                        <div class="no-record">No Record</div>
                    <?php }?>
                    <button type="button" class="btn btn-default" onclick="btn_back_onclick();"><i class="fa fa-reply"></i>Back</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function btn_back_onclick(){
        window.history.back(-1);
    }

    function btn_submit_onclick(){
        $('#frm_operator').submit();
    }

</script>






