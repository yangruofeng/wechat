<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/report.css?v=2" rel="stylesheet" type="text/css"/>
<style>
    .summary-div {
        width: 16.66%;
        float: left;
        position: relative;
        min-height: 1px;
        padding-right: 10px;
        padding-left: 10px;
    }

    .summary-div h2 {
        margin-top: 10px!important;
    }

    .stats .stat {
        padding: 7px 12px!important;
    }
</style>
<?php $client = $output['summary']; ?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Client Member</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Main</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <?php include(template("data_center_member/client.summary")); ?>
        <?php include(template("data_center_member/client.search")); ?>

        <div class="data-center-base-info"></div>

        <div class="data-center-list"></div>
    </div>
</div>
<script>
    $(function () {
        $("#txt_search_phone").focus();
    });

    function btn_search_by_onclick(_e) {
        var _item = $(_e).val();
        if (_item == '1') {
            $("#span_phone_country").show();
        } else {
            $("#span_phone_country").hide();
        }
    }

    function btnSearch_onclick() {
        var _country_code = $('select[name="country_code"]').val();
        var _phone = $('#txt_search_phone').val();
        var _search_by = $('input:radio[name="rbn_search_by"]:checked').val();
        if (!$.trim(_phone)) return;
        window.location.href = "<?php echo getUrl('common', 'showClientDetail', array(), false, BACK_OFFICE_SITE_URL); ?>&country_code="+_country_code+"&phone_number="+_phone+"&search_by="+_search_by;
    }

    /*function btnSearch_onclick() {
        var _country_code = $('select[name="country_code"]').val();
        var _phone = $('#txt_search_phone').val();
        var _search_by = $('input:radio[name="rbn_search_by"]:checked').val();

        if (!$.trim(_phone)) {
            return;
        }

        $(".data-center-base-info").waiting();
        yo.dynamicTpl({
            tpl: "common/client.detail",
            dynamic: {
                api: "common",
                method: "getClientDetail",
                param: {country_code: _country_code, phone_number: _phone, search_by: _search_by}
            },
            callback: function (_tpl) {
                $(".data-center-base-info").unmask();
                $(".data-center-base-info").html(_tpl);
                $(".data-center-list").html('');
            }
        });
    } */
</script>
