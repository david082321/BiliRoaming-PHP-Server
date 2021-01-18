<?php
// 加载配置
include ("config.php");

// 缓存用
if (SAVE_CACHE==1){
    include ("log.php");
}

// 判断要转发的host
$path = explode('/index.php', $_SERVER['PHP_SELF'])[0];
if ($path=="/intl/gateway/v2/ogv/playurl"){
    $host = "api.global.bilibili.com";
    include (BLOCK_TYPE.".php"); // 鉴权
}elseif ($path=="/intl/gateway/v2/app/search/type"){
    $host = "app.global.bilibili.com";
    include (BLOCK_TYPE.".php"); // 鉴权
}elseif ($path=="/pgc/player/api/playurl"){
    $host = "api.bilibili.com";
    include (BLOCK_TYPE.".php"); // 鉴权
}elseif ($path=="/intl/gateway/v2/app/subtitle"){
    $host = "app.global.bilibili.com";
}else {
    // 欢迎语
    exit(WELCOME);
}

// 模块请求都会带上X-From-Biliroaming的请求头，为了防止被盗用，可以加上请求头判断
$headerStringValue = $_SERVER['HTTP_X_FROM_BILIROAMING'];
if ($headerStringValue=="" && BILIROAMING==1){
    exit(BLOCK_RETURN);
}

// 获取缓存
if (SAVE_CACHE==1){
    include ("cache.php");
    get_cache();
}

// 转发到b站服务器
$url = "https://".$host.$path."?".$_SERVER['QUERY_STRING'];
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_FOLLOWLOCATION,false); 
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_HTTPHEADER, array(
    'User-Agent: '.@$_SERVER["HTTP_USER_AGENT"]
));
$output = curl_exec($ch);
curl_close($ch);
print($output);

if (SAVE_CACHE==1){
    write_cache();
}

?>