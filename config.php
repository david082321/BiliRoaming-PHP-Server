<?php
// 防止外部破解
define('SYSTEM', TRUE);

// 代码放置路径（通常不需要修改）
define('ROOT_PATH',$_SERVER['DOCUMENT_ROOT'].'/');

// 封锁
define('BLOCK_TYPE', 'blacklist'); // 封锁类型：blacklist 在线黑名单, whitelist 在线白名单, local_blacklist 本地黑名单, local_whitelist 本地白名單, 不是填写前面几个就是不封锁
define('REPLACE_TYPE', 'random'); // 是否替换视频(仅黑/白名单时生效)。random 随机番剧（海尔兄弟，喜羊羊，猫和老鼠，大耳朵图图等等）
define('NEED_LOGIN', 0); // 是否要登录用户才能用。0 否, 1 是
define('BILIROAMING', 1); // 是否要用哔哩漫游才能使用。0 否, 1 是
define('WEB_ON', 0); // 是否开启web接口 0 否, 1 是 目前Web接口不受哔哩漫游请求头影响，且不受服务器锁区影响（待更新）
define('LOCK_AREA', 0); // 服务器锁区，须设置$SERVER_AREA。0 否, 1 是
$BLACKLIST = array('1', '2', '3'); // 本地黑名单，填写 uid，可自行添加、删除，注意使用英文,和'
$WHITELIST = array('1', '2', '3'); // 本地白名单，填写 uid，可自行添加、删除，注意使用英文,和'
define('ROAMING_MIN_VER', 0); // 设置最低允许漫游版本，详情看哔哩漫游仓库 versionCode

// 封锁指定epid视频
define('BAN_EP', 0); // 是否开启ban特定视频
//$epid_list = array('369747','371362','371461','371462','371463','371596'); //示范内容：巨人最终季
/* // 示范内容：○之空(范围屏蔽)
foreach (range(368703, 368714) as $n) {
	array_push($epid_list, $n);
}
*/
$epid_list = array();
// 封锁指定cid视频
define('BAN_CID', 0); // 是否开启ban特定视频
$cid_list = array();

// 缓存
define('SAVE_CACHE', 0); // 开启缓存，须配置MySQL数据库。0 否, 1 是
define('CACHE_TIME', 60*60*1.95); // Playurl缓存时长（秒），目前最长可到14400秒，建议7200秒以下(部分视频只有2小时)
define('CACHE_TIME_SEASON', 60*60*0.5); // 泰国 season 的缓存时长（秒）
// 发生错误时的缓存时长
define('CACHE_TIME_10403', 60*60*24*14); // 返回10403（地区错误）时的缓存时长（秒）
define('CACHE_TIME_412', 60*60*24*7); // 返回412（服务器IP被风控）时的缓存时长（秒）
define('CACHE_TIME_404', 60*60*24*1); // 返回404时的缓存时长（秒）
define('CACHE_TIME_OTHER', 60*60*1); // 返回其他错误时的缓存时长（秒）
define('CACHE_TIME_USER', 60*60*24*1); //缓存用户key的时长（秒）
define('CACHE_TIME_BLACKLIST', 60*15); //缓存黑白名单的时长（秒）

// MySQL数据库
define('DB_HOST', 'localhost');
define('DB_USER', '这里改成登录的用户名'); // 登录的用户名
define('DB_PASS', '登录的密码'); // 登录的密码
define('DB_NAME', '数据库名称'); // 数据库名称

// 服务器所在的地区
/*
	可不填，填写后可以锁区。
	若要填写，请正确填写，以确保数据库写入正确。
	下方为填写例子
	$SERVER_AREA = array('cn');
	$SERVER_AREA = array('hk','tw');
	$SERVER_AREA = array('th');
	*/
$SERVER_AREA = array(); // 空白，不锁区
// 指定锁区（不论上面怎么设置，只要填这个，可以锁定指定的地区）
$BAN_SERVER_AREA = array(); // 空白，不锁区

// API相关(视频服务器)
define('CUSTOM_HOST_DEFAULT', 'https://api.bilibili.com'); // 兼容未发送 area 参数的其他脚本
define('CUSTOM_HOST_CN', 'https://api.bilibili.com'); // CN 解析api
define('CUSTOM_HOST_HK', 'https://api.bilibili.com'); // HK 解析api
define('CUSTOM_HOST_TW', 'https://api.bilibili.com'); // TW 解析api

