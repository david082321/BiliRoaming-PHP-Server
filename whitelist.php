<?php
// 防止外部破解
if(!defined('SYSTEM')){
    header('HTTP/1.1 404 Not Found');
    exit(BLOCK_RETURN);
}

if (ACCESS_KEY != ""){ // access_key 是否存在
    if (SAVE_CACHE==1){ // 是否开启缓存
        $uid = get_uid_fromsql(); // 从数据库获取
    }else{
        $uid = get_uid(); // 从API获取
    }
    // 是否在白名单内
    if (!in_array($uid, $WHITELIST) || $baned == 1) {
        if (REPLACE_TYPE=="hlw" || REPLACE_TYPE=="tom" || REPLACE_TYPE=="xyy" || REPLACE_TYPE=="404"){ // 替换成葫芦娃、猫和老鼠、喜羊羊、肥肠抱歉
            include ("replace.php");
            replace();
        }else {
            exit(BLOCK_RETURN);
        }
    }
}else{
    exit(BLOCK_RETURN);
}

function get_uid(){
    $sign = md5("access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS.APPSEC);
    $testurl = "https://app.bilibili.com/x/v2/account/myinfo?access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS."&sign=".$sign;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    $array = json_decode($output, true);
    $code = $array['code'];
    if ($code=="0"){
        $uid = $array['data']['mid'];
    }else{
        $uid = "0";
    }
    return $uid;
}

function get_uid_fromsql(){
    global $dbh;
    $sqlco = "SELECT `uid` FROM `keys` WHERE `access_key` = '".ACCESS_KEY."'";
    $cres = $dbh -> query($sqlco);
    $vnum = $cres -> fetch();
    $uid = $vnum['uid'];
    return $uid;
}

?>
