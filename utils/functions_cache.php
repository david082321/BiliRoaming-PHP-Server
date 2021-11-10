<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();}
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
	//echo $e;
	echo '数据库连接失敗';
}

// 参数（判断是否刷新缓存）
$refresh_cache = 0;
$refresh_cache_season = 0;
$refresh_cache_season_main = 0;

// 刷新用户信息的缓存
function refresh_userinfo() {
	global $dbh;
	global $member_type;
	$out = get_userinfo();
	$uid = $out[0];
	$due = $out[1];
	if ($uid != "0") {
		$sql = " UPDATE `keys` SET `add_time` = now(), `uid` = '".$uid."', `due_date` = '".$due."', `expired` = '0' WHERE `keys`.`access_key` = '".ACCESS_KEY."';";
		$dbh -> exec($sql);
		if ((int)$due > time()*1000) {
			$member_type = 2; // 大会员
		} else {
			$member_type = 1; // 不是大会员
		}
		$expired = 0;
	} else {
		$sql = " UPDATE `keys` SET `expired` = '1' WHERE `keys`.`access_key` = '".ACCESS_KEY."';";
		$dbh -> exec($sql);
		$member_type = 0; //未登录
		$uid = 0;
		$due = 0;
		$expired = 1;
	}
	return [$uid, $due, $expired];
}

// 从缓存获取用户信息
function get_userinfo_fromsql() {
	global $dbh;
	global $member_type;
	$sqlco = "SELECT `uid`,`add_time`,`due_date`,`expired` FROM `keys` WHERE `access_key` = '".ACCESS_KEY."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum) {
		$member_type = 0; //未登录
		return ["0","0","0","0"];
	}
	$uid = $vnum['uid'];
	$add_time = $vnum['add_time'];
	$due = $vnum['due_date'];
	$expired = $vnum['expired'];
	if ((int)$due > time()*1000) {
		$member_type = 2; // 大会员
	} else {
		$member_type = 1; // 不是大会员
	}
	return [$uid, $add_time, $due, $expired];
}

// 获取playurl缓存
function get_cache() {
	global $dbh;
	global $member_type;
	global $cache_type;
	global $refresh_cache;
	$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".AREA."' AND `type` = '".$member_type."' AND `cache_type` = '".$cache_type."' AND `cid` = '".CID."' AND `ep_id` = '".EP_ID."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum){
		return "";
	}
	@$cache = $vnum['cache'];
	@$expired_time = $vnum['expired_time'];
	//修复读取问题
	$cache = str_replace("u0026", "&", $cache);
	$cache = str_replace("\r", "\\r", $cache);
	$cache = str_replace("\n", "\\n", $cache);
	if ($cache != "") {
		if (time() <= (int)$expired_time) {
			exit($cache);
		} else {
			// 准备刷新缓存
			$refresh_cache = 1;
		}
	}
	return "";
}

