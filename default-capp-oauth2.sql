/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  ittron
 * Created: Dec 5, 2019
 */



DROP TABLE IF EXISTS `oauth_code`;
CREATE TABLE `oauth_auth_code` (
  `oauth_code_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` int(64) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_code_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `oauth_access_token`;
CREATE TABLE `oauth_access_token` (
  `oauth_access_token_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` int(1) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_access_token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `oauth_refresh_token`;
CREATE TABLE `oauth_refresh_token` (
  `oauth_refresh_token_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `oauth_access_token_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `revoked` int(1) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_refresh_token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `oauth_client`;
CREATE TABLE `oauth_client` (
  `oauth_client_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `redirect` varchar(500) DEFAULT NULL,
  `personal_access_client` int(1) DEFAULT 0,
  `password_client` int(1) DEFAULT 0,
  `revoked` int(1) DEFAULT 0,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS `oauth_personal_access_client`;
CREATE TABLE `oauth_personal_access_client` (
  `oauth_personal_access_client_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `oauth_client_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `deleted` datetime DEFAULT NULL,
  `deletedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`oauth_personal_access_client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
