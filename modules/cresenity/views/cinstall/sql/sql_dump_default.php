<?php defined("SYSPATH") or die("No direct script access.") ?>
SET NAMES 'utf8';


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>app`;

CREATE TABLE `<?php echo $table_prefix ?>app` (
  `app_id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `version`int(11) default 0,
  `have_menu` tinyint(4) default '0',
  `is_desktop` tinyint(4) NOT NULL default '0',
  `is_mobile` tinyint(4) NOT NULL default '0',
  `is_web` tinyint(4) NOT NULL default '0',
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`app_id`),
  UNIQUE KEY `app_id` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert  into `<?php echo $table_prefix ?>app`(`app_id`,`name`,`version`,`is_desktop`,`is_mobile`,`is_web`,`created`,`updated`,`status`) values (1,'Web Application',1,0,0,1,now(),now(),1);

CREATE TABLE `<?php echo $table_prefix ?>config` (
  `config_id` bigint(21) NOT NULL auto_increment,
  `context` varchar(200) NOT NULL,
  `key` varchar(200) NOT NULL,
  `value` varchar(200) default NULL,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_id` (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `<?php echo $table_prefix ?>config`(`context`,`key`,`value`,`created`,`updated`) VALUES ('ccms', 'page_views', 'default, extended',now(),now());
INSERT INTO `<?php echo $table_prefix ?>config`(`context`,`key`,`value`,`created`,`updated`) VALUES ('ccms', 'site_title', 'My Website',now(),now());
INSERT INTO `<?php echo $table_prefix ?>config`(`context`,`key`,`value`,`created`,`updated`) VALUES ('ccms', 'default_sidebar_title', 'About',now(),now());
INSERT INTO `<?php echo $table_prefix ?>config`(`context`,`key`,`value`,`created`,`updated`) VALUES ('ccms', 'default_sidebar_content', 'Cresenity Content Management System.',now(),now());
INSERT INTO `<?php echo $table_prefix ?>config`(`context`,`key`,`value`,`created`,`updated`) VALUES ('ccms', 'theme', 'default',now(),now());



DROP TABLE IF EXISTS `<?php echo $table_prefix ?>modules`;

CREATE TABLE `<?php echo $table_prefix ?>modules` (
  `module_id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `version` int(11) default 0,
  `is_enabled` int(11) default 1,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`module_id`),
  UNIQUE KEY `moduleid` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>module_depend`;

CREATE TABLE `<?php echo $table_prefix ?>module_depend` (
  `module_depend_id` bigint(20) unsigned NOT NULL auto_increment,
  `module_id` bigint(20) unsigned NOT NULL,
  `depend_id` bigint(20) unsigned NOT NULL,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`module_depend_id`),
  UNIQUE KEY `module_depend_id` (`module_depend_id`),
  KEY `fk_module_depend_module` (`module_id`),
  KEY `fk_module_depend_depend` (`depend_id`),
  CONSTRAINT `fk_module_depend_module` FOREIGN KEY (`module_id`) REFERENCES `<?php echo $table_prefix ?>modules` (`module_id`),
  CONSTRAINT `fk_module_depend_depend` FOREIGN KEY (`depend_id`) REFERENCES `<?php echo $table_prefix ?>modules` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `<?php echo $table_prefix ?>roles`;

CREATE TABLE `<?php echo $table_prefix ?>roles` (
  `role_id` bigint(20) unsigned NOT NULL auto_increment,
  `is_base` tinyint(4) NOT NULL default '0',
  `name` varchar(60) default NULL COMMENT 'The name of an entity (record) is used as an default search option in addition to the search key. The name is up to 60 characters in length.',
  `description` varchar(255) default NULL,
  `created` datetime default NULL,
  `createdby` varchar(32) default NULL,
  `updated` datetime default NULL,
  `updatedby` varchar(32) default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`role_id`),
  UNIQUE KEY `roleid` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert  into `<?php echo $table_prefix ?>roles`(`role_id`,`is_base`,`name`,`description`,`created`,`createdby`,`updated`,`updatedby`,`status`) values (1,1,'admin',NULL,NULL,NULL,NULL,NULL,1);

DROP TABLE IF EXISTS `<?php echo $table_prefix ?>users`;

CREATE TABLE `<?php echo $table_prefix ?>users` (
  `user_id` bigint(20) unsigned NOT NULL auto_increment,
  `role_id` bigint(20) unsigned NOT NULL,
  `username` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `last_login` datetime default NULL,
  `login_count` int(11) default '0',
  `description` varchar(255) default NULL,
  `is_base` tinyint(1) NOT NULL default '0',
  `created` datetime default NULL,
  `createdby` varchar(32) default NULL,
  `updated` datetime default NULL,
  `updatedby` varchar(32) default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `fk_users_roles` (`role_id`),
  CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `<?php echo $table_prefix ?>roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert  into `<?php echo $table_prefix ?>users`(`user_id`,`role_id`,`username`,`password`,`email`,`last_login`,`login_count`,`description`,`is_base`,`created`,`createdby`,`updated`,`updatedby`,`status`) values (1,1,'admin','21232f297a57a5a743894a0e4a801fc3',NULL,now(),1,NULL,0,NULL,NULL,NULL,NULL,1);


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>menu_type`;

CREATE TABLE `<?php echo $table_prefix ?>menu_type` (
  `menu_type_id` bigint(20) unsigned NOT NULL auto_increment,
  `is_base` tinyint(4) NOT NULL default '0',
  `name` varchar(60) default NULL COMMENT 'The name of an entity (record) is used as an default search option in addition to the search key. The name is up to 60 characters in length.',
  `key` varchar(60) default NULL,
  `description` varchar(255) default NULL,
  `created` datetime default NULL,
  `createdby` varchar(32) default NULL,
  `updated` datetime default NULL,
  `updatedby` varchar(32) default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`menu_type_id`),
  UNIQUE KEY `menutypeid` (`menu_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert  into `<?php echo $table_prefix ?>menu_type`(`menu_type_id`,`is_base`,`name`,`key`,`description`,`created`,`createdby`,`updated`,`updatedby`,`status`) values (1,1,'Default Menu','default',NULL,NULL,NULL,NULL,NULL,0);
