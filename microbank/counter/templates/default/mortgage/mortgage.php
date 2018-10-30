<style>
    .product-info {
        width: 98%;
        background: #FFF;
        margin: 15px;
    }

    .product-info .info {
        padding: 15px;
        position: relative;
        height: 85px;
        /*border-bottom: 1px solid #e7eaec;*/
    }

    .product-info .name {
        font-size: 20px;
        font-weight: 600;
    }

    .product-info .product-report {
        height: 90px;
    }

    .product-info .product-report .item {
        width: 25%;
        text-align: center;
        float: left;
        padding: 20px 0;
        max-height: 110px;
        border-top: 1px solid #e7eaec;
        border-right: 1px solid #e7eaec;
        font-weight: 600;
    }

    .product-info .product-report .item:nth-child(4n) {
        border-right: 0;
    }

    .product-info .product-report .item p {
        margin-bottom: 0;
        font-size: 20px;
        margin-top: 5px;
        color: #f60;
    }

    .product-info .custom-btn-group {
        position: absolute;
        right: 10px;
        top: 20px;
    }

    .custom-btn-group a {
        margin-left: 8px;
    }
</style>
<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css" rel="stylesheet" type="text/css"/>
<?php
$certificationTypeEnumLang = enum_langClass::getCertificationTypeEnumLang();
?>
<div class="page">
    <?php require_once template('widget/sub.menu.nav'); ?>
    <div class="container">
        <div class="product-info" style="width: 100%;margin:0px 0px 30px 0px!important;">
            <div class="product-report clearfix">
                <?php foreach ($output['asset'] as $key => $value){?>
                    <div class="item">
                        <?php echo $certificationTypeEnumLang[$key] ?>
                        <p><?php echo $value?></p>
                    </div>
                <?php }?>
            </div>
        </div>
        <div class="business-condition" style="margin-bottom: -5px!important;">
            <form class="form-inline" id="frm_search_condition">
                <table class="search-table">
                    <tbody>
                    <tr>
<!--                        <td>-->
<!--                            <select class="form-control" style="height: 34px" name="asset_type" onclick="btn_search_onclick()">-->
<!--                                <option value="">All</option>-->
<!--                                --><?php //foreach ($output['asset'] as $key => $value){?>
<!--                                    <option value="--><?php //echo $key ?><!--">--><?php //echo $certificationTypeEnumLang[$key] ?><!--</option>-->
<!--                                --><?php //}?>
<!--                            </select>-->
<!---->
<!--                        </td>-->
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" style="height: 34px" name="search_text" placeholder="Search for client code">
                                <span class="input-group-btn">
                                     <button type="button" class="btn btn-default square" id="btn_search_list" onclick="btn_search_onclick();">
                                      <i class="fa fa-search"></i>
                                      Search
                                    </button>
                                    </span>
                            </div>
                        </td>
                        <td>
                            <div class="input-group" style="margin-left: 20px">
                                <a href="<?php echo getUrl('mortgage', 'receiveMortgageList', array(), false, ENTRY_COUNTER_SITE_URL) ?>" class="btn btn-default square" id="receive" style="width: 100px">Receive</a>
                            </div>
                        </td>
                        <td>
                            <div class="input-group" style="margin-left: 5px">
                                <a  href="<?php echo getUrl('mortgage', 'takeoutMortgageList', array(), false, ENTRY_COUNTER_SITE_URL) ?>" class="btn btn-default square" id="take_out" style="width: 100px">Take out</a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
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

    //  分页展示贷款申请列表
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
            tpl: "mortgage/mortgage.list",
            control:'counter_base',
            dynamic: {
                api: "mortgage",
                method: "getMortgageList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>