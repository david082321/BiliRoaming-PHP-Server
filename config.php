<?php
// 封锁
define('BLOCK_TYPE','blacklist'); // 封锁类型：none 不封锁, blacklist 黑名单, whitelist 本地白名单
define('REPLACE_TYPE', "tom"); // 是否替换视频(仅黑/白名单时生效)。hlw 葫芦娃, tom 猫和老鼠, xyy 喜羊羊
define('NEED_LOGIN', 0); //是否要登录用户才能用。0 否, 1 是
define('BILIROAMING', 1); //是否要用哔哩漫游才能使用。0 否, 1 是
define('WEB_ON', 0);//是否开启web接口 0 否, 1 是 目前Web接口不受哔哩漫游请求头影响，且无法服务器锁区（待更新）
define('LOCK_AREA', 0); //服务器锁区，须设置$SERVER_AREA。0 否, 1 是
$WHITELIST = array('1', '2', '3'); // 本地白名单，填写 uid，可自行添加、删除，注意使用英文,和'

// 封锁指定epid视频
define('BAN_EP', 0); //是否开启ban特定视频
//$epid_list = array("369747","371362","371461","371462","371463","371596"); //示范内容：巨人最终季
$epid_list = array();
// 封锁指定cid视频
define('BAN_CID', 0); //是否开启ban特定视频
$cid_list = array();

// 缓存
define('SAVE_CACHE', 0); //开启缓存，须配置MySQL。0 否, 1 是
define('CACHE_TIME', 7200); //缓存时长（秒）
define('DB_HOST', 'localhost');
define('DB_USER', '这里改成登录的用户名'); //登录的用户名
define('DB_PASS', '登录的密码'); //登录的密码
define('DB_NAME', '数据库名称'); //数据库名称

// 服务器所在的地区
    /*
    可不填，填写后可以锁区，及缓存-10403。
    若要填写，请正确填写，以确保数据库写入正确。
    下方为填写例子
    $SERVER_AREA = array("cn");
    $SERVER_AREA = array("hk","tw");
    $SERVER_AREA = array("th");
    */
$SERVER_AREA = array(); // 空白，不锁区

// API相关
define('CUSTOM_HOST_DEFAULT', 'https://api.bilibili.com'); // 兼容未发送 area 参数的其他脚本
define('CUSTOM_HOST_CN', 'https://api.bilibili.com'); // CN 解析api
define('CUSTOM_HOST_HK', 'https://api.bilibili.com'); // HK 解析api
define('CUSTOM_HOST_TW', 'https://api.bilibili.com'); // TW 解析api
define('CUSTOM_HOST_TH', 'https://api.global.bilibili.com'); //泰区 解析api
define('CUSTOM_HOST_SUB', 'https://app.global.bilibili.com'); //泰区 搜索字幕用api

// 自定义API,避免集中请求，降低风控几率
//$hk_api = array("host1","host2","host3");//可以自定义其他反代api,例如云函数,CFW
//$tw_api = array("host1","host2","host3");//可以自定义其他反代api,例如云函数,CFW
// $hk_sum = array_rand($hk_api);//计数
// $tw_sum = array_rand($tw_api);//计数
// define('CUSTOM_HOST_HK', $hk_api[$hk_sum]); //随机调用HK 启用要注释上方默认api
// define('CUSTOM_HOST_TW', $tw_api[$tw_sum]); //随机调用TW 启用要注释上方默认api

// 指定ip回源
define('IP_RESOLVE', 0); // 开启功能。0 否, 1 是
$ips=array("172.0.0.1","192.168.0.1","1.2.3.4");
$hosts=array("workers.dev","workers.dev");

// 其他
define('WELCOME', 'Success!'); //首页欢迎语
define('BLOCK_RETURN', '{"code":-10403,"message":"抱歉您已被封锁"}'); //封锁返回内容

// 参数，不懂就别改
define('APPKEY', '1d8b6e7d45233436');
define('APPSEC', '560c52ccd288fed045859ed18bffd973');
define('ACCESS_KEY', @$_GET['access_key']);
define('CID', @$_GET['cid']);
define('EP_ID', @$_GET['ep_id']);
if (@$_GET['area']==""){
    define('AREA', 'noarea');
}else{
    define('AREA', @$_GET['area']);
}
if (@$_GET['ts']==""){
    define('TS', time());
}else{
    define('TS', @$_GET['ts']);
}
$baned = 0;
if (in_array(EP_ID, $epid_list) && BAN_EP == 1) {
    $baned = 1;
}
if (in_array(CID, $cid_list) && BAN_CID == 1) {
    $baned = 1;
}
// 防止外部破解
if(!defined('SYSTEM')){
    exit(BLOCK_RETURN);
}
?>
