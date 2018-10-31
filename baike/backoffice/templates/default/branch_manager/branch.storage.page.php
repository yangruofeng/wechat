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
        width: 20%;
        text-align: center;
        float: left;
        padding: 20px 0;
        max-height: 110px;
        border-top: 1px solid #e7eaec;
        border-right: 1px solid #e7eaec;
        font-weight: 600;
    }

    .product-info .product-report .item:nth-child(5n) {
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
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Counter Business</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Mortgage</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 99%;">
        <div class="product-info" style="margin:0px 0px 30px 0px!important;">
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
            <form class="form-inline" id="frm_search_condition" onkeydown="if(event.keyCode==13){return false;}">
                <table class="search-table">
                    <tbody>
                    <tr>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search_text" style="height: 34px;min-width: 240px" name="search_text" placeholder="Search for client code/name/phone">
                                <span class="input-group-btn">
                                     <button type="button" class="btn btn-default square" id="btn_search_list" onclick="btn_search_onclick();">
                                         <i class="fa fa-search"></i>
                                         Search
                                     </button>
                                </span>
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

        $('#frm_search_condition').keyup(function (event) {
            if (event.keyCode == 13) {
                btn_search_onclick();
            }
        });
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
            tpl: "branch_manager/branch.storage.list",
            dynamic: {
                api: "branch_manager",
                method: "getMyStorageList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>