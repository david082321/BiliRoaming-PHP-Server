<?php
// 防止外部破解
if(!defined('SYSTEM')){
    exit(BLOCK_RETURN);
}

if (BLOCK_TYPE=="none"){ // 无鉴权模式
    // pass
}else if (ACCESS_KEY != ""){ // access_key 存在
    if (BLOCK_TYPE == "blacklist"){ // 黑名单鉴权
        $url = "https://black.qimo.ink/?access_key=".ACCESS_KEY;
        $out = get_webpage($url);
        // 如果是黑名单
        if ($out=="ban" || $baned == 1){
            if (REPLACE_TYPE=="hlw" || REPLACE_TYPE=="tom" || REPLACE_TYPE=="xyy"){ // 替换成葫芦娃、猫和老鼠、喜羊羊
                include ("replace.php");
                replace();
            }else {
                exit(BLOCK_RETURN);
            }
        }
    }else if (BLOCK_TYPE == "whitelist"){ // 白名单鉴权
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
    }
}else{  // access_key 不存在
    if (BLOCK_TYPE == "whitelist" || NEED_LOGIN == 1){ // 白名单模式 或 黑名单模式+需要登录
        exit(BLOCK_RETURN);
    }
}


function get_uid(){
    $sign = md5("access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS.APPSEC);
    $url = "https://app.bilibili.com/x/v2/account/myinfo?access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS."&sign=".$sign;
    $output = get_webpage($url);
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
