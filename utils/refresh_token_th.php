<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, CUSTOM_HOST_TH_TOKEN."/x/intl/passport-login/oauth2/refresh_token");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded; charset=utf-8"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);

if (PROXY_ON == 1) {
	curl_setopt($ch, CURLOPT_PROXYTYPE, PROXY_TYPE);
	curl_setopt($ch, CURLOPT_PROXY, PROXY_IP_TH);
}
if (IP_RESOLVE == 1) {
	$host = $hosts[array_rand($hosts)];
	$ip = $ips[array_rand($ips)];
	curl_setopt($ch, CURLOPT_RESOLVE, [$host.":443:".$ip]);
}
$PostData = "access_token=".ACCESS_TOKEN."&refresh_token=".REFRESH_TOKEN;
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
header('Content-Type: application/json; charset=utf-8');
$output2 = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Error 301
if ($httpcode == "301") {
	// 转发到指定服务器
	$url = CUSTOM_HOST_TH_TOKEN."/x/intl/passport-login/oauth2/refresh_token/index.php?".$PostData;
	if (IP_RESOLVE == 1) {
		$output2 = get_webpage($url,$host,$ip);
	} else {
		$output2 = get_webpage($url);
	}
}
// print($output2);
?>
