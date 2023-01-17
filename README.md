# 写在开头
* 动手能力强的可以用这个，服务器回应的速度比我这个快多了。
* Go: https://github.com/JasonKhew96/biliroaming-go-server
* Rust: https://github.com/pchpub/BiliRoaming-Rust-Server

----

# BiliRoaming-PHP-Server
哔哩漫游 PHP 解析服务器

自建解析服务器参考配置

## 下载：

* [下载最新版](https://github.com/david082321/BiliRoaming-PHP-Server/releases/latest)

## 用法：

* 直接放到网站根目录，例如 wwwroot 或 public_html 或 private_html 或 网站域名的文件夹(宝塔)

* 默认为「在线黑名单」模式。

## 示例：

![示例](https://i.loli.net/2021/01/10/VwJ5D1GoRBbyfmq.jpg)


(完成)

------

## (非必要步骤) 修改黑白名单模式、修改其他设置

* 默认为「在线黑名单」模式，若您想使用「本地黑名单」或是「黑名单-替换国产动画」或是「在线白名单」或是「本地白名单」或是「无任何限制」等其他模式，请手动修改 config.php

* config.php 每个修改项目，后面都有注释提供参考

## (非必要步骤) 防止重复的 301 转址

若要开启网页版油猴脚本的支持，务必设置这个

### Apache

* [下载这个，然后放在网站根目录 (.htaccess) ](https://github.com/david082321/BiliRoaming-PHP-Server/blob/main/.htaccess)

### Nginx

* 在配置文件中加入以下代码

```
server {
    #...(中间略过，请加在配置文件最底下)...
    rewrite "^/intl/gateway/v2/app/search/type?(.*)$" /intl/gateway/v2/app/search/type/index.php?$1 last;
    rewrite "^/intl/gateway/v2/app/search/v2?(.*)$" /intl/gateway/v2/app/search/v2/index.php?$1 last;
    rewrite "^/intl/gateway/v2/app/subtitle?(.*)$" /intl/gateway/v2/app/subtitle/index.php?$1 last;
    rewrite "^/intl/gateway/v2/ogv/playurl?(.*)$" /intl/gateway/v2/ogv/playurl/index.php?$1 last;
    rewrite "^/intl/gateway/v2/ogv/view/app/season?(.*)$" /intl/gateway/v2/ogv/view/app/season/index.php?$1 last;
    rewrite "^/intl/gateway/v2/ogv/view/app/season2?(.*)$" /intl/gateway/v2/ogv/view/app/season2/index.php?$1 last;
    rewrite "^/intl/gateway/v2/ogv/view/app/episode?(.*)$" /intl/gateway/v2/ogv/view/app/episode/index.php?$1 last;
    rewrite "^/pgc/player/api/playurl?(.*)$" /pgc/player/api/playurl/index.php?$1 last;
    rewrite "^/pgc/player/web/playurl?(.*)$" /pgc/player/web/playurl/index.php?$1 last;
    rewrite "^/pgc/view/web/season?(.*)$" /pgc/view/web/season/index.php?$1 last;
    rewrite "^/x/intl/passport-login/oauth2/refresh_token?(.*)$" /x/intl/passport-login/oauth2/refresh_token/index.php?$1 last;
    rewrite "^/x/v2/search/type?(.*)$" /x/v2/search/type/index.php?$1 last;
    rewrite "^/x/web-interface/search/type?(.*)$" /x/web-interface/search/type/index.php?$1 last;
}
```

### Nginx，但是是宝塔用户

* 打开网站设置的「伪静态」，然后直接复制下面代码，然后保存

```
rewrite "^/intl/gateway/v2/app/search/type?(.*)$" /intl/gateway/v2/app/search/type/index.php?$1 last;
rewrite "^/intl/gateway/v2/app/search/v2?(.*)$" /intl/gateway/v2/app/search/v2/index.php?$1 last;
rewrite "^/intl/gateway/v2/app/subtitle?(.*)$" /intl/gateway/v2/app/subtitle/index.php?$1 last;
rewrite "^/intl/gateway/v2/ogv/playurl?(.*)$" /intl/gateway/v2/ogv/playurl/index.php?$1 last;
rewrite "^/intl/gateway/v2/ogv/view/app/season?(.*)$" /intl/gateway/v2/ogv/view/app/season/index.php?$1 last;
rewrite "^/intl/gateway/v2/ogv/view/app/season2?(.*)$" /intl/gateway/v2/ogv/view/app/season2/index.php?$1 last;
rewrite "^/intl/gateway/v2/ogv/view/app/episode?(.*)$" /intl/gateway/v2/ogv/view/app/episode/index.php?$1 last;
rewrite "^/pgc/player/api/playurl?(.*)$" /pgc/player/api/playurl/index.php?$1 last;
rewrite "^/pgc/player/web/playurl?(.*)$" /pgc/player/web/playurl/index.php?$1 last;
rewrite "^/pgc/view/web/season?(.*)$" /pgc/view/web/season/index.php?$1 last;
rewrite "^/x/intl/passport-login/oauth2/refresh_token?(.*)$" /x/intl/passport-login/oauth2/refresh_token/index.php?$1 last;
rewrite "^/x/v2/search/type?(.*)$" /x/v2/search/type/index.php?$1 last;
rewrite "^/x/web-interface/search/type?(.*)$" /x/web-interface/search/type/index.php?$1 last;
```

## (非必要步骤) 缓存

* 安装 MySQL

* 配置 config.php 的缓存设置

* 导入 cache.sql

## (非必要步骤，实验性) 支持网页版油猴脚本

* [油猴脚本地址](https://github.com/ipcjs/bilibili-helper/blob/user.js/packages/unblock-area-limit/README.md)

* 修改 config.php 的 WEB_ON 设置

* 配置上面的禁用 301 转址

* 脚本的 代理服务器->自定义 输入以下内容 (example.com 请改成你的服务器地址)

##### 　　　　https://example.com

* 注意：不配置上面的 「禁用 301 转址」，将无法正常加载。(哔哩漫游无影响)

* 题外话：如果不会配置，可以改用[这个脚本](https://github.com/zzc10086/grocery_store/blob/master/bili_proxy/BPplayurl.php)

--------

# 文件功能介绍

```
├─intl/gateway/v2
│　├─app
│　│　└─search
│　│　　├─type/index.php (东南亚APP 搜索)
│　│　　└─v2/index.php (东南亚APP 搜索)
│　└─subtitle/index.php (东南亚APP 字幕)
│　└─ogv
│　　　├─playurl/index.php (东南亚APP 播放地址)
│　　　├─view/app/episode/index.php (东南亚APP episode)
│　　　├─view/app/season/index.php (东南亚APP season)
│　　　└─view/app/season2/index.php (东南亚APP season)
├─pgc
│　├─player
│　│　├─api/playurl/index.php (APP 播放地址)
│　│　└─web/playurl/index.php (WEB 播放地址)
│　└─view
│　　　└─web/season/index.php (WEB season)
├─x/
│　├─intl/passport-login/oauth2/refresh_token/index.php (东南亚APP refresh_token)
│　├─v2/search/type/index.php (APP 搜索)
│　└─web-interface/search/type/index.php (WEB 搜索)
├─utils/
│　├─auth.php (鉴权)
│　├─fuck_search.php (在搜索中添加提示)
│　├─fuck_sub.php (添加东南亚番剧字幕)
│　├─functions.php (功能函数合集)
│　├─functions_cache.php (功能函数合集)[仅缓存使用]
│　├─functions_cache_key.php (功能函数合集)[仅缓存使用]
│　├─lock_area.php (锁区、web接口判断)
│　├─process.php (处理用户传入参数)
│　├─refresh_token.php (自动刷新访问密钥)
│　├─refresh_token_th.php (自动刷新访问密钥)
│　├─replace.php (修改返回内容)
│　├─replace_playurl.php (替换视频)
│　├─resign.php (替换访问密钥)
│　└─version.php (版本信息、Header)
├─.htaccess (防止重复的 301 转址)
├─add_key.php (添加访问密钥)[仅缓存使用]
├─cache.sql (导入 MySQL 用的)[仅缓存使用]
├─config.php (设置本程序各种参数) ＜──参数设置在这里
├─hello.php (默认欢迎页面)
└─index.php (WEB 播放地址、显示欢迎页)
```
