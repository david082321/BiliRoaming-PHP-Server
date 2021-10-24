<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit(BLOCK_RETURN);}

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
		include (ROOT_PATH."utils/fuck_sub.php"); // 添加中文字幕
		break;
	case 3: // 东南亚APP 字幕
		break;
}
?>