insert  into `<?php echo $table_prefix ?>menu_type`(`menu_type_id`,`is_base`,`name`,`key`,`description`,`created`,`createdby`,`updated`,`updatedby`,`status`) values (2,1,'Group Menu','group',NULL,NULL,NULL,NULL,NULL,1);
insert  into `<?php echo $table_prefix ?>menu_type`(`menu_type_id`,`is_base`,`name`,`key`,`description`,`created`,`createdby`,`updated`,`updatedby`,`status`) values (3,1,'Module Menu','module',NULL,NULL,NULL,NULL,NULL,1);
insert  into `<?php echo $table_prefix ?>menu_type`(`menu_type_id`,`is_base`,`name`,`key`,`description`,`created`,`createdby`,`updated`,`updatedby`,`status`) values (4,1,'Page Menu','page',NULL,NULL,NULL,NULL,NULL,0);
insert  into `<?php echo $table_prefix ?>menu_type`(`menu_type_id`,`is_base`,`name`,`key`,`description`,`created`,`createdby`,`updated`,`updatedby`,`status`) values (5,1,'Custom Menu','custom',NULL,NULL,NULL,NULL,NULL,0);


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>menu_module`;

CREATE TABLE `<?php echo $table_prefix ?>menu_module` (
  `menu_module_id` bigint(20) unsigned NOT NULL auto_increment,
  `app_id` bigint(20) unsigned default NULL,
  `context` varchar(100) default NULL,
  `name` varchar(100) default NULL,
  `url` varchar(255) default NULL,
  `controller` varchar(255) default NULL,
  `method` varchar(255) default NULL,
  `have_add` int(11) unsigned NOT NULL default '0',
  `have_edit` int(11) unsigned NOT NULL default '0',
  `have_delete` int(11) unsigned NOT NULL default '0',
  `have_void` int(11) unsigned NOT NULL default '0',
  `have_print` int(11) unsigned NOT NULL default '0',
  `have_download` int(11) unsigned NOT NULL default '0',
  `have_upload` int(11) unsigned NOT NULL default '0',
  `description` text,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  CONSTRAINT `fk_menu_module_app` FOREIGN KEY  (`app_id`) REFERENCES `<?php echo $table_prefix ?>app`(`app_id`),
  PRIMARY KEY  (`menu_module_id`),
  UNIQUE KEY `menumoduleid` (`menu_module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert  into `<?php echo $table_prefix ?>menu_module`(`menu_module_id`,`app_id`,`context`,`name`,`url`,`controller`,`method`,`have_add`,`have_edit`,`have_delete`,`have_void`,`have_print`,`have_download`,`have_upload`,`description`,`created`,`updated`,`status`) values (1,1,NULL,'dashboard','home','home','index',0,0,0,0,0,0,0,NULL,NULL,NULL,1);
