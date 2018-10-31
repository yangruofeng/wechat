<style>
    #total_amount_box label{
        margin-left: 10px;
    }

    #total_amount_box .amount{
        margin-left: 8px;
        color:red;
        font-size: 16px;
    }

    .error-msg label{
        color:red;
    }
</style>

<?php
$payment_schema = $data['payment_schemas'];
$bank_id = $data['bank_id'];
$bill_code = $data['bill_code'];
?>
<?php if( empty($payment_schema) ){ ?>
    <div class="no-record">
        Not found any loan installment schema.
    </div>
<?php }else{  ?>
    <div>
        <form action="" class="form form-inline" id="bill_pay_form">
            <input type="hidden" name="bank_id" value="<?php echo $bank_id; ?>">
            <input type="hidden" name="bill_code" value="<?php echo $bill_code; ?>">
            <table class="table table-bordered table-hover">
                <tr class="table-header">
                    <td></td>
                    <td>
                        Contract Sn
                    </td>
                    <td>
                        Schema Idx
                    </td>
                    <td>
                        Schema Name
                    </td>
                    <td>
                        Receivable Date
                    </td>
                    <td>
                        Receivable Amount
                    </td>
                    <td>
                        Currency
                    </td>

                </tr>
                <?php foreach( $payment_schema as $item ){
                    $payment_amount = $item['amount']-$item['actual_payment_amount'];
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="schema-ids" name="schema_ids" value="<?php echo $item['uid']; ?>"
                                   data-amount="<?php echo $payment_amount; ?>" data-currency="<?php echo $item['currency']; ?>">
                        </td>
                        <td><?php echo $item['contract_sn']; ?></td>
                        <td><?php echo $item['uid']; ?></td>
                        <td><?php echo $item['scheme_name']; ?></td>
                        <td><?php echo date('Y-m-d',strtotime($item['receivable_date'])); ?></td>
                        <td><?php echo ncPriceFormat($payment_amount); ?></td>
                        <td><?php echo $item['currency']; ?></td>

                    </tr>

                <?php } ?>
                <tr>
                    <td>
                        <input type="checkbox" name="check_all" id="check_all_box" value="1">
                    </td>
                    <td colspan="10" class="text-center">
                        <label for="">Total :</label>
                        <span id="total_amount_box">

                        </span>
                    </td>

                </tr>

                <tr>
                    <td colspan="3" class="text-right">
                        <label for="">Client Deposit Amount</label>
                    </td>
                    <td colspan="5">
                        <input type="number" class="form-control" name="amount" value="">
                        <div class="error-msg"></div>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="text-right">
                        <label for="">Remark</label>
                    </td>
                    <td colspan="5">
                        <input type="text" class="form-control" name="remark" value="">

                    </td>
                </tr>

                <tr>
                    <td colspan="3">

                    </td>
                    <td colspan="5">
                        <span class="btn btn-danger" onclick="sumitBillPay();">Submit</span>

                    </td>
                </tr>

            </table>
        </form>
    </div>
<?php } ?>


<script>


    function sumitBillPay()
    {
        if (!$("#bill_pay_form").valid()) {
            return false;
        }
        var _values = getFormJson('#bill_pay_form');

        $('body').waiting();
        yo.loadData({
            _c: "financial",
            _m: "submitBillPay",
            param: _values,
            callback: function (_o) {
                $('body').unmask();
                if (_o.STS) {
                    alert(_o.MSG,1,function(){
                        window.location.reload();
                    });
                } else {
                    alert(_o.MSG,2);
                }
            }
        });

    }

    function calculateTotalAmount()
    {
        var _check_list = $('input[name="schema_ids"] ');
        var _total_amount = {};
        _check_list.each(function(i){
            console.log(i);
            var _item = $(_check_list[i]);
            if( _item.is(':checked') ){
                var _amount = parseFloat(_item.attr('data-amount'));
                var _currency = _item.attr('data-currency');
                //console.log(_amount);
                //console.log(_currency);
                if( _total_amount[_currency] ){
                    _total_amount[_currency] += _amount*1;
                }else{
                    _total_amount[_currency] = _amount;
                }

            }

        });
        //console.log(_total_amount);
        var _str = '';
        for( var i in _total_amount ){
            _str += '<label >'+i+'</label>'+'<span class="amount">'+_total_amount[i]+'</span>';
        }
        if( _str == '' ){
            _str = '<label>0</label>';
        }

        $('#total_amount_box').html(_str);

    }

    $('#check_all_box').click(function(){
        var _is_check = $('#check_all_box').is(':checked');
        console.log(_is_check);
        var _check_list = $('input[name="schema_ids"]');
        if( _is_check ){
            _check_list.prop("checked", true);
        }else{
            _check_list.prop("checked", false);
        }
        calculateTotalAmount();
    });
    $('input[name="schema_ids"]').click(function(){
        calculateTotalAmount();
    });

    $('#bill_pay_form').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.next('.error-msg'));
        },
        rules: {
            amount: {
                required: true
            }
        },
        messages: {
            amount: {
                required:  '<?php echo 'Please input client deposit amount!';?>'
            }
        }
    });
</script>
