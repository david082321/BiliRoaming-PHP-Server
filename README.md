# BiliRoaming-PHP-Server
哔哩漫游 PHP 解析服务器


自建解析服务器参考配置

## 下载：

* [下载(GitHub) v2.9.17](https://github.com/david082321/BiliRoaming-PHP-Server/raw/main/Server_v2.9.17.zip)


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

    }

## (非必要步骤) 缓存

* 安装 MySQL

* 配置 config.php 的缓存设置

* 导入 cache.sql

## (非必要步骤，实验性) 支持网页版油猴脚本

* [油猴脚本地址](https://github.com/ipcjs/bilibili-helper/blob/user.js/packages/unblock-area-limit/README.md)

* 配置 config.php 的 WEB_ON

* 脚本的 代理服务器->自定义 输入以下内容 ( example.com 请改成你的服务器地址)

##### 　　　https://example.com/

* 脚本的 代理服务器->自定义(泰国/东南亚) 输入以下内容 ( example.com 请改成你的服务器地址)

##### 　　　https://example.com/intl/gateway/v2/ogv/playurl/

* (可选步骤) 配置上面的禁用 301 转址。然后脚本的 代理服务器->自定义 改成

* 注意：不配置上方的 「禁用 301 转址」，少部分 泰国/东南亚番剧 将无法加载。

##### 　　　https://example.com

--------

# 文件功能介绍

├─intl/gateway/v2

│　├─app

│　│　├─search/type/index.php (转发到根目录的 index.php 处理)

│　│　└─subtitle/index.php (转发到根目录的 index.php 处理)

│　└─ogv

│　　　├─playurl/index.php (转发到根目录的 index.php 处理)

│　　　└─view/app/season/index.php (转发到根目录的 index.php 处理)

├─pgc/player/

│　├─api/playurl/index.php (转发到根目录的 index.php 处理)

│　└─web/playurl/index.php (转发到根目录的 index.php 处理)

├─auth.php (鉴权)

├─cache.php (缓存)[仅缓存使用]

├─cache_season.php (缓存泰国/东南亚season)[仅缓存使用]

├─cache.sql (导入MySQL用的)[仅缓存使用]

├─config.php (用户设置)

├─index.php (接受上面index.php) ＜──主要入口在这里

├─log.php (缓存用户)[仅缓存使用]

├─replace.php (替换视频)

├─sign.php (重签名)[未公开源码]

└─resign.php (替换access_key)[未公开源码]
