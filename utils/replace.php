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
		if ($cache_type == "app" && AREA == "th") {
			$output = str_replace('"need_vip":true','"need_vip":false',$output); //然而这个替换似乎没用，是客户端本地验证
			$output = str_replace('"need_login":true','"need_login":false',$output);
		}
		break;
	case 2: // 东南亚APP season
		include (ROOT_PATH."utils/fuck_sub.php"); // 添加中文字幕
		break;
	case 3: // 东南亚APP 字幕
		break;
}
?>
