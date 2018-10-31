<style>


    .table .form-control {
        height: 28px;width: 80px;
    }

    .table span.input-group-addon {
        min-width: 0;padding-left: 0;padding-right: 0;background-color: #ffffff;border:none
    }

    .td-new {
        background-color: red;
    }
</style>
<div class="page">
    <?php if (!$output['is_readonly']) { ?>
        <div class="fixed-bar">
            <div class="item-title">
                <h3>Interest Package</h3>
                <ul class="tab-base">
                    <li>
                        <a href="<?php echo getUrl('loan', 'productPackagePage', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Package List</span></a>
                    </li>
                    <li><a class="current"> <span>Set Special Interest For <?php echo $output['package_name'] ?></span></a></li>
                </ul>
            </div>
        </div>
    <?php } ?>

    <div class="container">
        <div class="business-content">
            <p>
                <input type="checkbox" onclick="changeListRowState(this)"> Only show active rows
            </p>
            <ul class="list-group">
                <?php foreach ($output['list'] as $prod) {
                    if ($prod['state'] != loanProductStateEnum::ACTIVE) {
                        continue;
                    }
                    ?>
                    <li class="list-group-item">
                        <label style="width: 300px" class="text-right">
                            <span style="width: 200px;"><?php echo $prod['sub_product_name'] ?></span>
                            <a class="btn btn-link" style="padding-left: 100px" role="button" data-toggle="collapse" href="#div_rate_list_<?php echo $prod['uid']?>" aria-expanded="false" aria-controls="div_rate_list_<?php echo $prod['uid']?>">
                                <i class="fa fa-angle-down"></i>
                            </a>
                        </label>
                        <?php
                        $data = array("data" => $prod['size_rate'], "type" => 'info');
                        include(template("loan/size_rate.list.edit"))
                        ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>
<script>
    function start_edit_rate_onclick(_e){
        var _e=$(_e).closest("td");
        if($(_e).data("state")=="1") return;
        $(_e).data("state",1);
        $(_e).find(".form-control").show();
        $(_e).find(".form-control").focus();
        $(_e).find(".span-val").hide();
        $(_e).find(".span-unit").hide();
        $(_e).find(".input-group-btn").show();
    }
    function cancel_edit_rate_onclick(_e){
        var _td=$(_e).closest("td");
        if(_td.data("state")=="0") return;
        _td.data("state",0);
        _td.find(".form-control").hide();
        _td.find(".span-val").show();
        _td.find(".span-unit").show();
        _td.find(".input-group-btn").hide();
    }
    function save_edit_rate_onclick(_e){
        var _td=$(_e).closest("td");
        $(document).waiting();
        var _special_grade = '<?php echo $output['special_grade']?>';
        var _size_rate_id =_td.data("size_rate_id");
        var _fld_name = _td.data("fld_name");
        var _val = _td.find(".input-val").val();
        var _old_value=_td.data("old_value");

        yo.loadData({
            _c:"loan",
            _m:"savePackageSizeRateForItem",
            param: {
                size_rate_id: _size_rate_id,
                special_grade: _special_grade,
                val: _val,
                fld_name: _fld_name
            },
            callback:function(_o){
                $(document).unmask();
                if(!_o.STS){
                    alert(_o.MSG);
                }else{
                    _td.find(".span-val").text(_o.DATA.new_value);
                    _td.find(".fld-value").val(_o.DATA.new_value);
                    if(_o.DATA.new_value!=_old_value){
                        _td.addClass("td-new");
                    }else{
                        _td.removeClass("td-new");
                    }
                    cancel_edit_rate_onclick(_e);
                }
            }
        })
    }
    function changeListRowState(_e){
        var _only_active = $(_e).is(':checked') ? 1 : 0;
        if(_only_active==1){
            $(".tr-rate-active").show();
            $(".tr-rate-not-active").hide();
        }else{
            $(".tr-rate-active").show();
            $(".tr-rate-not-active").show();
        }
    }

    function showForClient(el, uid) {
        if (!uid) {
            return;
        }

        var is_show_for_client = $(el).is(':checked') ? 1 : 0;
        var _special_grade = '<?php echo $output['special_grade']?>';
        var _size_rate_id =uid;

        $(document).waiting();
        yo.loadData({
            _c: "loan",
            _m: "setSpecialRateStateOfClient",
            param: {size_rate_id: _size_rate_id,special_grade:_special_grade,is_show_for_client: is_show_for_client},
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert('Setting Successful.');
                } else {
                    alert(_o.MSG);
                }
            }
        });

    }
    function setActiveForSpecialItem(el,uid){
        if (!uid) {
            return;
        }

        var _is_active = $(el).is(':checked') ? 1 : 0;
        var _special_grade = '<?php echo $output['special_grade']?>';
        var _size_rate_id =uid;

        $(document).waiting();
        yo.loadData({
            _c: "loan",
            _m: "setSpecialRateStateOfActive",
            param: {size_rate_id: _size_rate_id,special_grade:_special_grade,is_active: _is_active},
            callback: function (_o) {
                $(document).unmask();
                if (_o.STS) {
                    alert('Setting Successful.');
                    if(_is_active==1){
                        $(el).closest("tr").removeClass("tr-rate-not-active").addClass("tr-rate-active");
                    }else{
                        $(el).closest("tr").removeClass("tr-rate-active").addClass("tr-rate-not-active");

                    }
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }
</script>
