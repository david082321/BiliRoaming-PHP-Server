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
define('QN', @$_GET['qn']);
define('BILIROAMING_VERSION', @$_SERVER['HTTP_X_FROM_BILIROAMING']);
define('BILIROAMING_VERSION_CODE', @$_SERVER['HTTP_BUILD']);
$baned = 0;
$th_paths = array("/intl/gateway/v2/app/search/type","/intl/gateway/v2/app/search/v2","/intl/gateway/v2/app/subtitle","/intl/gateway/web/v2/subtitle","/intl/gateway/v2/ogv/view/app/season","/intl/gateway/v2/ogv/view/app/season2","/intl/gateway/v2/ogv/playurl");
$get_area = @$_GET['area'];
if (BILIROAMING_VERSION == '' && BILIROAMING_VERSION_CODE == '') {
	if (BILIROAMING == 1 && WEB_ON == 0) { //仅限漫游用户，且未开放web脚本
		block(10, "服务器限漫游使用");
	}
	if ($get_area == '' || $get_area == 'false') { //web脚本,兼容泰区无area情况
		if (in_array($path, $th_paths)) {
			define('AREA', 'th');
		} else {
			define('AREA', 'noarea');
		}
	} else {
		define('AREA', $get_area);
	}
} else if (BILIROAMING_VERSION != '' && BILIROAMING_VERSION_CODE != '') {
	if ((int)BILIROAMING_VERSION_CODE < ROAMING_MIN_VER) {
		block(14, "哔哩漫游模块版本过低");
	}
	//适配老漫游版本，兼容泰区无area情况
	if ($get_area == '') {
		if (in_array($path, $th_paths)) {
			define('AREA', 'th');
		} else {
			define('AREA', 'oldversion');
		}
	} else {
		define('AREA', $get_area);
	}
} else {
	block(15, "错误请求头");
}
if (@$_GET['ts'] == '') {
	define('TS', time());
}else{
	define('TS', @$_GET['ts']);
}
if (in_array(EP_ID, $epid_list) && BAN_EP == 1) {
	block(11, "ep_id黑名单");
}
if (in_array(CID, $cid_list) && BAN_CID == 1) {
	block(12, "cid黑名单");
}
if (in_array(AREA, $BAN_SERVER_AREA)) {
	block(13, "area黑名单");
}

function block($code, $reason){
	http_response_code(200); // B站就是都返回200
	exit('{"code":-'.$code.',"message":"'.$reason.'"}');
}
?>
