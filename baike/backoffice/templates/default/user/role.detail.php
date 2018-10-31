<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/user.css?v=8" rel="stylesheet" type="text/css"/>
<style>
    .log-list .table tr td:nth-child(6n+1), .log-list .table tr td:nth-child(6n+2) {
        display: none;
    }
</style>
<?php $allow_back_office = $output['role_info']['allow_back_office']?>
<?php $allow_counter = $output['role_info']['allow_counter']?>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>User</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'role', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('user', 'addRole', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="client-detail-wrap clearfix">
            <div class="left-wrap">
                <div class="member-info">
                    <div class="ibox-title">
                        <h5><?php echo $output['role_info']['role_name']?></h5>
                    </div>
                    <div class="ibox-content" style="padding:0;">
                        <table class="table">
                            <tbody class="table-body">
                            <tr style="background: #F3F4F6">
                                <td><label class="control-label">Back Office</label></td>
                                <td>
                                    <?php foreach ($output['auth_group_back_office'] as $key => $group) {
                                        if (in_array($key, $allow_back_office['role_group'])) { ?>
                                            <div style="font-weight: 600"><?php echo L('auth_' . strtolower($key)) ?></div>
                                            <?php $auth_select = array();
                                            foreach ($group as $auth) {
                                                if (in_array($auth, $allow_back_office['allow_auth'])) {
                                                    $auth_select[] = L('auth_' . strtolower($auth));
                                                }
                                            } ?>
                                            <div style="padding-left: 20px;margin-bottom: 5px"><?php echo implode(' / ', $auth_select) ?></div>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr style="background-color: #FFFFFF">
                                <td><label class="control-label">Counter</label></td>
                                <td>
                                    <?php foreach ($output['auth_group_counter'] as $key => $group) {
                                        if (in_array($key, $allow_counter['role_group'])) { ?>
                                            <div style="font-weight: 600"><?php echo L('auth_counter_' . strtolower($key)) ?></div>
                                            <?php $auth_select = array();
                                            foreach ($group as $auth) {
                                                if (in_array($auth, $allow_counter['allow_auth'])) {
                                                    $auth_select[] = L('auth_counter_' . strtolower($auth));
                                                }
                                            } ?>
                                            <div style="padding-left: 20px;margin-bottom: 5px"><?php echo implode(' / ', $auth_select) ?></div>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="wrap-right">
                <div class="contract-wrap">
                    <div class="contract-list">
                        <div class="ibox-title">
                            <h5>User List</h5>
                        </div>
                        <div class="ibox-content user-list" style="padding:0;">

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    $(document).ready(function () {
        btn_search_onclick();
    });

    function btn_search_onclick(_pageNumber, _pageSize) {
        if (!_pageNumber) _pageNumber = $(".business-content").data('pageNumber');
        if (!_pageSize) _pageSize = $(".business-content").data('pageSize');
        if (!_pageNumber) _pageNumber = 1;
        if (!_pageSize) _pageSize = 20;
        $(".business-content").data("pageNumber", _pageNumber);
        $(".business-content").data("pageSize", _pageSize);

        var _uid = '<?php echo $output['role_info']['uid']?>';

        yo.dynamicTpl({
            tpl: "user/role.user.list",
            dynamic: {
                api: "user",
                method: "getUserListByRole",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, role_id: _uid}
            },
            callback: function (_tpl) {
                $(".user-list").html(_tpl);
            }
        });
    }
</script>