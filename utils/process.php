<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

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
		$baned = 10;
		block($baned);
	}
	if (@$_GET['area'] == '' || @$_GET['area'] == 'false') { //web脚本,以及泰区相关AREA定义为TH
		if ($path == "/intl/gateway/v2/app/search/type" || $path == "/intl/gateway/v2/app/subtitle" || $path == "/intl/gateway/v2/ogv/view/app/season") {
			define('AREA', 'th');
		} else {
			define('AREA', 'noarea');
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
	$baned = 11;
	block($baned);
}
if (in_array(CID, $cid_list) && BAN_CID == 1) {
	$baned = 12;
	block($baned);
}
if (in_array(AREA, $BAN_SERVER_AREA)) {
	$baned = 13;
	block($baned);
}

function block($baned){
	switch ($baned) {
		case 10:
			$reason = "服务器限漫游使用";
			break;
		case 11:
			$reason = "ep_id黑名单";
			break;
		case 12:
			$reason = "cid黑名单";
			break;
		case 13:
		case 30:
			$reason = "area黑名单";
			break;
		case 20:
			$reason = "访问密钥已过期或不存在(脚本设置左下角重新授权)";
			break;
		case 21:
			$reason = "uid黑名单";
			break;
		case 22:
			$reason = "uid不在白名单";
			break;
		case 23:
			$reason = "未提供访问密钥(漫游需要登录、脚本需要授权)";
			break;
		case 31:
			$reason = "此API仅限漫游用户，若误封请到这里提出 github.com/david082321/BiliRoaming-PHP-Server/issues";
			break;
		case 32:
			$reason = "服务器未开放web接口";
			break;
		default:
			$reason = "未知错误";
	}
	http_response_code(404);
	exit('{"code":-418,"message":"'.$reason.'"}');
}
?>
