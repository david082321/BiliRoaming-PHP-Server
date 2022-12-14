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

function get_blacklist($uid) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://black.qimo.ink/api/users/".$uid);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"User-Agent: biliroaming-php-server/".VERSION
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
	$appsec = appkey2sec(APPKEY);
	$sign = md5("access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS.$appsec);
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
function check_412($output,$get_area) {
	if (TG_NOTIFY == 1) {
		$status = json_decode($output, true);
		$msg = "";
		if (SAVE_CACHE == 0) {
			if ($status['code'] == -412) {
				$msg = '破服务器412啦，地区:'.$get_area;
			}
		} else {
			$latest_code = read_status($get_area);
			if ($latest_code != $status['code']) {
				if ($status['code'] == -412) {
					$msg = '破服务器412啦，地区:' . $get_area;
					write_status($status['code'],$get_area);
				} else {
					if ($latest_code == -412) {
						$msg = '破服务器恢复啦，地区:' . $get_area;
					}
					write_status(0,$get_area);
				}
			}
		}
		if ($msg != "") {
			try {
				file_get_contents(TG_BOT_API.'/bot'.TG_BOT_KEY.'/sendMessage?chat_id='.TG_CHAT_ID.'&text='.$msg);
			} catch (Exception $e) {
				// 不做任何事
			}
		}
	}
}

// appsec 查表
function appkey2sec($appkey) {
	if ($appkey == "") {return "";}
	$appkey2sec = array("9d5889cf67e615cd" => "8fd9bb32efea8cef801fd895bef2713d", // Ai4cCreatorAndroid
		"1d8b6e7d45233436" => "560c52ccd288fed045859ed18bffd973", // 安卓 客户端
		"57263273bc6b67f6" => "a0488e488d1567960d3a765e8d129f90", // 安卓 客户端
		"bca7e84c2d947ac6" => "60698ba2f68e01ce44738920a0ffe768", // 安卓 客户端 登录专用
		"07da50c9a0bf829f" => "25bdede4e1581c836cab73a48790ca6e", // 安卓 概念版
		"178cf125136ca8ea" => "34381a26236dd1171185c0beb042e1c6", // 安卓 概念版
		"7d336ec01856996b" => "a1ce6983bc89e20a36c37f40c4f1a0dd", // 安卓 概念版
		"dfca71928277209b" => "b5475a8825547a4fc26c7d518eaaa02e", // 安卓 HD版
		"37207f2beaebf8d7" => "e988e794d4d4b6dd43bc0e89d6e90c43", // 安卓 BiliLink
		"8d23902c1688a798" => "710f0212e62bd499b8d3ac6e1db9302a", // 安卓 车机版
		"bb3101000e232e27" => "36efcfed79309338ced0380abd824ac1", // 安卓 国际版
		"8e16697a1b4f8121" => "f5dd03b752426f2e623d7badb28d190a", // 安卓 国际版
		"ae57252b0c09105d" => "c75875c596a69eb55bd119e74b07cfe3", // 安卓 国际版
		"7d089525d3611b1c" => "acd495b248ec528c2eed1e862d393126", // 安卓 東南亞版
		"cc578d267072c94d" => "ffb6bb4c4edae2566584dbcacfc6a6ad", // 安卓 轻视频
		"4409e2ce8ffd12b8" => "59b43e04ad6965f34319062b478f83dd", // 安卓 TV版
		"cc8617fd6961e070" => "3131924b941aac971e45189f265262be", // 安卓 漫画
		"5dce947fe22167f9" => "", // 安卓 必剪
		"4c6e1021617d40d9" => "e559a59044eb2701b7a8628c86aa12ae", // AndroidMallTicket
		"c034e8b74130a886" => "e4e8966b1e71847dc4a3830f2d078523", // AndroidOttSdk
		"9a75abf7de2d8947" => "35ca1c82be6c2c242ecc04d88c735f31", // BiliScan
		"7d089525d3611b1c" => "acd495b248ec528c2eed1e862d393126", // 安卓 東南亞版
		"4ebafd7c4951b366" => "8cb98205e9b2ad3669aad0fce12a4c13", // iPhone
		"27eb53fc9058f8c3" => "c2ed53a74eeefe3cf99fbd01d8c9c375", // ios 客户端
		"aae92bc66f3edfab" => "af125a0d5279fd576c1b4418a3e8276d", // PC 投稿工具
		"84956560bc028eb7" => "94aba54af9065f71de72f5508f1cd42e", // 未知
		"85eb6835b0a1034e" => "2ad42749773c441109bdc0191257a664", // 未知
	);
	return $appkey2sec[$appkey];
}

// 强制添加参数
function add_query($appkey, $query, $add_query) {
	parse_str($query, $query_arr);
	parse_str($add_query, $query_arr2);
	$query_arr = array_merge($query_arr, $query_arr2);
	// 泰区删除 area 参数
	if ($appkey == "7d089525d3611b1c") {
		unset($query_arr["area"]);
	}
	unset($query_arr["sign"]);
	$query_arr["appkey"] = $appkey;
	ksort($query_arr);
	$query_new = http_build_query($query_arr);
	$appsec = appkey2sec($appkey);
	if ($appsec == "") {
		return $query_new;
	}
	$sign = md5($query_new.$appsec);
	return $query_new."&sign=".$sign;
}

// 验证 sign
function check_sign($appkey, $sign, $query) {
	$appsec = appkey2sec($appkey);
	if ($appsec == "") {
		block(40, "参数appkey错误");
	}
	parse_str($query, $query_arr);
	// 去除 sign
	unset($query_arr["sign"]);
	// 按 key 排序
	ksort($query_arr);
	$query_new = http_build_query($query_arr);
	if ($sign != md5($query_new.$appsec)) {
		block(41, "参数sign错误");
	}
}
?>
