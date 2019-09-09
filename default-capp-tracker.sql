/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  Hery Kurniawan
 * Created: Jun 23, 2019
 */

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


DROP TABLE IF EXISTS `log_device`;
CREATE TABLE `log_device` (
  `log_device_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `kind` varchar(16) DEFAULT NULL,
  `model` varchar(64) DEFAULT NULL,
  `platform` varchar(64) DEFAULT NULL,
  `platform_version` varchar(16) DEFAULT NULL,
  `is_mobile` tinyint(4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `log_geoip`;
CREATE TABLE `log_geoip` (
  `log_geoip_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `country_code3` varchar(3) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `region` varchar(2) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `area_code` bigint(20) DEFAULT NULL,
  `dma_code` double DEFAULT NULL,
  `metro_code` double DEFAULT NULL,
  `continent_code` varchar(2) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_geoip_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `log_agent`;
CREATE TABLE `log_agent` (
  `log_agent_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(1000) DEFAULT NULL,
  `name_hash` varchar(65) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `browser_version` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_agent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `log_domain`;
CREATE TABLE `log_domain` (
  `log_domain_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_domain_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;




DROP TABLE IF EXISTS `log_referer`;
CREATE TABLE `log_referer` (
  `log_referer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `log_domain_id` bigint(20) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `search_terms_hash` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_referer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;




DROP TABLE IF EXISTS `log_referer_search_term`;
CREATE TABLE `log_referer_search_term` (
  `log_referer_search_term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `log_referer_id` bigint(20) unsigned DEFAULT NULL,
  `search_term` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_referer_search_term_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_cookie`;
CREATE TABLE `log_cookie` (
  `log_cookie_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `uuid` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_cookie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_language`;
CREATE TABLE `log_language` (
  `log_language_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `preference` varchar(255) DEFAULT NULL,
  `language_range` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_session`;
CREATE TABLE `log_session` (
  `log_session_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `uuid` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_type` varchar(50) DEFAULT 'user',
  `log_device_id` bigint(20) unsigned DEFAULT NULL,
  `log_agent_id` bigint(20) unsigned DEFAULT NULL,
  `log_referer_id` bigint(20) unsigned DEFAULT NULL,
  `log_cookie_id` bigint(20) unsigned DEFAULT NULL,
  `log_geoip_id` bigint(20) unsigned DEFAULT NULL,
  `log_language_id` bigint(20) unsigned DEFAULT NULL,
  `client_ip` varchar(255) DEFAULT NULL,
  `is_robot` tinyint(4) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_session_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `log_path`;
CREATE TABLE `log_path` (
  `log_path_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `path` varchar(500) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_path_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_query`;
CREATE TABLE `log_query` (
  `log_query_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `query` varchar(1000) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_query_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_query_argument`;
CREATE TABLE `log_query_argument` (
  `log_query_argument_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `log_query_id` bigint(20) unsigned DEFAULT NULL,
  `argument` varchar(500) DEFAULT NULL,
  `value` varchar(500) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_query_argument_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `log_route`;
CREATE TABLE `log_route` (
  `log_route_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_route_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `log_route_path`;
CREATE TABLE `log_route_path` (
  `log_route_path_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `log_route_id` bigint(20) unsigned DEFAULT NULL,
  `path` varchar(500) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_route_path_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `log_route_path_parameter`;
CREATE TABLE `log_route_path_parameter` (
  `log_route_path_parameter_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `log_route_path_id` bigint(20) unsigned DEFAULT NULL,
  `parameter` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_route_path_parameter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_connection`;
CREATE TABLE `log_connection` (
  `log_connection_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_connection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `log_sql_query`;
CREATE TABLE `log_sql_query` (
  `log_sql_query_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `sha1` varchar(40) DEFAULT NULL,
  `statement` text DEFAULT NULL,
  `time` double DEFAULT NULL,
  `log_connection_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_sql_query_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `log_sql_query_binding`;
CREATE TABLE `log_sql_query_binding` (
  `log_sql_query_binding_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `sha1` varchar(40) DEFAULT NULL,
  `serialized` text DEFAULT NULL,
  `log_sql_query_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_sql_query_binding_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;




DROP TABLE IF EXISTS `log_sql_query_binding_parameter`;
CREATE TABLE `log_sql_query_binding_parameter` (
  `log_sql_query_binding_parameter_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `log_sql_query_binding_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_sql_query_binding_parameter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_error`;
CREATE TABLE `log_error` (
  `log_error_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_error_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;




DROP TABLE IF EXISTS `log_event`;
CREATE TABLE `log_event` (
  `log_event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `log_system_class`;
CREATE TABLE `log_system_class` (
  `log_system_class_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_system_class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `log_log`;
CREATE TABLE `log_log` (
  `log_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `log_session_id` bigint(20) unsigned DEFAULT NULL,
  `log_path_id` bigint(20) unsigned DEFAULT NULL,
  `log_query_id` bigint(20) unsigned DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `log_referer_id` bigint(20) unsigned DEFAULT NULL,
  `log_route_path_id` bigint(20) unsigned DEFAULT NULL,
  `is_ajax` tinyint(4) NOT NULL DEFAULT '1',
  `is_secure` tinyint(4) NOT NULL DEFAULT '1',
  `is_json` tinyint(4) NOT NULL DEFAULT '1',
  `wants_json` tinyint(4) NOT NULL DEFAULT '1',
  `log_error_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
  

DROP TABLE IF EXISTS `log_sql_query_log`;
CREATE TABLE `log_sql_query_log` (
  `log_sql_query_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `log_log_id` bigint(20) unsigned DEFAULT NULL,
  `log_sql_query_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_sql_query_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `log_event_log`;
CREATE TABLE `log_event_log` (
  `log_event_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` bigint(20) unsigned DEFAULT NULL,
  `log_log_id` bigint(20) unsigned DEFAULT NULL,
  `log_event_id` bigint(20) unsigned DEFAULT NULL,
  `log_system_class_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(50) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`log_event_log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
