<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
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
$is_ajax = $output['is_ajax'];
?>
<!--Ajax 去padding值-->
<div <?php if(!$is_ajax){?>class="page"<?php }?>>
    <!--Ajax 去Title-->
    <?php if(!$is_ajax){?>
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Voucher Flow</h3>
            <ul class="tab-base">
                <li><a class="current">
                        <span style="cursor: pointer" onclick="javascript:history.go(-1);"> BACK </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php }?>

    <!--Ajax 去padding值-->
    <div <?php if(!$is_ajax){?>class="container"<?php }?>>
        <div class="business-content">
            <?php $info = $output['info'];?>
            <div class="col-sm-12 user-info">
                <?php if($info){?>
                    <div class="col-sm-3">
                        Trade ID: <label for=""><?php echo $info['uid'];?></label>
                    </div>
                    <div class="col-sm-3">
                        Trade Type: <label for=""><?php echo $info['trading_type'];?></label>
                    </div>
                    <div class="col-sm-3">
                        Time: <label for=""><?php echo timeFormat($info['update_time']);?></label>
                    </div>
                    <!--Ajax 添加back按钮-->
                    <?php if($is_ajax){?>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-info btn-sm" onclick="ajaxBack();">
                                Back
                            </button>
                        </div>
                    <?php }?>
                    <div class="col-sm-10">
                        Remark: <label for=""><?php echo $info['remark'];?></label>
                    </div>
                    <div class="col-sm-10">
                        Memo: <label for=""><?php echo $info['sys_memo'];?></label>
                    </div>
                <?php }else{?>
                    <div class="tip">* The gl account does not exist or has been deleted</div>
                <?php }?>
            </div>
            <div class="business-list">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr class="table-header">
                        <td>Flow ID</td>
                        <td>Name</td>
                        <td>Time</td>
                        <td>Begin Balance</td>
                        <td class="number">Credit</td>
                        <td class="number">Debit</td>
                        <td class="number">End Balance</td>
                        <td>Subject</td>
                        <td>Remark</td>
                    </tr>
                    </thead>
                    <tbody class="table-body">
                    <?php $list = $output['list'];?>
                    <?php foreach ($list as $k => $v) { ?>
                        <tr>
                            <td><?php echo $v['uid'];?></td>
                            <td><?php echo $v['book_name'];?></td>
                            <td>
                                <?php echo timeFormat($v['update_time']); ?>
                            </td>
                            <td class="currency">
                                <?php echo ncPriceFormat($v['begin_balance']); ?>
                            </td>
                            <td class="currency"><?php echo $v['credit']?ncPriceFormat($v['credit']):'-';?></td>
                            <td class="currency"><?php echo $v['debit']?ncPriceFormat($v['debit']):'-';?></td>
                            <td class="currency"><?php echo $v['end_balance']?ncPriceFormat($v['end_balance']):'-';?></td>
                            <td><?php echo $v['subject']?:'-';?></td>
                            <td>
                                <?php echo $v['remark']; ?>
                            </td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script>
    function ajaxBack(){
        $(".btn-center-item.current").click();
    }
</script>
