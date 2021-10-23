<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit(BLOCK_RETURN);}

// 判断登录状态
if ($member_type > 0) {
	// pass
} else if (ACCESS_KEY == "") {
	$member_type = 0; //未登录
} else {
	// 判断大会员
	$sqlco = "SELECT `due_date` FROM `keys` WHERE `access_key` = '".ACCESS_KEY."'";
	$cres = $dbh -> query($sqlco);
	$vnum = $cres -> fetch();
	$due = $vnum['due_date'];
	if ((int)$due > time()*1000) {
		$member_type = 2; // 大会员
	} else {
		$member_type = 1; // 不是大会员
	}
}
?>