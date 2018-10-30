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
        font-size: 14px;
        font-weight: 700;
    }

    .verify-column .line{
        height: 50px;
        border-bottom: solid 3px #000;
        width: 160px;
    }

    .verify-column .ep{
        text-align: center;
        width: 160px;
        margin: 5px 0;
    }
</style>

<?php $ccy_list=(new currencyEnum())->Dictionary();?>
<div>
    <table class="table" style="margin-bottom: 30px">
        <tr>
            <td style="width: 33%;vertical-align: top;">
                <img src="resource/img/login/c-logo.png" >
            </td>
            <td style="width: 33%;vertical-align: top;min-width: 300px;">
                <div style="text-align: center;">
                    <div class="b2" >
                        SAMRITHISAK LIMITED
                        <br />
                        <?php echo $output['branch_name']; ?>
                        <br >
                        <?php echo $output['end_date']; ?>
                    </div>

                    <br>
                    <div class="b4" style="margin-top: 20px;">
                        Branch - Daily Report
                    </div>
                    <div class="b4">
                        【<?php echo $output['user_code'] ?>】 <?php echo $output['user_name']; ?>
                    </div>
                </div>


            </td>
            <td style="width: 33%;vertical-align: top;" >
                <div style="text-align: right" class="n2">
                    <?php echo date('d/m/Y',strtotime(Now())); ?><br />
                    <?php echo date('H:i:s').' '.date('A'); ?><br />
                </div>

            </td>
        </tr>
    </table>


</div>
<table class="table table-bordered table-hover">
    <tr class="table-header">
        <td>ID</td>
        <td> Account Code</td>
        <td>Account Name</td>
        <?php foreach($ccy_list as $ccy){?>
            <td><?php echo $ccy?></td>
        <?php }?>
        <td>
            Remark
        </td>
    </tr>

    <?php foreach($output['data'] as $item){?>
        <tr>
            <td><?php echo $item['uid']?></td>
            <td><?php echo $item['book_code']?></td>
            <td><?php echo $item['book_name']?></td>
            <?php foreach($ccy_list as $ccy){?>
                <td><?php if($item['balance'][$ccy]>0) echo ncPriceFormat($item['balance'][$ccy])?></td>
            <?php }?>
            <td>
                <?php echo $item['remark']?>
            </td>
        </tr>
    <?php }?>
</table>

<div class="verify-row" style="margin-top: 40px;padding-left: 20px">
    <div class="verify-column">
        <span class="title">Verified By:</span>
        <div class="line">

        </div>
        <div class="ep">
            <?php echo $output['branch_manager']['user_name']; ?>
        </div>
        <div>
            Date:
        </div>
    </div>

    <div class="verify-column" style="margin-left: 50px">
        <span class="title">Prepared By:</span>
        <div class="line">

        </div>
        <div class="ep">
            <?php echo $output['user_name']; ?>
        </div>
        <div>
            Date:
        </div>
    </div>
</div>