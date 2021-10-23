# BiliRoaming-PHP-Server
哔哩漫游 PHP 解析服务器


自建解析服务器参考配置

## 下载：

* [下载最新版](https://github.com/david082321/BiliRoaming-PHP-Server/releases/latest)

* [下载历史版本](https://github.com/david082321/BiliRoaming-PHP-Server/releases)

* ！！！重要！！！由于网页脚本的更新，为作缓存区分，v3.0.0 更新了数据库字段。从 1.x 或 2.x 升级的用户，请务必更新数据库。

## 用法：

* 直接放到网站根目录，例如 wwwroot 或 public_html 或 private_html

* 默认为「黑名单-猫和老鼠」模式，若要切换到其他模式，请看底下说明。

## 示例：

![示例](https://i.loli.net/2021/01/10/VwJ5D1GoRBbyfmq.jpg)


(完成)

------

## (非必要步骤) 切换到其他模式

* 默认为「黑名单-猫和老鼠」模式，若您想使用「黑名单」或是「黑名单-葫芦娃」或是「本地白名单」或是「无任何限制」等其他模式，请手动修改 config.php

* 每行后面都有注释提供参考

## (非必要步骤) 防止重复的 301 转址

### apache

* [下载这个，然后放在网站根目录 (.htaccess) ](https://github.com/david082321/BiliRoaming-PHP-Server/blob/main/.htaccess)

### nginx

* 在配置文件中加入以下代码

    server

    {

    #...(中间略过，请加在配置文件最底下)...

    rewrite "^/pgc/player/api/playurl?(.*)$" /pgc/player/api/playurl/index.php?$1 last;

    rewrite "^/pgc/player/web/playurl?(.*)$" /pgc/player/web/playurl/index.php?$1 last;

    rewrite "^/intl/gateway/v2/ogv/playurl?(.*)$" /intl/gateway/v2/ogv/playurl/index.php?$1 last;

    rewrite "^/intl/gateway/v2/app/search/type?(.*)$" /intl/gateway/v2/app/search/type/index.php?$1 last;

    rewrite "^/intl/gateway/v2/app/subtitle?(.*)$" /intl/gateway/v2/app/subtitle/index.php?$1 last;

    rewrite "^/intl/gateway/v2/ogv/view/app/season?(.*)$" /intl/gateway/v2/ogv/view/app/season/index.php?$1 last;

    rewrite "^/x/v2/search/type?(.*)$" /x/v2/search/type/index.php?$1 last;

    rewrite "^/x/web-interface/search/type?(.*)$" /x/web-interface/search/type/index.php?$1 last;

    }

## (非必要步骤) 缓存

* 安装 MySQL

* 配置 config.php 的缓存设置

* 导入 cache.sql

* ！！！重要！！！v4.0.0 更新了数据库字段。从 1.x 或 2.x 或 3.x 升级的用户，请务必更新数据库。

## (非必要步骤，实验性) 支持网页版油猴脚本

* [油猴脚本地址](https://github.com/ipcjs/bilibili-helper/blob/user.js/packages/unblock-area-limit/README.md)

* 配置 config.php 的 WEB_ON

* 脚本的 代理服务器->自定义 输入以下内容 ( example.com 请改成你的服务器地址)

##### 　　　https://example.com/

* 脚本的 代理服务器->自定义(泰国/东南亚) 输入以下内容 ( example.com 请改成你的服务器地址)

##### 　　　https://example.com/intl/gateway/v2/ogv/playurl/

* (可选步骤) 配置上面的禁用 301 转址。然后脚本的 代理服务器->自定义 改成

##### 　　　https://example.com

* 注意：不配置上面的 「禁用 301 转址」，少部分 泰国/东南亚番剧 将无法在网页版加载。(哔哩漫游无影响)

--------

# 文件功能介绍

├─intl/gateway/v2

│　├─app

│　│　├─search/type/index.php (东南亚APP 搜索)

│　│　└─subtitle/index.php (东南亚APP 字幕)

│　└─ogv

│　　　├─playurl/index.php (东南亚APP playurl)

│　　　└─view/app/season/index.php (东南亚APP season)

├─pgc/player/

│　├─api/playurl/index.php (APP playurl)

│　└─web/playurl/index.php (WEB playurl)

├─x/

│　├─v2/search/type/index.php (APP 搜索)

│　└─web-interface/search/type/index.php (WEB 搜索)

├─utils/

│　├─auth.php (鉴权)

│　├─cache.php (缓存playurl判断是否为大会员)[仅缓存使用]

│　├─fuck_search.php (在搜索中添加提示)[未公开]

│　├─fuck_sub.php (添加东南亚番剧字幕)[未公开]

│　├─functions.php (功能函数合集)

│　├─functions_cache.php (功能函数合集)[仅缓存使用]

│　├─lock_area.php (锁区、web接口判断)

│　├─log.php (缓存用户)[仅缓存使用]

│　├─process.php (处理用户传入参数)

│　├─refresh_token.php (自动刷新访问密钥)[未公开]

│　├─replace.php (修改返回内容)

│　├─replace_playurl.php (替换视频)

│　├─resign.php (替换访问密钥)[未公开]

│　└─version.php (版本信息、Header)

├─cache.sql (导入MySQL用的)[仅缓存使用]

├─config.php (设置本程序各种参数) ＜──参数设置在这里

├─hello.php (默认欢迎页面)

└─index.php (WEB playurl、显示欢迎页)
