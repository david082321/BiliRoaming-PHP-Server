-- phpMyAdmin SQL Dump
-- version 5.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE `cache` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id',
  `expired_time` int(11) NOT NULL COMMENT '到期时间',
  `area` varchar(10) DEFAULT NULL,
  `type` tinyint(4) NOT NULL,
  `cache_type` varchar(100) NOT NULL,
  `cid` varchar(100) NOT NULL,
  `ep_id` varchar(100) DEFAULT NULL,
  `cache` mediumtext NOT NULL COMMENT '缓存内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='缓存';

ALTER TABLE `cache`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cache`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';

CREATE TABLE `keys` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `uid` int(20) DEFAULT NULL COMMENT '用户ID',
  `access_key` varchar(100) DEFAULT NULL,
  `due_date` bigint(50) DEFAULT NULL,
  `expired` tinyint(1) NOT NULL DEFAULT 0 COMMENT '过期key'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访客表';

ALTER TABLE `keys`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';

CREATE TABLE `my_keys` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id',
  `expired_time` int(11) NOT NULL COMMENT '到期时间',
  `type` tinyint(4) NOT NULL COMMENT '类型',
  `uid` int(20) DEFAULT NULL COMMENT '用户ID',
  `access_token` varchar(100) NOT NULL,
  `refresh_token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='我的key';

ALTER TABLE `my_keys`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `my_keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';

CREATE TABLE `status` (
  `id` int(10) NOT NULL COMMENT 'id',
  `expired_time` int(11) NOT NULL COMMENT '到期时间',
  `uid` int(20) NOT NULL COMMENT '用户id',
  `is_blacklist` tinyint(1) NOT NULL,
  `is_whitelist` tinyint(1) NOT NULL,
  `reason` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='黑白名单';

ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';

CREATE TABLE `log` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id',
  `time` datetime NOT NULL COMMENT '添加时间',
  `ip` tinytext,
  `area` tinytext COMMENT '地区',
  `version` tinytext COMMENT '漫游版本号',
  `version_code` smallint(6) DEFAULT '0' COMMENT '漫游版本编号',
  `access_key` tinytext,
  `uid` int(11) DEFAULT '0',
  `ban_code` tinyint(4) DEFAULT '0' COMMENT '封禁代号',
  `path` text COMMENT '请求路径',
  `query` mediumtext COMMENT '请求参数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='日志';

ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';

COMMIT;