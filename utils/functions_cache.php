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
} catch(PDOException $e) {
	//pass
	//echo $e;
	echo '数据库连接失敗';
}

// 参数（判断是否刷新缓存）
$refresh_cache = 0;
$refresh_cache_season = 0;
$refresh_cache_subtitle = 0;
$refresh_cache_status = 0;

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
	if (QN != "" && ($cache_type == "app" || $cache_type == "appV2")) {
			$cache = str_replace('"data":{"video_info":{"quality":','"data":{"video_info":{"quality":'.QN.',"quality_fuck":',$cache);
			$cache = str_replace('"data":{"playurl":{"quality":','"data":{"playurl":{"quality":'.QN.',"quality_fuck":',$cache);
	}
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
			/* 开始检测这个了
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
			*/
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
	if ($output !== "") { //没有获取到输出内容不写入缓存
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
	
	if (AREA == "th") {
		$area = "th"; //泰区
	} else {
		$area = "main"; //主站
	}
	if (EP_ID != "") {
		$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".$area."' AND `type` = '0' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '0' AND `ep_id` = '".EP_ID."'";
	} elseif (SS_ID != "") {
		$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".$area."' AND `type` = '0' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '".SS_ID."' AND `ep_id` = '0'";
	} else {
		return "";
	}
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum) {
		//给主站的一次机会获取自身 AREA 可能 code!=0 的缓存
		if ($area == "main") {
			if (EP_ID != "") {
				$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".AREA."' AND `type` = '0' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '0' AND `ep_id` = '".EP_ID."'";
			} elseif (SS_ID != "") {
				$sqlco = "SELECT `cache`,`expired_time` FROM `cache` WHERE `area` = '".AREA."' AND `type` = '0' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '".SS_ID."' AND `ep_id` = '0'";
			}
			$cres = $dbh -> query($sqlco);
			$vnum2 = $cres -> fetch();
		}
	}
	@$cache1 = $vnum['cache'];
	@$expired_time1 = $vnum['expired_time'];
	@$cache2 = $vnum2['cache'];
	@$expired_time2 = $vnum2['expired_time'];
	
	if ($cache1 != "") {
		$cache = $cache1;
		$expired_time = $expired_time1;
	} elseif ($cache2 != "") {
		$cache = $cache2;
		$expired_time = $expired_time2;
	} else {
		return "";
	}
	if (time() <= (int)$expired_time) {
		//修复读取问题
		$cache = str_replace("u0026", "&", $cache);
		$cache = str_replace("\r", "\\r", $cache);
		$cache = str_replace("\n", "\\n", $cache);
		exit($cache);
	} else {
		// 准备刷新缓存
		$refresh_cache_season = 1;
		return "";
	}
}

