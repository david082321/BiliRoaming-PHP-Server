<?php
// 加载配置
include ($_SERVER['DOCUMENT_ROOT']."/config.php");
// 加载版本
include(ROOT_PATH."utils/version.php");
// 加载functions
include (ROOT_PATH."utils/functions.php");
if (SAVE_CACHE == 1) {
	include (ROOT_PATH."utils/functions_cache.php");
	include (ROOT_PATH."utils/functions_cache_key.php");
}
$type = @$_GET['type'];
$key = @$_GET["access_token"];
$refresh = @$_GET["refresh_token"];
$sign = @$_GET['sign'];

if ($sign != MAGIC_KEY || $sign == "123") {
	exit("密码错误或未设置密码。");
} elseif ($type == "") {
	exit("需要参数type (数字)：1=登录会员、2=大会员、8=东南亚登录会员、9=东南亚大会员");
} elseif ($key == "") {
	exit("需要参数access_token");
} elseif ($refresh == "") {
	exit("需要参数refresh_token");
} else {
	$out = add_mykey($type, $key, $refresh);
	exit($out);
}
?>
