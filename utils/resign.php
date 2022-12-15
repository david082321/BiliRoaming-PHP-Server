<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}
include (ROOT_PATH."utils/functions_cache_key.php");

// 鉴权判断逻辑
if (ACCESS_KEY != "" && SAVE_CACHE == 1) {
	if ($path == "/intl/gateway/v2/ogv/playurl"){
		if (RESIGN_TH_PAID == 1 && $is_whitelist) { // 泰国付费会员
			$keys_th_paid = array(get_mykey("9")); // 访问密钥
			define('RESIGN_TH_PAID_KEY', $keys_th_paid[array_rand($keys_th_paid)]);
			if (RESIGN_TH_PAID_KEY != "No key") {
				// 替换
				$query = sign("bstar_a", RESIGN_TH_PAID_KEY, $query);
				$member_type = 9;
			}
		} elseif (RESIGN_TH == 1){ // 泰国登录会员
			$keys_th = array(get_mykey("8")); // 访问密钥
			define('RESIGN_TH_KEY', $keys_th[array_rand($keys_th)]);
			if (RESIGN_TH_KEY != "No key") {
				// 替换
				$query = sign("bstar_a", RESIGN_TH_KEY, $query);
				$member_type = 8;
			}
		}
	}
}

// sign计算
function sign($mobi_app, $access_key, $query) {
	parse_str($query, $query_arr);
	// 去除 sign
	unset($query_arr["sign"]);	
	// 加上参数
	$check = check_mobi_app($mobi_app);
	$query_arr["appkey"] = $check[0];
	if ($access_key != "") {
		$query_arr["access_key"] = $access_key;
	}
	if ($cache_type != "web") {
		$query_arr["mobi_app"] = $check[2];
		$query_arr["platform"] = $check[3];
	}
	// 按 key 排序
	ksort($query_arr);
	// 签名
	$query_new = http_build_query($query_arr);
	$sign = md5($query_new.$check[1]);
	return $query_new."&sign=".$sign;
}
?>
