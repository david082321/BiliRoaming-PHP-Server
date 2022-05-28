<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}
$member_type = 0; // 判断用户状态

function get_webpage($url,$host="",$ip="") {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	if (PROXY_ON == 1) { // 指定代理
		curl_setopt($ch, CURLOPT_PROXYTYPE, PROXY_TYPE);
		switch (AREA) {
			case "cn":
				curl_setopt($ch, CURLOPT_PROXY, PROXY_IP_CN);
				break;
			case "hk":
				curl_setopt($ch, CURLOPT_PROXY, PROXY_IP_HK);
				break;
			case "tw":
				curl_setopt($ch, CURLOPT_PROXY, PROXY_IP_TW);
				break;
			case "th":
				curl_setopt($ch, CURLOPT_PROXY, PROXY_IP_TH);
				break;
			default:
				curl_setopt($ch, CURLOPT_PROXY, PROXY_IP);
		}
	}
	if (IP_RESOLVE == 1) { // 指定ip回源
		curl_setopt($ch, CURLOPT_RESOLVE,[$host.":443:".$ip]);
	}
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"User-Agent: ".@$_SERVER["HTTP_USER_AGENT"]
	));
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function get_host($type,$cache_type) {
	switch ($type) {
		case 1: // playurl
			switch (AREA) {
				case "cn":
					$host = CUSTOM_HOST_CN;
					break;
				case "tw":
					$host = CUSTOM_HOST_TW;
					break;
				case "hk":
					$host = CUSTOM_HOST_HK;
					break;
				default:
					$host = CUSTOM_HOST_DEFAULT;
			}
			break;
		case 0: // search
			if ($cache_type == "app"){
				switch (AREA) {
					case "cn":
						$host = CUSTOM_HOST_CN_SEARCH;
						break;
					case "tw":
						$host = CUSTOM_HOST_TW_SEARCH;
						break;
					case "hk":
						$host = CUSTOM_HOST_HK_SEARCH;
						break;
					default:
						$host = CUSTOM_HOST_DEFAULT_SEARCH;
				}
			} else {
				switch (AREA) {
					case "cn":
						$host = CUSTOM_HOST_CN_WEB_SEARCH;
						break;
					case "tw":
						$host = CUSTOM_HOST_TW_WEB_SEARCH;
						break;
					case "hk":
						$host = CUSTOM_HOST_HK_WEB_SEARCH;
						break;
					default:
						$host = CUSTOM_HOST_DEFAULT_WEB_SEARCH;
				}
			}
			break;
		case 2: // season
			if ($cache_type == "web"){
				switch (AREA) {
					case "cn":
						$host = CUSTOM_HOST_CN;
						break;
					case "tw":
						$host = CUSTOM_HOST_TW;
						break;
					case "hk":
						$host = CUSTOM_HOST_HK;
						break;
					default:
						$host = CUSTOM_HOST_DEFAULT;
				}
			} else {
				$host = CUSTOM_HOST_TH;
			}
			break;
		default:
	}
	return $host;
}

// 获取用户信息
function get_userinfo() {
	global $member_type;
	$sign = md5("access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS.APPSEC);
	$url = "https://app.bilibili.com/x/v2/account/myinfo?access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS."&sign=".$sign;
	$output = get_webpage($url);
	$array = json_decode($output, true);
	$code = $array['code'];
	if ($code == "0") {
		$out[0] = $array['data']['mid'];
		$out[1] = $array['data']['vip']['due_date'];
		if ((int)$out[1] > time()*1000) {
			$member_type = 2; // 大会员
		} else {
			$member_type = 1; // 不是大会员
		}
	} else {
		$out[0] = "0";
		$out[1] = "0";
		$member_type = 0; //未登录
	}
	return $out;
}

// 412 提醒
function check_412($output,$get_area){
	if (TG_NOTIFY == 1) {
		$status = json_decode($output, true);
		if(SAVE_CACHE == 0){
			if ($status['code'] == -412) {
				file_get_contents(TG_BOT_API.'/'.TG_BOT_KEY.'/sendMessage?chat_id='.TG_CHAT_ID.'&text=破服务器412啦，地区:' . $get_area);
			}
		} else {
			$latest_code = read_status($get_area);
			if($latest_code != $status['code']){
				if($status['code'] == -412){
					file_get_contents(TG_BOT_API.'/'.TG_BOT_KEY.'/sendMessage?chat_id='.TG_CHAT_ID.'&text=破服务器412啦，地区:' . $get_area);
					write_status($status['code'],$get_area);
				} else {
					if($latest_code == -412){
						file_get_contents(TG_BOT_API.'/'.TG_BOT_KEY.'/sendMessage?chat_id='.TG_CHAT_ID.'&text=破服务器恢复啦，地区:' . $get_area);
					}
					write_status(0,$get_area);
				}
			}
		}
	}
}

// appsec 查表
function appkey2sec($appkey) {
	return $appkey2sec[$appkey];
}

// 强制添加参数
function add_query($appkey, $query, $add_query) {
	parse_str($query, $query_arr);
	parse_str($add_query, $query_arr2);
	$query_arr = array_merge($query_arr, $query_arr2);
	unset($query_arr["sign"]);
	$query_arr["appkey"] = $appkey;
	ksort($query_arr);
	$query_new = http_build_query($query_arr);
	if ($appsec == "") {
		return $query_new;
	}
	$sign = md5($query_new.$appsec);
	$appsec = appkey2sec($appkey);
	return $query_new."&sign=".$sign;
}

// 验证 sign
function check_sign($appkey, $sign, $query) {
	$appsec = appkey2sec($appkey);
	if ($appsec == "") {
		define('UID', '0');
		block(40, "参数appkey错误");
	}
	parse_str($query, $query_arr);
	// 去除 sign
	unset($query_arr["sign"]);
	// 按 key 排序
	ksort($query_arr);
	$query_new = http_build_query($query_arr);
	if ($sign != md5($query_new.$appsec)) {
		define('UID', '0');
		block(41, "参数sign错误");
	}
}

?>
