<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();} // 防止外部破解，此行勿改

define('VERSION', '4.2.2');
// 加上json的Header
header('Content-Type: application/json; charset=utf-8');
// 加上web的Header
header("Access-Control-Allow-Origin: https://www.bilibili.com");
header("Access-Control-Allow-Credentials: true");

// 这个参数，不懂就别改
define('APPKEY', '1d8b6e7d45233436');
define('APPSEC', '560c52ccd288fed045859ed18bffd973');
define('APPKEY_TH', '7d089525d3611b1c');
define('APPSEC_TH', 'acd495b248ec528c2eed1e862d393126');
?>
