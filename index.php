<?php
define('SYSTEM', TRUE); // 防止外部破解
include ("config.php"); // 加载配置

// 缓存用
if (SAVE_CACHE==1){
    include ("log.php");
}

// 判断要转发的host
$path = explode('/index.php', $_SERVER['PHP_SELF'])[0];
if ($path=="/intl/gateway/v2/ogv/playurl"){
    $host = CUSTOM_HOST_TH;
}elseif ($path=="/intl/gateway/v2/app/search/type"){
    $host = CUSTOM_HOST_SUB;
}elseif ($path=="/intl/gateway/v2/app/subtitle"){
    $host = CUSTOM_HOST_SUB;
}elseif ($path=="/pgc/player/api/playurl"){
    if ($area=="cn"){$host = CUSTOM_HOST_CN;}
    else if ($area=="hk"){$host = CUSTOM_HOST_HK;}
    else if ($area=="tw"){$host = CUSTOM_HOST_TW;}
    else {$host = CUSTOM_HOST_DEFAULT;}
}else {
    // 欢迎语
    exit(WELCOME);
}

// 模块请求都会带上X-From-Biliroaming的请求头，为了防止被盗用，可以加上请求头判断
$headerStringValue = $_SERVER['HTTP_X_FROM_BILIROAMING'];
if ($headerStringValue=="" && BILIROAMING==1){
    exit(BLOCK_RETURN);
}

// 服务器锁区
$area = @$_GET['area'];
if ( !empty($SERVER_AREA) && !in_array($area, $SERVER_AREA) && LOCK_AREA=="1" ){
    exit(BLOCK_RETURN);
}

// 鉴权
if ($path!="/intl/gateway/v2/app/subtitle"){
    include (BLOCK_TYPE.".php"); 
}

// 获取缓存
if (SAVE_CACHE==1){
    include ("cache.php");
    $cache = get_cache();
    if ($cache != ""){
        exit($cache);
    }
}


$url = "https://".$link.$path."?".$_SERVER['QUERY_STRING'];
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RESOLVE,[$link.":443:".$ip]);

// 指定ip回源
if (IP_RESOLVE==1){
	$host = $links[array_rand($hosts)];
	$ip = $ips[array_rand($ips)];
}

// 转发到指定服务器
$url = "https://".$host.$path."?".$_SERVER['QUERY_STRING'];
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
if (IP_RESOLVE==1){curl_setopt($ch,CURLOPT_RESOLVE,[$host.":443:".$ip]);} // 指定ip回源
curl_setopt($ch,CURLOPT_HTTPHEADER, array(
    'User-Agent: '.@$_SERVER["HTTP_USER_AGENT"]
));
$output = curl_exec($ch);
curl_close($ch);
print($output);

// 写入缓存
if (SAVE_CACHE==1){
    write_cache();
}

?>