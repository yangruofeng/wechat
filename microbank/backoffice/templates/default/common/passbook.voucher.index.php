<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
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
                <h3>Journal Voucher ( <?php echo $output['title'];?> )</h3>
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
                <div class="form-group">
                    <label for="exampleInputName2">ID</label>
                    <input type="text" class="form-control input-search" name="trade_id">
                </div>
                <div class="form-group">
                    <label for="exampleInputName2">Remark</label>
                    <input type="text" class="form-control input-search" name="remark">
                </div>
                <div class="form-group">
                    <label for="exampleInputName2">Trade Type</label>
                    <select id="branch_id" class="form-control" name="trade_type" onclick="btn_search_onclick();">
                        <option value="">All Type</option>
                        <?php foreach ($output['trade_type'] as $k => $v) { ?>
                            <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default btn-search" id="btn_search_list"
                              onclick="btn_search_onclick();">
                          <i class="fa fa-search"></i>
                          <?php echo 'Search'; ?>
                      </button>
                    </span>
                </div>
                <div class="form-group">
                    <?php include(template("widget/inc_condition_datetime")); ?>
                </div>


            </form>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="basic-info">
                    <div class="business-content">
                        <div class="business-list">

                        </div>
                    </div>
                </div>
            </div>

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
        if (!_pageSize) _pageSize = 50;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _values = $('#frm_search_condition').getValues();
        _values.pageNumber = _pageNumber;
        _values.pageSize = _pageSize;
        yo.dynamicTpl({
            tpl: "common/passbook.voucher.list",
            dynamic: {
                api: "common",
                method: "getPassbookJournalVoucherList",
                param: _values
            },
            callback: function (_tpl) {
                $(".business-list").html(_tpl);
            }
        });
    }
</script>
