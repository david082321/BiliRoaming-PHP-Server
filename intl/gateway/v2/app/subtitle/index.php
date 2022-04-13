<?php
// 分类
$type = 3;
$cache_type = "app";
// 加载配置
include ($_SERVER['DOCUMENT_ROOT']."/config.php");
// 加载版本
include(ROOT_PATH."utils/version.php");
// 加载functions
include (ROOT_PATH."utils/functions.php");
if (SAVE_CACHE == 1) {
	include (ROOT_PATH."utils/functions_cache.php");
}
// 处理用户传入参数
include (ROOT_PATH."utils/process.php");
// 设置host
$host = CUSTOM_HOST_TH_SUB;
// 锁区、web接口、X-From-Biliroaming
//// （无）
// 鉴权、替换access_key、获取缓存
if ($baned == 1) {
	block();
}
if (SAVE_CACHE == 1) {
	get_cache_subtitle(); // 获取缓存
}
// 指定ip回源
if (IP_RESOLVE == 1) {
	$host = $hosts[array_rand($hosts)];
	$ip = $ips[array_rand($ips)];
}
// 加入必要参数
$query = add_query("th", $query, "mobi_app=bstar_a&s_locale=zh_SG&ts=".time());
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
	write_cache_subtitle(); // 写入东南亚subtitle
}
?>