insert  into `<?php echo $table_prefix ?>menu_module`(`menu_module_id`,`app_id`,`context`,`name`,`url`,`controller`,`method`,`have_add`,`have_edit`,`have_delete`,`have_void`,`have_print`,`have_download`,`have_upload`,`description`,`created`,`updated`,`status`) values (2,1,NULL,'roles','roles','roles','index',1,1,1,0,0,0,0,NULL,NULL,NULL,1);
insert  into `<?php echo $table_prefix ?>menu_module`(`menu_module_id`,`app_id`,`context`,`name`,`url`,`controller`,`method`,`have_add`,`have_edit`,`have_delete`,`have_void`,`have_print`,`have_download`,`have_upload`,`description`,`created`,`updated`,`status`) values (3,1,NULL,'users','users','users','index',1,1,1,0,0,0,0,NULL,NULL,NULL,1);
insert  into `<?php echo $table_prefix ?>menu_module`(`menu_module_id`,`app_id`,`context`,`name`,`url`,`controller`,`method`,`have_add`,`have_edit`,`have_delete`,`have_void`,`have_print`,`have_download`,`have_upload`,`description`,`created`,`updated`,`status`) values (4,1,NULL,'user_rights','user_rights','user_rights','index',0,0,0,0,0,0,0,NULL,NULL,NULL,1);


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>menu`;

CREATE TABLE `<?php echo $table_prefix ?>menu` (
  `menu_id` bigint(20) unsigned NOT NULL auto_increment,
  `parent_id` bigint(20) unsigned default NULL,
  `app_id` bigint(20) unsigned default NULL,
  `menu_type_id` bigint(20) unsigned default NULL,
  `menu_module_id` bigint(20) unsigned default NULL,
  `context` varchar(100) default NULL,
  `name` varchar(100) default NULL,
  `caption` varchar(30) default NULL,
  `url` varchar(255) default NULL,
  `controller` varchar(255) default NULL,
  `method` varchar(255) default NULL,
  `seqno` int(11) default NULL,
  `have_add` int(11) unsigned NOT NULL default '0',
  `have_edit` int(11) unsigned NOT NULL default '0',
  `have_delete` int(11) unsigned NOT NULL default '0',
  `have_void` int(11) unsigned NOT NULL default '0',
  `have_print` int(11) unsigned NOT NULL default '0',
  `have_download` int(11) unsigned NOT NULL default '0',
  `have_upload` int(11) unsigned NOT NULL default '0',
  `is_leaf` int(11) unsigned NOT NULL default '1',
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`menu_id`),
  UNIQUE KEY `menuid` (`menu_id`),
  KEY `fk_menu_parent` (`parent_id`),
  KEY `fk_menu_app` (`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>menu_role`;

CREATE TABLE `<?php echo $table_prefix ?>menu_role` (
  `menu_role_id` bigint(20) unsigned NOT NULL auto_increment,
  `role_id` bigint(20) unsigned NOT NULL,
  `menu_id` bigint(20) unsigned NOT NULL,
  `context` varchar(100) default NULL,
  `can_add` int(11) default '1',
  `can_edit` int(11) NOT NULL default '1',
  `can_delete` int(11) NOT NULL default '1',
  `can_void` int(11) NOT NULL default '1',
  `can_print` int(11) NOT NULL default '1',
  `can_download` int(11) NOT NULL default '1',
  `can_upload` int(11) NOT NULL default '1',
  `created` datetime default NULL,
  `createdby` varchar(32) default NULL,
  `updated` datetime default NULL,
  `updatedby` varchar(32) default NULL,
  `status` int(11) default '1',
  PRIMARY KEY  (`menu_role_id`),
  UNIQUE KEY `menu_role_id` (`menu_role_id`),
  UNIQUE KEY `role_id` (`role_id`,`menu_id`),
  KEY `fk_menu_role_menu` (`menu_id`),
  CONSTRAINT `fk_menu_role_menu` FOREIGN KEY (`menu_id`) REFERENCES `<?php echo $table_prefix ?>menu` (`menu_id`),
  CONSTRAINT `fk_menu_role_roles` FOREIGN KEY (`role_id`) REFERENCES `<?php echo $table_prefix ?>roles` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>var`;

