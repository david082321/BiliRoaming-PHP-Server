# BiliRoaming-PHP-Server
哔哩漫游 PHP 解析服务器


自建解析服务器参考配置

## 下载：

* [下载(GitHub) v2.7](https://github.com/david082321/BiliRoaming-PHP-Server/raw/main/Server_v2.7.zip)


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
  
	rewrite "^/intl/gateway/v2/ogv/playurl?(.*)$" /intl/gateway/v2/ogv/playurl/index.php?$1 last;
  
	rewrite "^/intl/gateway/v2/app/search/type?(.*)$" /intl/gateway/v2/app/search/type/index.php?$1 last;
  
	rewrite "^/intl/gateway/v2/app/subtitle?(.*)$" /intl/gateway/v2/app/subtitle?$1 last;
  
	}

## (非必要步骤) 缓存

* 安装 MySQL

* 配置 config.php 的缓存设置

* 导入 cache.sql

