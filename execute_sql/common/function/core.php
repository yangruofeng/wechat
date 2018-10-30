<?php

function urlDesktop($act = '', $op = '', $args = array()) {
    return getUrl($act, $op, $args, false, DESKTOP_SITE_URL);
}

/**
 * 加载广告
 *
 * @param  $ap_id 广告位ID
 * @param $type 广告返回类型 html,js
 */
function loadadv($ap_id = null, $type = 'html'){
    if (!is_numeric($ap_id)) return false;
    require_once('adv.php');
    return advshow($ap_id,$type);
}