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
			case "noarea":
				curl_setopt($ch, CURLOPT_PROXY, PROXY_IP);
				break;
			default:
				if ($host = CUSTOM_HOST_TH_TOKEN || $host = CUSTOM_HOST_TH_SEARCH || $host = CUSTOM_HOST_TH || $host = CUSTOM_HOST_TH_SUB) {
					curl_setopt($ch, CURLOPT_PROXY, PROXY_IP_TH);
				} else {
					curl_setopt($ch, CURLOPT_PROXY, PROXY_IP);
				}
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
?>
