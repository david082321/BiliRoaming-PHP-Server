<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit(BLOCK_RETURN);}
//pdo连接数据库
$db_host=DB_HOST;
$db_user=DB_USER;
$db_pass=DB_PASS;
$db_name=DB_NAME;
$dbh='mysql:host='.$db_host.';'.'dbname='.$db_name;
try{
   $dbh = new PDO($dbh,$db_user,$db_pass);
   //echo '连接成功';
}catch(PDOException $e) {
   //pass
}
// 判断登录状态
if (ACCESS_KEY != "") {
	$sqlco = "SELECT `uid` as num FROM `keys` WHERE `access_key` = '".ACCESS_KEY."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	$uid = $vnum['num'];
	if ($uid == "" || $uid == "0") {
		$out = get_userinfo();
		$uid = $out[0];
		$due = $out[1];
		if ($uid != "0") {
			$sql = " INSERT INTO `keys` (`add_time`,`uid`,`access_key`,`due_date`) VALUES (now(),'$uid','".ACCESS_KEY."','$due')";
			$dbh -> exec($sql);
		}
	}
}

function get_userinfo() {
	$sign = md5("access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS.APPSEC);
	$url = "https://app.bilibili.com/x/v2/account/myinfo?access_key=".ACCESS_KEY."&appkey=".APPKEY."&ts=".TS."&sign=".$sign;
	$output = get_webpage($url);
	$array = json_decode($output, true);
	$code = $array['code'];
	if ($code == "0") {
		$out[0] = $array['data']['mid'];
		$out[1] = $array['data']['vip']['due_date'];
	}else{
		$out[0] = "0";
		$out[1] = "0";
	}
	return $out;
}
?>