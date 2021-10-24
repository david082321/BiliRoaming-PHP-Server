<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit(BLOCK_RETURN);}

// 判断要转发的内容
$path = explode('/index.php', $_SERVER['PHP_SELF'])[0];
$query = str_replace("/&", "", $_SERVER['QUERY_STRING']);

define('ACCESS_KEY', @$_GET['access_key']);
define('CID', @$_GET['cid']);
define('EP_ID', @$_GET['ep_id']);
define('SS_ID', @$_GET['season_id']);
define('BILIROAMING_VERSION', @$_SERVER['HTTP_X_FROM_BILIROAMING']);
$baned = 0;
if (BILIROAMING_VERSION == '') {
	if (BILIROAMING == 1 && WEB_ON == 0) { //仅限漫游用户，且未开放web脚本
		$baned = 1;
	}
	if (@$_GET['area'] == '' || @$_GET['area'] == 'false') { //web脚本
		define('AREA', 'noarea');
	} else {
		define('AREA', @$_GET['area']);
	}
} else if (@$_GET['area'] == '') { //适配老漫游版本
	define('AREA', 'oldversion');
} else {
	define('AREA', @$_GET['area']);
}
if (@$_GET['ts'] == '') {
	define('TS', time());
}else{
	define('TS', @$_GET['ts']);
}
if (in_array(EP_ID, $epid_list) && BAN_EP == 1) {
	$baned = 1;
}
if (in_array(CID, $cid_list) && BAN_CID == 1) {
	$baned = 1;
}
if (in_array(AREA, $BAN_SERVER_AREA)) {
	$baned = 1;
	block();
}

function block(){
	http_response_code(404);
	exit(BLOCK_RETURN);
}
?>