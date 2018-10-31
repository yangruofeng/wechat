<link href="<?php echo BACK_OFFICE_SITE_URL?>/resource/css/user.css?v=8" rel="stylesheet" type="text/css"/>
<style>
    .log-list .table tr td:nth-child(6n+1), .log-list .table tr td:nth-child(6n+2) {
        display: none;
    }
</style>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>User</h3>
            <ul class="tab-base">
                <li><a href="<?php echo getUrl('user', 'user', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>List</span></a></li>
                <li><a href="<?php echo getUrl('user', 'addUser', array(), false, BACK_OFFICE_SITE_URL) ?>"><span>Add</span></a></li>
                <li><a class="current"><span>Detail</span></a></li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="client-detail-wrap clearfix">
            <dl class="account-basic clearfix">
                <dt class="pull-left">
                <p class="account-head">
                    <img src="<?php echo getImageUrl($output['user_info']['user_icon'], imageThumbVersion::AVATAR) ?: ENTRY_DESKTOP_SITE_URL . DS . 'resource/img/default_avatar.jpg'?>" class="avatar-lg" alt="">
                </p>
                </dt>
                <dd class="pull-left margin-large-left margin-large-right">
                    <p class="text-small">
                        <span class="show pull-left base-name marginright10">User Name</span>:
                        <span class="marginleft10"><?php echo $output['user_info']['user_name']?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright10">Branch</span>:
                        <span class="marginleft10"><?php echo $output['user_info']['branch_name']?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright10">Position</span>:
                        <span class="marginleft10">
                            <?php echo ucwords(str_replace('_', ' ', $output['user_info']['user_position']))?>
                        </span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright10">Email</span>:
                        <span class="marginleft10"><?php echo $output['user_info']['email']?></span>
                    </p>
                </dd>
                <dd class="pull-left margin-large-left">
                    <p class="text-small">
                        <span class="show pull-left base-name marginright10">User Code</span>:
                        <span class="marginleft10"><?php echo $output['user_info']['user_code']?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright10">Department</span>:
                        <span class="marginleft10"><?php echo $output['user_info']['depart_name']?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright10">Mobile Phone</span>:
                        <span class="marginleft10"><?php echo $output['user_info']['mobile_phone']?></span>
                    </p>
                    <p class="text-small">
                        <span class="show pull-left base-name marginright10">Status</span>:
                        <span class="marginleft10"><?php echo $output['user_info']['user_status'] == 1 ? 'Valid' : 'Invalid'?></span>
                    </p>
                </dd>
            </dl>

            <div class="left-wrap">
                <div class="member-info">
                    <div class="ibox-title">
                        <h5>Authority</h5>
                    </div>
                    <div class="ibox-content" style="padding:0;">
                        <table class="table">
                            <tbody class="table-body">
                            <?php if($output['user_info']['user_position'] == userPositionEnum::BACK_OFFICER){?>
                            <tr>
                                <td><label class="control-label">Back office(Allow)</label></td>
                                <td>
                                    <?php echo implode(' / ',$output['allow_auth_back_office'])?>
                                </td>
                            </tr>
                            <tr>
                                <td><label class="control-label">Back office(Limit)</label></td>
                                <td>
                                    <?php echo implode(' / ',$output['limit_auth_back_office'])?>
                                </td>
                            </tr>
                            <?php } elseif(in_array($output['user_info']['user_position'],array(userPositionEnum::CHIEF_TELLER,userPositionEnum::TELLER))) { ?>
                                <tr>
                                    <td><label class="control-label">Counter(Allow)</label></td>
                                    <td>
                                        <?php echo implode(' / ',$output['allow_auth_counter'])?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Counter(Limit)</label></td>
                                    <td>
                                        <?php echo implode(' / ',$output['limit_auth_counter'])?>
                                    </td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="2">Null</td>
                                </tr>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="wrap-right">
                <div class="contract-wrap">
                    <div class="contract-list">
                        <div class="ibox-title">
                            <h5>Log List</h5>
                        </div>
                        <div class="ibox-content log-list" style="padding:0;">

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

        var _uid = '<?php echo $output['user_info']['uid']?>';

        yo.dynamicTpl({
            tpl: "user/log.list",
            dynamic: {
                api: "user",
                method: "getLogList",
                param: {pageNumber: _pageNumber, pageSize: _pageSize, uid: _uid}
            },
            callback: function (_tpl) {
                $(".log-list").html(_tpl);
            }
        });
    }
</script>