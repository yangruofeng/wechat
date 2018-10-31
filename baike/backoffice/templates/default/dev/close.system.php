<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Close System</h3>
        </div>
    </div>
    <?php $items = $output['items'];
        $system_member_app = $items[dictionaryKeyEnum::SYSTEM_CLOSE_MEMBER_APP];
        $member_app_values = json_decode($system_member_app['dict_value'], true);
        $system_co_app = $items[dictionaryKeyEnum::SYSTEM_CLOSE_CREDIT_OFFICER_APP];
        $co_app_values = json_decode($system_co_app['dict_value'], true);
        $system_console = $items[dictionaryKeyEnum::SYSTEM_CLOSE_CONSOLE];
        $console_values = json_decode($system_console['dict_value'], true);
        $system_operator = $items[dictionaryKeyEnum::SYSTEM_CLOSE_OPERATOR];
        $operator_values = json_decode($system_operator['dict_value'], true);
        $system_branch = $items[dictionaryKeyEnum::SYSTEM_CLOSE_BRANCH_MANAGER];
        $branch_values = json_decode($system_branch['dict_value'], true);
        $system_counter = $items[dictionaryKeyEnum::SYSTEM_CLOSE_COUNTER];
        $counter_values = json_decode($system_counter['dict_value'], true);
    ?>
    <div class="container" >
        <table class="table">
        <thead>
        <tr class="table-header">
            <td><?php echo 'Close Item';?></td>
            <td><?php echo 'Key';?></td>
            <td><?php echo 'State';?></td>
            <td><?php echo 'Reopen Time';?></td>
            <td><?php echo 'Close Reason';?></td>
            <td><?php echo 'Function';?></td>
        </tr>
        </thead>
        <tbody class="table-body">
            <tr>
                <td>Close Member.APP</td>
                <td><?php echo dictionaryKeyEnum::SYSTEM_CLOSE_MEMBER_APP; ?></td>
                <td>
                    <?php if($system_member_app){
                        echo $member_app_values['state'] ? 'Open' : 'Close';
                    }else{ 
                        echo 'Open';
                     } ?>
                </td>
                <td>
                    <?php if($system_member_app){
                        echo $member_app_values['state'] ? timeFormat($member_app_values['update_time']) : '--';
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <?php if($system_member_app){
                        echo $member_app_values['state'] ? '--' : $member_app_values['remark'];
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <?php if($system_member_app){ ?>
                            <?php if($member_app_values['state']){?>
                                <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_MEMBER_APP; ?>','Member APP');">
                                    <span><i class="fa fa-times"></i>Close</span>
                                </a>
                            <?php }else{ ?>
                                <a class="custom-btn custom-btn-secondary" href="javascript:;" onclick="openSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_MEMBER_APP; ?>');">
                                    <span><i class="fa fa-edit"></i>Open</span>
                                </a>
                            <?php } ?>
                        <?php }else{ ?>
                            <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_MEMBER_APP; ?>','Member APP');">
                                <span><i class="fa fa-times"></i>Close</span>
                            </a>
                        <?php } ?>
                        
                    </div>
                </td>
            </tr>
            <tr>
                <td>Close CreditPfficer.APP</td>
                <td><?php echo dictionaryKeyEnum::SYSTEM_CLOSE_CREDIT_OFFICER_APP; ?></td>
                <td>
                    <?php if($system_co_app){
                        echo $co_app_values['state'] ? 'Open' : 'Close';
                    }else{ 
                        echo 'Open';
                     } ?>
                </td>
                <td>
                    <?php if($system_co_app){
                        echo $co_app_values['state'] ? timeFormat($co_app_values['update_time']) : '--';
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <?php if($system_co_app){
                        echo $co_app_values['state'] ? '--' : $co_app_values['remark'];
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <?php if($system_co_app){ ?>
                            <?php if($co_app_values['state']){?>
                                <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_CREDIT_OFFICER_APP; ?>','Credit Officer APP');">
                                    <span><i class="fa fa-times"></i>Close</span>
                                </a>
                            <?php }else{ ?>
                                <a class="custom-btn custom-btn-secondary" href="javascript:;" onclick="openSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_CREDIT_OFFICER_APP; ?>');">
                                    <span><i class="fa fa-edit"></i>Open</span>
                                </a>
                            <?php } ?>
                        <?php }else{ ?>
                            <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_CREDIT_OFFICER_APP; ?>','Credit Officer APP');">
                                <span><i class="fa fa-times"></i>Close</span>
                            </a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Close Console</td>
                <td><?php echo dictionaryKeyEnum::SYSTEM_CLOSE_CONSOLE; ?></td>
                <td>
                    <?php if($system_console){
                        echo $console_values['state'] ? 'Open' : 'Close';
                    }else{ 
                        echo 'Open';
                     } ?>
                </td>
                <td>
                    <?php if($system_console){
                        echo $console_values['state'] ? timeFormat($console_values['update_time']) : '--';
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <?php if($system_console){
                        echo $console_values['state'] ? '--' : $console_values['remark'];
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <?php if($system_console){ ?>
                            <?php if($console_values['state']){?>
                                <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_CONSOLE; ?>','Console');">
                                    <span><i class="fa fa-times"></i>Close</span>
                                </a>
                            <?php }else{ ?>
                                <a class="custom-btn custom-btn-secondary" href="javascript:;" onclick="openSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_CONSOLE; ?>');">
                                    <span><i class="fa fa-edit"></i>Open</span>
                                </a>
                            <?php } ?>
                        <?php }else{ ?>
                            <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_CONSOLE; ?>','Console');">
                                <span><i class="fa fa-times"></i>Close</span>
                            </a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Close Operator</td>
                <td><?php echo dictionaryKeyEnum::SYSTEM_CLOSE_OPERATOR; ?></td>
                <td>
                    <?php if($system_operator){
                        echo $operator_values['state'] ? 'Open' : 'Close';
                    }else{ 
                        echo 'Open';
                     } ?>
                </td>
                <td>
                    <?php if($system_operator){
                        echo $operator_values['state'] ? timeFormat($operator_values['update_time']) : '--';
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <?php if($system_operator){
                        echo $operator_values['state'] ? '--' : $operator_values['remark'];
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <?php if($system_operator){ ?>
                            <?php if($operator_values['state']){?>
                                <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_OPERATOR; ?>','Operator');">
                                    <span><i class="fa fa-times"></i>Close</span>
                                </a>
                            <?php }else{ ?>
                                <a class="custom-btn custom-btn-secondary" href="javascript:;" onclick="openSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_OPERATOR; ?>');">
                                    <span><i class="fa fa-edit"></i>Open</span>
                                </a>
                            <?php } ?>
                        <?php }else{ ?>
                            <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_OPERATOR; ?>','Operator');">
                                <span><i class="fa fa-times"></i>Close</span>
                            </a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Close Branch Manager</td>
                <td><?php echo dictionaryKeyEnum::SYSTEM_CLOSE_BRANCH_MANAGER; ?></td>
                <td>
                    <?php if($system_branch){
                        echo $branch_values['state'] ? 'Open' : 'Close';
                    }else{ 
                        echo 'Open';
                     } ?>
                </td>
                <td>
                    <?php if($system_branch){
                        echo $branch_values['state'] ? timeFormat($branch_values['update_time']) : '--';
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <?php if($system_branch){
                        echo $branch_values['state'] ? '--' : $branch_values['remark'];
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <?php if($system_branch){ ?>
                            <?php if($branch_values['state']){?>
                                <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_BRANCH_MANAGER; ?>','Branch Manager');">
                                    <span><i class="fa fa-times"></i>Close</span>
                                </a>
                            <?php }else{ ?>
                                <a class="custom-btn custom-btn-secondary" href="javascript:;" onclick="openSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_BRANCH_MANAGER; ?>');">
                                    <span><i class="fa fa-edit"></i>Open</span>
                                </a>
                            <?php } ?>
                        <?php }else{ ?>
                            <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_BRANCH_MANAGER; ?>','Branch Manager');">
                                <span><i class="fa fa-times"></i>Close</span>
                            </a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Close Counter</td>
                <td><?php echo dictionaryKeyEnum::SYSTEM_CLOSE_COUNTER; ?></td>
                <td>
                    <?php if($system_counter){
                        echo $counter_values['state'] ? 'Open' : 'Close';
                    }else{ 
                        echo 'Open';
                     } ?>
                </td>
                <td>
                    <?php if($system_counter){
                        echo $counter_values['state'] ? timeFormat($counter_values['update_time']) : '--';
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <?php if($system_counter){
                        echo $counter_values['state'] ? '--' : $counter_values['remark'];
                    }else{ 
                        echo '--';
                     } ?>
                </td>
                <td>
                    <div class="custom-btn-group">
                        <?php if($system_counter){ ?>
                            <?php if($counter_values['state']){?>
                                <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_COUNTER; ?>','Counter');">
                                    <span><i class="fa fa-times"></i>Close</span>
                                </a>
                            <?php }else{ ?>
                                <a class="custom-btn custom-btn-secondary" href="javascript:;" onclick="openSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_COUNTER; ?>');">
                                    <span><i class="fa fa-edit"></i>Open</span>
                                </a>
                            <?php } ?>
                        <?php }else{ ?>
                            <a class="custom-btn custom-btn-danger" href="javascript:;" onclick="closeSystem('<?php echo dictionaryKeyEnum::SYSTEM_CLOSE_COUNTER; ?>','Counter');">
                                <span><i class="fa fa-times"></i>Close</span>
                            </a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
</div>
<div class="modal" id="closeSystemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo 'Close '?><span id="modelTitle"></span></h4>
            </div>
            <div class="modal-body">
                <div class="modal-form">
                    <form class="form-horizontal" id="closeSystemForm">
                        <input type="hidden" name="dict_key" value="">
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="required-options-xing">*</span><?php echo 'Close Reason'?></label>
                            <div class="col-sm-8">
                                <textarea name="close_reason" rows="5" class="form-control"></textarea>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i><?php echo 'Cancel'?></button>
                <button type="button" class="btn btn-danger" onclick="submitCloseSystem();"><i class="fa fa-check"></i><?php echo 'Submit'?></button>
            </div>
        </div>
    </div>
</div>
<script>
    function closeSystem(key, title){
        if(!key) return;
        $('#closeSystemForm input[name="dict_key"]').val(key);
        $('#modelTitle').text(title);
        $('#closeSystemModal').modal('show');
    }
    function submitCloseSystem(){
        var values = $('#closeSystemForm').getValues(), reason = $.trim(values.close_reason);
        if(!reason){
            alert('Please input close reason.');
            return;
        }
        yo.loadData({
            _c: 'dev',
            _m: 'submitcloseSystem',
            param: values,
            callback: function (_o) {
                if (_o.STS) {
                    $('#closeSystemModal').modal('hide');
                    window.location.reload(); 
                } else {
                    alert(_o.MSG);
                }
            }
        });
    }

    function openSystem(key){
        if(!key) return;
        $.messager.confirm("Confirm", "<?php echo 'Are you sure you open?' ?>", function(r){
            if (r) {
                yo.loadData({
                    _c: 'dev',
                    _m: 'submitOpenSystem',
                    param: {dict_key: key},
                    callback: function (_o) {
                        if (_o.STS) {
                            window.location.reload(); 
                        } else {
                            alert(_o.MSG);
                        }
                    }
                });
            }
        });
    }
</script>