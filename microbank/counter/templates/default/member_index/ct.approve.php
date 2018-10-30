<link href="<?php echo ENTRY_COUNTER_SITE_URL; ?>/resource/css/member.css?v=6" rel="stylesheet" type="text/css"/>

<div class="page">
    <video id="task-hint" src="resource/voice/hint.mp3" style="display: none" preload="auto"></video>
    <div class="row">
        <div class="col-sm-2">
            <div style="background-color: #0099CC;color:#ffffff;padding: 10px;border-radius: 5px;text-align: center">
                <h3 id="h_task_new"><?php echo $output['task_new_count']?></h3>
                <h5>New Task</h5>
            </div>
        </div>
        <div class="col-sm-2">
            <div style="background-color: #009900;color: #ffffff;padding: 10px;border-radius: 5px;text-align: center">
                <h3 id="h_task_total"><?php echo $output['task_total_count']?></h3>
                <h5>Total Task</h5>
            </div>
        </div>
    </div>
    <?php foreach ($output['biz_list'] as $biz_code=>$biz){?>
        <div class="row biz-row" style="<?php if(!count($biz['items'])) echo 'display:none'?>">
            <ul class="list-group" style="margin-top: 20px">
                <li class="list-group-item list-group-item-text">
                    <span style="padding: 10px;font-size: 15px;font-weight: bold;background-color:lawngreen;"><?php echo $biz['biz_caption']?></span>
                </li>
                <li class="list-group-item">
                    <table class="table table-no-background biz-table-<?php echo $biz_code?>">
                        <tr class="table-header">
                            <td colspan="2">
                                Client
                            </td>
                            <td>
                                Teller
                            </td>
                            <td>
                                Amount
                            </td>
                            <td>Scene</td>
                            <td>
                                Function
                            </td>
                        </tr>
                        <?php foreach($biz['items'] as $item){?>
                            <?php echo $item['tpl']?>
                        <?php }?>
                    </table>
                </li>
            </ul>
        </div>
    <?php }?>

</div>
<script>
    var m_last_get_time=0;
    var m_timer_id=null;
    function reloadTask(){
        yo.loadData({
            _c:"member_index",
            _m:"getChiefTellerApproveTask",
            param:{last_get_time:m_last_get_time},
            callback:function(_o){
                _o=_o.DATA;
                console.log(_o);
                m_last_get_time=_o.last_get_time;
                var _tpl_items=_o.items;
                var _item_keys=_o.item_keys;
                var _old_item_keys=[];
                $(".tr-approve-item").each(function(){
                    var _item_key=$(this).data("biz-code")+$(this).data("biz-id");
                    if(_item_keys.indexOf(_item_key)<0){
                        $(this).remove();//先删除
                    }else{
                        _old_item_keys.push(_item_key);
                    }
                });
                for(var _i in _tpl_items){
                    var _tpl_item=_tpl_items[_i];
                    if(_old_item_keys.indexOf(_tpl_item.key)<0){
                        $(".biz-table-"+_tpl_item.biz_code).append(_tpl_item.tpl);
                        $(".biz-table-"+_tpl_item.biz_code).closest(".row").show();
                    }
                }
                $(".biz-row").each(function(){
                    if($(this).find(".tr-approve-item").length==0){
                        $(this).hide();
                    }
                });
                m_timer_id=setTimeout(reloadTask,2000);
                if(_o.task_new>0){
                    $('#task-hint')[0].play();
                }
                $("#h_task_new").text(_o.task_new);
                $("#h_task_total").text(_o.task_total);
            }
        });
    }
    $(document).ready(function(){
        reloadTask();
    });
</script>