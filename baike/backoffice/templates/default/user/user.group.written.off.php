<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>User</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Written Off Loan</span></a></li>
                <li><a href="<?php echo getUrl('user', 'committee', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Grant Credit</span></a></li>
                <li><a href="<?php echo getUrl('user', 'committeeFastCredit', array(), false, BACK_OFFICE_SITE_URL)?>"><span>Fast Credit</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="business-condition">
            <form class="form-inline" id="frm_search_condition" action="<?php echo getUrl("user","addUserToGroup",array(),false,BACK_OFFICE_SITE_URL)?>" method="post">
                <input type="hidden" name="group_key" value="<?php echo $output['group_key']?>">
                <div class="input-group">
                    <input type="text" class="form-control" required="true" name="user_code" placeholder="Input User-Code">
                                <span class="input-group-btn">
                                  <button type="submit" class="btn btn-default">
                                      <i class="fa fa-plus"></i> <?php echo 'ADD'; ?>
                                  </button>
                                </span>
                </div>
            </form>
        </div>

        <div class="business-content">
            <div class="business-list">
                <table class="table table-hover" style="width: 500px">
                    <tr class="table-header">
                        <td>User Code</td>
                        <td>User Name</td>
                        <td>Function</td>
                    </tr>
                    <?php foreach($output['user_list'] as $item){?>
                        <tr class="tr-group-item">
                            <td><?php echo $item['user_code']?></td>
                            <td><?php echo $item['user_name']?></td>
                            <td>
                                <button class="btn btn-default" data-user-id="<?php echo $item['user_id']?>" type="button" onclick="remove_user_onclick(this)">Delete</button>
                            </td>
                        </tr>
                    <?php }?>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function remove_user_onclick(_e){
        var _uid=$(_e).data("user-id");
        if (!_uid) {
            return;
        }
        var _tr_group_item=$(_e).closest(".tr-group-item");

        $.messager.confirm("<?php echo $lang['common_delete']?>", "<?php echo $lang['common_confirm_delete']?>", function (_r) {
            if (!_r) return;
            $(document).waiting();
            yo.loadData({
                _c: "user",
                _m: "removeUserFromGroup",
                param: {group_key: "<?php echo $output['group_key']?>",user_id:_uid},
                callback: function (_o) {
                    $(document).unmask();
                    if (_o.STS) {
                        _tr_group_item.remove();
                        alert(_o.MSG);
                    } else {
                        alert(_o.MSG);
                    }
                }
            });
        });
    }

</script>