// 写入season缓存
function write_cache_season() {
	global $dbh;
	global $output;
	global $cache_type;
	global $refresh_cache_season;
	
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
	if (EP_ID != "") {
		$ep_id = EP_ID;
		$ss_id = "0";
	} elseif (SS_ID != "") {
		$ss_id = SS_ID;
		$ep_id = "0";
	} else {
		return "no cache";
	}
	if (AREA == "th") {
		$area = "th"; //泰区
	} else {
		$area = "main"; //主站
	}
	if ($code == 0 && $area == "main") {
		// 修复转义问题
		$output = str_replace("\\", "\\\\", $output);
		// 当 code==0 缓存成 area=main
		$sql = "INSERT INTO `cache` (`expired_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('".$ts."','".$area."','0','season_".$cache_type."','".$ss_id."','".$ep_id."','".$output."')";
		// 刷新缓存
		if ($refresh_cache_season == 1) {
			$sql = "UPDATE `cache` SET `expired_time` = '".$ts."', `cache` = '".$output."' WHERE `area` = '".$area."' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '".$ss_id."' AND `ep_id` = '".$ep_id."';";
		}
	} elseif ($code !== "") {
		// 修复转义问题
		$output = str_replace("\\", "\\\\", $output);
		// 缓存到自身 AREA 里面
		$sql = "INSERT INTO `cache` (`expired_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('".$ts."','".AREA."','0','season_".$cache_type."','".$ss_id."','".$ep_id."','".$output."')";
		// 刷新缓存
		if ($refresh_cache_season == 1) {
			$sql = "UPDATE `cache` SET `expired_time` = '".$ts."', `cache` = '".$output."' WHERE `area` = '".AREA."' AND `cache_type` = 'season_".$cache_type."' AND `cid` = '".$ss_id."' AND `ep_id` = '".$ep_id."';";
		}
	}
	$dbh -> exec($sql);
	// 缓存泰区字幕
	if (AREA == "th") {
		$array = json_decode($output, true);
		$code = $array['code'];
		if ($code == "0" || $code == 0) {
			$ss_id = $array['result']['season_id'];
			$items = $array['result']['modules'][0]['data']['episodes'];
			for ($i=0; $i<count($items); $i++) {
				$ep_id = $items[$i]['id'];
				$sqlco = "SELECT `expired_time`,`cid` FROM `cache` WHERE `area` = '".$area."' AND `cache_type` = 'subtitle_".$cache_type."' AND `ep_id` = '".$ep_id."'";
				$cres = $dbh -> query($sqlco);
				$vnum = $cres -> fetch();
				if ($vnum) {
					@$expired_time = $vnum2['expired_time'];
					if (time() <= (int)$expired_time) {
						return "no cache";
					}
					$refresh_cache_subtitle = 1; // UPDATE
				} else {
					$refresh_cache_subtitle = 0; // INSERT
				}
				$sub_arr = $array['result']['modules'][0]['data']['episodes'][$i]['subtitles'];
				$sub_count = count($sub_arr);
				$sub_init = '{"code":0,"message":"0","ttl":1,"data":{"suggest_key":"en","subtitles":null}}';
				$sub_json = json_decode($sub_init, true);
				if ($sub_count>0) {
					$sub_json['data']['suggest_key'] = $sub_arr[0]['key']; // 使用第一个作为推荐语言
					$sub_json['data']['subtitles'] = $sub_arr;
				}
				$sub = json_encode($sub_json, JSON_UNESCAPED_UNICODE);
				if (!$refresh_cache_subtitle) {
					$sql = "INSERT INTO `cache` (`expired_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('".$ts."','".AREA."','0','subtitle_".$cache_type."','".$ss_id."','".$ep_id."','".$sub."')";
				} else {
					$sql = "UPDATE `cache` SET `expired_time` = '".$ts."', `cache` = '".$sub."', `cid` = '".$ss_id."' WHERE `area` = '".AREA."' AND `cache_type` = 'subtitle_".$cache_type."' AND `ep_id` = '".$ep_id."';";
				}
				$dbh -> exec($sql);
				
			}
		}
	}
}

// 获取subtitle缓存
function get_cache_subtitle() {
	global $dbh;
	global $cache_type;
	global $refresh_cache_subtitle;
	
	if (AREA == "th") {
		$area = "th"; //泰区
	} else {
		$area = "main"; //主站
	}
	if (EP_ID != "") {
		$sqlco = "SELECT `expired_time`,`cid`,`cache` FROM `cache` WHERE `area` = '".$area."' AND `cache_type` = 'subtitle_".$cache_type."' AND `ep_id` = '".EP_ID."'";
	} else {
		return "";
	}
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum) {
		return "";
	}
	@$cache = $vnum['cache'];
	@$expired_time = $vnum['expired_time'];
	@$ss_id = $vnum['cid'];
	
	if ($cache != "") {
		if (time() <= (int)$expired_time) {
			//修复读取问题
			$cache = str_replace("u0026", "&", $cache);
			$cache = str_replace("\r", "\\r", $cache);
			$cache = str_replace("\n", "\\n", $cache);
			exit($cache);
		} else {
			// 准备刷新缓存
			if ($ss_id == "0") {
				$refresh_cache_subtitle = 1;
			} else {
				exit($cache); // 存在SS_ID，不缓存
			}
		}
	return "";
	}
}

// 写入subtitle缓存
function write_cache_subtitle() {
	global $dbh;
	global $output;
	global $cache_type;
	global $refresh_cache_subtitle;

	if (EP_ID == "") {
		return "no cache";
	}
	if (AREA == "th") {
		$area = "th"; //泰区
	} else {
		$area = "main"; //主站
	}
	$sqlco = "SELECT `cid` FROM `cache` WHERE `area` = '".$area."' AND `cache_type` = 'subtitle_".$cache_type."' AND `ep_id` = '".EP_ID."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum) {
		$ss_id = "0";
	} else {
		@$ss_id = $vnum['cid'];
	}
	if ($ss_id != "" && $ss_id != "0") {
		return "no cache";
	}
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
	$sql = "INSERT INTO `cache` (`expired_time`,`area`,`type`,`cache_type`,`cid`,`ep_id`,`cache`) VALUES ('".$ts."','".AREA."','0','subtitle_".$cache_type."','0','".EP_ID."','".$output."')";
	// 刷新缓存
	if ($refresh_cache_subtitle == 1) {
		$sql = "UPDATE `cache` SET `expired_time` = '".$ts."', `cache` = '".$output."' WHERE `area` = '".AREA."' AND `cache_type` = 'subtitle_".$cache_type."' AND `ep_id` = '".EP_ID."';";
	}
	$dbh -> exec($sql);
}

