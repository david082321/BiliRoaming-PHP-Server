<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

// 判断登录状态
if (ACCESS_KEY != "" && SAVE_CACHE == 1) {
	$out = get_userinfo_fromsql();
	$uid = $out[0];
	$add_time = $out[1];
	if ($uid == "" || $uid == "0") {
		$out = get_userinfo();
		$uid = $out[0];
		$due = $out[1];
		if ($uid != "0") {
			$sql = " INSERT INTO `keys` (`add_time`,`uid`,`access_key`,`due_date`) VALUES (now(),'$uid','".ACCESS_KEY."','$due')";
			$dbh -> exec($sql);
		} elseif (NEED_LOGIN == 1) {
			$baned = 20;
			block($baned);
		}
	} elseif (strtotime(time()) - strtotime($add_time) >= CACHE_TIME_USER) {
		refresh_userinfo();
	}
} elseif (ACCESS_KEY != "") {
	$out = get_userinfo();
	$uid = $out[0];
	$due = $out[1];
	if ($uid == "0" && NEED_LOGIN == 1) {
		$baned = 20;
		block($baned);
	}
}

// 开始鉴权
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
				$baned = 21;
				block($baned);
			}
		}
	} else if (BLOCK_TYPE == "whitelist") { // 白名单鉴权
		// 是否在白名单内
		if (!in_array($uid, $WHITELIST) || $baned == 1 || $uid == 0) {
			if (REPLACE_TYPE == "hlw" || REPLACE_TYPE == "tom" || REPLACE_TYPE == "xyy" || REPLACE_TYPE == "all") { // 替换成葫芦娃、猫和老鼠、喜羊羊、肥肠抱歉
				include (ROOT_PATH."utils/replace_playurl.php");
				replace_playurl();
			} else {
				$baned = 22;
				block($baned);
			}
		}
	}
} else {  // access_key 不存在
	if (CID == "13073143" || CID == "120453316") { // 漫游测速
		//pass
	} else if (BLOCK_TYPE == "whitelist" || NEED_LOGIN == 1) { // 白名单模式 或 黑名单模式+需要登录
		$baned = 23;
		block($baned);
	}
}
?>
