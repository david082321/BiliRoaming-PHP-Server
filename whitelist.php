<?php
// 防止外部破解
if(!defined('SYSTEM')){
    header('HTTP/1.1 404 Not Found');
    exit('禁止访问');
}

$access_key = @$_GET['access_key']; // 获取 access_key

if ($access_key != ""){ // access_key 是否存在
    if (SAVE_CACHE==1){ // 是否开启缓存
        $uid = get_uid_fromsql(); // 从数据库获取
    }else{
        $uid = get_uid(); // 从API获取
    }
    // 是否在白名单内
    if (in_array($uid, $whitelist)) {
        // pass
    }else{
        if (REPLACE_HLW==1){
            include ("hlw.php");
            hlw(); // 替换成葫芦娃
        }else{
            exit(BLOCK_RETURN);
        }
    }
}else{
    exit(BLOCK_RETURN);
}

function get_uid(){
    $access_key = $_GET['access_key'];
    $ts = time();
    $appkey = "1d8b6e7d45233436";
    $appsec = "560c52ccd288fed045859ed18bffd973";
    $sign = md5("access_key=".$access_key."&appkey=".$appkey."&ts=".$ts.$appsec);
    $testurl = "https://app.bilibili.com/x/v2/account/myinfo?access_key=".$access_key."&appkey=".$appkey."&ts=".$ts."&sign=".$sign;
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
    $db_host=DB_HOST;
    $db_user=DB_USER;
    $db_pass=DB_PASS;
    $db_name=DB_NAME;
    $dbh='mysql:host='.$db_host.';'.'dbname='.$db_name;
    try{
       $dbh = new PDO($dbh,$db_user,$db_pass);
       //echo '成功';
    }catch(PDOException $e){
       //echo '错误';
       $uid = get_uid();
       return $uid;
    }
    $access_key = $_GET['access_key'];
    $sqlco = "SELECT `uid` FROM `keys` WHERE `access_key` = '".$access_key."'";
    $cres = $dbh -> query($sqlco);
    $vnum = $cres -> fetch();
    $uid = $vnum['uid'];
    return $uid;
}

?>