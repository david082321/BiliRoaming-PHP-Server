<?php
// 封锁
define('BLOCK_TYPE','blacklist'); // 封锁类型：none 不封锁, blacklist 黑名单, whitelist 本地白名单
define('REPLACE_HLW', 1); // 是否替换成葫芦娃(仅黑/白名单时生效)。0 否, 1 是
define('NEED_LOGIN', 0); //是否要登录用户才能用。0 否, 1 是
define('BILIROAMING', 1); //是否要用哔哩漫游才能使用。0 否, 1 是

// 缓存
define('SAVE_CACHE', 0); //开启缓存，须配置MYSQL。0 否, 1 是
define('CACHE_TIME', 7200); //缓存时长（秒）
define('DB_HOST', 'localhost');
define('DB_USER', '这里改成登录的用户名'); //登录的用户名
define('DB_PASS', '登录的密码'); //登录的密码
define('DB_NAME', '数据库名称'); //数据库名称

//其他
define('WELCOME', 'Success!'); //首页欢迎语
define('BLOCK_RETURN', '{"code":-10403,"message":"你已被封锁"}'); //封锁返回内容
?>