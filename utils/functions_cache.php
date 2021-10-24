<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit(BLOCK_RETURN);}

//pdo连接数据库
$db_host = DB_HOST;
$db_user = DB_USER;
$db_pass = DB_PASS;
$db_name = DB_NAME;
$dbh = 'mysql:host='.$db_host.';'.'dbname='.$db_name;
try {
	$dbh = new PDO($dbh, $db_user, $db_pass);
	//echo '连接成功';
}catch(PDOException $e) {
	//pass
}

// 参数（判断是否刷新缓存）
$refresh_cache = 0;
$refresh_cache_season = 0;

function get_userinfo() {
	$sign = md5("access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS.APPSEC);
	$url = "https://app.bilibili.com/x/v2/account/myinfo?access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS."&sign=".$sign;
	$output = get_webpage($url);
	$array = json_decode($output, true);
	$code = $array['code'];
	if ($code == "0") {
		$out[0] = $array['data']['mid'];
		$out[1] = $array['data']['vip']['due_date'];
	} else {
		$out[0] = "0";
		$out[1] = "0";
	}
	return $out;
}

function refresh_userinfo() {
	$out = get_userinfo();
	$uid = $out[0];
	$due = $out[1];
	if ($uid != "0") {
		$sql = " UPDATE `keys` SET `uid` = '$uid', `due_date` = '$due' WHERE `keys`.`access_key` = '".ACCESS_KEY."';";
		$dbh -> exec($sql);
	} else {
		$sql = " UPDATE `keys` SET `expired` = '1' WHERE `keys`.`access_key` = '".ACCESS_KEY."';";
		$dbh -> exec($sql);
	}
	return $uid;
}

// 获取缓存
function get_cache() {
	global $dbh;
	global $member_type;
	global $cache_type;
	global $refresh_cache;
	$ts = time();
	$sqlco = "SELECT `cache`,`add_time` FROM `cache` WHERE `area` = '".AREA."' AND `type` = '".$member_type."' AND `cache_type` = '".$cache_type."' AND `cid` = '".CID."' AND `ep_id` = '".EP_ID."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	$cache = $vnum['cache'];
	$add_time = $vnum['add_time'];
	//修复读取问题
	$cache = str_replace("u0026", "&", $cache);
	$cache = str_replace("\r", "\\r", $cache);
	$cache = str_replace("\n", "\\n", $cache);
	if ($cache != "") {
		if ((int)$add_time + CACHE_TIME >= $ts) {
			return $cache;
		} else {
			// 准备刷新缓存
			$refresh_cache = 1;
		}
	}
	return "";
}

// 写入缓存
function write_cache() {
	global $dbh;
	global $SERVER_AREA;
	global $member_type;
	global $cache_type;
	global $output;
	global $refresh_cache;
	$ts = time();
	switch ($code) {
		case "0":
			//pass
			break;
		case "-10403":
			$ts = $ts + CACHE_TIME_10403;
			break;
		case "-404":
			$ts = $ts + CACHE_TIME_404;
			break;
		case "-412":
			$ts = $ts + CACHE_TIME_412;
			break;
		default:
			$ts = $ts + CACHE_TIME_OTHER;
	}
	$array = json_decode($output, true);
	$code = $array['code'];
	$a = explode('mid=', $output);
	$out = $a[0];
	for ($j = 1; $j < count($a)-1; $j++) {
		//echo $a[$j];
		$b = explode('orderid=', $a[$j]);
		$out = $out.'orderid='.$b[1];
	}
	$output = $out.$a[count($a)-1];
	$sql = "INSERT INTO `cache` (`add_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('$ts','".AREA."','".$member_type."','".$cache_type."','".CID."','".EP_ID."','$output')";
	// 刷新缓存
	if ($refresh_cache == 1) {
		$sql = "UPDATE `cache` SET `add_time` = '$ts', `cache` = '$output' WHERE `area` = '".AREA."' AND `type` = '".$member_type."' AND `cache_type` = '".$cache_type."' AND `cid` = '".CID."' AND `ep_id` = '".EP_ID."';";
	}
	$dbh -> exec($sql);
}

// 获取缓存
function get_cache_season() {
	global $dbh;
	global $member_type;
	global $refresh_cache_season;
	$ts = time();
	$sqlco = "SELECT * FROM `cache` WHERE `area` = 'season' AND `type` = '0' AND `cache_type` = 'season' AND `cid` = '0' AND `ep_id` = '".SS_ID."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	$cache = $vnum['cache'];
	$add_time = $vnum['add_time'];
	//修复读取问题
	$cache = str_replace("u0026", "&", $cache);
	$cache = str_replace("\r", "\\r", $cache);
	$cache = str_replace("\n", "\\n", $cache);
	if ($cache != "") {
		if ((int)$add_time + CACHE_TIME_SEASON >= $ts) {
			return $cache;
		} else {
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
	if ($code == "0") {
		// pass
	} elseif ($code == "-10403") {
		$ts = $ts + CACHE_TIME_10403;
	} elseif ($code == "-404") {
		$ts = $ts + CACHE_TIME_404;
	} elseif ($code == "-412") {
		$ts = $ts + CACHE_TIME_412;
	} else {
		$ts = $ts + CACHE_TIME_OTHER;
	}
	$array = json_decode($output, true);
	$code = $array['code'];

	$sql = "INSERT INTO `cache` (`add_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('$ts','season','0','season','0','".SS_ID."','$output')";
	// 刷新缓存
	if ($refresh_cache_season == 1) {
		$sql = "UPDATE `cache` SET `add_time` = '$ts', `cache` = '$output' WHERE `area` = '".AREA."' AND `cache_type` = 'season' AND `type` = '".$member_type."' AND `cid` = '".CID."' AND `ep_id` = '".EP_ID."';";
	}
	$dbh -> exec($sql);
}
?>