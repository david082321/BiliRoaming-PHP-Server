<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

// 判断登录状态
if (ACCESS_KEY != "" && SAVE_CACHE == 1) {
	// 从数据库获取
	$out = get_userinfo_fromsql();
	$uid = $out[0];
	$add_time = $out[1];
	$due = $out[2];
	$expired = $out[3];
	// 判断是否不在数据库里
	$insert = 0;
	if ($add_time == "0") {
		$insert = 1; // INSERT 添加内容
	}
	if ($uid == "0" && $expired == "0") {
		$out = get_userinfo();
		$uid = $out[0];
		$due = $out[1];
		$expired = "0";
		if ($insert == 1 && $uid != "0") {
			// 写入新 key
			$sql = "INSERT INTO `keys` (`add_time`,`uid`,`access_key`,`due_date`) VALUES (now(),'$uid','".ACCESS_KEY."','$due')";
			$dbh -> exec($sql);
		}
	} elseif (time() - strtotime($add_time) >= CACHE_TIME_USER) {
		// 超时，开始刷新
		$out = refresh_userinfo();
		$uid = $out[0];
		$due = $out[1];
		$expired = $out[2];
	}
	// key已过期 或 服务器不允许未登录用户
	if ($uid == "0" && (NEED_LOGIN == 1 || $expired == "1")) {
		block(20, "访问密钥已过期或不存在(脚本设置左下角重新授权)");
	}
} elseif (ACCESS_KEY != "") {
	// 有 access_key 但没开缓存，只会在需要时检查用户
	if (NEED_LOGIN == 1 || (BLOCK_TYPE == "blacklist" || BLOCK_TYPE == "whitelist" || BLOCK_TYPE == "local_blacklist" || BLOCK_TYPE == "local_whitelist" )) {
		$out = get_userinfo();
		$uid = $out[0];
		$due = $out[1];
		if ($uid == "0") {
			block(20, "访问密钥已过期或不存在(脚本设置左下角重新授权)");
		}
	}
}

// 开始鉴权
if (ACCESS_KEY != "") { // access_key 存在
	// resign.php 可能会用到
	$is_blacklist = false;
	$is_whitelist = false;
	$ban_reason = "";
	define('UID', $uid);
	
	if (BLOCK_TYPE == "blacklist" || BLOCK_TYPE == "whitelist") {
		if (SAVE_CACHE == 1) {
			// 获取黑白名单缓存
			$out = get_cache_blacklist();
			$is_blacklist = $out[0];
			$is_whitelist = $out[1];
			@$ban_reason = $out[2];
		}
		if ((SAVE_CACHE == 1 && $is_blacklist == "⑨") || SAVE_CACHE == 0) {
			$url = "https://black.qimo.ink/status.php?uid=".UID;
			$status = json_decode(get_webpage($url), true);
			@$code = $status['code'];
			if ((string)$code == "0") {
				$is_blacklist = $status['data']['is_blacklist'];
				$is_whitelist = $status['data']['is_whitelist'];
				$ban_reason = $status['data']['reason'];
				if (SAVE_CACHE == 1) {
					write_cache_blacklist(); // 写入缓存
				}
			} else if (BLACKLIST_ERROR == 2) {
				block(24, "黑名单服务器连接异常，请联系服务器提供者，或是等待修复。");
			} else if (BLACKLIST_ERROR == 1) {
				if (in_array($uid, $BLACKLIST)) {
					$is_blacklist = true;
				} else if (in_array($uid, $WHITELIST)) {
					$is_whitelist = true;
				}
			} else {
				$is_blacklist = false;
				$is_whitelist = false;
			}
		}
	}
	$is_baned = false;
	switch (BLOCK_TYPE) {
		case "blacklist": // 在线黑名单
			if ($is_blacklist) {
				$is_baned = true;
				$baned = 21;
				$reason = $uid." 在黑名单：".$ban_reason;
			}
			break;
		case "whitelist": // 在线白名单
			if (!$is_whitelist) {
				$is_baned = true;
				$baned = 22;
				$reason = $uid." 不在白名单";
			}
			break;
		case "local_blacklist": // 本地黑名单
			if (in_array($uid, $BLACKLIST)) {
				$is_baned = true;
				$baned = 21;
				$reason = $uid." 在黑名单：".$ban_reason;
			}
			if (in_array($uid, $WHITELIST)) {
				$is_whitelist = true;
			}
			break;
		case "local_whitelist": // 本地白名单
			if (!in_array($uid, $WHITELIST)) {
				$is_baned = true;
				$baned = 22;
				$reason = $uid." 不在白名单";
			} else {
				$is_whitelist = true;
			}
			break;
		default:
			// pass
	}
	// 写入日志
	if (SAVE_LOG == 1 && $type != 1) {
		define('BAN_CODE', $baned);
		write_log();
	}
	// 开始ban
	$support_replace_type = array("hlw","tom","txbb","xyy","all","random"); // 允许替换的类型（兼容旧版config）
	if ($is_baned) {
		if (in_array(REPLACE_TYPE, $support_replace_type)) {
			include (ROOT_PATH."utils/replace_playurl.php");
			replace_playurl();
		} else {
			block($baned, $reason);
		}
	}
} else {  // access_key 不存在
	if (CID == "13073143" || CID == "120453316") { // 漫游测速
		// pass
	} elseif (BLOCK_TYPE == "whitelist" || BLOCK_TYPE == "local_whitelist" || NEED_LOGIN == 1) { // 白名单模式 或 黑名单模式+需要登录
		block(23, "未提供访问密钥(漫游需要登录、脚本需要授权)");
	}
}
?>
