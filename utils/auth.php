<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit(BLOCK_RETURN);}

if (ACCESS_KEY != "") { // access_key 存在
	if (BLOCK_TYPE == "blacklist") { // 黑名单鉴权
		$url = "https://black.qimo.ink/?access_key=".ACCESS_KEY;
		$out = get_webpage($url);
		// 如果是黑名单
		if ($out == "ban" || $baned == 1) {
			if (REPLACE_TYPE == "hlw" || REPLACE_TYPE == "tom" || REPLACE_TYPE == "xyy" || REPLACE_TYPE == "all") { // 替换成葫芦娃、猫和老鼠、喜羊羊
				include (ROOT_PATH."utils/replace_playurl.php");
				replace_playurl();
			} else {
				block();
			}
		}
	} else if (BLOCK_TYPE == "whitelist") { // 白名单鉴权
		if (SAVE_CACHE == 1 && $cache_type != "web") { // 是否开启缓存
			$uid = get_uid_fromsql(); // 从数据库获取
		} else {
			$uid = get_uid(); // 从API获取
		}
		// 是否在白名单内
		if (!in_array($uid, $WHITELIST) || $baned == 1 || $uid == 0) {
			if (REPLACE_TYPE == "hlw" || REPLACE_TYPE == "tom" || REPLACE_TYPE == "xyy" || REPLACE_TYPE == "all") { // 替换成葫芦娃、猫和老鼠、喜羊羊、肥肠抱歉
				include (ROOT_PATH."utils/replace_playurl.php");
				replace_playurl();
			} else {
				block();
			}
		}
	}
} else {  // access_key 不存在
	if (CID == "13073143" || CID == "120453316") { // 漫游测速
		//pass
	} else if (BLOCK_TYPE == "whitelist" || NEED_LOGIN == 1) { // 白名单模式 或 黑名单模式+需要登录
		block();
	}
}
?>
