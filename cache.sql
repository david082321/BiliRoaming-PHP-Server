-- phpMyAdmin SQL Dump
-- version 5.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

CREATE TABLE `cache` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `area` varchar(10) DEFAULT NULL,
  `type` tinyint(4) NOT NULL,
  `cache_type` varchar(100) NOT NULL,
  `cid` varchar(100) NOT NULL,
  `ep_id` varchar(100) DEFAULT NULL,
  `cache` mediumtext NOT NULL COMMENT '缓存内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='缓存';

ALTER TABLE `cache`
  ADD PRIMARY KEY (`id`),
  ADD KEY `add_time` (`add_time`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `add_time` (`add_time`);

ALTER TABLE `keys`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id';
COMMIT;
