<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

// 锁区、web接口、X-From-Biliroaming
if ($cache_type == "app"){
	if ($type == 1 && LOCK_AREA == 1 && !empty($SERVER_AREA) && !in_array(AREA, $SERVER_AREA)) {
		block(30, "area黑名单");
	}
	if (BILIROAMING_VERSION == "" && BILIROAMING == 1) {
		if (WEB_ON == 1 && $path == "/intl/gateway/v2/ogv/view/app/season"){
			// web接口会用到东南亚season，特殊放行
		} else {
			block(31, "此API仅限漫游用户，若误封请到这里提出 github.com/david082321/BiliRoaming-PHP-Server/issues");
		}
	}
} elseif ($cache_type == "web" && WEB_ON == 0) {
	block(32, "服务器未开放web接口");
}
?>
