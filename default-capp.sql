-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.21 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

DROP TABLE IF EXISTS `log_activity`;
CREATE TABLE `log_activity` (
  `log_activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `app_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `remote_addr` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `platform_version` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `browser_version` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `routed_uri` varchar(255) DEFAULT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `query_string` varchar(255) DEFAULT NULL,
  `nav` varchar(255) DEFAULT NULL,
  `nav_label` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `action_label` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `activity_date` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`log_activity_id`),
  UNIQUE KEY `log_activity_id` (`log_activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_login`;
CREATE TABLE IF NOT EXISTS `log_login` (
  `log_login_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `app_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `remote_addr` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `platform_version` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `browser_version` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `login_date` datetime DEFAULT NULL,
  PRIMARY KEY (`log_login_id`),
  UNIQUE KEY `log_login_id` (`log_login_id`)
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_login_fail`;
CREATE TABLE IF NOT EXISTS `log_login_fail` (
  `log_login_fail_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `app_id` bigint(20) unsigned DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `remote_addr` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `platform_version` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `browser_version` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `login_fail_date` datetime DEFAULT NULL,
  PRIMARY KEY (`log_login_fail_id`),
  UNIQUE KEY `log_login_fail_id` (`log_login_fail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `org`;
CREATE TABLE IF NOT EXISTS `org` (
  `org_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(100) DEFAULT '',
  `org_category` varchar(100) DEFAULT '',
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `contact_person` varchar(50) DEFAULT NULL,
  `credit_limit` double DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`org_id`),
  UNIQUE KEY `org_id` (`org_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table torsb2c.org: ~0 rows (approximately)
/*!40000 ALTER TABLE `org` DISABLE KEYS */;
INSERT INTO `org` (`org_id`, `code`, `org_category`, `name`, `address`, `city`, `fax`, `email`, `phone`, `mobile`, `contact_person`, `credit_limit`, `created`, `createdby`, `updated`, `updatedby`, `status`) VALUES
	(1, 'ittron', 'Ittron', 'Ittron', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 1);
/*!40000 ALTER TABLE `org` ENABLE KEYS */;


-- Dumping structure for table torsb2c.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `depth` bigint(20) unsigned DEFAULT NULL,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `lft` bigint(20) DEFAULT NULL,
  `rgt` bigint(20) DEFAULT NULL,
  `is_base` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(60) DEFAULT NULL COMMENT 'The name of an entity (record) is used as an default search option in addition to the search key. The name is up to 60 characters in length.',
  `description` varchar(255) DEFAULT NULL,
  `store_id` bigint(20) DEFAULT NULL,
  `role_level` int(11) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `createdby` varchar(32) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(32) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `roleid` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table torsb2c.roles: ~6 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`role_id`, `parent_id`, `depth`, `org_id`, `lft`, `rgt`, `is_base`, `name`, `description`, `store_id`, `created`, `createdby`, `updated`, `updatedby`, `status`, `role_level`) VALUES
	(1, NULL, NULL, NULL, 1, 2, 0, 'SUPERADMIN', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;


-- Dumping structure for table torsb2c.role_nav
DROP TABLE IF EXISTS `role_nav`;
CREATE TABLE IF NOT EXISTS `role_nav` (
  `role_nav_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `app_id` bigint(20) unsigned DEFAULT NULL,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `nav` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(32) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(32) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`role_nav_id`),
  UNIQUE KEY `role_nav_id` (`role_nav_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE IF NOT EXISTS `role_permission` (
  `role_permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned DEFAULT NULL,
  `app_id` bigint(20) unsigned DEFAULT NULL,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `nav` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(32) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(32) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`role_permission_id`),
  UNIQUE KEY `role_permission_id` (`role_permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;


DROP TABLE IF EXISTS `sys_counter`;
CREATE TABLE IF NOT EXISTS `sys_counter` (
  `sys_counter_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`sys_counter_id`),
  UNIQUE KEY `counter_id` (`sys_counter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `user_photo` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_request` datetime DEFAULT NULL,
  `login_count` int(11) DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `activation_date` datetime DEFAULT NULL,
  `have_notification` int(11) DEFAULT '0',
  `is_base` tinyint(1) NOT NULL DEFAULT '0',
  `is_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `have_issued` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `createdby` varchar(32) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(32) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `fk_users_roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`user_id`, `role_id`, `org_id`, `store_id`, `username`, `user_photo`, `first_name`, `last_name`, `password`, `email`, `last_login`, `last_request`, `login_count`, `description`, `activation_code`, `activation_date`, `have_notification`, `is_base`, `is_disabled`, `have_issued`, `created`, `createdby`, `updated`, `updatedby`, `status`) VALUES
	(1, 1, 15, NULL, 'superadmin', NULL, NULL, NULL, '21232f297a57a5a743894a0e4a801fc3', NULL, '2016-01-13 21:13:17', '2016-01-13 22:07:39', 195, NULL, NULL, NULL, 0, 0, 0, 1, '2015-07-24 11:10:49', NULL, NULL, NULL, 1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;


DROP TABLE IF EXISTS `var`;
CREATE TABLE IF NOT EXISTS `var` (
  `var_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) NOT NULL,
  `key` varchar(32) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `value` longtext,
  `is_var_user` tinyint(1) NOT NULL DEFAULT '0',
  `data_type` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`var_id`),
  UNIQUE KEY `var_id` (`var_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*!40000 ALTER TABLE `var` DISABLE KEYS */;
/*!40000 ALTER TABLE `var` ENABLE KEYS */;


DROP TABLE IF EXISTS `var_user`;
CREATE TABLE IF NOT EXISTS `var_user` (
  `var_user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `key` varchar(32) NOT NULL,
  `value` longtext,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`var_user_id`),
  UNIQUE KEY `var_user_id` (`var_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `resource`;
CREATE TABLE `resource` (
  `resource_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) DEFAULT NULL,
  `collection_name` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) DEFAULT NULL,
  `size` int(20) unsigned DEFAULT NULL,
  `manipulations` longtext,
  `custom_properties` longtext,
  `responsive_images` longtext,
  `order_column` int(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40000 ALTER TABLE `var_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `var_user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
