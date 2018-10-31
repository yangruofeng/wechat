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
<?php $info = $output['info'];?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Balance Sheet</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('report', 'balance_sheet', array(), false, BACK_OFFICE_SITE_URL);?>"><span>Main</span></a></li>
                <li><a href="<?php echo getUrl('report', $output['back'], array(), false, BACK_OFFICE_SITE_URL);?>"><span>Liability</span></a></li>
                <li><a class="current"><span>Flow</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="col-sm-12">
            <div class="basic-info">
                <div class="ibox-title">
                    <h5><i class="fa fa-money"></i>&nbsp;<?php echo $output['title'];?></h5>
                </div>
                
                <div class="col-sm-12 user-info">
                    <?php if($info){?>
                        <div class="col-sm-3">
                            CID: <label for=""><?php echo $info['obj_guid'];?></label>
                        </div>
                        <div class="col-sm-3">
                            Login Code: <label for=""><?php echo $info['login_code'];?></label>
                        </div>
                        <div class="col-sm-3">
                            User Name: <label for=""><?php echo $info['display_name'];?></label>
                        </div>
                        <div class="col-sm-3">
                            Mobile Phone: <label for=""><?php echo $info['phone_id'];?></label>
                        </div>
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

        var _search_text = $('#search_text').val();
        yo.dynamicTpl({
            tpl: "report/receivable.principal.flow.list",
            dynamic: {
                api: "report",
                method: "<?php echo $output['method'];?>",
                param: {
                    pageNumber: _pageNumber,
                    pageSize: _pageSize,
                    pid: '<?php echo $_GET['pid'];?>'
                }
            },
            callback: function (_tpl) {
                $('.business-content').html(_tpl);
            }
        });
    }
</script>