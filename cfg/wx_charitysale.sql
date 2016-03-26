-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016-03-26 14:51:44
-- 服务器版本: 5.5.44-0ubuntu0.14.04.1
-- PHP 版本: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `wx_charitysale`
--
CREATE DATABASE IF NOT EXISTS `wx_charitysale` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `wx_charitysale`;

-- --------------------------------------------------------

--
-- 表的结构 `admin_info`
--

DROP TABLE IF EXISTS `admin_info`;
CREATE TABLE IF NOT EXISTS `admin_info` (
  `uid` int(2) NOT NULL AUTO_INCREMENT COMMENT '管理员用户id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员密码md5(pass+salt)',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;

--
-- 插入之前先把表清空（truncate） `admin_info`
--

TRUNCATE TABLE `admin_info`;
--
-- 转存表中的数据 `admin_info`
--

INSERT INTO `admin_info` (`uid`, `name`, `password`) VALUES
(1, 'admin', '73cb5607665cc06905051af9bc312ec3');

-- --------------------------------------------------------

--
-- 表的结构 `donor_info`
--

DROP TABLE IF EXISTS `donor_info`;
CREATE TABLE IF NOT EXISTS `donor_info` (
  `did` int(10) NOT NULL AUTO_INCREMENT COMMENT '捐赠人id',
  `name` varchar(15) NOT NULL DEFAULT '' COMMENT '捐赠人姓名',
  `info` text NOT NULL COMMENT '捐赠人相关信息',
  PRIMARY KEY (`did`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=12 ;

--
-- 插入之前先把表清空（truncate） `donor_info`
--

TRUNCATE TABLE `donor_info`;
--
-- 转存表中的数据 `donor_info`
--

INSERT INTO `donor_info` (`did`, `name`, `info`) VALUES
(1, '4', '5'),
(2, '4', '5'),
(3, '4', '5'),
(4, '4', '5'),
(5, '4', '5'),
(6, '4', '5'),
(7, '4', '5'),
(8, '真实姓名', '个人资料'),
(9, '真实姓名', '个人资料'),
(10, '真实姓名', '个人资料'),
(11, '真实姓名', '个人资料');

-- --------------------------------------------------------

--
-- 表的结构 `goods_categories_info`
--

DROP TABLE IF EXISTS `goods_categories_info`;
CREATE TABLE IF NOT EXISTS `goods_categories_info` (
  `cateid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL,
  PRIMARY KEY (`cateid`),
  KEY `name` (`name`),
  KEY `name_2` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=12 ;

--
-- 插入之前先把表清空（truncate） `goods_categories_info`
--

TRUNCATE TABLE `goods_categories_info`;
--
-- 转存表中的数据 `goods_categories_info`
--

INSERT INTO `goods_categories_info` (`cateid`, `name`) VALUES
(10, '类别1'),
(11, '类别2');

-- --------------------------------------------------------

--
-- 表的结构 `goods_comments`
--

DROP TABLE IF EXISTS `goods_comments`;
CREATE TABLE IF NOT EXISTS `goods_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gid` (`gid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;

--
-- 插入之前先把表清空（truncate） `goods_comments`
--

TRUNCATE TABLE `goods_comments`;
--
-- 转存表中的数据 `goods_comments`
--

INSERT INTO `goods_comments` (`id`, `gid`, `uid`, `content`) VALUES
(1, 42, 5, '这是评论');

-- --------------------------------------------------------

--
-- 表的结构 `goods_info`
--

DROP TABLE IF EXISTS `goods_info`;
CREATE TABLE IF NOT EXISTS `goods_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '拍品id',
  `name` varchar(127) NOT NULL DEFAULT '' COMMENT '拍品名称',
  `category` varchar(30) NOT NULL DEFAULT '未分类' COMMENT '拍品类别',
  `description` longtext NOT NULL COMMENT '商品简介、描述之类',
  `donorinfo` varchar(255) NOT NULL DEFAULT '' COMMENT '捐赠人信息',
  `status` int(3) NOT NULL DEFAULT '0' COMMENT '商品状态',
  `imgurl` text NOT NULL,
  `price` float NOT NULL DEFAULT '0' COMMENT '拟拍价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=48 ;

--
-- 插入之前先把表清空（truncate） `goods_info`
--

TRUNCATE TABLE `goods_info`;
--
-- 转存表中的数据 `goods_info`
--

INSERT INTO `goods_info` (`id`, `name`, `category`, `description`, `donorinfo`, `status`, `imgurl`, `price`) VALUES
(37, '拍品1', '类别1', '没有简介', '张三', 101, 'image/auction/thumb/1458654910_1.jpg,', 0),
(38, '拍品2', '类别2', '没有简介2', '李四', 101, 'image/auction/thumb/1458655038_1.jpg,', 0),
(39, '拍品3', '类别1', '这个是个好东西\r\n今天的这个东西是个好东西', '张三', 101, 'image/auction/thumb/1458722728_1.jpg,', 0),
(40, '金龙鱼', '类别1', '食用油。。', '张三', 101, 'image/auction/thumb/1458723392_1.jpg,', 0),
(41, '救生圈', '类别1', '救生圈还有什么好说的', '张三', 101, 'image/auction/thumb/1458723427_1.jpg,image/auction/thumb/1458723427_2.jpg,', 0),
(42, '自行车', '类别1', '嗯', '张三', 101, 'image/auction/thumb/1458723465_1.jpg,', 0),
(43, '烤炉罩布', '类别1', '好嗯', '张三', 101, 'image/auction/thumb/1458723529_1.jpg,', 0),
(44, '手表', '类别1', '好嗯', '张三', 101, 'image/auction/thumb/1458723540_1.jpg,', 0),
(45, '手机', '类别1', '好嗯', '张三', 101, 'image/auction/thumb/1458723559_1.jpg,', 0),
(46, '宠物用饮水机', '类别1', '好嗯', '张三', 101, 'image/auction/thumb/1458723603_1.jpg,', 0),
(47, '玩具笔', '类别1', '好嗯', '张三', 101, 'image/auction/thumb/1458725113_1.jpg,', 0);

-- --------------------------------------------------------

--
-- 表的结构 `wx_relevant_info`
--

DROP TABLE IF EXISTS `wx_relevant_info`;
CREATE TABLE IF NOT EXISTS `wx_relevant_info` (
  `key` varchar(63) NOT NULL DEFAULT '' COMMENT '键',
  `value` text NOT NULL COMMENT '值',
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 插入之前先把表清空（truncate） `wx_relevant_info`
--

TRUNCATE TABLE `wx_relevant_info`;
--
-- 转存表中的数据 `wx_relevant_info`
--

INSERT INTO `wx_relevant_info` (`key`, `value`) VALUES
('access_token', 'EPA50JjB83xSg4e9n-6dF7kUf4PUIzyE8uf3EelV9zH7tVCWo1ZtRrJeXhOWI-a59P41Y6PhXXgknkHDicyqaqwZR3HthxuF0jfAr07qeevTxDUOhvKcgKbyLZojGJIDLWNcAFARCT'),
('jsapi_ticket', 'sM4AOVdWfPE4DxkXGEs8VHjpLeb8G_LlXqdsO0WqHr2VDnoeuqqobc0sXTf5BGxpoFSubpK0jZNH3RDNELIRyQ');

-- --------------------------------------------------------

--
-- 表的结构 `wx_user_info`
--

DROP TABLE IF EXISTS `wx_user_info`;
CREATE TABLE IF NOT EXISTS `wx_user_info` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_info_meta` text NOT NULL,
  `user_token_meta` text NOT NULL,
  `session_id` varchar(32) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `session_id` (`session_id`),
  KEY `session_id_2` (`session_id`),
  KEY `session_id_3` (`session_id`),
  KEY `session_id_4` (`session_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=6 ;

--
-- 插入之前先把表清空（truncate） `wx_user_info`
--

TRUNCATE TABLE `wx_user_info`;
--
-- 转存表中的数据 `wx_user_info`
--

INSERT INTO `wx_user_info` (`uid`, `user_info_meta`, `user_token_meta`, `session_id`) VALUES
(4, '{"openid":"o-TKct3xVAzsREYvnH4sJYY1Mv_8","nickname":"嗯","sex":1,"language":"zh_CN","city":"长沙","province":"湖南","country":"中国","headimgurl":"http://wx.qlogo.cn/mmopen/PiajxSqBRaELmF8A6jtKibRmia2MJMuSZ8xwd7zRDvEiaZmaiaorTskSEnXPqffLdYXXc78E6ZQmQ5HIiblKz96EfXEw/0","privilege":[]}', '{"openid":"o-TKct3xVAzsREYvnH4sJYY1Mv_8","access_token":"OezXcEiiBSKSxW0eoylIeGorfwLFHuZRs4Ts9YBMQedJxKQKGhTOSDXUMm9BxykCuxxRvI50rkggMec-jddyRWsHnC4DC8LimBJILhEEGxVAuXw7Ldf-X_GUfEAgb6rJ7tqcH7joufywWHtOz1Sutw","expires_in":7200,"refresh_token":"OezXcEiiBSKSxW0eoylIeGorfwLFHuZRs4Ts9YBMQedJxKQKGhTOSDXUMm9BxykCApP25h0RUF2xSjQJjGUSK1Cz5_ALdLJqip0i0ZMzK8XmAAaPdA3TmI9ZVeBfoTu7uQOkb_rmN6uhES1r9nH-3w","scope":"snsapi_base,snsapi_userinfo,"}', 'hIeL2rD2YMPYs3jnemVUadq5i879bIys'),
(5, '{"openid":"o-TKct3xVAzsREYvnH4sJYY1Mv_8","nickname":"嗯","sex":1,"language":"zh_CN","city":"长沙","province":"湖南","country":"中国","headimgurl":"http://wx.qlogo.cn/mmopen/PiajxSqBRaELmF8A6jtKibRmia2MJMuSZ8xwd7zRDvEiaZmaiaorTskSEnXPqffLdYXXc78E6ZQmQ5HIiblKz96EfXEw/0","privilege":[]}', '{"openid":"o-TKct3xVAzsREYvnH4sJYY1Mv_8","access_token":"OezXcEiiBSKSxW0eoylIeGorfwLFHuZRs4Ts9YBMQedJxKQKGhTOSDXUMm9BxykCFcBA6M-_YzZCbhjDvwbmQxriT5_UeMM4V057gs9F4hDGGlTTdVOdF1jKPM7bxyWwp2mGTy2tEN_fJ5PuGw4ENw","expires_in":7200,"refresh_token":"OezXcEiiBSKSxW0eoylIeGorfwLFHuZRs4Ts9YBMQedJxKQKGhTOSDXUMm9BxykCFcBA6M-_YzZCbhjDvwbmQwjddYf0ZQgOBND1szgUc6b-8HUk62iSsSsD0YdxWOF-UJpBS0vrKgQr80nhCp4ERQ","scope":"snsapi_base,snsapi_userinfo,"}', 'dn3sTfvFX9Hs1uIyt0iAxmNEWtwxiPWw');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
