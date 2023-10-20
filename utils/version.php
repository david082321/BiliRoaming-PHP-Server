<?php
// 防止外部破解
if(!defined('SYSTEM')) {exit();} // 防止外部破解，此行勿改

const VERSION = '4.5.3';
define('AGENT', "biliroaming-php-server/".VERSION);
// 加上json的Header
header('Content-Type: application/json; charset=utf-8');
// 加上web的Header
header("Access-Control-Allow-Origin: https://www.bilibili.com");
header("Access-Control-Allow-Credentials: true");
?>
