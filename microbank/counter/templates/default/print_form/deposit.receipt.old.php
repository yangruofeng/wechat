<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    tr{
        height: 30px;
        border: 1px solid black;
    }

    td{
        padding-left: 10px;
        font-size: 12px!important;
    }

    .head{
        height:60px;

    }

   #to_bank td:first-child{
        width: 80px;
       border: 1px solid black;
    }

    #to_client td:first-child{
        width: 95px;
    }
    #to_client td:nth-child(2){
        padding:2px
    }

    body{
        font-size: 12px;
        margin: 50px;
    }


</style>
<?php $info = $output['deposit_info']; ?>

<div style="width: 800px">
    <div class="col-sm-7" style="padding-right: 0px" id="to_bank">
        <div class="head">
            <div>
                <span>
                    ◎（logo）XXXbank
                </span>
                <span style="margin-left: 70px;font-size: 16px;font-weight: bold">
                    Deposit Receipt
                </span>
            </div>
            <div style="margin-top: 10px;padding: 5px">
                <span>
                    Receipt No:<?php echo $info['uid'] ?>
                </span>
                <span style="margin-left: 160px">
                   Deposit time：<?php echo $info['update_time'] ?>
                </span>
            </div>
        </div>
        <div>
            <table width="100%">
                <tr>
                    <td>Account</td>
                    <td colspan="2"><?php echo $info['login_code'] ?></td>
                </tr>
                <tr>
                    <td>Account No</td>
                    <td colspan="2"><?php echo $info['obj_guid'] ?></td>
                </tr>
                <tr>
                    <td>Currency</td>
                    <td style="width: 100px;border: 1px solid black"><?php echo $info['currency'] ?></td>
                    <td style="padding: 0px">
                       <p style="border-bottom: 1px solid black;height: 25px;text-align: center">Amount</p>
                       <p style="text-align: center"><?php echo  ncAmountFormat($info['amount'],false,$info['currency']) ?></p>
                    </td>
                </tr>
                <tr>
                    <td>Fee</td>
                    <td colspan="2"></td>
                </tr>

                <tr style="height: 60px;">
                    <td style="border: none;padding-top: 0px!important;">Remark:</td>
                    <td colspan="2"><?php echo $info['remark'] ?></td>
                </tr>
                <tr>
                    <td colspan="3" style="padding-left: 260px">Client Sign: </td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 5px">
            <p style="padding-left: 5px">
                <span>
                    Operator : <?php echo $info['cashier_name'] ?>
                </span>
                <span style="margin-left: 200px">
                   Auditor：<?php echo $info['bm_name'] ?>
                </span>
        </div>


    </div>

    <div class="col-sm-5" style="padding-left: 0px" id="to_client">
        <div class="head">
            <p style="margin-left: 60px;font-size: 14px;font-weight: bold">
                ◎XXXbank
                Receipt For Client
            </p>
            <p style="padding-left: 5px;margin-top: 16px">
                Receipt No:<?php echo $info['uid'] ?>
            </p>
        </div>
        <div>
            <table width="100%">
                <tr>
                    <td>Business Type :</td>
                    <td>Deposit</td>
                </tr>
                <tr>
                    <td>Account No :</td>
                    <td><?php echo $info['obj_guid'] ?></td>
                </tr>
                <tr>
                    <td>Account :</td>
                    <td><?php echo $info['login_code'] ?></td>
                </tr>
                <tr>
                    <td>Deposit time :</td>
                    <td><?php echo $info['update_time'] ?></td>
                </tr>
                <tr>
                    <td>Currency:</td>
                    <td><?php echo $info['currency'] ?></td>
                </tr>
                <tr>
                    <td>Amount :</td>
                    <td><?php echo  ncAmountFormat($info['amount'],false,$info['currency']) ?></td>
                </tr>
                <tr>
                    <td>Fee :</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Remark ：</td>
                    <td><?php echo $info['remark'] ?></td>
                </tr>

            </table>
        </div>
    </div>
</div>


