<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo GLOBAL_RESOURCE_SITE_URL;?>/js/jquery.validation.min.js"></script>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container" style="min-height: 600px;background-color: #ffffff">
        <h3 style="text-align: center;padding: 20px">VOUCHER</h3>
        <form id="frmNode" class="form-voucher" style="width: 1000px;margin: auto" action="<?php echo getUrl("gl_tree","submitNewVoucher",array(),false,BACK_OFFICE_SITE_URL)?>" method="post">
            <table class="table table-without-background">
                <tr>
                    <td>Currency</td>
                    <td>
                        <div class="form-group">
                            <select class="form-control" id="sel_currency" name="biz_currency" style="width: 150px">
                                <?php foreach((new currencyEnum())->Dictionary() as $k=>$v){?>
                                    <option value="<?php echo $v;?>"><?php echo $k;?></option>
                                <?php }?>
                            </select>
                        </div>
                    </td>
                    <td style="text-align: right">Amount</td>
                    <td>
                        <div class="form-group">
                            <input type="number"  style="width: 100px" class="form-control" name="biz_amount" id="txt_biz_amount">
                            <div class="error_msg"></div>
                        </div>

                    </td>
                </tr>
                <tr>
                    <td>Remark</td>
                    <td colspan="5">
                        <input type="text" class="form-control" name="biz_remark">
                    </td>
                </tr>
                <tr>
                    <td colspan="10" style="padding: 20px">
                        <p>
                            <button type="button" onclick="btn_add_voucher_item_onclick(1)" class="btn btn-default"><i class="fa fa-plus"></i>  Dr</button>
                            <button type="button" onclick="btn_add_voucher_item_onclick(0)" class="btn btn-default"><i class="fa fa-plus"></i>  Cr</button>
                        </p>
                        <table class="table tbl-voucher-detail">
                            <tr class="table-header">
                                <td>GL-Code</td>
                                <td>GL-Name</td>
                                <td>Subject</td>
                                <td>Debit</td>
                                <td>Credit</td>
                                <td>Function</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div>
                <button type="button" onclick="btn_submit_onclick()" class="btn btn-primary btn-block" style="max-width: 300px;margin: auto">Submit</button>
            </div>

        </form>
    </div>
</div>
<script>
    function btn_add_voucher_item_onclick(_is_debit){
        var _tr=$(".tbl-voucher-detail").children("tbody").children("tr").last();
        var _top_subject=_tr.find(".item-gl-subject").first();
        var _default_subject='';
        if(_top_subject){
            _default_subject=_top_subject.val();
        }
        var _amt=$("#txt_biz_amount").val();

        _amt=Math.round(parseFloat(_amt),2);
        var _sub_amt=0;
        if(_is_debit){
            $(".gl-amount-debit").each(function(){
                _sub_amt+=parseFloat($(this).val());
            });
        }else{
            $(".gl-amount-credit").each(function(){
                _sub_amt+=parseFloat($(this).val());
            });
        }

        var _default_amt=_amt-_sub_amt;
        if(_default_amt<0) _default_amt=0;

        yo.dynamicTpl({
            tpl: "gl/voucher.editor.tr",
            ext:{data:{is_debit:_is_debit,gl_subject:_default_subject,gl_amount:_default_amt}},
            callback: function (_tpl) {
                $(".tbl-voucher-detail").append(_tpl);
                //addValidRule(_tr);
            }
        });
    }
    function btn_remove_voucher_item_onclick(_e){
        $(_e).closest("tr").remove();
    }
    function btn_submit_onclick(){
        if (!$('.form-voucher').valid()) {
            return;
        }
        $('.form-voucher').submit();
    }


    $('.form-voucher').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.error_msg'));
        },
        rules: {
            biz_amount: {
                required: true
            }
        },
        messages: {
            biz_amount: {
                required: '<?php echo 'Required'?>'
            }
        }
    });
    function checkGlCodeValidation(_e){
        var _tr=$(_e).closest("tr");
        var _lbl=_tr.find(".lbl-gl-name");
        var _code=$(_e).val();
        var _ccy=$("").val();
        yo.loadData({
            _c:"gl_voucher",
            _m:"checkGlCodeValid",
            param:{code:_code,currency:$("#sel_currency").val()},
            callback:function(_o){
               if(_o.STS){
                   _lbl.text(_o.DATA.gl_name);
               }else{
                   _lbl.text(_o.MSG);
               }
            }
        });
    }


</script>