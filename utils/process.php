<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

// 判断要转发的内容
$path = explode('/index.php', $_SERVER['PHP_SELF'])[0];
$query = str_replace("&&", "",str_replace("/&", "", $_SERVER['QUERY_STRING']));
// 修复参数开头为 & 的问题
if (substr($query,0,1) == "&") {
	$query = substr($query,1);
}
// 日志参数
if (SAVE_LOG == 1) {
	define('PATH', $path);
	define('QUERY', $query);
}

if (@$_GET['appkey'] == "") {
	define('APPKEY', "1d8b6e7d45233436"); // 兼容web脚本
} else {
	define('APPKEY', @$_GET['appkey']);
}
define('ACCESS_KEY', @$_GET['access_key']);
define('CID', @$_GET['cid']);
define('EP_ID', @$_GET['ep_id']);
define('SS_ID', @$_GET['season_id']);
define('QN', @$_GET['qn']);
define('BILIROAMING_VERSION', @$_SERVER['HTTP_X_FROM_BILIROAMING']);
define('BILIROAMING_VERSION_CODE', @$_SERVER['HTTP_BUILD']);
$baned = 0;
$th_paths = array("/intl/gateway/v2/app/search/type","/intl/gateway/v2/app/search/v2","/intl/gateway/v2/app/subtitle","/intl/gateway/web/v2/subtitle","/intl/gateway/v2/ogv/view/app/season","/intl/gateway/v2/ogv/view/app/season2","/intl/gateway/v2/ogv/playurl","/intl/gateway/v2/ogv/view/app/episode");
$get_area = @$_GET['area'];
if (BILIROAMING_VERSION == '' && BILIROAMING_VERSION_CODE == '') {
	if (BILIROAMING == 1 && WEB_ON == 0 && $path!="") { // 仅限漫游用户，且未开放web脚本
		define('AREA', $get_area);
		block(10, "本服务器限漫游使用");
	}
	if ($get_area == '' || $get_area == 'false') { // web脚本,兼容泰区无area情况
		if (in_array($path, $th_paths)) {
			define('AREA', 'th');
		} else {
			define('AREA', 'noarea');
		}
	} else {
		define('AREA', $get_area);
	}
} elseif (BILIROAMING_VERSION != '' && BILIROAMING_VERSION_CODE != '') {
	// 适配老漫游版本，兼容泰区无area情况
	if ($get_area == '') {
		if (in_array($path, $th_paths)) {
			define('AREA', 'th');
		} else {
			define('AREA', 'oldversion');
		}
	} else {
		define('AREA', $get_area);
	}
	// 检查版本号
	if ((int)BILIROAMING_VERSION_CODE < ROAMING_MIN_VER) {
		block(14, "哔哩漫游模块版本过低");
	} elseif ((int)BILIROAMING_VERSION_CODE > ROAMING_MAX_VER && ROAMING_MAX_VER != 0) {
		block(16, "哔哩漫游模块版本过高");
	}
} else {
	block(15, "错误请求头");
}
$ts = @$_GET['ts'];
if ($ts == '' || !SIGN) {
	define('TS', time());
} else {
	if ($ts < time()-60 || $ts > time()+60) {
		block(17, "参数ts错误");
	}
	define('TS', $ts);
}
if (in_array(EP_ID, $epid_list) && BAN_EP == 1) {
	block(11, "禁止解锁此视频，请改用其他解析服务器");
}
if (in_array(CID, $cid_list) && BAN_CID == 1) {
	block(12, "禁止解锁此视频，请改用其他解析服务器");
}
if (in_array(AREA, $BAN_SERVER_AREA)) {
	block(13, "不支持解锁「".AREA."」地区，请将「".AREA."」改用其他解析服务器");
}

// 验证 sign（playurl）
if ($type == 1 && SIGN) {
	$sign = @$_GET['sign'];
	if (APPKEY != "" && $sign != "" && TS != "") {
		check_sign(APPKEY, $sign, $query);
	}
}

// 写入日志（非 playurl）
if (SAVE_LOG == 1 && $type != 1) {
	define('BAN_CODE', '0');
	include_once(ROOT_PATH."utils/functions_cache.php");
	write_log();
}

function block($code, $reason){
	// 写入日志
	if (SAVE_LOG == 1 && $code <= 20) {
		define('BAN_CODE', $code);
		include_once(ROOT_PATH."utils/functions_cache.php");
		write_log();
	}
	// 返回内容
	http_response_code(200); // B站就是都返回200
	exit('{"code":-'.$code.',"message":"'.$reason.'(E='.$code.')"}');
}
?>
