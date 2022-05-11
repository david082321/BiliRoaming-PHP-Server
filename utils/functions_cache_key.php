<?php

// 从缓存获取key
function get_mykey($type) {
	global $dbh;
	$sqlco = "SELECT `uid`,`expired_time`,`access_token`,`refresh_token`,`type` FROM `my_keys` WHERE `type` = '".$type."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum) {
		return "No key";
	}
	$key = $vnum['access_token'];
	$time = $vnum['expired_time'];
	if (time() >= $time) {
		$refresh = $vnum['refresh_token'];
		$key = refresh_mykey($type, $key, $refresh);
	}
	return $key;
}

// 刷新缓存的key
function refresh_mykey($type, $key, $refresh) {
	define('ACCESS_TOKEN', $key);
	define('REFRESH_TOKEN', $refresh);
	$ts = time();
	if ($type == "8" || $type == "9") {
		include(ROOT_PATH."utils/refresh_token_th.php");
	} else {
		include(ROOT_PATH."utils/refresh_token.php");
	}
	$array = json_decode($output2, true);
	$token_info = $array["data"]["token_info"];
	$uid = $token_info["mid"];
	$access_token = $token_info["access_token"];
	$refresh_token = $token_info["refresh_token"];
	$expires_in = $token_info["expires_in"];
	$time = $ts + $expires_in;
	// 写入数据库
	global $dbh;
	$sql = "UPDATE `my_keys` SET `access_token` = '".$access_token."', `refresh_token` = '".$refresh_token."', `expired_time` = '".$time."' WHERE `uid` = '".$uid."';";
	$dbh -> exec($sql);
	return $access_token;
}

// 新增key
function add_mykey($type, $key, $refresh) {
	global $dbh;
	define('ACCESS_TOKEN', $key);
	define('REFRESH_TOKEN', $refresh);
	$ts = time();
	if ($type == "8" || $type == "9") {
		include(ROOT_PATH."utils/refresh_token_th.php");
	} else {
		include(ROOT_PATH."utils/refresh_token.php");
	}
	$array = json_decode($output2, true);
	$code = $array["code"];
	if ($code != 0){
		header('Content-Type: application/json; charset=utf-8');
		exit($output2);
	}
	print($output2);
	$token_info = $array["data"]["token_info"];
	$uid = $token_info["mid"];
	$access_token = $token_info["access_token"];
	$refresh_token = $token_info["refresh_token"];
	$expires_in = $token_info["expires_in"];
	$time = $ts + $expires_in;
	// 判断是否已在数据库
	$sqlco = "SELECT `uid` FROM `my_keys` WHERE `uid` = '".$uid."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	if (!$vnum) {
	   	$sql = "INSERT INTO `my_keys` (`access_token`,`refresh_token`,`expired_time`,`uid`,`type`) VALUES ('".$access_token."','".$refresh_token."','".$time."','".$uid."','".$type."')";
	   	$info = "成功添加";
	} else {
	   	$sql = "UPDATE `my_keys` SET `access_token` = '".$access_token."', `refresh_token` = '".$refresh_token."', `expired_time` = '".$time."' WHERE `uid` = '".$uid."';";
	   	$info = "已刷新";
	}
	// 写入数据库
	$dbh -> exec($sql);
	return $info;
}
?>
