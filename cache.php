<?php
// 防止外部破解
if(!defined('SYSTEM')){
    header('HTTP/1.1 404 Not Found');
    exit(BLOCK_RETURN);
}

// 参数
$refresh_cache = 0;

if (ACCESS_KEY ==""){
    define('TYPE', 0); //未登录
}else{
    // 判断大会员
    $sqlco = "SELECT `due_date` FROM `keys` WHERE `access_key` = '".ACCESS_KEY."'";
    $cres = $dbh -> query($sqlco);
    $vnum = $cres -> fetch();
    $due = $vnum['due_date'];
    if ((int)$due > time()*1000 ){
        define('TYPE', 2); // 大会员
    }else{
        define('TYPE', 1); // 不是大会员
    }
}

// 获取缓存
function get_cache(){
    global $dbh;
    global $refresh_cache;
    $ts = time();
    
    $sqlco = "SELECT * FROM `cache` WHERE `area` = '".AREA."' AND `type` = '".TYPE."' AND `cid` = '".CID."' AND `ep_id` = '".EP_ID."'";
    $cres = $dbh -> query($sqlco);
    $vnum = $cres -> fetch();
    $cache = $vnum['cache'];
    $add_time = $vnum['add_time'];
    $cache = str_replace("u0026","&",$cache);
    if ($cache != ""){
        if( (int)$add_time+CACHE_TIME>=$ts){
            return $cache;
        }else{
            // 准备刷新缓存
            $refresh_cache = 1;
            return "";
        }
    }
    return "";
}

// 写入缓存
function write_cache(){
    global $dbh;
    global $SERVER_AREA;
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
        $sql ="INSERT INTO `cache` (`add_time`,`area`,`type`,`cid`,`ep_id`,`cache`) VALUES ('$ts','".AREA."','".TYPE."','".CID."','".EP_ID."','$output')";
        // 刷新缓存
        if ($refresh_cache==1){
            $sql = "UPDATE `cache` SET `add_time` = '$ts', `cache` = '$output' WHERE `area` = '".AREA."' AND `type` = '".TYPE."' AND `cid` = '".CID."' AND `ep_id` = '".EP_ID."';";
        }
        $dbh -> exec($sql);
    // 缓存地区错误
    }else if (in_array(AREA, $SERVER_AREA)){
        $sql ="INSERT INTO `cache` (`add_time`,`area`,`type`,`cid`,`ep_id`,`cache`) VALUES ('9999999999','".AREA."','".TYPE."','".CID."','".EP_ID."','$output')";
        if ($code == "-10403"){// 10403 地区错误
            $dbh -> exec($sql);
        }else if ($code == "-404" && AREA == "th"){// 404 泰版地区错误
            $dbh -> exec($sql);
        }
    }
}

?>