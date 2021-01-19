<?php
// 防止外部破解
if(!defined('SYSTEM')){
    header('HTTP/1.1 404 Not Found');
    exit('禁止访问');
}

// https://zhuanlan.zhihu.com/p/122967323
//获取访客信息
//pdo连接数据库
$db_host=DB_HOST;
$db_user=DB_USER;
$db_pass=DB_PASS;
$db_name=DB_NAME;
$dbh='mysql:host='.$db_host.';'.'dbname='.$db_name;
try{
   $dbh = new PDO($dbh,$db_user,$db_pass);
   //echo '连接成功';
}catch(PDOException $e){
   //pass
}

$access_key = @$_GET['access_key'];
if ($access_key !=""){
    $sqlco = "SELECT `uid` as num FROM `keys` WHERE `access_key` = '".$access_key."'";
    $cres = $dbh -> query($sqlco);
    $vnum = $cres -> fetch();
    $uid = $vnum['num'];
    if ($uid==""||$uid=="0"){
        $out = get_userinfo();
        $uid = $out[0];
        $due = $out[1];
        if ($uid != "0"){
            $sql =" INSERT INTO `keys` (`add_time`,`uid`,`access_key`,`due_date`) VALUES (now(),'$uid','$access_key','$due')";
            $dbh -> exec($sql);
        }
    }
}

function get_userinfo(){
    $access_key = $_GET['access_key'];
    $ts = $_GET['ts'];
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
        $out[0] = $array['data']['mid'];
        $out[1] = $array['data']['vip']['due_date'];
    }else{
        $out[0] = "0";
        $out[1] = "0";
    }
    return $out;
}

?>