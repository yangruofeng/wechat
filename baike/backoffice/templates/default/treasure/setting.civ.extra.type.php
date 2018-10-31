<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>CIV Ext.Trade Type</h3>
            <ul class="tab-base">
                <li><a class="current"><span>List</span></a></li>
                <li>
                    <a href="<?php echo getUrl('treasure', 'addCIVExtraType', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container">
        <table class="table">
            <thead>
            <tr class="table-header">
                <td><?php echo 'No.'; ?></td>
                <td><?php echo 'Trade Type'; ?></td>
                <td><?php echo 'Ext Type'; ?></td>
                <td><?php echo 'Creator'; ?></td>
                <td><?php echo 'Update Time'; ?></td>
                <td><?php echo 'Function'; ?></td>
            </tr>
            </thead>
            <tbody class="table-body">
            <?php $list = $output['list'];?>
            <?php if ($list) { ?>
                <?php $i = 0;foreach ($list as $key => $row) { ++$i?>
                    <tr>
                        <td>
                            <?php echo $i?>
                        </td>
                        <td>
                            <?php echo $row['trade_type'] ?>
                        </td>
                        <td>
                            <?php echo $row['ext_type']>0 ? 'Cash In' : 'Cash Out' ?>
                        </td>
                        <td>
                            <?php echo $row['creator_name']; ?>
                        </td>
                        <td>
                            <?php echo timeFormat($row['update_time']); ?>
                        </td>
                        <td>
                            <div class="custom-btn-group">
                                <a title="" class="custom-btn custom-btn-secondary"
                                   href="<?php echo getUrl('treasure', 'editCIVExtraType', array('uid' => $row['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <span><i class="fa fa-edit"></i>Edit</span>
                                </a>
                            </div>
                            <div class="custom-btn-group" style="margin-left: 10px">
                                <a title="" class="custom-btn custom-btn-secondary" href="#" onclick="delType(<?php echo $row['uid'];?>)">
                                    <span><i class="fa fa-remove"></i>Delete</span>
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6">No Record</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    function delType(uid){
        if (!uid) {
            return;
        }
        $.messager.confirm("<?php echo $lang['common_delete']?>", "<?php echo $lang['common_confirm_delete']?>", function (_r) {
            if (!_r) return;
            $(".business-content").waiting();
            yo.loadData({
                _c: "treasure",
                _m: "delCIVExtraType",
                param: {uid: uid},
                callback: function (_o) {
                    $(".business-content").unmask();
                    if (_o.STS) {
                        alert(_o.MSG,1,function(){
                            window.location.reload();
                        });
                    } else {
                        alert(_o.MSG,2);
                    }
                }
            });
        });
    }
</script>