CREATE TABLE `<?php echo $table_prefix ?>var` (
  `var_id` bigint(20) unsigned NOT NULL auto_increment,
  `key` varchar(32) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `value` text,
  `is_var_user` tinyint(1) NOT NULL default '0',
  `data_type` varchar(255) NOT NULL,
  `description` varchar(255) default NULL,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`var_id`),
  UNIQUE KEY `var_id` (`var_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


insert  into `<?php echo $table_prefix ?>var`(`key`,`caption`,`value`,`is_var_user`,`data_type`,`description`,`created`,`updated`,`status`) values ('WEB_PAGING','Paging Number','20',1,'int',NULL,NULL,NULL,1);


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>var_user`;

CREATE TABLE `<?php echo $table_prefix ?>var_user` (
  `var_user_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned NOT NULL,
  `key` varchar(32) NOT NULL,
  `value` text,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`var_user_id`),
  UNIQUE KEY `var_user_id` (`var_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>log_login`;

CREATE TABLE `<?php echo $table_prefix ?>log_login` (
  `log_login_id` bigint(20) unsigned NOT NULL auto_increment,
  `user_id` bigint(20) unsigned default NULL,
  `app_id` bigint(20) unsigned default NULL,
  `session_id` varchar(255) default NULL,
  `remote_addr` varchar(50) default NULL,
  `user_agent` varchar(255) default NULL,
  `login_date` datetime default NULL,
  PRIMARY KEY  (`log_login_id`),
  UNIQUE KEY `log_login_id` (`log_login_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `<?php echo $table_prefix ?>widgets`;

CREATE TABLE `<?php echo $table_prefix ?>widgets` (
  `widget_id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `version` int(11) default '0',
  `is_enabled` int(11) default '1',
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`widget_id`),
  UNIQUE KEY `widgetid` (`widget_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `<?php echo $table_prefix ?>sys_counter`;

CREATE TABLE `<?php echo $table_prefix ?>sys_counter` (
  `sys_counter_id` bigint(20) unsigned NOT NULL auto_increment,
  `key` varchar(20) default NULL,
  `counter` int(11) default NULL,
  `created` datetime default NULL,
  `updated` datetime default NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`sys_counter_id`),
  UNIQUE KEY `counter_id` (`sys_counter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8