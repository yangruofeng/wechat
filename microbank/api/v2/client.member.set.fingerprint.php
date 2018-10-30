<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/1/24
 * Time: 10:06
 */
// client.member.set.fingerprint

$_GET['act']	= 'api_counter';
$_GET['op']		= 'setClientMemberFingerprint';
require_once(dirname(__FILE__) . '/../cookieless.php');