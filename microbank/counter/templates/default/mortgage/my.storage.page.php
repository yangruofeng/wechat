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
                    <div class="item" style="width: 20%;border-left: 2px solid gainsboro">
                        <?php echo $certificationTypeEnumLang[$key] ?>
                        <p><?php echo $value?></p>
                    </div>
                <?php }?>
            </div>
        </div>
        <div class="business-condition">
            <div class="content col-sm-12" id="div_client_box" data-client_id="0">
                <div class="input-group" style="width: 400px">
                                        <span id="span_phone_country" class="input-group-addon" style="padding: 0;border: 0;">
                                            <select class="form-control" name="country_code" style="min-width: 80px;height: 30px">
                                                <option value="855">+855</option>
                                                <option value="66">+66</option>
                                                <option value="86">+86</option>
                                            </select>
                                        </span>
                    <input type="text" class="form-control" id="txt_search_phone" name="phone_number" value="" placeholder="">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary" onclick="btn_search_onclick()" id="btn_search" style="height: 30px;line-height: 14px;border-radius: 0">
                                                <i class="fa fa-search"></i>
                                                Search
                                            </button>
                                        </span>

                </div>
                <div class="btn-group" data-toggle="buttons" style="width: 400px;">
                    <label class="btn btn-default active">
                        <i class="fa fa-phone"></i>
                        <input type="radio" onchange="btn_search_by_onclick(this)" value="1" name="rbn_search_by" id="rbn_option1" autocomplete="off" checked>Phone
                    </label>
                    <label class="btn btn-default">
                        <i class="fa fa-id-card"></i>
                        <input type="radio"  onchange="btn_search_by_onclick(this)" value="2" name="rbn_search_by" id="rbn_option2" autocomplete="off"> CID
                    </label>
                    <label class="btn btn-default">
                        <i class="fa fa-at"></i>
                        <input type="radio"  onchange="btn_search_by_onclick(this)" value="3" name="rbn_search_by" id="rbn_option3" autocomplete="off"> LoginAccount
                    </label>
                    <label class="btn btn-default ">
                        <i class="fa fa-at"></i>
                        <input type="radio"  onchange="btn_search_by_onclick(this)" value="4" name="rbn_search_by" id="rbn_option4" autocomplete="off"> Name
                    </label>
                </div>
            </div>
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
    function btn_search_by_onclick(_e){
        var _item=$(_e).val();
        if(_item=='1'){
            $("#span_phone_country").show();
        }else{
            $("#span_phone_country").hide();
        }

    }
    $('#txt_search_phone').bind('keydown',function(event){
        if(event.keyCode == "13") {
            btn_search_onclick();
        }
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
        var _search_by=$('input:radio[name="rbn_search_by"]:checked').val();
        var _country_code = $('select[name="country_code"]').val();
        var _phone = $('#txt_search_phone').val();

        _values.search_by=_search_by;
        _values.country_code=_country_code;
        _values.phone_number=_phone;

        yo.dynamicTpl({
            tpl: "mortgage/my.storage.list",
            control:'counter_base',
            dynamic: {
                api: "mortgage",
                method: "getMyStorageList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }

</script>