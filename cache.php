<?php
// 防止外部破解
if(!defined('SYSTEM')){
    header('HTTP/1.1 404 Not Found');
    exit('禁止访问');
}

$db_host=DB_HOST;
$db_user=DB_USER;
$db_pass=DB_PASS;
$db_name=DB_NAME;
$dbh='mysql:host='.$db_host.';'.'dbname='.$db_name;
try{
   $dbh = new PDO($dbh,$db_user,$db_pass);
   //echo '成功';
}catch(PDOException $e){
   //echo '失败';
}

// 参数
$access_key = @$_GET['access_key'];
$area = @$_GET['area'];
$cid = @$_GET['cid'];
$ep_id = @$_GET['ep_id'];
$refresh_cache = 0;

if ($access_key ==""){
    $type = "0"; // 未登录
}else{
    // 判断大会员
    $sqlco = "SELECT `due_date` FROM `keys` WHERE `access_key` = '".$access_key."'";
    $cres = $dbh -> query($sqlco);
    $vnum = $cres -> fetch();
    $due = $vnum['due_date'];
    if ((int)$due > time()*1000 ){
        $type = "2"; // 大会员
    }else{
        $type = "1"; // 不是大会员
    }
}

// 获取缓存
function get_cache(){
    global $dbh;
    global $type;
    global $area;
    global $cid;
    global $ep_id;
    global $refresh_cache;
    $ts = time();
    
    $sqlco = "SELECT * FROM `cache` WHERE `area` = '$area' AND `type` = '$type' AND `cid` = '$cid' AND `ep_id` = '$ep_id'";
    $cres = $dbh -> query($sqlco);
    $vnum = $cres -> fetch();
    $cache = $vnum['cache'];
    $add_time = $vnum['add_time'];
    $cache = str_replace("u0026","&",$cache);
    if ($cache != ""){
        if( (int)$add_time+CACHE_TIME>=$ts){
            return $cache;
        }else{
            //刷新缓存
            $refresh_cache = 1;
            return "";
        }
    }
    return "";
}

// 写入缓存
function write_cache(){
    global $dbh;
    global $type;
    global $area;
    global $cid;
    global $ep_id;
    global $output;
    global $refresh_cache;
    $ts = time();
    $array = json_decode($output, true);
    $code = $array['code'];
    if ($code == "0"){
        $a = explode('mid=', $output);
        $out = $a[0];
        for($j=1; $j<count($a)-1; $j++){
            //echo $a[$j];
            $b = explode('orderid=', $a[$j]);
            $out = $out.'orderid='.$b[1];
        }
        $output = $out.$a[count($a)-1];
        $sql ="INSERT INTO `cache` (`add_time`,`area`,`type`,`cid`,`ep_id`,`cache`) VALUES ('$ts','$area','$type','$cid','$ep_id','$output')";
        //刷新缓存
        if ($refresh_cache==1){
            $sql = "UPDATE `cache` SET `add_time` = '$ts', `cache` = '$output' WHERE `area` = '$area' AND `type` = '$type' AND `cid` = '$cid' AND `ep_id` = '$ep_id';";
        }
        $dbh -> exec($sql);
    // 10403 地区错误
    }else if ($code == "-10403" && SERVER_AREA == $area){
        $sql ="INSERT INTO `cache` (`add_time`,`area`,`type`,`cid`,`ep_id`,`cache`) VALUES ('9999999999','$area','$type','$cid','$ep_id','$output')";
        $dbh -> exec($sql);
    // 404 泰版地区错误
    }else if ($code == "-404" && $area == "th"){
        $sql ="INSERT INTO `cache` (`add_time`,`area`,`type`,`cid`,`ep_id`,`cache`) VALUES ('9999999999','$area','$type','$cid','$ep_id','$output')";
        $dbh -> exec($sql);
    }
}

?>