// 获取黑白名单缓存
function get_cache_blacklist() {
	global $dbh;
	global $uid;
	global $refresh_cache_status;
	$sqlco = "SELECT * FROM `status` WHERE `uid` = '".$uid."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum) {
		return ["⑨","⑨"];
	}
	//$uid = $vnum['uid'];
	$expired_time = $vnum['expired_time'];
	$is_blacklist = $vnum['is_blacklist'];
	$is_whitelist = $vnum['is_whitelist'];
	if (time() > (int)$expired_time) {
		$refresh_cache_status = 1; // 刷新缓存
		return ["⑨","⑨"];
	}
	return [$is_blacklist, $is_whitelist];
}

// 写入黑白名单缓存
function write_cache_blacklist() {
	global $dbh;
	global $uid;
	global $is_blacklist;
	global $is_whitelist;
	global $refresh_cache_status;
	if ($is_blacklist) {
		$is_blacklist = 1;
	} else {
		$is_blacklist = 0;
	}
	if ($is_whitelist) {
		$is_whitelist = 1;
	} else {
		$is_whitelist = 0;
	}
	$ts = time() + CACHE_TIME_BLACKLIST;
	$sql = "INSERT INTO `status` (`expired_time`,`uid`,`is_blacklist`,`is_whitelist`,`reason`) VALUES ('".$ts."','".$uid."','".$is_blacklist."','".$is_whitelist."',NULL)";
	// 刷新缓存
	if ($refresh_cache_status == 1) {
		$sql = "UPDATE `status` SET `expired_time` = '".$ts."', `is_blacklist` = '".$is_blacklist."', `is_whitelist` = '".$is_whitelist."', `reason` = NULL WHERE `uid` = '".$uid."';";
	}
	$dbh -> exec($sql);
}
//读取上次解析状态
function read_status($area){
	global $dbh;
	$result = $dbh -> query("SHOW TABLES LIKE 'status_code'");
	$row = $result -> fetchAll();
	//判断表是否存在
	if ( count($row) == '1' ) {
		$sqlco = "SELECT `code` FROM `status_code` WHERE `area` = '".$area."'";
		$result = $dbh -> query($sqlco);
		$code = $result -> fetch();
		return $code['code'];
	} else {
		return 0;
	}
}

//写入此次解析状态
function write_status($code,$area) {
	global $dbh;
	$result = $dbh -> query("SHOW TABLES LIKE 'status_code'");
	$row = $result -> fetchAll();
	//判断表是否存在
	if ( count($row) == '1' ) {
		$sql = "UPDATE `status_code` SET `time` = '".time()."', `code` = '".$code."' WHERE `area` = '".$area."';";
		$dbh -> exec($sql);
	} else {
		$sql = "CREATE TABLE status_code (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
		area VARCHAR(10),
		code VARCHAR(10),
		time INT
		)";
		$dbh -> exec($sql);
		$sql = "INSERT INTO `status_code` (`area`,`code`,`time`) VALUES ('cn','".$code."','".time()."')";
		$dbh -> exec($sql);
		$sql = "INSERT INTO `status_code` (`area`,`code`,`time`) VALUES ('hk','".$code."','".time()."')";
		$dbh -> exec($sql);
		$sql = "INSERT INTO `status_code` (`area`,`code`,`time`) VALUES ('tw','".$code."','".time()."')";
		$dbh -> exec($sql);
		$sql = "INSERT INTO `status_code` (`area`,`code`,`time`) VALUES ('th','".$code."','".time()."')";
		$dbh -> exec($sql);
	}
}

// 写入日志
function write_log() {
	global $dbh;
	if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])){
		$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
	} elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} elseif (!empty($_SERVER["HTTP_CLIENT_IP"])){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	} else {
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	if (BILIROAMING_VERSION_CODE==""){
		$version_code = "0";
	} else {
		$version_code = BILIROAMING_VERSION_CODE;
	}
	if (!defined('UID')) {
		define('UID', '0');
	}
	$ts = time();
	$sql = "INSERT INTO `log` (`time`,`ip`,`area`,`version`,`version_code`,`access_key`,`uid`,`ban_code`,`path`,`query`) VALUES (now(),'".$ip."','".AREA."','".BILIROAMING_VERSION."','".$version_code."','".ACCESS_KEY."','".UID."','".BAN_CODE."','".PATH."','".QUERY."')";
	$dbh -> exec($sql);
}
?>
