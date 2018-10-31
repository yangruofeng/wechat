<script>

    /****************************************************************override by tim*********************************************/

    $(document).ready(function () {
        <?php if($output['has_task']){?>
        setInterval(getTaskPendingCount, 10000);
        $('.hint-switch').click(function () {
            var hint = $(this).attr('hint');
            if (hint == 'open') {
                $(this).attr('hint', 'close');
                $(this).attr('src', 'resource/img/hint_close.png');

            } else {
                $(this).attr('hint', 'open');
                $(this).attr('src', 'resource/img/hint_open.png');
            }
            return false;
        });
        <?php }?>
    });

    function playHint() {
        $('#task-hint')[0].play();
    }

    var _task_time = 0;
    function getTaskPendingCount() {
        yo.loadData({
            _c: 'index',
            _m: 'getTaskPendingCount',
            param: {task_time: _task_time},
            callback: function (_o) {
                if (!_o.STS) return;
                _task_time = _o.DATA.task_time;
                var _data = _o.DATA.list;
                var _hint_play = false;
                for (var _task_k in _data) {
                    var _item = _data[_task_k];;
                    if (_item.count_new > 0) {
                        var _hint_state = $('.submenu a[data-task-code="' + _task_k + '"]').find(".hint-switch").attr('hint');
                        if (_hint_state == 'open') {
                            _hint_play = true;
                        }
                    }
                    $('.menu_a[data-task-code="' + _task_k + '"]').find(".label-important").text(_item.count_pending);
                    $('.menu_x[data-task-code="' + _task_k + '"]').find(".label-important").text(_item.count_pending);
                    if (_task_k == '<?php echo userTaskTypeEnum::OPERATOR_NEW_CERT;?>'
                        || _task_k == '<?php echo userTaskTypeEnum::OPERATOR_RELATIVE_NEW_CERT; ?>'
                    ) {
                        //console.log(_task_k);
                        //处理cert的子集
                        var _group_by = _item.group_by;
                        for (var _group_k in _group_by) {
                            //$('.menu_a[data-cert-code="' + _group_k + '"]').find(".label-important").text(_group_by[_group_k].count_pending);
                            $('.submenu[data-task-type="'+_task_k+'"]').find('.menu_a[data-cert-code="' + _group_k + '"]').find(".label-important").text(_group_by[_group_k].count_pending);

                        }
                    }
                }
                if (_hint_play) {
                    playHint();
                }
            }
        });
    }

</script>