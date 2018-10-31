<link href="<?php echo BACK_OFFICE_SITE_URL ?>/resource/css/report.css?v=5" rel="stylesheet" type="text/css"/>
<style>
    .lv0 {
        font-weight: bold;
    }

    .lv1 label {
        padding-left: 10px;
        font-weight: normal;
    }

    .lv2 label {
        padding-left: 20px;
        font-weight: normal;
    }

    .lv3 label {
        padding-left: 30px;
        font-weight: normal;
    }

    .amount {
        text-align: right;
    }
    .input-search-box {
        margin-bottom: 15px;
    }
    .user-info {
        background: #fff;
        padding: 15px 0;
    }
    .tip {
        color: #e40000;
        font-size: 14px;
        padding: 0 40px;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Income Statement</h3>
            <ul class="tab-base">
            <li><a href="<?php echo getUrl('report', 'incomeStatement', array(), false, BACK_OFFICE_SITE_URL);?>"><span>Main</span></a></li>
                <li><a class="current"><span>Flow</span></a></li>
            </ul>
        </div>
    </div>
    <?php $info = $output['info'][$_GET['time']];?>
    <div class="container">
        <div class="col-sm-12">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-money"></i>&nbsp;<?php echo $_GET['time'];?></h5>
                </div>
                
                <div class="col-sm-12 user-info">
                    <?php if($info){?>
                        <div class="col-sm-3">
                            Date: <label for=""><?php echo $_GET['time'];?></label>
                        </div>
                        <?php if($_GET['currency']){?>
                            <?php if($_GET['currency'] == 'USD'){?>
                                <div class="col-sm-3">
                                    USD: <label for=""><?php echo ncPriceFormat($info['children']['USD']);?></label>
                                </div>
                            <?php }else{?>
                                <div class="col-sm-3">
                                    KHR: <label for=""><?php echo ncPriceFormat($info['children']['KHR']);?></label>
                                </div>
                            <?php }?>
                        <?php }else{?>
                            <div class="col-sm-3">
                                USD: <label for=""><?php echo ncPriceFormat($info['children']['USD']);?></label>
                            </div>
                            <div class="col-sm-3">
                                KHR: <label for=""><?php echo ncPriceFormat($info['children']['KHR']);?></label>
                            </div>
                        <?php }?>
                    <?php }else{?>
                            <div class="tip">* The user does not exist or has been deleted</div>
                    <?php }?>
                </div>
                
            </div>
            
            <div class="business-content"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        yo.dynamicTpl({
            tpl: "report/income.statement.flow.list",
            dynamic: {
                api: "report",
                method: "getIncomeStatementFlowList",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    time: "<?php echo $_GET['time']; ?>",
                    currency: "<?php echo $_GET['currency']; ?>"
                }
            },
            callback: function (_tpl) {
                $('.business-content').html(_tpl);
            }
        });
    }
</script>
