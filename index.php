<?php
// 防止外部破解
define('SYSTEM', TRUE);
define('VERSION', '2.9.9');
// 加上json的Header
header('Content-Type: application/json; charset=utf-8');
// 加载配置
include ("config.php");
// 缓存用
if (SAVE_CACHE==1) {
    include ("log.php");
}
// 判断要转发的host
$path = explode('/index.php', $_SERVER['PHP_SELF'])[0];
if ($path=="/intl/gateway/v2/ogv/playurl") {
    $host = CUSTOM_HOST_TH;
} elseif ($path=="/intl/gateway/v2/app/search/type" || $path=="/intl/gateway/v2/app/subtitle") {
    $host = CUSTOM_HOST_SUB;
} elseif ($path=="/pgc/player/api/playurl" || $path=="/pgc/player/web/playurl") {
    if (AREA=="cn") {
        $host = CUSTOM_HOST_CN;
    } else if (AREA=="hk") {
        $host = CUSTOM_HOST_HK;
    } else if (AREA=="tw") {
        $host = CUSTOM_HOST_TW;
    } else {
        $host = CUSTOM_HOST_DEFAULT;
    }
} elseif (WEB_ON == 1) {
    $host = CUSTOM_HOST_DEFAULT;
	$path = "/pgc/player/web/playurl";
    header("Access-Control-Allow-Origin: https://www.bilibili.com");
    header("Access-Control-Allow-Credentials: true");
} else {
    // 欢迎语
    exit(WELCOME);
}
// 判断服务器锁区 及 web接口
if ($path=="/intl/gateway/v2/ogv/playurl" || $path=="/pgc/player/api/playurl") {
    if ( LOCK_AREA=="1" && !empty($SERVER_AREA) && !in_array(AREA, $SERVER_AREA)) {
        exit(BLOCK_RETURN);
    }
}elseif ($path=="/pgc/player/web/playurl") {
    if(WEB_ON == 0){
        exit(BLOCK_RETURN);
    }
    header("Access-Control-Allow-Origin: https://www.bilibili.com");
    header("Access-Control-Allow-Credentials: true");
}
// 模块请求都会带上X-From-Biliroaming的请求头，为了防止被盗用，可以加上请求头判断，WEB接口暂不限制
$headerStringValue = $_SERVER['HTTP_X_FROM_BILIROAMING'];
if ($headerStringValue=="" && BILIROAMING==1 && $path!="/pgc/player/web/playurl") {
    exit(BLOCK_RETURN);
}
// 鉴权
if ($path!="/intl/gateway/v2/app/search/type" && $path!="/intl/gateway/v2/app/subtitle"){
    include ("auth.php");
}
// 获取缓存
if (SAVE_CACHE==1) {
    include ("cache.php");
    $cache = get_cache();
    if ($cache != "") {
        exit($cache);
    }
}
// 指定ip回源
if (IP_RESOLVE==1) {
    $host = $hosts[array_rand($hosts)];
    $ip = $ips[array_rand($ips)];
}
// 转发到指定服务器
$url = $host.$path."?".$_SERVER['QUERY_STRING'];
if (IP_RESOLVE==1) {
    $output = get_webpage($url,$host,$ip);
}else {
    $output = get_webpage($url);
}
print($output);
// 写入缓存
if (SAVE_CACHE==1) {
    write_cache();
}

function get_webpage($url,$host="",$ip=""){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    if (IP_RESOLVE==1) { // 指定ip回源
        curl_setopt($ch,CURLOPT_RESOLVE,[$host.":443:".$ip]);
    }
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HTTPHEADER, array(
        'User-Agent: '.@$_SERVER["HTTP_USER_AGENT"]
    ));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}
?>