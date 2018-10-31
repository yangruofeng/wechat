<?php $product_list = $output['product_list']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Product</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Product List</span></a></li>
                <li>
                    <a href="<?php echo getUrl('savings', 'editProductPage', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add Product</span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 99%">
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <table  class="search-table">
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control input-search" id="search_text" name="search_text" placeholder="Search for...">
                                <span class="input-group-btn">
                                  <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                                          onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      <?php echo 'Search'; ?>
                                  </button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <select class="form-control" name="category_id" id="category_id" onchange="btn_search_onclick();">
                                <option value="0">Select Category</option>
                                <?php foreach ($output['category_list'] as $category) { ?>
                                    <option value="<?php echo $category['uid']?>"><?php echo $category['category_name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="state" id="state" onchange="btn_search_onclick();">
                                <option value="0">Select State</option>
                                <?php foreach ($output['state_arr'] as $key => $state) { ?>
                                    <option value="<?php echo $key ?>"><?php echo $lang['savings_product_state_' . $key]; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="business-content" style="margin-top: 10px">
            <div class="business-list">

            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick() {
        var _values = $('#frm_search_condition').getValues();
        yo.dynamicTpl({
            tpl: "savings/savings.product.list",
            dynamic: {
                api: "savings",
                method: "getProductList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

    function deleteProduct(_uid) {
        if(!_uid){
            return;
        }
        $.messager.confirm("Confirm", "are you sure to delete this record?", function (_r) {
            if (!_r) return;
            $(document).waiting();
            yo.loadData({
                _c: "savings",
                _m: "removeProduct",
                param: {uid: _uid},
                callback: function (_o) {
                    $(document).unmask();
                    if (!_o.STS) {
                        alert(_o.MSG);
                    } else {
                        btn_search_onclick();
                    }
                }
            });
        })
    }
</script>
