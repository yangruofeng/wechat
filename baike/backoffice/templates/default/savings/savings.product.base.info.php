<form class="form-horizontal" id="frm_editor" method="post">
    <input type="hidden" name="act" value="savings">
    <input type="hidden" name="op" value="submitProductMain">
    <input type="hidden" name="tab" value="">
    <input type="hidden" name="tab" value="">

    <input type="hidden" name="uid" value="<?php echo $product_info['uid']?>">
    <table class="table table-bordered table-hover" style="width: 500px;">
        <tr>
            <td class="text-right">
                <span class="red">*</span>Product Code
            </td>
            <td class="text-left">
                <input type="text" class="form-control" value="<?php echo $product_info['product_code']?>" name="product_code">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Product Name
            </td>
            <td class="text-left">
                <input type="text" class="form-control" value="<?php echo $product_info['product_name']?>" name="product_name">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">Category</td>
            <td class="text-left">
                <?php if ($output['category_info']) { ?>
                    <span class="form-control">
                        <?php echo $output['category_info']['category_name'] ?>
                    </span>
                <?php } else { ?>
                <select class="form-control" name="category_id">
                    <?php foreach ($category_list as $category) { ?>
                        <option value="<?php echo $category['uid'] ?>" data-term-style="<?php echo $category['category_term_style'] ?>"><?php echo $category['category_name']; ?></option>
                    <?php } ?>
                </select>
                <?php } ?>
            </td>
        </tr>

        <?php if (!$output['category_info'] || $output['category_info']['category_term_style'] == savingsCategoryTermStyleEnum::RANGE) { ?>
            <tr>
                <td class="text-right">
                    <span class="red">*</span><span id="sp_min_term">Min Term Days</span>
                </td>
                <td class="text-left">
                    <input type="number" class="form-control" value="<?php echo $product_info['min_terms']?>" name="min_terms">
                    <div class="error_msg"></div>
                </td>
            </tr>
            <tr id="tr_max_term">
                <td class="text-right">
                    <span class="red">*</span>Max Term Days
                </td>
                <td class="text-left">
                    <input type="number" class="form-control" value="<?php echo $product_info['max_terms'] > 0 ? $product_info['max_terms'] : ''?>" name="max_terms">
                    <div class="error_msg"></div>
                </td>
            </tr>
        <?php } else if ($output['category_info']['category_term_style'] == savingsCategoryTermStyleEnum::FIXED) { ?>
            <tr>
                <td class="text-right">
                    <span class="red">*</span>Term Days
                </td>
                <td class="text-left">
                    <input type="number" class="form-control" value="<?php echo $product_info['min_terms']?>" name="min_terms">
                    <div class="error_msg"></div>
                </td>
            </tr>
        <?php } else if ($output['category_info']['category_term_style'] == savingsCategoryTermStyleEnum::FREE) { ?>
            <tr>
                <td class="text-right">
                    <span class="red">*</span>Min Term Days
                </td>
                <td class="text-left">
                    <input type="number" class="form-control" value="<?php echo $product_info['min_terms']?>" name="min_terms">
                    <div class="error_msg"></div>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Interest Rate(%)
            </td>
            <td class="text-left">
                <input type="number" class="form-control" value="<?php echo $product_info['interest_rate']?>" name="interest_rate">
                <div class="error_msg"></div>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red">*</span>Interest Rate Unit
            </td>
            <td class="text-left">
                <select class="form-control" name="interest_rate_unit">
                    <?php foreach ($period_unit as $key => $unit) { ?>
                        <option value="<?php echo $key ?>" <?php if ($product_info['interest_rate_unit'] == $key) echo 'selected' ?>><?php echo ucwords(strtolower($unit)); ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red"></span>Supported Currency
            </td>
            <td class="text-left">
                <select class="form-control" name="currency">
                <?php foreach ($currency_list as $key => $currency) { ?>
                    <option value="<?php echo $key; ?>" <?php echo $product_info['currency'] == $key ? 'selected' : ''?>>
                        <?php echo $currency; ?>
                    </option>
                <?php } ?>
                </select>
            </td>
        </tr>

        <tr>
            <td class="text-right">
                <span class="red"></span>State
            </td>
            <td class="text-left" style="height: 34px">
                <span style="margin-left: 15px">
                    <?php echo $lang['savings_product_state_' . (isset($product_info['state']) ? $product_info['state'] : savingsProductStateEnum::TEMP) ]?>
                </span>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="text-center" style="padding: 20px">
                <?php if ($product_info['uid']) { ?>
                    <a class="btn btn-primary" style="width: 100px" onclick="formSubmit();">Submit</a>
                    <a class="btn btn-success" style="min-width: 100px" onclick="changeState(<?php echo $state['state']?>);"><?php echo $state['title']?></a>
                    <a type="button" class="btn btn-default" style="width: 100px;" onclick="javascript:history.back(-1)">Cancel</a>
                <?php } else { ?>
                    <a class="btn btn-primary" style="width: 150px; margin-right: 10px" onclick="formSubmit('');">Submit</a>
                    <a type="button" class="btn btn-default" style="width: 150px; margin-left: 10px" onclick="javascript:history.back(-1)">Cancel</a>
                <?php } ?>
            </td>
        </tr>
    </table>
</form>
<script>
    <?php if(!$product_info['uid']) { ?>
        $(function () {
            $('select[name="category_id"]').change(function () {
                var category_id = $(this).val();
                yo.dynamicTpl({
                    tpl: "savings/savings.category.temp",
                    dynamic: {
                        api: "savings",
                        method: "getTempByCategoryId",
                        param: {category_id: category_id}
                    },
                    callback: function (_tpl) {
                        $("#category_temp").html(_tpl);
                    }
                });
            })
        })
    <?php } ?>

    function formSubmit(tab)
    {
        if (!$('#frm_editor').valid()) {
            return false;
        }
        $('input[name="tab"]').val(tab);
        $('#frm_editor').submit();
    }

    function changeState(_state) {
        var _uid = '<?php echo $product_info['uid']?>';
        $.messager.confirm("<?php echo $state['title']; ?>", "Are you sure you want to <?php echo strtolower($state['title']); ?> this product?", function (_r) {
            if (!_r) return;
            yo.loadData({
                _c: "savings",
                _m: "changeProductState",
                param: {uid: _uid, state: _state},
                callback: function (_obj) {
                    if (!_obj.STS) {
                        alert(_obj.MSG);
                    } else {
                        window.location = "<?php echo getUrl('savings', 'product', array(), false, BACK_OFFICE_SITE_URL); ?>";
                    }
                }
            });
        });
    }

    $("select[name=category_id]").change(function(){
        var term_style = $(this).find("option:selected").attr("data-term-style");
        if (term_style == "<?php echo savingsCategoryTermStyleEnum::FIXED ?>") {
            $("#tr_max_term").hide();
            $("#sp_min_term").text("Term Days");
        } else if (term_style == "<?php echo savingsCategoryTermStyleEnum::RANGE ?>") {
            $("#tr_max_term").show();
            $("#sp_min_term").text("Min Term Days");
        } else if (term_style == "<?php echo savingsCategoryTermStyleEnum::FREE ?>") {
            $("#tr_max_term").hide();
            $("#sp_min_term").text("Min Term Days");
        }
    }).change();

    $('#frm_editor').validate({
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('td').find('.error_msg'));
        },
        rules: {
            product_code: {
                required: true
            },
            product_name: {
                required: true
            },
            min_terms: {
                required: true,
                min: 0
            },
            max_terms: {
//                required: true,
                min: 0,
                checkMaxTerms: true
            },
            interest_rate: {
                required: true,
                min: 0
            }
        },
        messages: {
            product_code: {
                required: '<?php echo 'Required.' ?>'
            },
            product_name: {
                required:  '<?php echo 'Required.' ?>'
            },
            min_terms: {
                required:  '<?php echo 'Required.' ?>',
                min: '<?php echo 'Not less than 0.' ?>'
            },
            max_terms: {
//                required:  '<?php //echo 'Required.' ?>//',
                min: '<?php echo 'Not less than 0.' ?>',
                checkMaxTerms: '<?php echo 'Not less than the min terms.' ?>'
            },
            interest_rate: {
                required:  '<?php echo 'Required.' ?>',
                min: '<?php echo 'Not less than 0.' ?>'
            }
        }
    });

    jQuery.validator.addMethod("checkMaxTerms", function (value, element) {
        value = parseInt(value);
        var min_terms = parseInt($('input[name="min_terms"]').val());
        if (value < min_terms) {
            return false;
        } else {
            return true;
        }
    });
</script>