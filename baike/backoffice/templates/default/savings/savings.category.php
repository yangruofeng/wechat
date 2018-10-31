<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>Category</h3>
            <ul class="tab-base">
                <li><a class="current"><span>Category List</span></a></li>
                <li>
                    <a href="<?php echo getUrl('savings', 'editCategory', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add Category</span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container" style="width: 99%">
        <div class="business-content">
            <table class="table table-hover table-striped table-bordered">
                <tr class="table-header">
                    <td>ID</td>
                    <td>Category Code</td>
                    <td>Category Name</td>
                    <td>Category Type</td>
                    <td>Term Style</td>
                    <td>State</td>
                    <td>Creator</td>
                    <td>Time</td>
                    <td>Function</td>
                </tr>
                <?php if ($output['list']) { ?>
                    <?php foreach ($output['list'] as $item) { ?>
                        <tr>
                            <td>
                                <?php echo $item['uid'] ?>
                            </td>
                            <td>
                                <?php echo $item['category_code'] ?>
                            </td>
                            <td>
                                <?php echo $item['category_name'] ?>
                            </td>
                            <td>
                                <?php echo ucwords(str_replace('_', ' ', $item['category_type'])) ?>
                            </td>
                            <td>
                                <?php echo ucwords(strtolower((new savingsCategoryTermStyleEnum())->Dictionary()[$item['category_term_style']])) ?>
                            </td>
                            <td>
                                <?php echo $lang['savings_category_state_' . $item['state']] ?>
                            </td>
                            <td>
                                <?php echo $item['creator_name'] ?>
                            </td>
                            <td>
                                <?php echo $item['update_time'] ?>
                            </td>
                            <td>
                                <a class="btn btn-link btn-xs" href="<?php echo getUrl("savings", "editCategory", array('uid' => $item['uid']), false, BACK_OFFICE_SITE_URL) ?>">
                                    <i class="fa fa-edit"></i>
                                    Edit
                                </a>
                                <a class="btn btn-link btn-xs" data-uid="<?php echo $item['uid']?>" onclick="btn_remove_category_onclick(this)" >
                                    <i class="fa fa-trash"></i>
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7">NO RECORD</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
<script>
    function btn_remove_category_onclick(_e) {
        var _uid = $(_e).data("uid");
        $.messager.confirm("Confirm", "are you sure to delete this record?", function (_r) {
            if (!_r) return;
            $(document).waiting();
            yo.loadData({
                _c: "savings",
                _m: "removeCategory",
                param: {uid: _uid},
                callback: function (_o) {
                    $(document).unmask();
                    if (!_o.STS) {
                        alert(_o.MSG);
                    } else {
                        window.location.reload();
                    }
                }
            });
        })
    }
</script>
