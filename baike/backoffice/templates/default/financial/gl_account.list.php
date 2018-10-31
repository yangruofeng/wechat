<style>
    #ul_main {
        padding-left: 50px;
        padding-bottom: 10px;
    }
    #ul_main .btn {
        padding: 9.5px 12px;
    }
    #ul_main span i {
        transform: rotate(0deg);
        -webkit-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
        -ms-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transition: all .5s ease;
        -webkit-transition: all .5s ease;
        -moz-transition: all .5s ease;
        -ms-transition: all .5s ease;
        -o-transition: all .5s ease;
    }
    #ul_main span.btn-up i {
        transform: rotate(90deg);
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transition: all .5s ease;
        -webkit-transition: all .5s ease;
        -moz-transition: all .5s ease;
        -ms-transition: all .5s ease;
        -o-transition: all .5s ease;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Region</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('gl_account', 'add', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Add</span></a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <ul class="list-group" id="ul_main">
            <?php echo $output['gl_account_tree']?>
        </ul>
    </div>
</div>
<script>
    $('#ul_main').delegate('.up-away', 'click', function () {
        var scrollTop = $(window).scrollTop();
        if ($(this).hasClass('btn-away')) {
            if ($(this).attr('is_child') == 0) {
                var uid = $(this).attr('uid');
                yo.loadData({
                    _c: "gl_account",
                    _m: "getHtml",
                    param: {uid: uid},
                    callback: function (_o) {
                        if (_o.STS) {
                            var data = _o.DATA;
                            var _this = $(".up-away[uid='" + uid + "']");
                            _this.attr('is_child', 1);
                            _this.removeClass('btn-away').addClass('btn-up');
                            _this.closest('.input-group').next().html(data).show(500);
                            $(window).scrollTop(scrollTop);
                        } else {
                            alert(_o.MSG,2);
                        }
                    }
                });
            } else {
                $(this).removeClass('btn-away').addClass('btn-up');
                $(this).closest('.input-group').next().show(500);
                return false;
            }
        } else {
            $(this).removeClass('btn-up').addClass('btn-away');
            $(this).closest('.input-group').next().hide(500);
            $(this).closest('.input-group').next().find('.up-away').removeClass('btn-up').addClass('btn-away');
            $(this).closest('.input-group').next().find('.input-group').next().hide();
            return false;
        }
    })
</script>