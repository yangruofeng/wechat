<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>GL-Account</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('gl_tree', 'index', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Tree Style</span></a></li>
                <li><a href="<?php echo getUrl('gl_tree', 'showTableStyle', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Table Style</span></a></li>
                <li><a href="<?php echo getUrl('gl_tree', 'showUserDefined', array(), false, BACK_OFFICE_SITE_URL)?>"><span>User Defined</span></a></li>
                <li><a class="current"><span>Add Account</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <form id="frmNode" style="width: 800px;margin: auto" action="<?php echo getUrl("gl_tree","submitNewNode",array(),false,BACK_OFFICE_SITE_URL)?>" method="post">
            <table class="table">
                <tr>
                    <td>Parent GL-Code</td>
                    <td>
                        <input type="text" class="form-control" onblur="calcCurrencyGlCode()" name="parent_gl_code" id="txt_p_code" style="width: 200px">
                    </td>
                    <td> Example:1-1110</td>
                </tr>
                <tr>
                    <td>GL-Code</td>
                    <td>
                        <input type="text" id="txt_new_code" onblur="calcCurrencyGlCode()" name="gl_code" class="form-control" style="width: 200px">
                    </td>
                    <td>
                        Example:1
                    </td>
                </tr>
                <tr>
                    <td>
                        GL-Name
                    </td>
                    <td>
                        <input type="text" id="txt_new_name" onblur="calcCurrencyGlCode()" name="gl_name" class="form-control" style="width: 200px">
                    </td>
                    <td>
                        Example:Expense
                    </td>
                </tr>
                <tr>
                    <td>

                    </td>
                    <td>
                        <input type="checkbox" checked id="chk_leaf" onchange="changeLeafCode()" name="is_leaf">
                        Leaf Node
                    </td>
                    <td>
                        No children account of leaf node
                    </td>
                </tr>
                <tr>
                    <td colspan="10">
                        <div id="div_ccy_code">
                            <ul class="list-group">
                                <?php foreach((new currencyEnum())->Dictionary() as $k=>$v){?>
                                    <li class="list-group-item list-group-item-text">
                                        <span class="span-ccy" data-ccy="<?php echo $k;?>" ><?php echo $k;?>  </span> :
                                        <br/>
                                        <label class="lbl-ccy" data-ccy="<?php echo $k;?>" id="lbl_ccy_<?php echo $k;?>"></label>
                                    </li>
                                <?php }?>
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>
            <div>
                <button type="submit" class="btn btn-primary btn-block" style="max-width: 300px;margin: auto">Submit</button>
            </div>

        </form>
    </div>
</div>
<script>
    function calcCurrencyGlCode(){
        var _p_code=$("#txt_p_code").val();
        var _n_code=$("#txt_new_code").val();
        var _n_name=$("#txt_new_name").val();
        $("#div_ccy_code").find(".lbl-ccy").each(function(){
            var _ccy=$(this).data("ccy");
            var _ccy_code=0;
            if(_ccy=='KHR') _ccy_code=1;
            if(_ccy=='USD') _ccy_code=2;
            if(_ccy=='THB') _ccy_code=5;
            var _str=_p_code.toString()+"-"+_n_code.toString()+"-"+_ccy_code.toString();
            $(this).text(_str);
        });
        $("#div_ccy_code").find(".span-ccy").each(function(){
            var _ccy=$(this).data("ccy");
            $(this).text(_n_name+"-"+_ccy);

        });

    }
    function changeLeafCode(){
        var _chk=$("#chk_leaf").prop("checked");
        if(_chk==true){
            $("#div_ccy_code").show();
        }else{
            $("#div_ccy_code").hide();
        }

    }

</script>