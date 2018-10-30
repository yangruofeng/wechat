<!--
<style>
    #top-counter{
        width: 100%;
        height: 80px;
        border: 2px solid #FFE499;
        background-color: white;
        margin-bottom: 10px;
        padding-left: 10px!important;
        padding-top: 10px!important;

    }

    .balance tr td{
        padding: 2px 8px 4px!important;
        background-color: #FFF!important;
    }

    .balance tr td span{
       font-weight: 600;
    }
</style>
<?php
$currency_list = (new currencyEnum())->Dictionary();
?>
<div id="top-counter" style="padding: 5px 20px">
    <div style="max-width: 1050px">
        <div class="counter-info col-sm-2">
            <div class="department"><label class="control-label"><?php echo 'Cash In Vault : ';?></label></div>
        </div>
        <div class="balance col-sm-10" >
            <table class="table">
                <tbody class="table-body">
                <tr class="cash_in_hand">
                    <td><label class="control-label">Balance</label></td>
                    <?php foreach ($currency_list as $key => $currency) { ?>
                        <td><span><?php echo $currency;?>:</span><span class="cash-in-hand" currency="cash_<?php echo $key?>"></span></td>
                    <?php }?>
                </tr>
                <tr class="outstanding">
                    <td><label class="control-label">Outstanding</label></td>
                    <?php foreach ($currency_list as $key => $currency) { ?>
                        <td><span><?php echo $currency;?>:</span><span class="cash-outstanding" currency="out_<?php echo $key?>"></span></td>
                    <?php }?>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>
-->
<script>
    $(function () {
        //getBalance();
    });

    function getBalance() {
        yo.loadData({
            _c: 'cash_in_vault',
            _m: 'getBranchBalance',
            param: '',
            callback: function (_o) {
                if (_o.STS) {
                    var data = _o.DATA;
                    $('.cash-in-hand').each(function () {
                        var currency = $(this).attr('currency');
                        $(this).text(data[currency]);
                    })

                    $('.cash-outstanding').each(function () {
                        var currency = $(this).attr('currency');
                        $(this).text(data[currency]);
                    })
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

</script>
