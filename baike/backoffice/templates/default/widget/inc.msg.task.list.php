<?php
    $task_list=$output['msg_task_list'];
?>
<?php if(count($task_list)){?>
<div class="row" style="padding-left: 50px">
    <div>
        New Task
        <label>
            <a style="font-weight: bold;color: red;font-size: 15px" class="btn btn-link btn-count" data-count="<?php echo count($task_list)?>"  role="button" data-toggle="collapse" href="#div_task_list" aria-expanded="false" aria-controls="div_task_list">
                <?php echo count($task_list)?>
            </a>
        </label>
        <button class="btn btn-link text-right"   role="button" data-toggle="collapse" href="#div_task_list" aria-expanded="false" aria-controls="div_task_list">
            <i class="fa fa-angle-down"></i>
        </button>
    </div>
    <div id="div_task_list" aria-expanded="false" class="collapse">
        <ul class="list-group">
            <?php foreach($task_list as $task_item){?>
                <li class="list-group-item">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" onclick="btn_finish_msg_task(this)"
                                data-task_id="<?php echo $task_item['task_id']?>"
                                data-task_type="<?php echo $task_item['task_type']?>"
                                data-receiver_id="<?php echo $task_item['receiver_id']?>"
                                data-receiver_type="<?php echo $task_item['receiver_type']?>"
                                class="close"><span aria-hidden="true">&times;</span></button>
                        <strong><?php echo strtoupper(str_replace("_"," ",$task_item['task_type']))?></strong>
                    <span>
                        <?php echo $task_item['msg']?>
                    </span>
                    </div>
                </li>
            <?php }?>
        </ul>
    </div>
</div>
    <script>
        function btn_finish_msg_task(_e){

            var _args={};
            _args.task_id=$(_e).data("task_id");
            _args.task_type=$(_e).data("task_type");
            _args.receiver_id=$(_e).data("receiver_id");
            _args.receiver_type=$(_e).data("receiver_type");
            var _ul=$(_e).closest(".list-group");
            _ul.waiting();
            yo.loadData({
                _c:"back_office_base",
                _m:'finishMsgTask',
                param:_args,
                callback:function(_o){
                    _ul.unmask();
                    if(!_o.STS){
                        alert(_o.MSG);
                    }else{
                        var _cnt=$(_e).find(".btn-count").data("count");
                        var _new_cnt=parseInt(_cnt)-1;
                        $(_e).find(".btn-count").data("count",_new_cnt);
                        $(_e).find(".btn-count").html(_new_cnt);
                        $(_e).closest(".list-group-item").remove();
                        if(_ul.find(".list-group-item").length==0){
                            _ul.remove();
                        }


                    }
                }
            });
        }
    </script>

<?php }?>