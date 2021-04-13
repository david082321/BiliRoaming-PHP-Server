<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit(BLOCK_RETURN);}

// 参数
$refresh_cache_season = 0;

//pdo连接数据库
$db_host=DB_HOST;
$db_user=DB_USER;
$db_pass=DB_PASS;
$db_name=DB_NAME;
$dbh='mysql:host='.$db_host.';'.'dbname='.$db_name;
try {
   $dbh = new PDO($dbh,$db_user,$db_pass);
   //echo '连接成功';
} catch(PDOException $e) {
   //pass
}

// 获取缓存
function get_cache_season() {
	global $dbh;
	global $member_type;
	global $refresh_cache_season;
	$ts = time();
	$sqlco = "SELECT * FROM `cache` WHERE `area` = 'season' AND `type` = '0' AND `cache_tpye` = 'season' AND `cid` = '0' AND `ep_id` = '".SS_ID."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	$cache = $vnum['cache'];
	$add_time = $vnum['add_time'];
	//修复读取问题
	$cache = str_replace("u0026","&",$cache);
	$cache = str_replace("\r","\\r",$cache);
	$cache = str_replace("\n","\\n",$cache);
	if ($cache != "") {
		if( (int)$add_time + CACHE_TIME_SEASON >= $ts) {
			return $cache;
		}else{
			// 准备刷新缓存
			$refresh_cache_season = 1;
			return "";
		}
	}
	return "";
}

// 写入缓存
function write_cache_season() {
	global $dbh;
	global $output;
	global $refresh_cache_season;
	$ts = time();
	$array = json_decode($output, true);
	$code = $array['code'];
	if ($code == "0") {
		$sql ="INSERT INTO `cache` (`add_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('$ts','season','0','season','0','".SS_ID."','$output')";
		// 刷新缓存
		if ($refresh_cache_season == 1) {
			$sql = "UPDATE `cache` SET `add_time` = '$ts', `cache` = '$output' WHERE `area` = '".AREA."' AND `cache_type` = 'season' AND `type` = '".$member_type."' AND `cid` = '".CID."' AND `ep_id` = '".EP_ID."';";
		}
		$dbh -> exec($sql);
	// 缓存 404 错误
	} else if ($code == "-404") {
		$ts = $ts + CACHE_TIME_SEASON_404;
		$sql ="INSERT INTO `cache` (`add_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('$ts','season','0','season','0','".SS_ID."','$output')";
		$dbh -> exec($sql);
	}
}
?>