<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit(BLOCK_RETURN);}

// 判断登录状态
if (ACCESS_KEY != "") {
	$sqlco = "SELECT `uid` as num, `add_time` FROM `keys` WHERE `access_key` = '".ACCESS_KEY."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	$uid = $vnum['num'];
	$add_time = $vnum['add_time'];
	if ($uid == "" || $uid == "0") {
		$out = get_userinfo();
		$uid = $out[0];
		$due = $out[1];
		if ($uid != "0") {
			$sql = " INSERT INTO `keys` (`add_time`,`uid`,`access_key`,`due_date`) VALUES (now(),'$uid','".ACCESS_KEY."','$due')";
			$dbh -> exec($sql);
		}
	} elseif (strtotime(now()) - strtotime($add_time) >= 60*60*24*1) {
		$uid = refresh_userinfo();
	}
}
?>