<?php
// 分类
$type = 1;
$cache_type = "web";
// 加载配置
include ($_SERVER['DOCUMENT_ROOT']."/config.php");
// 加载版本
include(ROOT_PATH."utils/version.php");
// 加载functions
include (ROOT_PATH."utils/functions.php");
// 处理用户传入参数
include (ROOT_PATH."utils/process.php");
// 缓存用
if (SAVE_CACHE == 1) {
	include (ROOT_PATH."utils/functions_cache.php");
	include (ROOT_PATH."utils/log.php");
}
// 设置host
$host = get_host($type,$cache_type);
// 锁区、web接口、X-From-Biliroaming
include (ROOT_PATH."utils/lock_area.php");
// 鉴权、替换access_key、获取缓存
include (ROOT_PATH."utils/auth.php"); // 鉴权
if (ACCESS_KEY != "") {
	include(ROOT_PATH."utils/resign.php"); // 替换access_key
}
if (SAVE_CACHE == 1) {
	include ((ROOT_PATH."utils/cache.php");
	get_cache(); // 获取缓存
}
// 指定ip回源
if (IP_RESOLVE == 1) {
	$host = $hosts[array_rand($hosts)];
	$ip = $ips[array_rand($ips)];
}
// 转发到指定服务器
$url = $host.$path."?".$query;
if (IP_RESOLVE == 1) {
	$output = get_webpage($url,$host,$ip);
} else {
	$output = get_webpage($url);
}
// 替换内容
include (ROOT_PATH."utils/replace.php");
// 返回内容给用户
print($output);
// 写入缓存
if (SAVE_CACHE == 1) {
	write_cache(); // 写入playurl
}
?>
