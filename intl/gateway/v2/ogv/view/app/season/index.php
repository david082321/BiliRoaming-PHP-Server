<?php
// 分类
$type = 2;
$cache_type = "app";
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
$host = CUSTOM_HOST_TH;
// 特殊参数
$query = "appkey=7d089525d3611b1c&autoplay=0&build=1052002&c_locale=&channel=master&lang=&locale=zh_SG&mobi_app=bstar_a&platform=android&s_locale=zh_SG&season_id=".SS_ID."&sim_code=&spmid=&ts=".TS;
// 锁区、web接口、X-From-Biliroaming
include (ROOT_PATH."utils/lock_area.php");
// 鉴权、替换access_key、获取缓存
if (SAVE_CACHE == 1) {
	get_cache_season(); // 获取缓存
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
	write_cache_season(); // 写入东南亚season
}
?>
