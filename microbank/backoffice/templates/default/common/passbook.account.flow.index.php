<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css" rel="stylesheet" type="text/css"/>
<style>
    .total{
        background-color: red !important;
    }
    .total td{
        font-size: 18px;
        color:#fff;
    }
</style>
<?php
 $book_info = $output['book_info'];
 $is_ajax = $output['is_ajax'];
?>
<!--Ajax 去padding值-->
<div <?php if(!$is_ajax){?>class="page"<?php }?>>
    <!--Ajax 去Title-->
    <?php if(!$is_ajax){?>
        <div class="fixed-bar">
            <div class="item-title">
                <h3><?php echo $output['title']?></h3>
                <ul class="tab-base">
                    <li><a class="current">
                            <span style="cursor: pointer" onclick="javascript:history.go(-1);"> BACK </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    <?php }?>
    <!--Ajax 去padding值-->
    <div <?php if(!$is_ajax){?>class="container"<?php }?>>
        <div class="business-condition">
            <form class="form-inline input-search-box" id="frm_search_condition">
                <input type="hidden" name="book_id" value="<?php echo $output['book_id'];?>">
                <input type="hidden" name="is_ajax" value="<?php echo $is_ajax;?>">
                <?php if(!$is_ajax){?><input type="hidden" name="currency" value="<?php echo $output['currency'];?>"><?php }?>
                <table class="search-table">
                    <tbody>
                        <tr>
                            <td>
                                <?php include(template("widget/inc_condition_datetime")); ?>
                            </td>
                            <!--Ajax 添加currency筛选条件-->
                            <?php if($is_ajax){?>
                                <td>
                                    <label for="exampleInputName2">Currency</label>
                                    <select id="choose_currency" class="form-control" name="currency">
                                        <?php foreach ($output['currency_list'] as $key => $currency) { ?>
                                            <option value="<?php echo $key;?>"><?php echo $currency;?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                            <?php }?>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="col-sm-12 user-info">
            <?php if($book_info){?>
                <div class="col-sm-3">
                    Book Code: <label for=""><?php echo $book_info['book_code'];?></label>
                </div>
                <div class="col-sm-3">
                    Book Name: <label for=""><?php echo $book_info['book_name'];?></label>
                </div>
            <?php }else{?>
                <div class="tip">* The gl account does not exist or has been deleted</div>
            <?php }?>
        </div>
        <div class="business-content">
            <div class="business-list">

            </div>
        </div>
    </div>

</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    var select = $('select[name=currency]').length;
    if(select){
        $("#choose_currency").change(function () {
            if (typeof(btn_search_onclick) != "undefined") {
                btn_search_onclick(1, 50);
            }
        });
    }


    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        yo.dynamicTpl({
            tpl: "common/passbook.account.flow.list",
            dynamic: {
                api: "common",
                method: "getPassbookAccountFlowList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
