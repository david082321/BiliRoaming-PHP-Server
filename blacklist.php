<?php
// 防止外部破解
if(!defined('SYSTEM')){
    header('HTTP/1.1 404 Not Found');
    exit(BLOCK_RETURN);
}


if (ACCESS_KEY != ""){ // access_key 是否存在
    $url = "https://black.qimo.ink/?access_key=".ACCESS_KEY;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $out = curl_exec($ch);
    curl_close($ch);
    // 如果是黑名单
    if ($out=="ban"){
        if (REPLACE_TYPE=="hlw" || REPLACE_TYPE=="tom" || REPLACE_TYPE=="xyy" || REPLACE_TYPE=="404"){ // 替换成葫芦娃、猫和老鼠、喜羊羊、肥肠抱歉
            include ("replace.php");
            replace();
        }else {
            exit(BLOCK_RETURN);
        }
    }
}else if (NEED_LOGIN == 1){
    exit(BLOCK_RETURN);
}
?>