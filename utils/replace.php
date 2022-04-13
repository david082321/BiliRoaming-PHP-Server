<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

// 替换内容
$output = str_replace("\u0026","&",$output);
switch ($type) {
	case 0: // 搜索
		if ($cache_type != "web") {
			include (ROOT_PATH."utils/fuck_search.php"); // 搜索结果添加提示
		}
		break;
	case 1: // playurl
		break;
	case 2: // 东南亚APP season
		$output = str_replace('"vip":0','"vip":1',$output); // 支持漫游 #320
		$output = str_replace('"status":13','"status":2',$output); // 13不能缓存
		include (ROOT_PATH."utils/fuck_sub.php"); // 添加中文字幕
		break;
	case 3: // 东南亚APP 字幕
		break;
	case 4: // 东南亚APP episode
		break;
}
?>
