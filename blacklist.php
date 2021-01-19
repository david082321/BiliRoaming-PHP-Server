<?php
// 防止外部破解
if(!defined('SYSTEM')){
    header('HTTP/1.1 404 Not Found');
    exit(BLOCK_RETURN);
}

// 获取 access_key
$access_key = @$_GET['access_key'];
// 判断 access_key
if ($access_key != ""){
    $url = "https://black.qimo.ink/?access_key=".$access_key;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $out = curl_exec($ch);
    curl_close($ch);
    // 如果是黑名单
    if ($out=="ban"){
        if (REPLACE_TYPE=="hlw"){ // 替换成葫芦娃
            include ("hlw.php");
            hlw();
        }elseif (REPLACE_TYPE=="tom"){ // 替换成猫和老鼠
            include ("tom.php");
            tom();
        }else {
            exit(BLOCK_RETURN);
        }
    }
}else if (NEED_LOGIN == 1){
    exit(BLOCK_RETURN);
}
?>