// API相关(搜索服务器-APP)
define('CUSTOM_HOST_DEFAULT_SEARCH', 'https://app.bilibili.com'); // 兼容未发送 area 参数的其他脚本
define('CUSTOM_HOST_CN_SEARCH', 'https://app.bilibili.com'); // CN 搜索api
define('CUSTOM_HOST_HK_SEARCH', 'https://app.bilibili.com'); // HK 搜索api
define('CUSTOM_HOST_TW_SEARCH', 'https://app.bilibili.com'); // TW 搜索api

// API相关(搜索服务器-WEB)
define('CUSTOM_HOST_DEFAULT_WEB_SEARCH', 'https://api.bilibili.com'); // 兼容未发送 area 参数的其他脚本
define('CUSTOM_HOST_CN_WEB_SEARCH', 'https://api.bilibili.com'); // CN 搜索api
define('CUSTOM_HOST_HK_WEB_SEARCH', 'https://api.bilibili.com'); // HK 搜索api
define('CUSTOM_HOST_TW_WEB_SEARCH', 'https://api.bilibili.com'); // TW 搜索api

// API相关(东南亚服务器)
define('CUSTOM_HOST_TH', 'https://app.biliintl.com'); // 泰区 解析api
define('CUSTOM_HOST_TH_SEARCH', 'https://app.biliintl.com'); // 泰区 搜索api
define('CUSTOM_HOST_TH_SUB', 'https://app.biliintl.com'); // 泰区 字幕api
define('CUSTOM_HOST_TH_WEBSUB', 'https://api.bilibili.tv'); // 泰区 字幕api
define('CUSTOM_HOST_TH_TOKEN', 'https://passport.biliintl.com'); // 泰区 刷新TOKEN api

// 自定义API,避免集中请求，降低风控几率
//$hk_api = array('host1','host2','host3');// 可以自定义其他反代api,例如云函数,CFW
//$tw_api = array('host1','host2','host3');// 可以自定义其他反代api,例如云函数,CFW
// $hk_sum = array_rand($hk_api);//计数
// $tw_sum = array_rand($tw_api);//计数
// define('CUSTOM_HOST_HK', $hk_api[$hk_sum]); // 随机调用HK 启用要注释上方默认api
// define('CUSTOM_HOST_TW', $tw_api[$tw_sum]); // 随机调用TW 启用要注释上方默认api

// 指定HTTP或SOCKS5代理
define('PROXY_ON', 0); // 开启功能。0 否, 1 是
//define('PROXY_TYPE', "CURLPROXY_HTTP"); // 使用HTTP代理
define('PROXY_TYPE', "CURLPROXY_SOCKS5"); // 使用SOCKS5代理
define('PROXY_IP', "127.0.0.1:1234"); // 自行修改地址（web脚本用）
define('PROXY_IP_CN', ""); // 自行修改地址（CN）
define('PROXY_IP_HK', ""); // 自行修改地址（HK）
define('PROXY_IP_TW', ""); // 自行修改地址（TW）
define('PROXY_IP_TH', ""); // 自行修改地址（泰区）

// 指定ip回源
define('IP_RESOLVE', 0); // 开启功能。0 否, 1 是
$ips = array('172.0.0.1', '192.168.0.1', '1.2.3.4');
$hosts = array('workers.dev', 'workers.dev');

// 其他
define('WELCOME', "file"); // 首页画面。text 文字， file 加载文件
define('WELCOME_TEXT', "Success! Power by BiliRoaming-PHP-Server."); // 首页欢迎语
define('WELCOME_FILE', "hello.php"); // 首页文件

// 【禁忌功能】替换访问密钥
define('MAGIC_KEY', '123'); // 请用密码生成器，随机生成任意内容，然后把123改成你生成的内容。(建议长一点)
define('RESIGN_CN', 0); // 替换成大会员。0 否, 1 是
define('RESIGN_TH', 0); // 替换成泰国登录会员。0 否, 1 是
define('RESIGN_TH_PAID', 0); // 替换成泰国付费会员。0 否, 1 是

?>
