<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();} // 防止外部破解，此行勿改

define('VERSION', '4.2.3');
// 加上json的Header
header('Content-Type: application/json; charset=utf-8');
// 加上web的Header
header("Access-Control-Allow-Origin: https://www.bilibili.com");
header("Access-Control-Allow-Credentials: true");

// 这些参数，不懂就别改
define('APPKEY', '1d8b6e7d45233436');
define('APPSEC', '560c52ccd288fed045859ed18bffd973');
define('APPKEY_TH', '7d089525d3611b1c');
define('APPSEC_TH', 'acd495b248ec528c2eed1e862d393126');
$appkey2sec = array("9d5889cf67e615cd" => "8fd9bb32efea8cef801fd895bef2713d", // Ai4cCreatorAndroid
	"1d8b6e7d45233436" => "560c52ccd288fed045859ed18bffd973", // Android
	"07da50c9a0bf829f" => "25bdede4e1581c836cab73a48790ca6e", // AndroidB
	"8d23902c1688a798" => "710f0212e62bd499b8d3ac6e1db9302a", // AndroidBiliThings
	"dfca71928277209b" => "b5475a8825547a4fc26c7d518eaaa02e", // AndroidHD
	"bb3101000e232e27" => "36efcfed79309338ced0380abd824ac1", // AndroidI
	"4c6e1021617d40d9" => "e559a59044eb2701b7a8628c86aa12ae", // AndroidMallTicket
	"c034e8b74130a886" => "e4e8966b1e71847dc4a3830f2d078523", // AndroidOttSdk
	"4409e2ce8ffd12b8" => "59b43e04ad6965f34319062b478f83dd", // AndroidTV
	"37207f2beaebf8d7" => "e988e794d4d4b6dd43bc0e89d6e90c43", // BiliLink
	"9a75abf7de2d8947" => "35ca1c82be6c2c242ecc04d88c735f31", // BiliScan
	"7d089525d3611b1c" => "acd495b248ec528c2eed1e862d393126", // BstarA
);
?>
