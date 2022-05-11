<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://passport.bilibili.com/x/passport-login/oauth2/refresh_token");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded; charset=utf-8"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);

if (PROXY_ON == 1) {
	curl_setopt($ch, CURLOPT_PROXYTYPE, PROXY_TYPE);
	if (PROXY_IP_CN != ""){
		$proxy_ip = PROXY_IP_CN;
	} else if (PROXY_IP_HK != ""){
		$proxy_ip = PROXY_IP_HK;
	} else if (PROXY_IP_TW != ""){
		$proxy_ip = PROXY_IP_TW;
	} else {
		$proxy_ip = PROXY_IP;
	}
	curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);
}
if (IP_RESOLVE == 1) {
	$host = $hosts[array_rand($hosts)];
	$ip = $ips[array_rand($ips)];
	curl_setopt($ch, CURLOPT_RESOLVE, [$host.":443:".$ip]);
}
$PostData = "access_token=".ACCESS_TOKEN."&appkey=1d8b6e7d45233436&refresh_token=".REFRESH_TOKEN."&ts=".time();
$sign = md5($PostData."560c52ccd288fed045859ed18bffd973");
$PostData = $PostData."&sign=".$sign;
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
header('Content-Type: application/json; charset=utf-8');
$output2 = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
?>
