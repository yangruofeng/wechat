<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<style>
    .form-group {
        margin-bottom: 20px;
    }

    .container {
        width: 800px !important;
    }

    .mincontent {
        padding: 15px
    }

    .btn {
        min-width: 80px;
        border-radius: 0;
    }

    .collection-div {
        margin-bottom: 70px;
    }

    .authorize_input {
        margin-top: -8px!important;
        margin-bottom: 10px;
    }

    .table{
        background-color: white!important;
    }

    #notCheck, #notCheckCashier {
        width: 20px;
        position: absolute;
        top: 6px;
        right: 10px;
    }

    #checkFailure, #checkCashierFailure {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }

    #checkDone, #checkCashierDone {
        display: none;
        width: 20px;
        position: absolute;
        top: 4px;
        right: 10px;
    }

    .table tr td{
        background-color: #fff !important;
    }
</style>

<div class="page">
    <?php require_once template('widget/item.member.business.nav'); ?>


        <?php $biz = $output['data'];?>
        <div class="basic-info container">
            <div class="ibox-title">
                <h5><i class="fa fa-id-card-o"></i>Exchange Check</h5>
            </div>
            <div class="content">
                <div class="col-sm-6 mincontent">
                    <table class="table">
                        <tr><td>From Currency </td> <td><?php echo $biz['from_currency']?></td></tr>
                        <tr style="background-color: white!important;"><td>To Currency </td> <td><?php echo $biz['to_currency']?></td></tr>

                        <tr>
                            <td style="background-color: white !important;">
                                Amount
                            </td>
                            <td style="background-color: white !important;">
                                <label style="font-size: 20px"><?php echo ncPriceFormat($biz['amount'])?></label>
                            </td>
                        </tr>

                    </table>

                </div>
                <div class="col-sm-6 mincontent" style="padding-left: 0 !important;">
                    <form id='exchange_check'>
                        <input type='hidden' class="form-control" name="biz_id" value="<?php echo $biz['uid']?>">
                        <table class="table">
                            <tr><td>Exchange Rate </td> <td><?php echo $biz['exchange_rate']?></td></tr>
                            <tr>
                                <td>Exchange Amount </td> <td style="font-size: 20px;color:red;"><?php echo ncPriceFormat($biz['exchange_amount']);?></td>

                            </tr>
                            <tr style="background-color: white!important;">
                                <td>Trading Password </td>
                                <td><input type="password" name="password" class="form-control" value=""></td>
                            </tr>
                        </table>

                    </form>
                </div>
                <div class="form-group col-sm-12" style="text-align: center">
                    <button type="button" class="btn btn-default" style="min-width: 80px" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
                    <button type="button" class="btn btn-primary" onclick="btn_submit_exchange_onclick()"><i class="fa fa-arrow-right"></i>Submit</button>
                </div>
            </div>

        </div>



</div>

<?php require_once template('widget/app.config.js'); ?>
<script src="<?php echo CURRENT_RESOURCE_SITE_URL;?>/js/upyun.js"></script>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<script>


    function btn_submit_exchange_onclick() {
        if (!$("#exchange_check").valid()) {
            return
        }
        $(document).waiting();
        var values = $('#exchange_check').getValues();
        yo.loadData({
            _c: 'cash_in_vault',
            _m: 'confirmExchange',
            param: values,
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert("Exchange Successful!");
                    setTimeout(function () {
                        window.location.href = "<?php echo getUrl('cash_in_vault', 'exchange', array(), false, ENTRY_COUNTER_SITE_URL) ?>";
                    },2000)
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }


    $('#exchange_check').validate({
        errorPlacement: function(error, element){
            element.next().html(error);
        },
        rules: {
            password : {
                required: true
            }
        },
        messages: {
            password : {
                required: '<?php echo 'Required'?>'
            }
        }
    });




</script>



