-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2025-10-04 14:52:37
-- 服务器版本： 5.7.44-log
-- PHP 版本： 7.3.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `sidebar_system`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `admin_password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ad_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `admin_password_hash`, `ad_url`, `created_at`, `updated_at`) VALUES
(1, '$2y$10$zaZnzth97NB6E73rtTCXIeej7ETGFM16EsXJSqSIl4sMw/ZFBrsp2', '', '2025-09-24 13:44:23', '2025-09-27 10:49:17');

-- --------------------------------------------------------

--
-- 表的结构 `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `is_active`, `created_at`, `updated_at`) VALUES
(9, '国庆特惠活动', '国庆特惠活动！挂机宝3元起，独立IP云服务器9.9起。活动详情见控制台-国庆促销', 1, '2025-10-01 13:54:10', '2025-10-01 13:54:10');

-- --------------------------------------------------------

--
-- 表的结构 `sidebar_config`
--

CREATE TABLE `sidebar_config` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('group','dropdown','button') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `sidebar_config`
--

INSERT INTO `sidebar_config` (`id`, `name`, `type`, `title`, `icon`, `url`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(581, '我的产品', 'group', '我的产品', 'fas fa-home', '', NULL, 5, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(582, '服务支持', 'group', '服务支持', 'fas fa-home', '', NULL, 10, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(583, '优惠福利', 'group', '优惠福利', 'fas fa-home', '', NULL, 13, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(584, '国庆促销', 'button', '国庆促销', 'fas fa-tag', 'https://app1.xuanyiy.cn/xyy/sales/guoqing.html', NULL, 1, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(585, '主页', 'button', '主页', 'fas fa-home', 'content.html', NULL, 2, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(586, '订购产品', 'button', '订购产品', 'fas fa-shopping-cart', 'https://app1.xuanyiy.cn/xyy/c', NULL, 3, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(587, '账单充值', 'button', '账单充值', 'fas fa-money-bill', 'https://app1.xuanyiy.cn/addfunds#', NULL, 4, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(588, '挂机宝', 'button', '挂机宝', 'fas fa-laptop', 'https://app1.xuanyiy.cn/service?groupid=339', NULL, 6, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(589, '云服务器', 'button', '云服务器', 'fas fa-server', 'https://app1.xuanyiy.cn/service?groupid=337', NULL, 7, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(590, '虚拟主机', 'button', '虚拟主机', 'fas fa-wifi', 'https://app1.xuanyiy.cn/service?groupid=338', NULL, 8, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(591, '其他', 'button', '其他', 'fas fa-clipboard-list', 'https://app1.xuanyiy.cn/service?groupid=340', NULL, 9, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(592, '微信客服', 'button', '微信客服', 'fas fa-chalkboard-teacher', 'https://app1.xuanyiy.cn/xyy/kf', NULL, 11, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(593, '我要投诉', 'button', '我要投诉', 'fas fa-fire', 'https://app1.xuanyiy.cn/supporttickets', NULL, 12, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(594, '推介计划', 'button', '推介计划', 'fas fa-passport', 'https://app1.xuanyiy.cn/addons?_plugin=points_mall&_controller=index&_action=task', NULL, 14, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08'),
(595, '积分中心', 'button', '积分中心', 'fas fa-gift', 'https://app1.xuanyiy.cn/xyy/jf.html', NULL, 15, 1, '2025-10-03 10:43:08', '2025-10-03 10:43:08');

--
-- 转储表的索引
--

--
-- 表的索引 `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `sidebar_config`
--
ALTER TABLE `sidebar_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `sort_order` (`sort_order`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `sidebar_config`
--
ALTER TABLE `sidebar_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=596;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