// 写入playurl缓存
function write_cache() {
	global $dbh;
	global $SERVER_AREA;
	global $member_type;
	global $cache_type;
	global $output;
	global $refresh_cache;
	$ts = time();
	$array = json_decode($output, true);
	$code = $array['code'];
	switch ($code) {
		case "0":
			// 删掉用户mid
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
			$ts = $ts + CACHE_TIME;
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
	if ($code !== "") { //没有获取到code不写入缓存
		$sql = "INSERT INTO `cache` (`expired_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('".$ts."','".AREA."','".$member_type."','".$cache_type."','".CID."','".EP_ID."','".$output."')";
		// 刷新缓存
		if ($refresh_cache == 1) {
		$sql = "UPDATE `cache` SET `expired_time` = '".$ts."', `cache` = '".$output."' WHERE `area` = '".AREA."' AND `type` = '".$member_type."' AND `cache_type` = '".$cache_type."' AND `cid` = '".CID."' AND `ep_id` = '".EP_ID."';";
		}
		$dbh -> exec($sql);
	} 	
}

// 获取season缓存
function get_cache_season() {
	global $dbh;
	global $member_type;
	global $cache_type;
	global $refresh_cache_season;
	global $refresh_cache_season_main;
	
	if (AREA == "th"){
		$area = "th"; //泰区
	} else {
		$area = "main"; //主站
	}
	if (EP_ID != ""){
		$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".$area."' AND `type` = '0' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '0' AND `ep_id` = '".EP_ID."'";
	} elseif (SS_ID != "") {
		$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".$area."' AND `type` = '0' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '".SS_ID."' AND `ep_id` = '0'";
	} else {
		return "";
	}
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum){
		//给主站的一次机会获取自身 AREA 可能 code!=0 的缓存
		if ($area == "main"){
			if (EP_ID != ""){
				$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".AREA."' AND `type` = '0' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '0' AND `ep_id` = '".EP_ID."'";
			} elseif (SS_ID != "") {
				$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".AREA."' AND `type` = '0' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '".SS_ID."' AND `ep_id` = '0'";
			}
			$cres = $dbh -> query($sqlco);
			$vnum2 = $cres -> fetch();
			if (!$vnum2){
				return "";
			}
		} else {
			return "";
		}
	}
	@$cache = $vnum['cache'];
	@$expired_time = $vnum['expired_time'];
	@$cache2 = $vnum2['cache'];
	@$expired_time2 = $vnum2['expired_time'];
	
	if ($cache != "") {
		if (time() <= (int)$expired_time) {
			//修复读取问题
			$cache = str_replace("u0026", "&", $cache);
			$cache = str_replace("\r", "\\r", $cache);
			$cache = str_replace("\n", "\\n", $cache);
			exit($cache);
		} else {
			// 准备刷新缓存
			$refresh_cache_season_main = 1;
			return "";
		}
	} elseif ($cache2 != "") {
		if (time() <= (int)$expired_time2) {
			//修复读取问题
			$cache2 = str_replace("u0026", "&", $cache2);
			$cache2 = str_replace("\r", "\\r", $cache2);
			$cache2 = str_replace("\n", "\\n", $cache2);
			exit($cache2);
		} else {
			// 准备刷新缓存
			$refresh_cache_season = 1;
			return "";
		}
	}
	return "";
}

// 写入season缓存
function write_cache_season() {
	global $dbh;
	global $output;
	global $cache_type;
	global $refresh_cache_season;
	global $refresh_cache_season_main;
	
	$ts = time();
	$array = json_decode($output, true);
	$code = $array['code'];
	switch ($code) {
		case "0":
			$ts = $ts + CACHE_TIME_SEASON;
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
	if (EP_ID != ""){
		$ep_id = EP_ID;
		$ss_id = "0";
	} elseif (SS_ID != "") {
		$ss_id = SS_ID;
		$ep_id = "0";
	} else {
		return "no cache";
	}
	if (AREA == "th"){
		$area = "th"; //泰区
	} else {
		$area = "main"; //主站
	}
	
	if ($code == 0 && $area == "main") {
		// 当 code==0 缓存成 area=main
		$sql = "INSERT INTO `cache` (`expired_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('".$ts."','".$area."','0','season_".$cache_type."','".$ss_id."','".$ep_id."','".$output."')";
		// 刷新缓存
		if ($refresh_cache_season_main == 1) {
			$sql = "UPDATE `cache` SET `expired_time` = '".$ts."', `cache` = '".$output."' WHERE `area` = '".$area."' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '".$ss_id."' AND `ep_id` = '".$ep_id."';";
		}
		$dbh -> exec($sql);
	} else if($code !== ""){		
		// 缓存到自身 AREA 里面
		$sql = "INSERT INTO `cache` (`expired_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('".$ts."','".AREA."','0','season_".$cache_type."','".$ss_id."','".$ep_id."','".$output."')";
		// 刷新缓存
		if ($refresh_cache_season == 1) {
			$sql = "UPDATE `cache` SET `expired_time` = '".$ts."', `cache` = '".$output."' WHERE `area` = '".AREA."' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '".$ss_id."' AND `ep_id` = '".$ep_id."';";
		}
	}
	$dbh -> exec($sql);
}

?>
