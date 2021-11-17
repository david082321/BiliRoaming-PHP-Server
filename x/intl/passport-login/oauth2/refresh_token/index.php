<?php
// 加载配置
include ($_SERVER['DOCUMENT_ROOT']."/config.php");
// 加载版本
include(ROOT_PATH."utils/version.php");
// 处理用户传入参数
if (@$_GET['access_token']!=''){
	define('ACCESS_TOKEN', @$_GET['access_token']);
	define('REFRESH_TOKEN', @$_GET['refresh_token']);
} else {
	define('ACCESS_TOKEN', @$_POST['access_token']);
	define('REFRESH_TOKEN', @$_POST['refresh_token']);
}
if (ACCESS_TOKEN == '' || REFRESH_TOKEN == '') {
	exit("Error");
}
// 开始获取
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://passport.biliintl.com/x/intl/passport-login/oauth2/refresh_token");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded; charset=utf-8"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
if (PROXY_ON == 1) {
	curl_setopt($ch, CURLOPT_PROXYTYPE, PROXY_TYPE);
	curl_setopt($ch, CURLOPT_PROXY, PROXY_IP_TH);
}
if (IP_RESOLVE == 1) {
	curl_setopt($ch, CURLOPT_RESOLVE, [$host.":443:".$ip]);
}
$PostData = "access_token=".ACCESS_TOKEN."&refresh_token=".REFRESH_TOKEN;
curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
header('Content-Type: application/json; charset=utf-8');
$output = curl_exec($ch);
curl_close($ch);
// 返回内容给用户
print($output);
?>
