<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Member Biz Limit</h3>
<!--            <ul class="tab-base">-->
<!--                <li><a href="--><?php //echo getUrl('dev', 'businessSwitch', array(), false, BACK_OFFICE_SITE_URL) ?><!--"><span>basic</span></a>-->
<!--                </li>-->
<!--                <li><a class="current"><span></span></a></li>-->
<!--            </ul>-->
        </div>
    </div>
    <div class="container" style="width: 700px;">
        <form class="form-horizontal" method="post" action="<?php echo getUrl('dev', 'memberBizLimit', array(), false, BACK_OFFICE_SITE_URL) ?>">
            <input type="hidden" name="form_submit" value="ok">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-4 control-label"><?php echo 'Member Grade' ?></label>
                <div class="col-sm-8">
                    <select class="col-sm-12 form-control" name="member_grade">
                        <?php foreach ($output['member_grade'] as $value) { ?>
                            <option value="<?php echo $value['uid'] ?>"><?php echo $value['grade_code'] ?> </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="business-list">
            </div>
        </form>
        <div class="form-group">
            <div class="col-sm-offset-4 col-col-sm-8" style="padding-left: 15px">
                <button type="button" class="btn btn-danger" id="save-limit"><i class="fa fa-check"></i><?php echo 'Save' ?></button>
                <button type="button" class="btn btn-default" onclick="javascript:history.go(-1);"><i class="fa fa-reply"></i><?php echo 'Back' ?></button>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        $('#save-limit').click(function () {
            $('.form-horizontal').submit();
        })

        $('select[name="member_grade"]').change(function () {
            get_limit();
        })

        get_limit();
    });

    function get_limit() {
        var _member_grade = $('select[name="member_grade"]').val();
        $('.page').waiting();
        yo.dynamicTpl({
            tpl: "dev/member.biz.limit.detail",
            dynamic: {
                api: "dev",
                method: "getMemberBizLimitByGrade",
                param: {member_grade: _member_grade}
            },
            callback: function (_tpl) {
                $('.page').unmask();
                $(".business-list").html(_tpl);
            }
        });
    }
</script>