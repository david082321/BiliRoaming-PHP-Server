<?php
// 防止外部破解
if(!defined('SYSTEM')){
    header('HTTP/1.1 404 Not Found');
    exit('禁止访问');
}

// 封锁
define('BLOCK_TYPE','blacklist'); // 封锁类型：none 不封锁, blacklist 黑名单, whitelist 本地白名单
define('REPLACE_HLW', 1); // 是否替换成葫芦娃(仅黑/白名单时生效)。0 否, 1 是
define('NEED_LOGIN', 0); //是否要登录用户才能用。0 否, 1 是
define('BILIROAMING', 1); //是否要用哔哩漫游才能使用。0 否, 1 是
define('SERVER_AREA', ''); //此服务器所在的地区，可不填，填写后可以锁区，及缓存-10403。cn hk tw th
$whitelist = array('1', '2', '3'); // 本地白名单，填写 uid，可自行添加、删除，注意使用英文,和'

// 缓存
define('SAVE_CACHE', 0); //开启缓存，须配置MySQL。0 否, 1 是
define('CACHE_TIME', 7200); //缓存时长（秒）
define('DB_HOST', 'localhost');
define('DB_USER', '这里改成登录的用户名'); //登录的用户名
define('DB_PASS', '登录的密码'); //登录的密码
define('DB_NAME', '数据库名称'); //数据库名称

//API相关
define('CUSTOM_HOST_DEFAULT', 'api.bilibili.com'); //默认解析HKTWapi
define('CUSTOM_HOST_TH', 'api.global.bilibili.com'); //泰区解析api
define('CUSTOM_HOST_SUB', 'app.global.bilibili.com'); //泰区搜索字幕用api

//自定义API,避免集中请求，降低风控几率
//$tw_api = array("api1.example.com","api2.example.com","api3.example.com","api.bilibili.com");//可以自定义其他反代api,例如云函数,CFW
//$tw_sum = array_rand($tw_api);//计数
//define('CUSTOM_HOST_DEFAULT', $tw_api[$tw_sum]); //随机调用

//其他
define('WELCOME', 'Success!'); //首页欢迎语
define('BLOCK_RETURN', '{"code":-10403,"message":"你已被封锁"}'); //封锁返回内容
?>